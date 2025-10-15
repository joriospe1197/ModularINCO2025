import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import mysql from 'mysql2/promise';

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

// Conexión a la base de datos
let db;
try {
  db = await mysql.createConnection({
    host: 'gondola.proxy.rlwy.net',
    user: 'root',
    password: 'hgVvISLFXzOyIALjHttvbipzQmSCPMAl',
    database: 'constructora'
  });
  console.log('Conectado a la base de datos MySQL');
} catch (error) {
  console.error('Error al conectar con la base de datos:', error);
  process.exit(1);
}

const userSessions = new Map();

io.on('connection', async (socket) => {
  console.log('Nuevo cliente conectado:', socket.id);

  // Evento para historial a demanda
  socket.on('pedir_historial', async () => {
    try {
      const [rows] = await db.execute(`
        SELECT e.nombre, m.mensaje, m.fecha, e.idempleado
        FROM mensajes_chat m
        JOIN empleados e ON e.idempleado = m.idempleado
        ORDER BY m.fecha ASC
        LIMIT 100
      `);

      const mensajes = rows.map(row => ({
        nombre: row.nombre,
        mensaje: row.mensaje,
        fecha: new Date(row.fecha).toISOString(),
        idempleado: row.idempleado
      }));

      socket.emit('historial', mensajes);
    } catch (error) {
      console.error('Error al obtener historial bajo demanda:', error);
      socket.emit('error_chat', 'No se pudo cargar el historial.');
    }
  });

  // Recibir mensajes
  socket.on('mensaje_chat', async (data) => {
    console.log('Mensaje recibido:', data);

    try {
      const [rows] = await db.execute(
        'SELECT idempleado FROM empleados WHERE nombre = ? LIMIT 1',
        [data.nombre]
      );

      if (rows.length === 0) {
        socket.emit('error_chat', 'Usuario no identificado. Inicia sesión para enviar mensajes.');
        return;
      }

      const idempleado = rows[0].idempleado;
      userSessions.set(socket.id, idempleado);

      const [insertResult] = await db.execute(
        'INSERT INTO mensajes_chat (idempleado, mensaje) VALUES (?, ?)',
        [idempleado, data.mensaje]
      );

      const [newMsgRows] = await db.execute(
        'SELECT m.*, e.nombre FROM mensajes_chat m JOIN empleados e ON e.idempleado = m.idempleado WHERE m.id = ?',
        [insertResult.insertId]
      );

      if (newMsgRows.length > 0) {
        const newMsg = newMsgRows[0];
        const responseData = {
          nombre: newMsg.nombre,
          mensaje: newMsg.mensaje,
          fecha: new Date(newMsg.fecha).toISOString(),
          idempleado: newMsg.idempleado,
          socketId: socket.id
        };

        io.emit('mensaje_chat', responseData);
      }
    } catch (error) {
      console.error('Error procesando mensaje:', error);
      socket.emit('error_chat', 'Ocurrió un error al procesar tu mensaje.');
    }
  });

  socket.on('disconnect', () => {
    console.log('Cliente desconectado:', socket.id);
    userSessions.delete(socket.id);
  });
});

server.listen(3001, () => {
  console.log('Servidor corriendo en http://localhost:3001');
});
