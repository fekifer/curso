<?php



class sucursalDao {
    
    protected $conexion; 
    
    public function __construct() {
        $this->conexion  = new Conexion();    
    }
    
    public function obtenerPorID($id) {
      return  $this->conexion->query_All("select descripcion sucursal, case activo when 1 then 'activado' else 'desactivado' end estatus  from cat_sucursal where id_sucursal= $id;");
    }
    
    public function obtenerTodas() {
        return $this->conexion->query_All("select descripcion sucursal, case activo when 1 then 'activado' else 'desactivado' end estatus  from cat_sucursal;");
    }
    
    public function elimina($id) {
        $this->conexion->query("delete from cat_sucursal where id_sucursal= $id;");
    }
    
    public function acualizar($id, $sucursal){
        $this->conexion->query("update cat_sucursal set descripcion = '$sucursal' where id_sucursal= $id;");
    }
    
    public function cambia_staus($id){
        $this->conexion->query("update cat_sucursal set activo = case when activo=1 then 0 else 1 end  where id_sucursal= $id;");
    }
    
    public function crear($sucursal){
        $this->conexion->query("insert into cat_sucursal (descripcion) values ('$sucursal');");
    }
    
}
