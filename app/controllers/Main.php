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
use system\core\View;
use system\core\Controller;
use system\http\Response;
use system\http\Request;
use system\database\Database;
use system\libraries\Encrypter;
class Main extends Controller
{
    protected $db;
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
        print_r($response->get_header("Content-Type"));
    }

    public function testAjax()
    {
        $view = new View("ajaxtest");
        echo $view->render();
    }
    function ajaxcall()
    {
        $request = new Request();
        if($request->is("post")){
            echo $request->is("ajax");
        }
        echo json_encode(array("listo"=>1));
    }
    public function session()
    {
        $_SESSION["favcolor"] = "green";
        $_SESSION["favanimal"] = "xxx";
       
        debug($_SESSION);
    }
    public function db()
    {
        $this->db = Database::connect();
        $sql= "SELECT * FROM cj WHERE id = :id";
       // print_r($this->db->query($sql,array("id"=>6))->results());
       //$this->db->table("cj")->insert(["id"=>7,"nombre"=>"prueba","edad"=>"10"]);
       $this->db->table("cj")->where('id', 1)->delete();
    }
    public function encrypt(){
        //it is possible to decrypt or encrypt because the ENCRYPT_KEY is defined in config.php 
        $payload = "todook";
        $encrypted = Encrypter::encrypt($payload);
        echo $encrypted;
        echo "<br>";
        $decryded = Encrypter::decrypt($encrypted);
        echo $decryded;
        echo "<br>";
    }

}
