<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<div class="nombre-pagina">
    <p class="mision">Misión</p>
    <p class="descripcion-mision">
    Construimos espacios seguros, innovadores y funcionales que mejoran la calidad de vida de las personas y aportan valor a la sociedad. Nuestro compromiso es ofrecer soluciones constructivas de alta calidad, cumpliendo con los más altos estándares de seguridad, sostenibilidad y eficiencia, mediante un equipo humano calificado y tecnología de vanguardia.
    </p>
    <p class="vision">Visión</p>
    <p class="descripcion-vision">
    Ser una empresa de venta de materiales para la construcción líder a nivel nacional (o regional), reconocida por la excelencia en nuestros proyectos, la confianza de nuestros clientes y nuestra contribución al desarrollo sostenible. Aspiramos a transformar el entorno urbano y rural con obras que perduren en el tiempo y reflejen nuestro compromiso con la innovación y la responsabilidad social.
    </p>
</div>


