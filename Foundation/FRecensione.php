<?php

/**
 * La classe FRecensione fornisce query per gli oggetti ERecensione
 * @author BPT
 * @package Foundation
 */
class FRecensione extends FDatabase{

    /**
     * @var string
     */
    private static $table = "recensione";
    /**
     * @var string
     */
    private static $class = "FRecensione";
    /**
     * @var string
     */
    private static $values = "(:commento, :valutazione, :idRecensione,  :dataPubblicazione, :autore, :idRecensito)";

    /**
     * Costruttore
     */
    public function __construct() {}

    /**
     * @return string
     */
    public static function getClass(): string
    {
        return self::$class;
    }

    /**
     * @return string
     */
    public static function getTable(): string
    {
        return self::$table;
    }

    /**
     * @return string
     */
    public static function getValues(): string
    {
        return self::$values;
    }

    /**
     * Metodo che lega gli attributi dell'annuncio da inserire con i parametri della insert
     * @param $stmt
     * @param ERecensione $recensione
     * @return void
     */
    public static function bind($stmt, ERecensione $recensione){
        $stmt->bindValue(':commento',$recensione->getCommento(), PDO::PARAM_STR);
        $stmt->bindValue(':valutazione',$recensione->getValutazione(), PDO::PARAM_INT);
        $stmt->bindValue(':idRecensione',$recensione->getIdRecensione(), PDO::PARAM_INT);
        $stmt->bindValue(':dataPubblicazione',$recensione->getDataPubblicazione(), PDO::PARAM_STR);
        $stmt->bindValue(':autore',$recensione->getAutore(), PDO::PARAM_INT); //idRecensione venditore
        $stmt->bindValue(':idRecensito',$recensione->getIdRecensito(),PDO::PARAM_INT);
    }
    /**
     * Metodo che permette di salvare una Recensione
     * @param $rec Recensione da salvare
     * @return $id della Recensione salvata
     */
    public static function store($rec) {
        $db = parent::getInstance();
        $id = $db->storeDB(self::class,$rec);
        $rec->setIdRecensione($id);

       
    }
    /**
     * Funzione che permette di verificare se esiste una Recensione nel database
     * @param  $id valore della riga di cui si vuol verificare l'esistenza
     * @param  string $field colonna su cui eseguire la verifica
     * @return bool $ris
     */
    public static function exist($field, $id) {
        $ris = false;
        $db = parent::getInstance();
        $result = $db->existDB(self::$class,$field, $id);
        if($result!=null)
            $ris = true;
        return $ris;
    }

    /**
     * Permette la delete sul DB in base all'idRecensione
     * @param int l'idRecensione dell'oggetto da eliminare dal db
     * @return bool
     */
    public static function delete($field, $id) {
        $db = parent::getInstance();
        $result = $db->deleteDB(self::$class,$field, $id);
        if($result)
            return true;
        else
            return false;
    }

    /** metodo che cerca una recensione nel DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed|false
     */
    public static function search($parametri=array(), $ordinamento='', $limite=''){
        $db = parent::getInstance();
        $result = $db->searchDB(self::$class, $parametri, $ordinamento, $limite);
        return $result;
    }
    /**
     * Metodo che può aggiornare i campi di una recensione
     * @param $field campo da aggiornare
     * @param  $primkey primary key
     * @param $newvalue nuovo valore da assegnare
     * @return true se esiste il mezzo, altrimenti false
     */
    public static function update($field, $newvalue, $primkey, $val){
        $db = parent::getInstance();
        $result = $db->updateDB(self::getClass(), $field, $newvalue, $primkey, $val);
        if ($result) return true;
        else return false;
    }
    /**
     * Metodo ch carica su DB in base a una parola
     * @param $parola valore da ricercare all'interno del campo text
     * @return object $rec Recensione
     */
    public static function loadByParola($parola) {
        $rec = null;
        $db = parent::getInstance();
        list ($result, $rows_number)=$db->search(self::$class,$parola, "testo");
        if(($result != null) && ($rows_number == 1)) {
            $rec = new ERecensione($result['commento'], $result['valutazione'],  $result['dataPubblicazione'], $result['autore'],$result['idRecensione'],$result['idRecensito']);
            $rec->setIdRecensione($result['idRecensione']);
        }
        else {
            if(($result != null) && ($rows_number > 1)){
                $rec = array();
                for($i = 0; $i < count($result); $i++){
                    $rec[] = new ERecensione($result[$i]['commento'], $result[$i]['valutazione'],  $result[$i]['dataPubblicazione'],$result[$i]['autore'],$result[$i]['idRecensione'],$result[$i]['idRecensito']);
                    $rec[$i]->setIdRecensione($result[$i]['idRecensione']);
                }
            }
        }
        return $rec;
    }
    /** Metodo che permette la load su DB
     * @return object $recensione recensione da caricare
     */
    public static function loadByField($parametri = array(), $ordinamento = '', $limite = ''){
        $recensione = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(), $parametri, $ordinamento, $limite);
        //var_dump($result);
        if (sizeof($parametri) > 0) {
            $rows_number = $db->getRowNum(static::getClass(), $parametri);
        } else {
            $rows_number = $db->getRowNum(static::getClass());
        }
        if(($result != null) && ($rows_number == 1)) {
            $recensione = new ERecensione($result['commento'], $result['valutazione'],  $result['dataPubblicazione'], $result['autore'],$result['idRecensione'],$result['idRecensito']);
            $recensione->setIdRecensione($result['idRecensione']);
        }
        else {
            if(($result != null) && ($rows_number > 1)){
                $recensione = array();
                for($i = 0; $i < sizeof($result); $i++){
                    $recensione[$i] = new ERecensione($result[$i]['commento'], $result[$i]['valutazione'],  $result[$i]['dataPubblicazione'],$result[$i]['autore'],$result[$i]['idRecensione'],$result[$i]['idRecensito']);
                    $recensione[$i]->setIdRecensione($result[$i]['idRecensione']);
                }
            }
        }
        return $recensione;
    }
    /**
     * Metodo che prende determinate righe dal DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed
     */
    public static function getRows($parametri = array(), $ordinamento = '', $limite = ''){
        $db = parent::getInstance();
        $result = $db->getRowNum(self::$class, $parametri, $ordinamento, $limite);
        return $result;
    }

    /**
     * Metodo che carica tutte le recensioni
     * @return mixed
     */
    public static function loadAll() {
        $rec = null;
        $db = FDatabase::getInstance();
        list ($result, $rows_number)=$db->getAllRev();
        if(($result != null) && ($rows_number == 1)) {
            $rec = new ERecensione($result['commento'], $result['valutazione'], $result['dataPubblicazione'], $result['autore'],$result['idRecensione'],$result['idRecensito']);
            $rec->setIdRecensione($result['idRecensione']);
        }
        else {
            if(($result != null) && ($rows_number > 1)){
                $rec = array();
                for($i = 0; $i < count($result); $i++){
                    $rec[] = new ERecensione($result[$i]['commento'], $result[$i]['valutazione'],  $result[$i]['dataPubblicazione'],$result[$i]['autore'],$result[$i]['idRecensione'],$result[$i]['idRecensito']);
                    $rec[$i]->setIdRecensione($result[$i]['idRecensione']);
                }
            }
        }
        return $rec;
    }
}