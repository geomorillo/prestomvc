<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace system\core;

/**
 * Description of Assets
 *
 * @author Daniel Navarro Ramírez
 */
use system\helpers\File;

class Assets
{

    private static $assets;

    public static function add($assets)
    {
        if (count($assets)) {
            foreach ($assets as $name => $path) {
                static::$assets[$name] = static::resolve($path);
            }
        }
    }

    public static function group($assets)
    {
        if (count($assets)) {
            
            foreach ($assets as $name => $paths) {
                $value = '';
                foreach ($paths as $path) {
                    $value.= static::resolve($path);
                }
                static::$assets[$name] = $value;
            }
        }
    }
    
    public static function addToGroup($group, $path){
        //get current group
        $current = static::$assets[$group];
        //append to current group
        $current.= static::resolve($path);
        //modify current group
        static::$assets[$group] = $current;        
    }

    public static function get($name)
    {
        if (isset(static::$assets[$name])) {
            return static::$assets[$name];
        }
    }

    public static function getAll()
    {
        return static::$assets;
    }

    private static function resolve($path)
    {
        $ext = '';
        if(!is_array($path)){
            $ext = substr($path, strrpos($path, '.') + 1);
        }
        
        switch ($ext) {
            case 'js':
                $value = '<script src="'.  baseUrl('/assets/' . $path) . '" type="text/javascript"></script>' . PHP_EOL;
                break;
            case 'css':
                $value = '<link href="'.baseUrl('/assets/' . $path) . '" rel="stylesheet" type="text/css">' . PHP_EOL;
                break;
            default:
                $value = '<img src="'.baseUrl('/assets/' . $path[0]) . '" ' .$path[1] . ">" . PHP_EOL;
                break;
        }
        return $value;
    }

}
