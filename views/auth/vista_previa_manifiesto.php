<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px; /* Espacio entre tablas */
        font-family: Arial, sans-serif;
    }

    th, td {
        padding: 8px 10px;
        border: 1px solid #ccc;
        vertical-align: top;
        font-size: 14px;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    td[rowspan] {
        font-weight: bold;
        width: 160px;
    }
</style>

        <form class="formulario" method="POST" action="/generar_PDF">.
            <input  type="hidden" name="clientes" value="<?= $clienteM ?>">
            <input  type="hidden"name="mes" value="<?= $mesM ?>">
            <input  type="hidden" name="anio" value="<?= $anio ?>">
            <input  type="hidden" name="direccion" value="<?= $direccion ?>">
            <input  type="hidden" name="correo" value="<?= $correo ?>">
            <input  type="hidden" name="tipoResiduo" value="<?= $tipoResiduo ?>">
            <input  type="hidden" name="totalm3" value="<?= $totalm3 ?>">
            <input  type="hidden" name="dirObra" value="<?= $dirObra ?>"> 
            <input  type="hidden" name="codigo" value="<?= $codigo ?>">
            <input  type="hidden" name="estado" value="<?= $estado ?>">
            <input  type="hidden" name="municipio" value="<?= $municipio ?>">
            <input  type="hidden" name="telefono" value="<?= $telefono ?>">
            <input  type="hidden" name="nombre" value="<?= $nombre ?>">
            <input type="submit" class="boton" value="Generar en PDF">
            <br><br>
            <a href="/manifiestos" >Regresar al inicio</a>    
        </form>
        <br><br><br>
        <table><thead>

            <tr>

                <td rowspan="8">TRANSPORTISTA</td>
                <td>Razón Social de la Empresa: <?= $nombre ?> </td>
                <td></td>
            </tr>
            <tr>
                <td>Domicilio: <?= $direccion ?></td>
                <td></td>
            </tr>
            <tr>
                <td>Municipio: <?= $municipio ?> --- Estado : <?= $estado ?></td>
                <td></td>
            </tr>
            <tr>
                <td>Correo electronico: <?= $correo ?></td>
                <td></td>
            </tr>
            <tr>
                <td>N° Autorización SEMADET:</td>
                <td></td>
            </tr>
            <tr>
                <td>Descripción de los residuos: </td>
                <td></td>
            </tr>
            <tr>
                <td>Tipo de Residuos:</td>
                <td>Cantidad en m3</td>
            </tr>
            
                <th><?= $tipoResiduo ?></t>
                <th><?= $totalm3 ?> m3</t>
        </tr></thead>
        </table>
        <table><thead>

            <tr>
                <td rowspan="12">GENERADOR</td>
                <td>Periodo de recolección:</td>
                <th><?= $mesM ?>-<?= $anio ?></th>
            </tr>
            <tr>
                <td>Responsable de la entrega de los residuos (nombre y firma) </td>
                <th><?= $dirObra ?></th>
            </tr>
            <tr>
                <td>Instrucciones para el manejo de los residuos</td>
                <td></td>
            </tr>
            <tr>
                <td></t>
                <td></td>
            </tr>
            <tr>
                <td>Nombre y razon social de la empresa:</t>
                <td>CONSTRUCTORA SA DE CV</td>
            </tr>
            <tr>
                <td>Domicilio:</td>
                <td>Calle 123 Colonia X Guadalajara,Jalisco</t>
            </tr>
            <tr>
                <td>N° de Autorización de SEMADET:</t>
                <td>ASDASD 12345</t>
            </tr>
            <tr>
                <td>Descripción del vehículo:</td>
                <td>Voleto 7m3</t>
            </tr>
            <tr>
                <td>N° de Placas:</td>
                <td>JG54946, ... , ..., ..., ...</t>
            </tr>
            <tr>
                <td colspan="2">Responsable de la recolección de los residuos:</t>
                </tr>
             
                <tr>
                    <td colspan="3" rowspan="4"></td>
                </tr>
                <tr>
                </tr>
                <tr>
                </tr>
                <tr>
                </tr>
                <tr>
                    <td colspan="3">Recibí los residuos en el presente manifiesto para su transporte&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma</td>
                </tr>
           
        </thead>
           
        </table>
        <table><thead>
            <tr>
                <td rowspan="6">DESTINO FINAL</td>
                <td>Nombre y/o razon social de la empresa:</td>
                <td>EJEMPLO JOSE</td>
            </tr>
            <tr>
                <td>Domicilio:</td>
                <td>CALE 123 COL. X</td>
            </tr>
            <tr>
                <td>N° Autorización SEMADET:</td>
                <td></td>
            </tr>
            <tr>
                <td>Oficio:</td>
                <td>EJEMPLO PARCELA</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Sello de recepción</td>
                <td>Nombre y Firma</td>
            </tr></thead>
        </table>


        


<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>