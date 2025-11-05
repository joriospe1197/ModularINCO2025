<?php 
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Invitado';
$idEmpleado = isset($_SESSION['idempleado']) ? $_SESSION['idempleado'] : 0;
?>

<div class="chat-container">
    <!-- Header -->
    <div class="chat-header">
        <div class="chat-logo">
            <div class="chat-avatar"><?php echo strtoupper(substr($nombreUsuario, 0, 1)); ?></div>
            <div class="chat-info">
                <h2>Chat en vivo</h2>
                <p>Conectado como <?php echo htmlspecialchars($nombreUsuario); ?></p>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    <div id="chat" class="chat-messages"></div>

    <!-- Input -->
    <div class="chat-input">
        <form id="form-chat" onsubmit="return false;">
            <input type="text" id="mensaje" placeholder="Escribe tu mensaje..." autocomplete="off" />
            <button id="enviar" type="submit">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<!-- Librerías -->
<script src="https://kit.fontawesome.com/1b7c1aabb5.js" crossorigin="anonymous"></script>
<script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>

<script>
    const socket = io('https://node-app-js-lton.onrender.com');
    const nombreUsuario = <?php echo json_encode($nombreUsuario); ?>;
    const idEmpleado = <?php echo json_encode($idEmpleado); ?>;

    const chatDiv = document.getElementById('chat');
    const inputMensaje = document.getElementById('mensaje');
    const btnEnviar = document.getElementById('enviar');
    const formChat = document.getElementById('form-chat');

    let mySocketId = sessionStorage.getItem('mySocketId');

    if (!mySocketId) {
        btnEnviar.disabled = true;
        inputMensaje.placeholder = "Conectando...";
    }

    // Al conectarse, guardar el socketId y pedir historial
    socket.on('connect', () => {
        mySocketId = socket.id;
        sessionStorage.setItem('mySocketId', mySocketId);
        btnEnviar.disabled = false;
        inputMensaje.placeholder = "Escribe tu mensaje...";
        console.log('Conectado con ID:', mySocketId);

        // ✅ Pedir historial cada vez que entres al chat
        socket.emit('pedir_historial');
    });

    formChat.addEventListener('submit', enviarMensaje);

    function enviarMensaje() {
    const msg = inputMensaje.value.trim();
    if (msg.length > 0) {
        const payload = {
            nombre: nombreUsuario,
            mensaje: msg
        };

        socket.emit('mensaje_chat', payload);

        // Ya no renderizamos aquí, solo limpiamos el input
        inputMensaje.value = '';
    }
    }

    // Mostrar mensaje recibido
    socket.on('mensaje_chat', (data) => {
        const esMio = data.socketId === mySocketId;

        renderMensaje({
            nombre: esMio ? "Tú" : data.nombre,
            mensaje: data.mensaje,
            hora: new Date(data.fecha).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
            mio: esMio
        });
    });

    // Mostrar historial al entrar o regresar al chat
    socket.on('historial', (mensajes) => {
        chatDiv.innerHTML = ''; // ✅ Limpiar antes de renderizar

        mensajes.forEach((data) => {
            const esMio = data.idempleado == idEmpleado;

            renderMensaje({
                nombre: esMio ? "Tú" : data.nombre,
                mensaje: data.mensaje,
                hora: new Date(data.fecha).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                mio: esMio
            });
        });
    });

    // Mostrar errores del servidor
    socket.on('error_chat', (msg) => {
        alert("Error del chat: " + msg);
    });

    // Función para renderizar mensajes
    function renderMensaje({nombre, mensaje, hora, mio}) {
        const div = document.createElement('div');
        div.classList.add('mensaje', mio ? 'mio' : 'otro');

        div.innerHTML = `
            <span class="usuario">${nombre}</span>
            <span>${mensaje}</span>
            <span class="hora">${hora}</span>
        `;
        chatDiv.appendChild(div);
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }
</script>
