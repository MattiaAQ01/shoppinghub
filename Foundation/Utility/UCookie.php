<?php

/**
 * La classe UCookie si occupa della crezione/eliminazione dei cookie
 * @author BPT
 * @package Foundation/Utility
 */
class UCookie
{
    /**
     * Metodo utilizzato per mandare cookie nell'header HTTP
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return void
     */
    static function set_cookie(string $name, string $value, int $expires, string $path, string $domain, bool $secure, bool $httponly) {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
    }

    /**
     * Metodo utilizzato per ottenere il valore di un cookie tramite il suo nome
     * @param string $name
     * @return mixed
     */
    static function get_cookie_value(string $name) {
        return $_COOKIE[$name];
    }

    /**
     * Metodo utilizzato per eliminare un cookie tramite il suo nome
     * @param $name
     * @return void
     */
    static function unset_cookie($name) {
        unset($_COOKIE[$name]);
    }

    /**
     * Metodo utilizzato per verificare se un cookie è settato,
     * restituisce 0 se non lo è mentre 1 se lo è
     * @param $name
     * @return bool
     */
    static function isset_cookie($name): bool {
        return isset($_COOKIE[$name]);
    }
}