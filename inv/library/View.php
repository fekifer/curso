<?php

class View {
    /* Arreglo para contener los atributos
     * que le asignamos a la vista desde
     * el controlador
     */

    protected $_params = array();

    /*
     * Extension del archivo de vistas,
     * por estnadar de Zend es la
     * extension *.phtml
     */
    const VIEW_EXTENSION = 'phtml';

    /*
     * Para obtener un atributo
     * almacenado
     */

    public function getAttribute($key) {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return null;
    }

    /*
     * Para almacenar un atributo
     * con su valor almacenado
     */

    public function setAttribute($key, $value) {
        if (isset($this->_params[$key])) {
            throw new Exception('La variable de vista' . $key . ' ya existe');
            return false;
        }

        $this->_params[$key] = $value;
        return $this;
    }

    /*
     * Realiza la salida o render de la vista *.phtml al
     * devolver el contenido del búfer de salida de php
     * con ob_get_contents
     * luego retorna el contenido en la variable $output
     */

    public function render($viewName) {
        $path = 'views/' . $viewName . '.' . self::VIEW_EXTENSION;

        if (file_exists($path) === false) {
            throw new Exception('El archivo de vista ' . $path . ' no existe');
            return false;
        }

        ob_start();
        include($path);
        $output = ob_get_contents();
        ob_get_clean();
        return $output;
    }

}