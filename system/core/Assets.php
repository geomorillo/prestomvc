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
class Assets {
    
    public static function css($files = array())
    {
        if(is_array($files)) {
            foreach($files as $css) {
                echo '<link href="/assets/' . $css . '" rel="stylesheet" type"text/css">' . "\n";
            }
            
            return;
        }
        
        if (!empty($files)) {
            echo '<link href="/assets/' . $files . '" rel="stylesheet" type"text/css">';
        }
    }
    
    public static function js($files = array())
    {
        if(is_array($files)) {
            foreach($files as $js) {
                echo '<script src="/assets/' . $js . '" type="text/javscript"></script>' . "\n";
            }
            
            return;
        }
        
        if (!empty($files)) {
            echo '<script src="/assets/' . $files . '" type="text/javscript"></script>';
        }
    }
    
    public static function img($files, $title, $class = null, $width = null, $height = null, $alt = null)
    {        
        if (!empty($files)) {
            echo '<img src="/assets/' . $files . '" class="'.$class.'" title="'.$title.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'">';
        }
    }
}
