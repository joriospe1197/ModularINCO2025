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
    port: 59369, // Puerto TCP proxy Railway (asegúrate que es accesible desde Render)
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

  socket.on('mensaje_chat', async (data) => {
    console.log('--- mensaje_chat recibido del cliente ---');
    console.log('data:', data);

    try {
      const [rows] = await db.execute(
        'SELECT idempleado FROM empleados WHERE nombre = ? LIMIT 1',
        [data.nombre]
      );
      console.log('rows al buscar empleado:', rows);

      if (rows.length === 0) {
        console.warn(`Usuario no encontrado con nombre: '${data.nombre}'`);
        socket.emit('error_chat', 'Usuario no identificado. No se insertará el mensaje.');
        return;
      }

      const idempleado = rows[0].idempleado;
      userSessions.set(socket.id, idempleado);

      const [insertResult] = await db.execute(
        'INSERT INTO mensajes_chat (idempleado, mensaje) VALUES (?, ?)',
        [idempleado, data.mensaje]
      );
      console.log('Resultado INSERT:', insertResult);

      if (!insertResult.insertId) {
        console.warn('No se obtuvo insertId después del INSERT');
      }

      const [newMsgRows] = await db.execute(
        `SELECT m.*, e.nombre
         FROM mensajes_chat m
         JOIN empleados e ON e.idempleado = m.idempleado
         WHERE m.id = ?`,
        [insertResult.insertId]
      );
      console.log('newMsgRows:', newMsgRows);

      if (newMsgRows.length === 0) {
        console.warn('No se encontró el mensaje recién insertado');
        return;
      }

      const newMsg = newMsgRows[0];

      const responseData = {
        nombre: newMsg.nombre,
        mensaje: newMsg.mensaje,
        fecha: new Date(newMsg.fecha).toISOString(),
        idempleado: newMsg.idempleado,
        socketId: socket.id
      };

      io.emit('mensaje_chat', responseData);

    } catch (error) {
      console.error('Error interno procesando mensaje_chat:', error);
      socket.emit('error_chat', 'Error interno al procesar mensaje.');
    }
  });

  socket.on('disconnect', () => {
    console.log('Cliente desconectado:', socket.id);
    userSessions.delete(socket.id);
  });
});

// ¡IMPORTANTE! Usar puerto de la variable de entorno PORT
const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`Servidor corriendo en puerto ${PORT}`);
});
