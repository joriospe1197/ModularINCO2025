// app.js
import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import mysql from 'mysql2/promise';

// Configuración de Express y HTTP Server
const app = express();
const server = http.createServer(app);

// Inicializar Socket.IO con CORS
const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

// Conexión a la base de datos MySQL
let db;
try {
  db = await mysql.createConnection({
    host: 'localhost',
    user: 'root',       
    password: 'veliz4$',
    database: 'Constructora'
  });
  console.log('Conectado a la base de datos MySQL');
} catch (error) {
  console.error('Error al conectar con la base de datos:', error);
  process.exit(1); // Detiene el servidor si falla la conexión
}

// Socket.IO - Manejo de eventos
io.on('connection', async (socket) => {
  console.log('Nuevo cliente conectado:', socket.id);

  // Enviar historial de mensajes desde la base de datos
  try {
    const [rows] = await db.execute(`
      SELECT e.nombre, m.mensaje
      FROM mensajes_chat m
      JOIN empleados e ON e.idempleado = m.idempleado
      ORDER BY m.fecha ASC
      LIMIT 100
    `);

    const mensajes = rows.map(row => ({
      nombre: row.nombre,
      mensaje: row.mensaje
    }));

    socket.emit('historial', mensajes);
  } catch (error) {
    console.error('Error al obtener historial:', error);
  }

  // Recibir y reenviar mensajes
  socket.on('mensaje_chat', async (data) => {
    console.log('Mensaje recibido en servidor:', data);
  
    try {
      const [rows] = await db.execute(
        'SELECT idempleado FROM empleados WHERE nombre = ? LIMIT 1',
        [data.nombre]
      );
  
      console.log(' Resultado búsqueda idempleado:', rows);
  
      if (rows.length > 0) {
        const idempleado = rows[0].idempleado;
  
        const [insertResult] = await db.execute(
          'INSERT INTO mensajes_chat (idempleado, mensaje) VALUES (?, ?)',
          [idempleado, data.mensaje]
        );
  
        console.log('Mensaje insertado:', insertResult);
      } else {
        console.warn(`No se encontró el usuario '${data.nombre}' en la base de datos.`);
      }
    } catch (error) {
      console.error('Error en la inserción:', error);
    }
  
    socket.broadcast.emit('mensaje_chat', data);
  });
  

  // Desconexión
  socket.on('disconnect', () => {
    console.log('Cliente desconectado:', socket.id);
  });
});

// Iniciar servidor
server.listen(3000, () => {
  console.log('Servidor de chat corriendo en http://localhost:3000');
});
