<?php

abstract class Controller {
    /*
     * Atributo del Objeto View
     * presentacion o vista
     */

    protected $_view;

    /*
     * Atributo del Objeto modelo
     * que serán los datos
     */
    protected $_model;

    /*
     * Metodo para obtener
     * algun parametro
     * del request POST o GET
     */

    protected function _getParam($key, $default = null) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }

    /*
     * Metodo para obtener
     * todos los parametros
     * del request POST o GET
     */

    protected function _getParams() {
        $return = array();
        if (isset($_GET) && is_array($_GET)) {
            $return += $_GET;
        }
        if (isset($_POST) && is_array($_POST)) {
            $return += $_POST;
        }
        return $return;
    }

    /*
     * Metodo para redirigir
     * hacia otra url.
     * usando header location
     */
    protected function _redirect($url, array $options = array()) {
        if (headers_sent ()) {
            throw new Exception('Cannot redirect because headers have already been sent.');
        }

        // prevenir inyeccion en header
        $url = str_replace(array("\n", "\r"), '', $url);

        // redirect
        header("Location: $url");
        exit();
    }
    
    

    /*
     * Metodo abstracto que se deben de implementar
     * en los controladores para
     * manejar o implementar la peticiones
     * de usuarios (Handle Request).
     *
     * Aqui implementamos la logica del controlador
     */
    abstract public function executeAction();
}