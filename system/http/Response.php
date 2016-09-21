<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\http;

/**
 * Description of Response
 *
 * @author geomorillo
 */
use system\http\StatusCode;

class Response
{

    protected $headers, $body, $status;

    /**
     * 
     * @param type $url Url a la cual se redirecciona 
     * @param type $method soportados location, refresh
     * @param type $status FOUND 302
     */
    public function redirect($url = '', $method = 'location', $status = Statuscode::HTTP_FOUND)
    {
        if (headers_sent()) {
            return $this;
        }
        if ($method == "refresh") {
            $this->set_header("Refresh", "5;url=$url");
        } else {
            $this->set_header("Location", $url);
        }
        $this->set_status($status);
        $this->send();
        exit();
    }

    /**
     * 
     * @param type $url
     * @param type $method
     * @param type $redirect_code
     */
    public function redirect_back($method = 'location', $status = Statuscode::HTTP_FOUND)
    {
        $url = $_SERVER['HTTP_REFERER'];
        $this->redirect($url, $method, $status);
    }

    /**
     * 
     * @param type $status
     */
    public function set_status($status = StatusCode::HTTP_OK)
    {
        $this->status = $status;
    }

    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $replace
     */
    public function set_header($name, $value, $replace = FALSE)
    {
        $this->headers[] = [$name, $value, $replace];
    }

    /**
     * 
     * @param array $headers
     * @param type $replace
     */
    public function set_headers($headers, $replace = FALSE)
    {
        foreach ($headers as $name => $value) {
            $this->set_header($name, $value, $replace);
        }
        /*     $response->set_headers(array
          'Content-Type' => 'application/pdf',
          'Content-Disposition' => 'attachment; filename="downloaded.pdf"',
          'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
          'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT'),
          'Pragma' => 'no-cache',
          )); */
    }

    /**
     * 
     * @param type $name
     */
    public function get_header($name = null)
    {
        $found[] = NULL;
        foreach ($this->headers as $header) {
            if($header[0]==$name){
               $found[] = $header[0].":".$header[1]; 
            }
        }
        return $found;
    }

    /**
     * NO IMPLEMENTADO TODAVIA
     * @param type $value
     */
    public function set_body($value = false)
    {
        $this->body = $value;
    }
    /**
     * NO IMPLEMENTADO TODAVIA
     * @return type
     */
    public function get_body()
    {
        return $this->body;
    }

    /**
     * 
     * @return \system\core\Response
     */
    private function send_headers()
    {
        if (headers_sent()) {
            return $this;
        }
        //crear buffer
        ob_clean();
        ob_start();
        foreach ($this->headers as $header) {
            $str_header = $header[0] . ":" . $header[1];
            header($str_header, $header[2], $this->status);
        }
        if ($this->body) {
            //send body
        }
    }

    /**
     * 
     * @param type $send_headers
     */
    public function send()
    {
        $this->send_headers();
    }

    public function sendJSON()
    {
        $this->nocache();
        $this->set_header("Content-Type", "application/json");
        $this->send();
    }

    /**
     * Send no cache headers and expires
     */
    public function nocache()
    {
        $this->set_header("Cache-Control", "no-cache, must-revalidate");
        $this->set_header("Expires", "Mon, 26 Jul 1997 05:00:00 GMT");
    }

}
