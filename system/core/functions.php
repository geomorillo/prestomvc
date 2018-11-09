<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2016
 * Contacto: geomorillo@yahoo.com
 */

function getWebroot()
{
    $webroot = dirname($_SERVER['PHP_SELF']);
    $webroot = str_replace('\\', '/', $webroot);
    $webroot = strtolower($webroot);
    return $webroot;
}

function baseUrl($url)
{
    return (WEBROOT == '/') ? $url : strtolower(WEBROOT) . $url;
}

/* Return a json content for ajax not only text */

function echo_json($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
}

/**
 * gets a json string from an ajax request
 */
function json_post()
{
    # Get JSON as a string
    $json_str = file_get_contents('php://input');

# Get as an object
    return json_decode($json_str);
}

/** Set CORS FOR METHODS GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD */
function setcors()
{
    // Handle on passed down request
    $response = new \system\http\Response();

    $response->set_header('Access-Control-Allow-Origin', '*', true);
    $response->set_header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD', true);
    $response->set_header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

    $request = new \system\http\Request();
    if ($request->getMethod() == 'OPTIONS') {
        exit();
    }

    $response->send();
}
