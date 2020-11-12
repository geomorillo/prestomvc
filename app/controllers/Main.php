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
use system\core\Assets;
use system\helpers\Auth\Auth;
use system\http\Response;
use system\http\Request;
use system\core\Encrypter;
use system\helpers\Key;
use system\core\Language;
class Main extends Controller
{
//    private $auth;
//    private $response;
//    private $request;

    public function __construct()
    {
        parent::__construct();
      //  $this->auth = new Auth(); 
        $this->response = new Response();
       $this->request = new Request();
    }
/*
    public function before()
    {
        if(!$this->auth->isLogged()) {
            $this->response->redirect("login");
        }
    }
*/
    public function index(...$args){
        if(count($args)>0){
           Language::setLang($args[0]);
        }
        $data = Language::translateAll();
     
        echo $this->view->render('main/index',$data);
        
    }
/* TEST FUNCTIONS*/ 
    public function dashboard()
    {
        echo $this->view->useTemplate("admin")->render("main/dashboard");
    }

    public function login()
    {
        $asset = ["login" => "css/login.css"];// login css
        Assets::add($asset);//add css to assets
        echo $this->view->useTemplate("login")->render("main/login");
    }

    public function authenticate()
    {
        //catch username an password inputs using the Request helper
        $username = $this->request->getQuery('username');
        $password = $this->request->getQuery('password');

        if ($this->auth->login($username, $password)) {
            $this->response->redirect("dashboard");
        } else {
            echo "not authenticated some error ocurred";
            // not authenticated you can redirect to a login view
        }
    }

    public function register()
    {
        $username = "geomorillo";
        $password = "12345";
        $email = "geomorillo@yahooo.com";
        $verifypassword = $password;
        if($this->auth->directRegister($username, $password, $verifypassword, $email)){
            echo "El usuario ha sido registrado correctamente";
        }else{
            echo "Ha ocurrido un error al intentar registrar el usuario";
        }
    }

    public function activate(){
        $username = $this->request->setPost('username');
        $activekey = $this->request->setPost('key');
        $this->auth->activateAccount($username, $activekey);
    }

    public function logout()
    {
        $this->auth->logout();
        echo "logged out";
        $this->response->redirect("login");
    }
    public function enc() {
        $encrypted = Encrypter::encrypt("okokokook");
        echo $encrypted;
        echo "<br>";
        echo(Encrypter::decrypt($encrypted));
    }
    public function key() {
        echo Key::generate();
    }

}