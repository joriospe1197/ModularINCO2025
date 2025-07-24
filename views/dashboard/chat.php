<?php 
//session_start();
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Invitado';

include_once __DIR__ . '/header-dashboard.php'; 
?>

    <div class="chat-container">

        <div id="chat" style="height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>

        <div class="chat-input-container" style="position: sticky; bottom: 0; background: white; padding: 10px; border-top: 1px solid #ccc;">
            <input type="text" id="mensaje" placeholder="Escribe tu mensaje" autocomplete="off" />
            <button id="enviar">Enviar</button>
        </div>

    </div>

    <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3001');

        const nombreUsuario = <?php echo json_encode($nombreUsuario); ?>;
        console.log('Usuario conectado como:', nombreUsuario);  // <-- debugueo

        const chatDiv = document.getElementById('chat');
        const inputMensaje = document.getElementById('mensaje');
        const btnEnviar = document.getElementById('enviar');

        btnEnviar.onclick = enviarMensaje;
        inputMensaje.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') enviarMensaje();
        });

        function enviarMensaje() {
            const msg = inputMensaje.value.trim();
            if (msg.length > 0) {
                const payload = {
                    nombre: nombreUsuario,
                    mensaje: msg,
                    socketId: socket.id
                };
                socket.emit('mensaje_chat', payload);

                chatDiv.innerHTML += `<p><b>Tú:</b> ${msg}</p>`;
                chatDiv.scrollTop = chatDiv.scrollHeight;
                inputMensaje.value = '';
            }
        }

        // Mostrar mensajes recibidos
        socket.on('mensaje_chat', (data) => {
            if (data.socketId !== socket.id) {
                chatDiv.innerHTML += `<p><b>${data.nombre}:</b> ${data.mensaje}</p>`;
                chatDiv.scrollTop = chatDiv.scrollHeight;
            }
        });

        // Mostrar historial al conectar
        socket.on('historial', (mensajes) => {
            mensajes.forEach((data) => {
                const etiqueta = data.socketId === socket.id ? 'Tú' : data.nombre;
                chatDiv.innerHTML += `<p><b>${etiqueta}:</b> ${data.mensaje}</p>`;
            });
            chatDiv.scrollTop = chatDiv.scrollHeight;
        });
    </script>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
