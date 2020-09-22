<?php

require_once '../../library/View.php';
require_once '../../library/Controller.php';
require_once 'model/InventarioDao.php';
require_once 'model/SucursalDao.php';




class InventarioController extends Controller{
    
    protected $_inventarioDao;
    
    protected $_sucursalDao;

    
    public function __construct() {
        $this->_view = new View();
        $this->_sucursalDao = new sucursalDao();
        $this->_inventarioDao = new InventarioDao();
        
    }
    
    
    public function executeAction() {
        switch($this->_getParam("action"))
        {
            case "movimiento":
                return $this->_movimientoAction();
                break;
             case "listado":
                return $this->_listadoAction();
                break;
            case "crea-sucursal":
                return $this->_creaSucursalAction();
                break;
            case "elimina-sucursal":
                return $this->_eliminaSucursalAction();
                break;
            case "edita-sucursal":
                return $this->_editaSucursalAction();
                break;
            case "cambiaestado-sucursal":
                return $this->_cambiaestadoSucursalAction();
                break;
             case "lista-sucursales":
                return $this->_listarSucursalesAction();
                break;
            default:
                return $this->_listadoAction();
        }
    }
    
    public function _movimientoAction()
    {
        $this->_view->setAttribute("titulo", 'Listado de Sustitutos');
        $this->_view->setAttribute("baseUrl", '/sye/public');
        
        $cantidad= $this->_getParam('cantidad');
        $id_producto= $this->_getParam('id_producto');
        $id_sucursal=$this->_getParam('id_sucursal');
        $movimiento = $this->_getParam('movimiento');
        $id_usuario= $this->_getParam('id_usuario');

        $this->inventarioDao->movimiento($cantidad, $id_sucursal, $id_producto, $id_usuario,$movimiento);


        return $this->_view->render('exitoso');
        
    }
    
    public function _listadoAction()
    {

        $this->_view->setAttribute("listado", $this->_inventarioDao->obtenerTodoInventario());
        
        return $this->_view->render('listado');
        
    }
       
    public function _creaSucursalAction() {
        $sucursal= $this->_getParam('sucursal');
        
        $this->_sucursalDao->crear($sucursal);
        
        return $this->_view->render('exitoso');
                
    }
    
    public function _eliminaSucursalAction() {
        
        $this->_sucursalDao->elimina($this->_getParam('id'));
        
        return $this->_view->render('exitoso');
        
    }
    
    public function _editaSucursalAction() {
        
        $this->_sucursalDao->acualizar($this->_getParam('id'),$this->_getParam('sucursal'));
        
        return $this->_view->render('exitoso');
    }
    
    public function _cambiaestadoSucursalAction() {
        
        $this->_sucursalDao->cambia_staus($this->_getParam('id') );    
        
        return $this->_view->render('exitoso');
    }
    
    public function _listarSucursalesAction() {
        
       $id_sucursal=$this->_getParam('id');
       //var_dump($id_sucursal);die();
       
       $sucursales = empty($id_sucursal)?$this->_sucursalDao->obtenerTodas():$this->_sucursalDao->obtenerPorID($id_sucursal);
        
        $this->_view->setAttribute("listado", $sucursales);
        
        return $this->_view->render('listado');
    }

}


