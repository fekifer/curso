<?php

require_once '../../library/View.php';
require_once '../../library/Controller.php';
require_once 'model/SustitutoDao.php';
require_once 'model/EquivalenteDao.php';


class CatalogoController extends Controller{
    private  $_modeltwo;
    
    public function __construct() {
        $this->_view = new View();
        $this->_model = new SustitutoDao();
        $this->_modeltwo = new EquivalenteDao();
        
    }
    
    
    public function executeAction() {
        switch($this->_getParam("action"))
        {
            case "mostrar":
                return $this->_mostrarAction();
                break;
            case "buscar":
                return $this->_buscarAction();
                break;
            case "agregar":
                return $this->_agregarAction();
                break;
            case "buscaequiv":
                return $this->_buscaequivAction();
                break;
            case "guardar":
                return $this->_guardarAction();
                break;
            case "encuentra":
                return $this->_encuentraAction();
                break;
            case "elimsust":
                return $this->_elimisustAction();
                break;
            case "buscasye":
                return $this->_buscasyeAction();
                break;
            case "elimequiv":
                return $this->_elimequivAction();
                break;
            case "elimequivdos":
                return $this->_elimequivdosAction();
                break;
            case "agregarsust":
                return $this->_agregarsustAction();
                break;
            case "guardasust":
                return $this->_guardasustAction();
                break;
            case "cuentaequiv":
                return $this->_cuentaequivAction();
                break;
            default:
                return $this->_listadoAction();
        }
    }
  
    /*Function para traer la cadena JSON a imprimir en la vista*/
    public function _mostrarAction()
    {
        $this->_model->obtenerTodos();
        $cadena = $this->_model->getCadenaJson();
        
        //Imprimiendo cadena JSON
        echo $cadena;
    }
    
    public function _listadoAction()
    {
        $this->_view->setAttribute("titulo", 'Listado de Sustitutos');
        $this->_view->setAttribute("baseUrl", '/sye/public');
        
        return $this->_view->render('listado');
        
    }
    
    public function _buscaequivAction()
    {
        session_start();
        $articulos = $_POST['arts'];
        $arts = json_decode($articulos);
        
        //foreach ($arts as $value) 
        //{
            $dato = $this->_modeltwo->buscaEquivalente($arts);
            return $dato;
        //}
        session_destroy();
    }
    
    public function _buscarAction()
    {
        $id = (int) $this->_getParam("id");
        $equivalentes = $this->_modeltwo->obtenerPorId($id);
        $this->_modeltwo->buscaDatosEquivalente($equivalentes);
        $cadenaJsonSG = $this->_modeltwo->getCadenaJSON();
        
        echo $cadenaJsonSG;
    }
    
    public function _agregarAction()
    {
        $this->_view->setAttribute("baseUrl", '/sye/public');
        //isset($_POST["id"]) ? $sustituto = $_POST["id"] : $sustituto = 0;
        
        return $this->_view->render('agregar');
    }
    
    public function _guardarAction()
    {
        session_start();
        $cadena1 = "";
        $cadena2 = "";
        $ban1 = 0;
        $ban2 = 0;
        $ban3 = 0;
        $yaExisteE = array();
        $yaExisteS = array();
        $noExisten = array();
        
        $verdaderos = array();
        $falsos = array();
        $arregloReturn = array();
        $idSustituto = (int) $_POST['id'];
        $articulos = $_POST['arts'];
        $arts = json_decode($articulos);
        
        foreach ($arts as $values) {
            //BUSCAMOS QUE ESTE DADO DE ALTA EN EL CATALOGO CON LAS VALIDACIONES
            //REQUERIDAS
            $primero = $this->_modeltwo->buscaEquivalente($values);
            $existe = explode(" ", $primero);
            
            if($existe[0] == "Existe")
            {
                $id = $this->_model->obtenerPorId($values);
                //SI EL ID == 0 QUIERE DECIR QUE EL ARTICULO NO ESTA EN LA TABLA
                // DE LOS SUSTITUTOS 
                if($id == 0)
                {
                    $valor = $this->_modeltwo->buscarPorNumArt($values);
                    //SI EL VALOR ES 0 NO EXISTE COMO ARTICULO EQUIVALENTE
                    //Y SE REGISTRARA en la tabla de sustitutos
                    if($valor == 2)
                    {
                        $save = $this->_modeltwo->guardar($idSustituto, $values);
                        if($save == "Inserto")
                            $verdaderos[] = $values;
                    }
                    else 
                    {
                        $ban3 = 1;
                        $yaExisteE[] = $values."=> ligado al sustituto: ".$valor;
                    }   
                }
                else
                {
                    $ban2 = 1;
                    $yaExisteS[] = $values;
                }
            }
            else
            {
                $ban1 = 1;
                $noExisten[] = $values;
                
                
            }     
        }
        
        /*LLENAMOS LOS ARREGLOS DE VERDADEROS - FALSOS PARA LLENARLOS CON LOS DATOS
         * CORRESPONDIENTES SEGUN LAS BANDERAS ENCENDIDAS
         */
        if($ban1 == 1 && $ban2 == 1 && $ban3 == 1)
        {
            //ban1
            $falsos["NoExisten"] = $noExisten;
            //ban2
            $falsos["yaExisteS"] = $yaExisteS;
            //ban3
            $falsos["yaExisteE"] = $yaExisteE;
            
            $arregloReturn[] = $verdaderos;
            $arregloReturn[] = $falsos;
        }
        else
        {
            if($ban1 == 1 && $ban2 == 1)
            {
                //ban1
                 $falsos["NoExisten"] = $noExisten;
                //ban2
                 $falsos["yaExisteS"] = $yaExisteS;
                 
                 $arregloReturn[] = $verdaderos;
                 $arregloReturn[] = $falsos;
            }
            else
            {
                if($ban1 == 1 && $ban3 == 1)
                {
                    //ban1
                     $falsos["NoExisten"] = $noExisten;
                    //ban3
                     $falsos["yaExisteE"] = $yaExisteE;
            
                     $arregloReturn[] = $verdaderos;
                     $arregloReturn[] = $falsos;
                    
                }
                else 
                    {
                        if($ban2 == 1 && $ban3 == 1)
                        {
                            //ban2
                            $falsos["yaExisteS"] = $yaExisteS;
                            //ban3
                            $falsos["yaExisteE"] = $yaExisteE;
                            
                            $arregloReturn[] = $verdaderos;
                            $arregloReturn[] = $falsos;
                        }
                        else
                        {
                            if($ban1 == 1)
                            {
                                //ban1
                                $falsos["NoExisten"] = $noExisten;
                                
                                $arregloReturn[] = $verdaderos;
                                $arregloReturn[] = $falsos;
                            }
                            if($ban2 == 1)
                            {
                                //ban2
                                $falsos["yaExisteS"] = $yaExisteS;
                                $arregloReturn[] = $verdaderos;
                                $arregloReturn[] = $falsos;
                            }
                            if($ban3 == 1)
                            {
                                //ban3
                                $falsos["yaExisteE"] = $yaExisteE;
                            
                                $arregloReturn[] = $verdaderos;
                                $arregloReturn[] = $falsos;
                            }
                        }
                    
                    }
            }
                
        }
        
        session_destroy();
        
        return json_encode($arregloReturn);
    }
    
    public function _encuentraAction()
    {
        session_start();
        
        $id_Sustituto = (int) $_POST['id'];
        
        $e = $this->_modeltwo->buscarArticuloPorId($id_Sustituto);
        
        session_destroy();
        return $e;
    }
    
    
    
    public function _buscasyeAction()
    {
        session_start();
        $id_Sustituto = (int) $_POST['idS'];
        
        $cad = $this->_model->obtenerSustitutoEquivalente($id_Sustituto);

        session_destroy();
        return $cad;
    }
    
    public function _elimisustAction()
    {
        session_start();
        $id_Sust = (int) $_POST['idS'];
        
        $e = $this->_model->eliminar($id_Sust);
        
        return $e;
        session_destroy;
    }
       
    public function _elimequivAction()
    {
        session_start();
        $id_Sust = (int) $_POST['idS'];
        $id_Equiv = (int) $_POST['idE'];
        
        //$id_Sust = (int) $_POST['idS'];
        
        $e = $this->_modeltwo->eliminar($id_Sust,$id_Equiv);
        
        return $e;
        session_destroy();
    }
    
    public function _elimequivdosAction()
    {
        session_start();
        $id_Sust = (int) $_POST['id'];
        
        $a = $this->_modeltwo->eliminarPorIdSustituto($id_Sust);
        
        return $a;
        session_destroy();
    }
    
    public function _agregarsustAction()
    {
        $this->_view->setAttribute("baseUrl", '/sye/public');
        
        return $this->_view->render('agregarsust');
    }
    
    public function _guardasustAction()
    {
        session_start();
        $sustituto = $_POST['sust'];
        $datos = $this->_model->guardar($sustituto);
        
        return json_encode($datos);
        session_destroy();
        
    }
    
    public function _cuentaequivAction()
    {
        session_start();
        $id_Sust = (int) $_POST['idS'];
        
        $cuantos = $this->_modeltwo->contarEquivalentes($id_Sust);
        return $cuantos;
        session_destroy();
    }
    
    
    
    
        
    
    
    

}


