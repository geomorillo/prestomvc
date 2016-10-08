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

        $namespace = explode("\\controllers", $this->namespace);
        $vpathstr = explode("\\", $namespace[0]);
        $vpath = implode(DS, $vpathstr);
        ob_start();
        extract($data);
        try {
            $template = "default";
            if ($this->template) {
                $template = $this->template;
            }
            $header = TEMPLATE_PATH . $template . DS . "header.php";
            $footer = TEMPLATE_PATH . $template . DS . "footer.php";
            //$viewPath = VIEW_PATH . $path . ".php";
            $viewPath = ROOT.$vpath . DS . "views" . DS . $path . ".php";
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
