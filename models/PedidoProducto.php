<?php
namespace Model;

use Model\ActiveRecord;

class PedidoProducto extends ActiveRecord {
    protected static $tabla = 'pedido_productos';
    protected static $columnasDB = ['id', 'id_pedido', 'idproducto', 'cantidad'];

    public $id;
    public $id_pedido;
    public $idproducto;
    public $cantidad;



    public static function obtenerProductosPorPedido($id_pedido) {
        $db =self::getDB();
        $query = "SELECT 
                    pp.id,
                    pp.id_pedido,
                    pp.idproducto,
                    pp.cantidad,
                    pr.descripcion, 
                    pr.precio 
                FROM pedido_productos pp
                JOIN productos pr ON pp.idproducto = pr.idproducto
                WHERE pp.id_pedido = $id_pedido";
        
        $resultado = $db->query($query);
        if ($resultado) {
        // Obtener todos los resultados como array asociativo
        return $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            // En caso de error, retornar array vacío
            error_log("Error en la consulta: " . $db->error);
            return [];
        }
    }

    public static function eliminarPorPedido($id_pedido) {
        $query = "DELETE FROM pedido_productos WHERE id_pedido = {$id_pedido}";
        $resultado = self::$db->query($query);
        return $resultado;
    }


}
