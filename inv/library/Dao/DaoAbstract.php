<?php

    require_once '../../library/Db/DSNMysql.php';
    require_once '../../library/Db/Conexion.php';

    abstract class Dao_DaoAbstract {

        protected $_dsn;
        protected $_tabla;
        protected $_campos; 
        
        protected $_filter;
        protected $_groupBy;
        protected $_orderBy;
        
        protected $_sqlFilter;
        protected $_sqlGroupBy;
        protected $_sqlOrderBy;
    /*
     * La instancia de ArticuloDao contiene el objeto de
     * Conexión a la Base de datos
     * La conexión es creada mediante el patrón de diseño
     * singleton por la clase DataBaseInstance que la implementa
     * Singleton permite crear siempre la misma instancia,
     * una sola instancia para cualquier llamado
     *
     */
    function __construct(DSNMysql $dsn = null, $tabla = null , $filter = array(), $campos = array() , $orderBy = array() ) {
        $this->setDsn($dsn);
        
        $this->_tabla = $tabla;
        $this->addFilter($filter);
        $this->_campos = $campos;
        $this->addOrderBy( $orderBy );
    }

    // obtiene el objeto de conección a la base de datos mediante singleton
    protected function _getConnection() {
        return Db_Conexion::getInstance()->getConnection($this->_dsn);
    }

    /*
     * Retorna el listado de Productos.
     * - Como PDOStatement es usado para las operaciones en la base de datos
     * - Como el objeto ResultSet object es usado para recorrer
     * un cursos retornando los registros
     */

    /* DSN */
    public function getDsn() {
        return $this->_dsn;
    }

    public function setDsn(DSNMysql $dsn) {
        $this->_dsn = $dsn;
    }
    
    /* CAMPOS */
    public function getCampos() {
        return $this->_campos;
    }

    public function setCampos($campos) {
        $this->_campos = $campos;
    }
    
    /* TABLA */
    public function setTabla( $tabla = null ){
            $this->_tabla = $tabla;
    }

    public function getTabla(){
        return $this->_tabla;
    }

    /* WHERE */    
    public function setFilter($filter = null) {
        $this->_filter = $filter;
        $this->refreshSQLFilter();
    }        
    public function getFilter(){
        return $this->_filter;
    }
    public function addFilter( $filter = null ){

        if( $filter !== null ){
            if( ! is_array( $filter )  ){
                $this->_filter[] = $filter;
            }else{
                foreach( $filter as $custom_filter ){
                   $this->_filter[] = $custom_filter ;
                }
            }
            $this->refreshSQLFilter();
        }else{
            $this->_filter = array();
        }
    }
    protected function refreshSQLFilter(){
       $do_once = 0;
       $filter = $this->getFilter();
       $sql = "";
       if( is_array( $filter ) ){
            foreach ($filter as $custom_sql) {
                if ($custom_sql != "") {
                    if ($do_once == 1) {
                        $sql .= " and ";
                    }
                    $do_once = 1;
                    $sql .= $custom_sql;
                }
            }
        }else{
           $sql = "";
       }
       $this->_sqlFilter = $sql;
    }
    public function getSQLFilter(){
        return $this->_sqlFilter;
    }

    /* ORDER BY */    
    public function setGroupBy($groupBy = null ) {
        $this->_groupBy = $groupBy;
        $this->refreshSQLGroupBy();
    }
    public function getGroupBy(){
        return $this->_groupBy;
    }
    public function addGroupBy( $groupBy = null ){

        if( $groupBy !== null ){
            if( ! is_array( $groupBy )  ){
                $this->_groupBy[] = $groupBy;
            }else{
                foreach( $groupBy as $custom_groupBy ){
                   $this->_groupBy[] = $custom_groupBy;
                }
            }
            $this->refreshSQLGroupBy();
        }
    }
    protected function refreshSQLGroupBy(){
       $do_once = 0;
       $groupBy = $this->getGroupBy();
       $sql = "";
       if( is_array( $groupBy ) ){
            foreach ($groupBy as $custom_sql) {
                
                  if ($custom_sql != "" ) {
                        if ($do_once == 1) {
                            $sql .= " , ";
                        }else{
                            $sql .= " group by ";
                        }
                        $do_once = 1;
                        $sql .= $custom_sql;
                    }
                
            }
       }else{
           if ( $groupBy != "") 
               $sql = " group by ".$groupBy;
           else
               $sql = "";
       }
       $this->_sqlGroupBy = $sql;
    }
    protected function getSQLGroupBy(){
        return $this->_sqlGroupBy;
    }        
       
    
    /* ORDER BY */    
    public function setOrderBy($orderBy) {
        $this->_orderBy = $orderBy;
        $this->refreshSQLOrderBy();
    }
    public function getOrderBy(){
        return $this->_orderBy;
    }
    public function addOrderBy( $orderBy = null ){

        if( $orderBy !== null ){
            if( ! is_array( $orderBy )  ){
                $this->_orderBy[] = $orderBy;
            }else{
                foreach( $orderBy as $custom_orderBy ){
                   $this->_orderBy[] = $custom_orderBy ;
                }
            }
            $this->refreshSQLOrderBy();
        }else{
            $this->_orderBy = array();
        }
    }
    protected function refreshSQLOrderBy(){
       $do_once = 0;
       $orderBy = $this->getOrderBy();
       $sql = "";
       if( is_array( $orderBy ) ){
            foreach ($orderBy as $custom_sql) {
                
                if( is_array( $custom_sql ) ){
                    if ( $custom_sql["campo"] != "" ) {
                        if ($do_once == 1) {
                            $sql .= " , ";
                        }else{
                            $sql .= " order by ";
                        }
                        $do_once = 1;
                        $sql .= $custom_sql["campo"] . " " . $custom_sql["orden"];
                    }
                }else{
                    if ($custom_sql != "" ) {
                        if ($do_once == 1) {
                            $sql .= " , ";
                        }else{
                            $sql .= " order by ";
                        }
                        $do_once = 1;
                        $sql .= $custom_sql;
                    }
                }
            }
        }else{
           if ( $orderBy != "") 
               $sql = " order by ".$orderBy;
           else
               $sql = "";
       }
       $this->_sqlOrderBy = $sql;
    }
    protected function getSQLOrderBy(){
        return $this->_sqlOrderBy;
    }        
   
    /* CONSTRUCCION DE LA SENTENCIA SQL */
    public function getSQL(){
            $sqlString = "select ";
            $campos = $this->getCampos();
            //var_dump($campos);
            if (is_array($campos) && empty($campos)) {
                $sqlString  .= " * ";
            } else {
                $campos = "";
                foreach ($this->getCampos() as $key => $value) {
                    if (!$campos == "") { 
                        $campos.=" ,"; 
                    };
                    $campos.=" ".$value." as ".$key." ";
                }
                $sqlString  .= $campos;
            }
            $sqlString  .= " from " . $this->getTabla();
            if ( $this->getSQLFilter() != "" ) {
               $sqlString  .= " where " . $this->getSQLFilter();
            }
            
            $sqlString  .= $this->getSQLGroupBy();
            $sqlString  .= $this->getSQLOrderBy();
            //echo $sqlString."\n";
       return $sqlString;        
    }   
    
    public function obtenerTodos() {
        //$lista = new ArrayObject();
        $stmt = null;
        try {
            $sql = $this->getSQL();
            //echo $sql."\n";
            //exit;
            $stmt = $this->_getConnection()->query($sql);
            //while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //      $lista->append($result);
            //}
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage();
            throw $e;
        }

        return $stmt;
    }
   
    public function obtenerTodosFast($sql) {
        //$lista = new ArrayObject();
        //echo $sql."\n";
        //exit;
        $stmt = null;
        try {
            $stmt = $this->_getConnection()->query($sql);
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage();
            throw $e;
        }

        return $stmt;
    }
    
    
    public function contar($where = "") {
        $contar=0;
        try {
            $sql = "SELECT COUNT(*) as contador FROM ".$this->_tabla;
            //echo $sql."\n";
            $stmt = $this->_getConnection()->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $contar = $result['contador'];
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage();
            throw $e;
        }

        return $contar;
    }
    
    public function obtenerPorId( $ids = array ()) {
        
        $resultado = array();
        try {
            
            $where = "";
            foreach ($ids as $key => $value) {
                if (!$where == "") { 
                    $where.=" and"; 
                };
                $where.=" ".$key." = ? ";
            }    
            
            $campos = $this->getCampos();
            if (count($campos) > 0) {
                $filtroOrig=$this->getFilter();
                $orderOrig=$this->getOrderBy();
                $this->setFilter(array());
                $this->setOrderBy(array());
                $sql=$this->getSQL()." WHERE ".$where;
                $this->setFilter($filtroOrig);
                $this->setOrderBy($orderOrig);
            } else {
                $sql = "SELECT * FROM ". $this->getTabla()." WHERE ".$where;
            }
            
            #debug echo $sql."\n";
            //echo $sql."\n";
            //exit;
            $stmt = $this->_getConnection()->prepare($sql);
            
            $i=0;
            foreach ($ids as $key => $value) {
                $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
                // debug echo "Valor => ".$value."\n";
                //echo "Valor => ".$value."\n";
                //exit;
            }
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_BOTH);
            //echo $pdo."\n";
            //exit;
            //$resultado = $stmt->fetch($pdo);
            // Vemos si no ha devuelto ningun resultado.
            if ($resultado === false) {
                $resultado = array();
            //    throw new PDOException("Registro con id $id NO encontrado");
            }            
            
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage();
            throw $e;
        }

        return $resultado;
    }
    
    
    
    /*
     * Entrada: Array($campo => $valor)
     *          Array con los campos y valores a insertar en la tabla
     */
    public function insert (array $datos = array()) {
        
        $resultado = null;
        try {
            $campos = "";
            $valores = "VALUES (";
            foreach ($datos as $key => $value) {
                if (!$campos == "") { 
                    $campos.=","; 
                    $valores.=","; 
                };
                $campos.=$key;
                #$valores.=" ?";
                #$valores.="'".$value."'";
                $valores.=$value;
            }
            $valores.=")";
            $sql="INSERT INTO ".$this->_tabla." (".$campos.") ".$valores;
            #echo "\n".$sql."\n";
            $stmt = $this->_getConnection()->prepare($sql);
            $i=0;
            foreach ($datos as $key => $value) {
                $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
            }
            $resultado = $stmt->execute();
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage() ." : ". $sql."\n";
            throw $e;
        }
        return $resultado;
    }    
    
    /*
     * Entrada :$ids Array($campo => $valor)
     *          Arreglo con los campos y valores a localizar en la tabla
     * 
     *          $datos Array($campo => $valor)
     *          Arreglo con los campos y valores a actualizar una vez encontrados
     *          el(los) registro(s) con $ids;
     */
    public function update ($ids, $datos){
        
        $resultado = null;
        try {
            
            $campos = "";
            foreach ($datos as $key => $value) {
                if (!$campos == "") { 
                    $campos.=","; 
                };
                $campos.=$key." = ?";
            }
            
            
            
            if (empty ($ids)) {
                $sql="UPDATE ".$this->_tabla." SET ".$campos;
            } else {
                $where = "";
                foreach ($ids as $key => $value) {
                        if (!$where == "") { 
                            $where.=" and"; 
                        };
                        //$where.=" ".$key." = '".$value."'";
                        $where.=" ".$key." = ? ";
                }
                $sql="UPDATE ".$this->_tabla." SET ".$campos;
                $sql.=" WHERE ".$where;
            }; 
            
            
            #echo "\n".$sql."\n";
            
            
            $stmt = $this->_getConnection()->prepare($sql);
            
            $i=0;
            foreach ($datos as $key => $value) {
                $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
            }
            if (! empty ($ids)) {
                foreach ($ids as $key => $value) {
                    $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
                }
            }
            
            $resultado = $stmt->execute();
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage() ." : ". $sql."\n";
            throw $e;
        }
        return $resultado;
    }
    
    public function updateV2 ($ids, $datos){
        
        $resultado = null;
        try {
            
            $campos = "";
            foreach ($datos as $key => $value) {
                if (!$campos == "") { 
                    $campos.=","; 
                };
                $campos.=$key." = ".$value."";
            }
            
            
            
            if (empty ($ids)) {
                $sql="UPDATE ".$this->_tabla." SET ".$campos;
            } else {
                $where = "";
                foreach ($ids as $key => $value) {
                        if (!$where == "") { 
                            $where.=" and"; 
                        };
                        $where.=" ".$key." = ".$value."";
                }
                $sql="UPDATE ".$this->_tabla." SET ".$campos;
                $sql.=" WHERE ".$where;
            }; 
            
            
            #echo "\n".$sql."\n";
            
            $stmt = $this->_getConnection()->prepare($sql);
            
            /*$i=0;
            foreach ($datos as $key => $value) {
                $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
            }
            if (! empty ($ids)) {
                foreach ($ids as $key => $value) {
                    $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT );
                }
            }*/
            
            $resultado = $stmt->execute();
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage() ." : ". $sql."\n";
            throw $e;
        }
        return $resultado;
    }
    
    public function updateFast ($sql){
        //echo "\n".$sql."\n";
        //exit;
        $resultado = null;
        try {
            $stmt = $this->_getConnection()->prepare($sql);
            $resultado = $stmt->execute();
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage() ." : ". $sql."\n";
            throw $e;
        }
        return $resultado;
    }
    
    public function eliminar($where = array())
    {
        $resultado = null;
        
        try{
            $sql = "Delete From $this->_tabla ";
            
            $donde = "";
            foreach ($where as $key => $value) {
                if(!$donde == "")
                {
                    $donde .= " and";
                }
                $donde .= " ".$key." = ? ";
            }
            
            $sql .= " WHERE ".$donde;
            
            $stmt = $this->_getConnection()->prepare($sql);
            
            $i = 0;
            
            foreach($where as $key => $value)
            {
                $stmt->bindValue(++$i, $value, PDO::PARAM_INPUT_OUTPUT);
            }
            
            //echo "delete: ".$sql."\n";
            //exit;
            $resultado = $stmt->execute();
            
        }catch(PDOException $e)
        {
            echo "Problema de coneccion: ". $e->getMessage() ." : ".$sql."\n";
            throw $e;
        }
        
        return $resultado;
    }
    
    public function getEstructura() {
        try {
            $sql = "DESCRIBE ".$this->_tabla;
            //echo $sql."\n";
            $stmt = $this->_getConnection()->query($sql);
        } catch (PDOException $e) {
            echo 'Problema de coneccion: ' . $e->getMessage();
            throw $e;
        }

        return $stmt;
    }
    
}
