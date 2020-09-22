<?php


class Conexion {
    
    protected $conexion;
    
    public function __construct() {
        $this->conexion = pg_connect("host=40.74.252.149 port=8082 dbname=db_punto_de_venta user=postgres password=123456");
    }
    
    public function query($query){
         $res = pg_query($this->conexion, $query)or die('Query failed: ' . pg_last_error());
         return $res;
    }
    
    public function query_result($query){
        return pg_fetch_array($this->query($query),null, PGSQL_ASSOC);
    }
    
    public function query_All($query){
        return pg_fetch_all($this->query($query));
    }
    
            
}
