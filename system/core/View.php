<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of View
 *
 * @author geomorillo
 */
use system\core\LogException;

class View
{

    private $template;
    private $namespace;

    public function render($path, array $data = array())
    {
        $path = explode('/', $path);
        $path = implode(DS, $path);
        $path = explode('\\', $path);
        $path = implode(DS, $path);
        if(!$this->namespace){
            $this->namespace = "app";
        }
        ob_start();
        extract($data);
        try {
            $template = "default";
            if ($this->template) {
                $template = $this->template;
            }
            $header = TEMPLATE_PATH . $template . DS . "header.php";
            $footer = TEMPLATE_PATH . $template . DS . "footer.php";
            $viewPath = ROOT.$this->namespace. DS . "views" . DS . $path . ".php";
            dump($viewPath);
            $useTemplate = false;
            if (file_exists($header) && file_exists($footer)) {
                $useTemplate = true;
            }
            if ($useTemplate) {
                include $header;
            }
            include $viewPath;
            if ($useTemplate) {
                include $footer;
            }
        } catch (LogException $le) {
            ob_end_clean();
            throw $le->logError();
        }
        return ob_get_clean();
    }

    public function useTemplate($template)
    {
        $this->$template = $template;
        return $this;
    }

    public function setCaller($namespace)
    {
        $this->namespace = $namespace;
    }

    function __toString()
    {
        return $this->render();
    }

}
