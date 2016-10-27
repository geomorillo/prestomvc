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
use system\core\Assets;

class View
{

    private $template;
    private $namespace;
    private $partial;

    public function render($path, array $data = array())
    {
        $path = explode('/', $path);
        $path = implode(DS, $path);
        $path = explode('\\', $path);
        $path = implode(DS, $path);
        if (!$this->namespace) {
            $this->namespace = "app";
        }
        ob_start();
        extract($data);
        extract(Assets::getAll());
        try {
            $template = "default";
            if ($this->template) {
                $template = $this->template;
            }
            $header = TEMPLATE_PATH . $template . DS . "header.php";
            $footer = TEMPLATE_PATH . $template . DS . "footer.php";
            if (!$this->partial) {
                $viewPath = ROOT . $this->namespace . DS . "views" . DS . $path . ".php";
            } else {
                $viewPath = ROOT . $this->namespace . DS . $path . ".php";
            }
            $useTemplate = false;
            if (file_exists($header) && file_exists($footer)) {
                $useTemplate = true;
            }
            if ($useTemplate && !$this->partial) {
                include $header;
            }
            include $viewPath;
            if ($useTemplate && !$this->partial) {
                include $footer;
            }
            $this->partial = FALSE;
        } catch (LogException $le) {
            ob_end_clean();
            throw $le->logError();
        }
        return ob_get_clean();
    }

    public function useTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function partial($path, $data = [])
    {
        $this->partial = TRUE;
        return $this->render($path, $data);
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
