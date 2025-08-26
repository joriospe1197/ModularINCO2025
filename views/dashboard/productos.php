<div class="contenedor-productos">
    <!-- Alertas de operaciones exitosas/errores -->
    <?php if (!empty($_SESSION['alerta'])): ?>
        <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
            <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
        </div>
        <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>

    <div class="card-productos">
        <div class="card-header-productos d-flex justify-content-between align-items-center">
            <h2 class="card-title-productos mb-0"><i class="fas fa-boxes"></i> Gestión de Productos</h2>
            <a href="/register_product" class="btn-flotante">
                <i class="fas fa-plus"></i> 
                <span>Nuevo Producto</span>
            </a>
        </div>
        <div class="card-body-productos">
            <!-- Filtros y búsqueda -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form class="d-flex" method="GET" action="/productos">
                        <input type="text" class="form-control me-2 input-lg" name="busqueda" 
                               placeholder="Buscar por descripción..." 
                               value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>"
                               style="font-size: 1.4rem; height: 45px;">
                        <button class="btn btn-outline-primary" type="submit"
                                style="font-size: 1.2rem; padding: 0.3rem;">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/search_product" class="btn btn-outline-secondary me-2"
                        style="font-size: 1.4rem; height: 40px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-edit"></i> Editar Producto
                    </a>
                    <?php if ($_SESSION['tipo_usuario'] == 1): ?>
                    <a href="/remove_product" class="btn btn-outline-danger"
                        style="font-size: 1.4rem; height: 40px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-trash"></i> Eliminar
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($productos)) : ?>
                <div class="alert alert-info-productos text-center py-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <h4>No hay productos registrados</h4>
                    <p class="mb-0">Agrega un producto</p>
                </div>
            <?php else : ?>
                <table class="tabla-productos">
                    <thead>
                        <tr>
                            <th>Id producto</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto) : ?>
                        <tr>
                            <td>
                                <span class="id-producto">
                                    <?= htmlspecialchars($producto->idproducto) ?>
                                </span>
                            </td>
                            <td class="text-left"><?= htmlspecialchars($producto->descripcion) ?></td>
                            <td class="precio-producto">$<?= number_format($producto->precio, 2) ?></td>
                            <td>
                                
                                <div class="btn-group btn-group-sm">
                                    <a href="/edit_product?idproducto=<?= $producto->idproducto ?>" 
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <?php if ($_SESSION['tipo_usuario'] == 1): ?>
                                    <a href="/remove_product?idproducto=<?= $producto->idproducto ?>" 
                                        class="btn btn-outline-danger" 
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>