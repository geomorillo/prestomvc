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

    return $webroot;
}

function baseUrl($url)
{
    return (WEBROOT == '/') ? $url : WEBROOT . $url;
 
}
