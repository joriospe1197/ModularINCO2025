<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>
    
    
        <div class="barra">
                <a href="/search_user" class="search_user">Editar empleado</a>
                <a href="/remove_user" class="remove_user">Eliminar empleado</a>
        </div>
        
                <!-- <p class="descripcion-pagina">Registrar un nuevo usuario</p> -->

                <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

                <form class="formulario" method="POST" action="/register">
                    <div class="campo">
                        <label for="nombre">Nombre</label>
                        <input
                            type="text"
                            id="nombre"
                            placeholder="Ingrese el nombre completo del empleado que desea registrar."
                            name="nombre"
                            value="<?php echo $usuario->nombre; ?>"
                        />
                    </div>
                    <div class="campo">
                    <label for="email">Correo electronico</label>
                        <input
                            type="email"
                            id="email"
                            placeholder="Ingrese el correo electronico del empleado."
                            name="email"
                            value="<?php echo $usuario->email; ?>"
                        />
                    </div>
                    <div class="campo">
                        <label for="direccion">Dirección</label>
                        <input
                            type="text"
                            id="direccion"
                            placeholder="Ingrese la direccion del empleado."
                            name="direccion"
                            value="<?php echo $usuario->direccion; ?>"
                        />
                    </div>
                    <div class="campo">
                        <label for="telefono">Teléfono</label>
                        <input
                            type="number"
                            id="telefono"
                            placeholder="Ingrese el telefono del empleado."
                            name="telefono"
                            value="<?php echo $usuario->telefono; ?>"
                        />
                    </div>
                    <div class="campo">
                    <label for="contrasena">Contraseña</label>
                        <input
                            type="password"
                            id="contrasena"
                            placeholder="Ingrese la contraseña que desea asignarle al empleado."
                            name="contrasena"
                        />
                    </div>
                    <div class="campo">
                    <label for="contrasena2">Repetir contraseña</label>
                        <input
                            type="password"
                            id="contrasena2"
                            placeholder="Repite la contraseña que desea asignarle al empleado."
                            name="contrasena2"
                        />
                    </div>
                    

                    <input type="submit" class="boton" value="Guardar registro">
                </form>       
  

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>