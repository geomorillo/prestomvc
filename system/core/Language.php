<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Supports internationalization
 *
 * @author geomorillo
 */
class Language
{

    private static $language = DEFAULT_LANG;
    private static $translation;

    /**
     * Sets the current language
     * @param type $lang
     * @return instance
     */
    public static function setLang($lang)
    {
        self::$language = $lang;
        self::setTranslation($lang);
        return new static;
    }

    /**
     * Gets the current language
     * @return type
     */
    public static function getLang()
    {
        return self::$language;
    }

    /**
     * Translates by providing the id and parameters
     * @param type $id
     * @param type $params
     * @return type
     */
    public static function translate($id, $params = [])
    {
        if (!self::$translation) {
            self::setTranslation(self::$language); //set default translation
        }
        $text = self::$translation[$id];
        if ($params) {
            $translated = self::findAndReplace($text, $params);
        } else {
            $translated = $text;
        }
        return $translated;
    }
    /**
     * Gets all the translated strings into an array
     */
    public static function translateAll(){
        if (!self::$translation) {
            self::setTranslation(self::$language); //set default translation
        }
        return self::$translation;
    }
    /**
     * Stores the included translation file
     * @param type $lang
     */
    private static function setTranslation($lang)
    {
        self::$translation = include LANGUAGE_PATH . "$lang.php";
    }

    /**
     * Finds the {tags} and replaces them in the text with the value in params 
     * @param type $text
     * @param type $params
     * @return type
     */
    public static function findAndReplace($text, $params)
    {
        preg_match_all("/{(.*?)}/", $text, $matches);
        foreach ($matches[1] as $match) {
            $text = str_replace("{" . $match . "}", $params[$match], $text);
        }
        return $text;
    }

}
