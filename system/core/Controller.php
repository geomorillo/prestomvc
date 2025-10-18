<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

/**
 * Description of Controller
 *
 * @author geomorillo
 */

namespace system\core;

abstract class Controller
{

    public $view;

    public function __construct()
    {
        $this->view = new View();
        $this->view->setCaller($this->get_namespace($this));//Allows to save the namespace
    }

    /**
     * Generate and return CSRF token for forms
     * @return string
     */
    protected function getCsrfToken()
    {
        if (!USE_SESSIONS) {
            return '';
        }
        $csrf = new Csrf();
        return $csrf->generate();
    }

    /**
     * Add CSRF token to view data
     * @param array $data
     * @return array
     */
    protected function withCsrfToken(array $data = [])
    {
        $data['csrf_token'] = $this->getCsrfToken();
        return $data;
    }

    private function get_namespace($instance): string
    {
        $namespace = get_class($instance);
        $namespace = explode("\\controllers", $namespace);
        $namespace = explode("\\", $namespace[0]);
        $namespace = implode(DS, $namespace);
        return  $namespace;
    }

    abstract function index(...$args);
}
