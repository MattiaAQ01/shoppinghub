<?php

/**
 * La classe FAnnuncio fornisce query per gli oggetti EAnnuncio
 * @author BPT
 * @package Foundation
 */
class FAnnuncio extends FDatabase{

    /**
     * @var string
     */
    private static $table = 'annuncio';
    /**
     * @var string
     */
    private static $class = 'FAnnuncio';
    /**
     * @var string
     */
    private static $values = '(:titolo, :descrizione, :prezzo, :data, :idVenditore, :idCompratore, :categoria, :ban, :idAnnuncio, :acquistato)';

    /**
     * Costruttore
     */
    public function __construct(){}

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
    public static function getClass(): string
    {
        return self::$class;
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
     * @param EAnnuncio $annuncio
     * @return void
     */
    public static function bind($stmt, EAnnuncio $annuncio)
    {
        $stmt->bindValue(':titolo', $annuncio->getTitolo(), PDO::PARAM_STR);
        $stmt->bindValue(':descrizione', $annuncio->getDescrizione(), PDO::PARAM_STR);
        $stmt->bindValue(':prezzo', $annuncio->getPrezzo(), PDO::PARAM_STR);
        $stmt->bindValue(':data', $annuncio->getData(), PDO::PARAM_STR);
        $stmt->bindValue(':idAnnuncio', $annuncio->getIdAnnuncio(), PDO::PARAM_INT);
        $stmt->bindValue(':idVenditore', $annuncio->getIdVenditore(), PDO::PARAM_INT);
        $stmt->bindValue(':idCompratore', $annuncio->getIdCompratore(), PDO::PARAM_INT);
        $stmt->bindValue(':categoria', $annuncio->getCategoria(), PDO::PARAM_INT);
        $stmt->bindValue(':ban', $annuncio->isBan(), PDO::PARAM_BOOL);
        $stmt->bindValue(':acquistato', $annuncio->isAcquistato(), PDO::PARAM_BOOL);
    }

    /**
     * Metodo che salva un annuncio nel DB
     * @param $object
     * @return void
     */
    public static function store($object){
        $db = parent::getInstance();
        $id = $db->storeDB(self::$class, $object);
        $object->setIdAnnuncio($id);
    }

    /**
     * Metodo che carica un annuncio dal DB sulla base di un dato attributo
     * @param $parametri
     * @param $ordinamento
     * @param $limite
     * @return array|EAnnuncio
     */
    public static function loadByField($parametri = array(), $ordinamento = '', $limite = ''){
        $annuncio = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(), $parametri, $ordinamento, $limite);
        if (sizeof($parametri) > 0) {
            $rows_number = $db->getRowNum(static::getClass(), $parametri);

        } else {
            $rows_number = $db->getRowNum(static::getClass());
        }
        if(($result != null) && ($rows_number == 1)) {
            $annuncio = new EAnnuncio($result['titolo'], $result['descrizione'], $result['prezzo'], $result['data'],
                $result['idVenditore'],$result['idCompratore'], $result['categoria'], $result['ban'], $result['idAnnuncio'], $result['acquistato']);
             //$annuncio->setIdAnnuncio($result['idAnnuncio']);
        }
        else {
            if(($result != null) && ($rows_number > 1)){
                $annuncio = array();
                for($i = 0; $i < count($result); $i++){
                    $annuncio[] = new EAnnuncio($result[$i]['titolo'], $result[$i]['descrizione'], $result[$i]['prezzo'],  $result[$i]['data'],
                        $result[$i]['idVenditore'],$result[$i]['idCompratore'], $result[$i]['categoria'], $result[$i]['ban'], $result[$i]['idAnnuncio'], $result[$i]['acquistato']);
                    //$annuncio[$i]->setIdAnnuncio($result[$i]['idAnnuncio']);

                }
            }
        }
        return $annuncio;
    }

    /**
     * Metodo che aggiorna un determinato campo di un annuncio nel DB
     * @param $field
     * @param $newvalue
     * @param $pk
     * @param $val
     * @return true|false
     */
    public static function update($field, $newvalue, $pk, $val){
        $db = parent::getInstance();
        $result = $db->updateDB(self::getClass(), $field, $newvalue, $pk, $val);
        if ($result) return true;
        else return false;
    }

    /**
     * Metodo che elimina un annuncio dato il suo id
     * @param $field
     * @param $id
     * @return true|false
     */
    public static function delete($field, $id){
        $db = parent::getInstance();
        $result = $db->deleteDB(self::getClass(), $field, $id);;
        if ($result) return true;
        else return false;
    }

    /**
     * Metodo che verifica se esiste un determinato annuncio dati il campo e l'id
     * @param $field
     * @param $id
     * @return true|false
     */
    public static function exist($field, $id){
        $db = parent::getInstance();
        $result = $db->existDB(self::getClass(), $field, $id);
        if ($result != null) return true;
        else return false;
    }

    /**
     * Metodo che cerca un determinato annuncio nel DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     */
    public static function search($parametri=array(), $ordinamento='', $limite=''){
        $db = parent::getInstance();
        $result = $db->searchDB(self::$class, $parametri, $ordinamento, $limite);
        return $result;
    }

    /**
     * Metodo che restituisce il numero di tuple risultanti di una query
     * @param $parametri
     * @param $ordinamento
     * @param $limite
     * @return int|null
     */
    public static function getRows($parametri = array(), $ordinamento = '', $limite = ''){
        $db = parent::getInstance();
        $result = $db->getRowNum(self::$class, $parametri, $ordinamento, $limite);
        return $result;
    }

    /**
     * Metodo che carica tutti i valori di un determinato attributo della tabella annuncio
     * @param $columns
     * @param $ordinamento
     * @param $limite
     * @return array|mixed|null
     */
    public static function loadDefCol($columns=array(), $ordinamento = '', $limite = '') {
        $db = parent::getInstance();
        $result = $db->loadDefColDB(self::$class, $columns, $ordinamento, $limite);
        return $result;
    }
}