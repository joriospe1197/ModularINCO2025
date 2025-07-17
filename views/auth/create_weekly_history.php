<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

                <!-- <p class="descripcion-pagina">Registro semanal</p> -->

                <?php include_once __DIR__ .'/../templates/alertas.php'; ?>
                <a href="/historial_de_pedidos" class="register">Regresar</a>
                
                <form class="formulario" method="POST" action="/create_weekly_history">
                   
                    <label for="chofer">Chofer:</label>
                    <select name="chofer" id="chofer">
                        <?php foreach ($choferes as $index => $chofer): ?>
                            <option value="<?= $chofer->idempleado ?>" <?= $chofer->idempleado == $choferSeleccionado ? 'selected' : '' ?>>
                                 <?= $chofer->nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="campo">
                        <label for="primer_folio">Primer folio</label>
                        <input
                            type="text"
                            id="primer_folio"
                            placeholder="Ingresa el primer folio correspondiente a esa semana."
                            name="primer_folio"
                            
                        />
                    </div>
                    <div class="campo">
                    <label for="ultimo_folio">Ultimo folio</label>
                        <input
                            type="text"
                            id="ultimo_folio"
                            placeholder="Ingresa el ultimo folio correspondiente a esa semana."
                            name="ultimo_folio"
                            
                        />
                    </div>
                    <div class="campo">
                    <label for="justificacion">Justificacion</label>
                        <input
                            type="number"
                            id="justificacion"
                            placeholder="Otros gastos."
                            name="justificacion"
                            
                        />
                    </div>
                    
                    

                    <input type="submit" class="boton" value="Guardar registro">
                </form>



<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>                