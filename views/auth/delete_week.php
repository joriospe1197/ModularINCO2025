<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

                <!-- <p class="descripcion-pagina">Registro semanal</p> -->

                <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

                <form class="formulario" method="POST" action="/edit_week">
                    <div class="campo">
                        <label for="primer_folio">Folio inicial de la semana a eliminar</label>
                        <input
                            type="text"
                            id="primer_folio"
                            placeholder="Ingresa el primer folio correspondiente a esa semana."
                            name="primer_folio"
                            
                        />
                    </div>
                    
                    <input type="submit" class="boton" value="Actualizar semana">
                </form>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>