<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace app\controllers;

/**
 * Description of Main
 *
 * @author geomorillo
 */
use system\core\Controller;
use system\http\Response;

class Main extends Controller
{

    public function index()
    {
        $response = new Response();
        //$response->set_status(\system\core\StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        //Content-Type: text/html
        $response->set_status(\system\http\StatusCode::HTTP_NOT_FOUND);
        $response->set_header("Content-Type", "text/html");
        $response->send();
        //$response->sendJSON();
        // echo json_encode(array("a"=>2));
       // $response->redirect("/main/testredireccion");
    }
    public function testmultiple()
    {
        $response = new Response();
          $response->set_headers(array(
          'Content-Type' => 'text/plain',
          'Content-Disposition' => 'attachment; filename="downloaded.pdf"',
          'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
          'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
          'Pragma' => 'no-cache',
          ));
          $response->send();
          echo "Esto es un texto";
    }

    public function testredireccion()
    {
        echo "se redirecciono";
    }

    public function testjson()
    {
        $response = new Response();

        $response->sendJSON();
        echo json_encode(array("a" => 2));
    }

    public function testHeader()
    {
        $response = new Response();
        $response->set_status(\system\http\StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        $response->set_header("Content-Type", "text/html");
        $response->send();
    }
    function getHeader()
    {
         $response = new Response();
        $response->set_status(\system\http\StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        $response->set_header("Content-Type", "text/html");
        print_r( $response->get_header("Content-Type"));
    }

}
