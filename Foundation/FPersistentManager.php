<?php

/**
 * La classe FPersistentManager viene utilizzata da tramite nelle classi Control per accedere a Foundation
 * @author BPT
 * @package Foundation
 */
class FPersistentManager
{

    /**
     * Metodo che permette di salvare un oggetto sul DB
     * @param $obj
     * @return void
     */
    public static function store($obj) {
        $Eclass = get_class($obj);
        $Fclass = str_replace("E", "F", $Eclass);
        $Fclass::store($obj);

    }

    /**
     * Metodo che permette di salvare una foto sul DB
     * @param $obj
     * @param $nome_file
     * @return void
     */
    public static function storeMedia($obj, $nome_file) {
        $Eclass = get_class($obj);
        $Fclass = str_replace("E", "F", $Eclass);
        $Fclass::storeMedia($obj, $nome_file);
    }

    /**
     * Metodo che permette di caricare il valore di un campo come parametro
     * @param $Fclass
     * @param $parametri
     * @param $ordinamento
     * @param $limite
     * @return mixed
     */
    public static function load($Fclass, $parametri = array(), $ordinamento = '', $limite = '') {
        $ris = $Fclass::loadByField($parametri , $ordinamento = '', $limite = '');
        return $ris;
    }


    /**
     * Metodo che permette il login di un utente fornite le credenziali
     * @param $user
     * @param $pass
     * @return array|EUtente|object|null
     */
    public static function loadLogin($user, $pass) {
        $ris = FUtente::loadLogin($user, $pass);
        return $ris;
    }

    /**
     * Metodo che permette di cancellare il valore di un campo passato come parametro
     * @param $field
     * @param $val
     * @param $Fclass
     * @return void
     */
    public static function delete($field, $val, $Fclass) {
        $Fclass::delete($field, $val);
    }

    /**
     * Metodo che verifica l'esistenza di un valore di un campo passato come parametro
     * @param $field
     * @param $val
     * @param $Fclass
     * @return mixed
     */
    public static function exist($field, $val, $Fclass) {
        $ris = $Fclass::exist($field, $val);
        return $ris;
    }

    /**
     * Metodo che permette l'aggiornamento di un campo passato come parametro
     * @param $field
     * @param $newvalue
     * @param $pk
     * @param $val
     * @param $Fclass
     * @return mixed
     */
    public static function update($field, $newvalue, $pk, $val, $Fclass) {
        $ris = $Fclass::update($field, $newvalue, $pk,$val, $Fclass);
        return $ris;
    }

    /**
     * Metodo che permette la ricerca per categoria
     * @param $class
     * @param $categoria
     * @return mixed
     */
    public static function filterByCategoria($class, $categoria) {
        if ($class == "FAnnuncio") {
            $ris = $class::filterByCategoria($categoria);
        }
        return $ris;
    }



    /**
     * Metodo che permette la ricerca secondo determinati parametri
     * @param $Fclass
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed
     */
    public static function search($Fclass, $parametri = array(),  $ordinamento='',  $limite='') {
        $ris = $Fclass::search($parametri = array(), $ordinamento='', $limite='');
        return $ris;
    }

    /**
     * Metodo che restituisce il numero di righe restituite dalla ricerca
     * @param $class
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed
     */
    public static function getRows($class, $parametri = array(),  $ordinamento='',  $limite='') {
        $ris = $class::getRows($parametri, $ordinamento, $limite);
        return $ris;
    }
    public static function loadAll($class, $field='', $criterio='', $id=''){
       $ris = $class::load($field='', $criterio='', $id='');
       return $ris;
    }

    /**
     * Metodo che carica tutti i valori di un determinato attributo di una tabella
     * @param $class
     * @param $columns
     * @param $order
     * @param $limite
     * @return mixed
     */
    public static function loadDefCol($class, $columns =array(), $order = '', $limite = '') {
        $ris = $class::loadDefCol($columns, $order='', $limite='');
        return $ris;
    }
}