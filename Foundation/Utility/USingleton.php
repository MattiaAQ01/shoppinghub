<?php

/**
 * Classe che viene usata per richiamare l'unica istanza delle classi, senza
 * starne a creare un nuovo oggetto ogni volta
 * @author BPT
 * @package Foundation/Utility
 */
final class USingleton
{

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * Costruttore privato della classe
     */
    private function __construct() {}

    /**
     * Metodo che richiama l'unica istanza della classe e,
     * se non esiste, la crea
     * @param string $class_name
     * @return mixed
     */
    public static function getInstance(string $class_name) {
        /* Genera un errore sull'interfaccia utente */
        if (!class_exists($class_name)) {
            trigger_error("La classe " . $class_name . "non esiste", E_USER_ERROR);
        }
        /* Converte una stringa in minuscolo */
        $class_name = strtolower($class_name);
        if (!array_key_exists($class_name, self::$instances)) {
            self::$instances[$class_name] = new $class_name;
        }

        return self::$instances[$class_name];
    }

    /**
     * Metodo che distrugge l'istanziazione della classe
     * @param string $class_name
     * @return mixed|null
     */
    public static function stopInstance(string $class_name) {

        if (!class_exists($class_name)) {
            trigger_error("La classe " . $class_name . "non esiste", E_USER_ERROR);
        }

        $class_name = strtolower($class_name);
        if (!array_key_exists($class_name, self::$instances)) {
            self::$instances[$class_name] = null;
        }

        return self::$instances[$class_name];
    }
}