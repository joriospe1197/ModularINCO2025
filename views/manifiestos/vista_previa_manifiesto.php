

<style>
        

    .container {
        max-width: 800px;
        margin: 0 auto;
        background-color: white;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .section {
        margin-bottom: 20px;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    }

    .section-header {
        background: linear-gradient(135deg, #4A90E2, #6B73FF);
        color: white;
        padding: 8px 15px;
        font-weight: bold;
        font-size: 15px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-content {
        background-color: white;
    }

    .field-row {
        display: flex;
        border-bottom: 1px solid #e0e0e0;
        min-height: 35px;
        align-items: stretch;
    }

    .field-row:last-child {
        border-bottom: none;
    }

    .field-label {
        background-color: #E8F0FE;
        padding: 8px 12px;
        font-weight: 500;
        color: #333;
        display: flex;
        align-items: center;
        min-width: 180px;
        border-right: 1px solid #d0d0d0;
        font-size: 13px;
    }

    .field-value {
        background-color: #F8F9FA;
        padding: 8px 12px;
        flex: 1;
        display: flex;
        align-items: center;
        color: #555;
        font-size: 13px;
    }

    .field-value.empty {
        background-color: #FFFFFF;
        border: 1px dashed #ccc;
        margin: 3px;
        min-height: 25px;
    }

    /* Estilos específicos para tipos de residuos */
    .residue-type-row {
        display: flex;
    }

    .residue-type-row .field-label,
    .residue-type-row .field-value {
        flex: 1;
        text-align: center;
        font-weight: bold;
    }

    /* Área de firma compacta */
    .signature-area {
        background-color: #F0F4FF;
        min-height: 80px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-style: italic;
        border: 2px dashed #B8D0FF;
        margin: 3px;
        font-size: 13px;
    }

    .instructions-field {
        background-color: #F0F8FF;
        min-height: 40px;
        padding: 10px;
        border: 1px solid #e0e0e0;
        font-size: 13px;
    }

    .description-field {
        background-color: #F9F9F9;
        padding: 8px 12px;
        text-align: center;
        font-weight: 500;
        color: #444;
        font-size: 13px;
    }

    /* Responsivo */
    @media (max-width: 600px) {
        .field-row {
            flex-direction: column;
        }
        
        .field-label {
            min-width: auto;
            border-right: none;
            border-bottom: 1px solid #d0d0d0;
        }
    }
</style>





<form class="formulario" method="POST" action="/generar_PDF">.
    <input type="hidden" name="clientes" value="<?= $clienteM ?>">
    <input type="hidden" name="mes" value="<?= $mesM ?>">
     <input type="hidden" name="mes_numero" value="<?= $mes_numero ?>"> <!--  Número para BD -->
    <input type="hidden" name="anio" value="<?= $anio ?>">
    <input type="hidden" name="direccion" value="<?= $direccion ?>">
    <input type="hidden" name="correo" value="<?= $correo ?>">
    <input type="hidden" name="tipoResiduo" value="<?= $tipoResiduo ?>">
    <input type="hidden" name="totalm3" value="<?= $totalm3 ?>">
    <input type="hidden" name="dirObra" value="<?= $dirObra ?>">
    <input type="hidden" name="codigo" value="<?= $codigo ?>">
    <input type="hidden" name="estado" value="<?= $estado ?>">
    <input type="hidden" name="municipio" value="<?= $municipio ?>">
    <input type="hidden" name="telefono" value="<?= $telefono ?>">
    <input type="hidden" name="nombre" value="<?= $nombre ?>">
    <input type="submit" class="boton" value="Generar en PDF">
    <!-- <br><br> -->
    <a href="/manifiestos" class="btn-regresar">Regresar al inicio</a>


</form>

<br><br><br>

<div class="container">
    <!-- GENERADOR -->
    <div class="section">
        <div class="section-header">GENERADOR</div>
        <div class="section-content">
            <div class="field-row">
                <div class="field-label">Razón social de la empresa</div>
                <div class="field-value"><?= $nombre ?></div>
            </div>
            <div class="field-row">
                <div class="field-label">Domicilio</div>
                <div class="field-value"><?= $direccion ?></div>
            </div>
            <div class="field-row">
                <div class="field-label">Municipio / Estado</div>
                <div class="field-value"><?= $municipio ?>,<?= $estado ?></div>
            </div>
            <div class="field-row">
                <div class="field-label">Correo electrónico</div>
                <div class="field-value"><?= $correo ?></div>
            </div>
            <div class="field-row">
                <div class="field-label">N° Autorización SEMADET</div>
                <div class="field-value"></div>
            </div>
            <div class="field-row">
                <div class="description-field">Descripción de los residuos</div>
            </div>
            <div class="field-row residue-type-row">
                <div class="field-label">Tipo de residuos</div>
                <div class="field-label">Cantidad en m³</div>
            </div>
            <div class="field-row residue-type-row">
                <div class="field-value"><?= $tipoResiduo ?></div>
                <div class="field-value"><?= $totalm3 ?> m3</div>
            </div>
        </div>
    </div>

    <!-- TRANSPORTISTA -->
    <div class="section">
        <div class="section-header">TRANSPORTISTA</div>
        <div class="section-content">
            <div class="field-row">
                <div class="field-label">Periodo de recolección</div>
                <div class="field-value"><?= $mesM ?>-<?= $anio ?></div>
            </div>
            <div class="field-row">
                <div class="field-label">Responsable de la entrega</div>
                <div class="field-value"><?= $dirObra ?></div>
            </div>
            <div class="field-row">
                <div class="instructions-field">Instrucciones de manejo</div>
            </div>
            <div class="field-row">
                <div class="field-label">Nombre / Razón social</div>
                <div class="field-value">CONSTRUCTORA SA DE CV</div>
            </div>
            <div class="field-row">
                <div class="field-label">Domicilio</div>
                <div class="field-value">Calle 123 Colonia X Guadalajara, Jalisco</div>
            </div>
            <div class="field-row">
                <div class="field-label">N° Autorización SEMADET</div>
                <div class="field-value">ASDASD 12345</div>
            </div>
            <div class="field-row">
                <div class="field-label">Descripción del vehículo</div>
                <div class="field-value">Voleto 7m3</div>
            </div>
            <div class="field-row">
                <div class="field-label">N° de placas</div>
                <div class="field-value">JG54946, ...</div>
            </div>
            <div class="field-row">
                <div class="description-field">Responsable de recolección</div>
            </div>
            <div class="field-row">
                <div class="signature-area">Firma</div>
            </div>
            <div class="field-row">
                <div class="field-value" style="text-align: center; padding: 15px;">
                    Recibí los residuos en el presente manifiesto para su transporte
                    <span style="float: right; margin-right: 50px;">Firma</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DESTINO FINAL -->
    <div class="section">
        <div class="section-header">DESTINO FINAL</div>
        <div class="section-content">
            <div class="field-row">
                <div class="field-label">Nombre / Razón social</div>
                <div class="field-value">EJEMPLO JOSE</div>
            </div>
            <div class="field-row">
                <div class="field-label">Domicilio</div>
                <div class="field-value">CALE 123 COL. X</div>
            </div>
            <div class="field-row">
                <div class="field-label">N° Autorización SEMADET</div>
                <div class="field-value"></div>
            </div>
            <div class="field-row">
                <div class="field-label">Oficio</div>
                <div class="field-value">EJEMPLO PARCELA</div>
            </div>
            <div class="field-row">
                <div class="description-field">Sello de recepción</div>
            </div>
            <div class="field-row">
                <div class="signature-area">Nombre y Firma</div>
            </div>
        </div>
    </div>
</div> 

    