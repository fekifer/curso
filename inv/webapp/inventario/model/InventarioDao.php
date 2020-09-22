<?php

include '../../library/Db/Conexion.php';

class InventarioDao {
    
    protected $conexion; 
            
    public function __construct() {
        
        $this->conexion  = new Conexion();
    }
    
    public function movimiento($cantidad,$id_sucursal,$id_producto,$id_usuario, $movimiento) {
        
        $operacion = ($movimiento == 1)?'+':'-';
        
        $this->conexion->query("insert into tb_inventario (cantidad,id_sucursal, id_producto) values($cantidad,$id_sucursal,$id_producto)
                                ON CONFLICT (id_sucursal, id_producto)
                                 DO UPDATE SET CANTIDAD = tb_inventario.CANTIDAD $operacion $cantidad;");
        
        $id_inventario = $this->obtenerInventarioPorSucursalProducto($id_sucursal, $id_producto)['id_inventario'];
        
        $this->conexion->query("INSERT INTO tb_movimiento(fecha,  cantidad, id_tipo_movimiento, id_inventario, id_usuario)
        VALUES (current_timestamp, $cantidad, $movimiento,$id_inventario,$id_usuario)" ) ;
        
    }
    
    
    public function obtenerInventarioPorSucursalProducto($id_sucursal,$id_producto){
        return $this->conexion->query_result("SELECT inv.id_inventario ,inv.cantidad, suc.descripcion sucursal , prod.descripcion producto
                                            FROM  tb_inventario inv
                                            INNER JOIN cat_sucursal suc on inv.id_sucursal = suc.id_sucursal
                                            INNER JOIN cat_producto prod on inv.id_producto= prod.id_producto 
                                              where inv.id_sucursal = $id_sucursal AND 
                                               inv.id_producto = $id_producto; ");
    }
    
    public function obtenerInventarioPorSucursal($id_sucursal,$id_producto){
        return $this->conexion->query_result("SELECT inv.id_inventario ,inv.cantidad, suc.descripcion sucursal , prod.descripcion producto
                                            FROM  tb_inventario inv
                                            INNER JOIN cat_sucursal suc on inv.id_sucursal = suc.id_sucursal
                                            INNER JOIN cat_producto prod on inv.id_producto= prod.id_producto 
                                            where inv.id_sucursal = $id_sucursal ; ");
    }
    
    public function obtenerInventarioPorProducto($id_sucursal,$id_producto){
        return $this->conexion->query_result("SELECT inv.id_inventario ,inv.cantidad, suc.descripcion sucursal , prod.descripcion producto
                                            FROM  tb_inventario inv
                                            INNER JOIN cat_sucursal suc on inv.id_sucursal = suc.id_sucursal
                                            INNER JOIN cat_producto prod on inv.id_producto= prod.id_producto 
                                            where inv.id_producto = $id_producto; ");
    }
    
        
    public function obtenerTodoInventario(){
        return $this->conexion->query_all("SELECT inv.id_inventario ,inv.cantidad, suc.descripcion sucursal , prod.descripcion producto
                                            FROM  tb_inventario inv
                                            INNER JOIN cat_sucursal suc on inv.id_sucursal = suc.id_sucursal
                                            INNER JOIN cat_producto prod on inv.id_producto= prod.id_producto 
                                            ;");
    }



    protected function registraEvento(){
        
    }
    
}
    