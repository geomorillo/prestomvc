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

    private static $template;

    public static function render($path, array $data = array())
    {
        ob_start();
        extract($data);
        try {
            $template = "default";
            if (self::$template) {
                $template = self::$template;
            }
            $header = TEMPLATE_PATH . $template . DS . "header.php";
            $footer = TEMPLATE_PATH . $template . DS . "footer.php";
            $viewPath = VIEW_PATH . $path . ".php";
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

    public static function useTemplate($template)
    {
        self::$template = $template;
        return new static;
    }

    function __toString()
    {
        return self::render();
    }

}
