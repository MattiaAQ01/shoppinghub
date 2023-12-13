<?php

/**
 * La classe FFotoUtente fornisce query per gli oggetti EFotoUtente
 * @author BPT
 * @package Foundation
 */
class FFotoUtente extends FDatabase
{
    /**
     * @var string
     */
    private static $table = "fotoUtente";
    /**
     * @var string
     */
    private static $class = "FFotoUtente";
    /**
     * @var string
     */
    private static $values = "(:idFoto, :nomeFoto, :size, :tipo, :foto, :idUser)";

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
     * Metodo che salva una foto nel DB
     * @param ECategoria $fotoUtente
     * @param $nome_file
     * @return void
     */
    public static function storeMedia(EFotoUtente $fotoUtente, $nome_file){
        $db = parent::getInstance();
        $db->storeMediaDB(static::getClass(), $fotoUtente, $nome_file);
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
     * Metodo che permette la cancellazione della foto di un utente in base all'id(del media)
     * @param int $id del media (dell'utente)
     * @return bool
     */
    public static function delete($field, $id){
        $db = parent::getInstance();
        $db->deleteDB(static::getClass(), $field, $id);
    }

    /**
     * Metodo che effettua il bind degli attributi di
     * EFotoUtente, con i valori contenuti nella tabella fotoutente
     * @param $stmt
     * @param $fotoUtente immagine da salvare
     * @param $nome_file
     */
    public static function bind($stmt, EFotoUtente $fotoUtente, $path){

        $file = fopen($path, 'rb') or die ("Attenzione! Impossibile da aprire!");
        $stmt->bindValue(':idFoto', NULL, PDO::PARAM_INT);
        $stmt->bindValue(':nomeFoto', $fotoUtente->getNomeFoto(), PDO::PARAM_STR);
        $stmt->bindValue(':size', $fotoUtente->getSize(), PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $fotoUtente->getTipo(), PDO::PARAM_STR);
        $stmt->bindValue(':foto', fread($file, filesize($path)), PDO::PARAM_LOB);
        $stmt->bindValue(':idUser', $fotoUtente->getIdUser(), PDO::PARAM_INT);
    }

    /**
     * Metodo che carica una foto dal DB sulla base di un dato attributo
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return array|EFotoUtente
     */
    public static function loadByField($parametri = array(), string $ordinamento, string $limite) {
        $foto = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(), $parametri, $ordinamento, $limite);
        $rows_number = $db->getRowNum(static::getClass(), $parametri, $ordinamento, $limite);
        if (($result != null) && ($rows_number = 1)) {
            $foto = new EFotoUtente($result['idFoto'], $result['nomeFoto'], $result['size'], $result['tipo'], $result['foto'],$result['idUser']);
        }
        else {
            if (($result != null) && ($rows_number > 1)) {
                $foto = array();
                for ($i = 0; $i < count($result); $i++) {
                    $foto[] = new EFotoUtente($result[$i]['idFoto'], $result[$i]['nomeFoto'], $result[$i]['size'], $result[$i]['tipo'], $result[$i]['foto'],$result[$i]['idUser']);
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