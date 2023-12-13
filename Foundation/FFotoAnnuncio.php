<?php

/**
 * La classe FFotoAnnuncio fornisce query per gli oggetti EFotoAnnuncio
 * @author BPT
 * @package Foundation
 */
class FFotoAnnuncio extends FDatabase{

    /**
     * @var string
     */
    private static $table = "fotoAnnuncio";
    /**
     * @var string
     */
    private static $class = "FFotoAnnuncio";
    /**
     * @var string
     */
    private static $values = "(:idFoto, :nomeFoto, :size, :tipo, :foto, :idAnnuncio)";

    /**
     * Costruttore
     */
    public function __construct() {}

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
     * Metodo che effettua il bind degli attributi di
     * EFotoAnnuncio, con i valori contenuti nella tabella fotoannuncio
     * @param $stmt
     * @param $fotoAnnuncio immagine da salvare
     * @param $nome_file
     */
    public static function bind($stmt, EFotoAnnuncio $fotoAnnuncio,$path){


        $file = fopen($path, 'rb') or die ("Attenzione! Impossibile da aprire!");
        $stmt->bindValue(':idFoto', NULL, PDO::PARAM_INT);
        $stmt->bindValue(':nomeFoto', $fotoAnnuncio->getNomeFoto(), PDO::PARAM_STR);
        $stmt->bindValue(':size', $fotoAnnuncio->getSize(), PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $fotoAnnuncio->getTipo(), PDO::PARAM_STR);
        $stmt->bindValue(':idAnnuncio', $fotoAnnuncio->getIdAnnuncio(), PDO::PARAM_INT);
        $stmt->bindValue(':foto', fread($file, filesize($path)), PDO::PARAM_LOB);

        unset($file);
        unlink($path);
    }

    /**
     * Metodo che salva una foto nel DB
     * @param EFotoAnnuncio $fotoAnnuncio
     * @param $nome_file
     * @return void
     */
    public static function storeMedia(EFotoAnnuncio $fotoAnnuncio, $nome_file){
        $db = parent::getInstance();
        $id=$db->storeMediaDB(static::getClass(), $fotoAnnuncio, $nome_file);
        $fotoAnnuncio->setIdFoto($id);
    }

    /**
     * Metodo che verifica se esiste una determinata foto dati il campo e l'id
     * @param $field
     * @param $id
     * @return bool
     */
    public static function exist($field, $id) {
        $db = parent::getInstance();
        $result = $db->existDB(static::getClass(), $field, $id);
        if ($result != null)
            return true;
        else
            return false;
    }

    /**
     * Metodo che elimina una foto dato il suo id
     * @param $field
     * @param $id
     * @return void
     */
    public static function delete($field, $id) {
        $db = parent::getInstance();
        $db->deleteDB(static::getClass(), $field, $id);
    }

    /**
     * Metodo che carica una foto dal DB sulla base di un dato attributo
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return array|EFotoAnnuncio
     */
    public static function loadByField($parametri = array(),  $ordinamento='',  $limite='') {
        $foto = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(), $parametri, $ordinamento, $limite);
        $rows_number = $db->getRowNum(static::getClass(), $parametri, $ordinamento, $limite);
        if (($result != null) && ($rows_number == 1)) {
            $foto = new EFotoAnnuncio($result['idFoto'], $result['nomeFoto'], $result['size'], $result['tipo'], $result['foto'], $result['idAnnuncio']);
        }
        else {
            if (($result != null) && ($rows_number > 1)) {
                $foto = array();
                for ($i = 0; $i < count($result); $i++) {
                    $foto[] = new EFotoAnnuncio($result[$i]['idFoto'], $result[$i]['nomeFoto'], $result[$i]['size'], $result[$i]['tipo'], $result[$i]['foto'],$result[$i]['idAnnuncio']);

                }
            }
        }
        return $foto;
    }

    /**
     * Metodo che cerca una foto nel DB
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return array|false|null
     */
    public static function search($parametri = array(), string $ordinamento, string $limite) {
        $db = parent::getInstance();
        $result = $db->searchDB(self::getClass(), $parametri, $ordinamento, $limite);
        return $result;
    }

    /**
     * Metodo che restituisce il numero di tuple risultanti di una query
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return int|null
     */
    public static function getRows($parametri=array(), string $ordinamento, string $limite) {
        $db = parent::getInstance();
        $result = $db->getRowNum(self::getClass(), $parametri, $ordinamento, $limite);
        return $result;
    }
}