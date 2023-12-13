<?php

/**
 * La classe FCategoria fornisce query per gli oggetti ECategoria
 * @author BPT
 * @package Foundation
 */
class FCategoria extends FDatabase
{
    /**
     * @var string
     */
    private static $table = "categoria";
    /**
     * @var string
     */
    private static $class = "FCategoria";
    /**
     * @var string
     */
    private static $values = "(:categoria, :idCate)";

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
     * Metodo che lega gli attributi della categoria da inserire con i parametri della insert
     * @param $stmt
     * @param ECategoria $categoria
     * @return void
     */
    public static function bind($stmt, ECategoria $categoria) {
        $stmt->bindValue(":categoria", $categoria->getCategoria(), PDO::PARAM_STR);
        $stmt->bindValue(":idCate", $categoria->getIdCate(), PDO::PARAM_INT);
    }

    /**
     * Metodo che salva una categoria sul DB
     * @param $categoria
     * @return void
     */
    public static function store($categoria) {
        $db = parent::getInstance();
        $id = $db->storeDB(self::getClass(), $categoria);
        $categoria->setIdCate($id);
    }

    /**
     * Metodo che aggiorna un determinato campo di una categoria nel DB
     * @param $field
     * @param $newvalue
     * @param $pk
     * @param $val
     * @return bool|null
     */
    public static function update($field, $newvalue, $pk, $val) {
        $db = parent::getInstance();
        $id = $db->updateDB(self::getClass(), $field, $newvalue, $pk, $val);
        if ($id)
            return $id;
        else
            return null;
    }

    /**
     * Metodo che elimina una categoria dato il suo id
     * @param $field
     * @param $id
     * @return bool|null
     */
    public static function delete($field, $id) {
        $db = parent::getInstance();
        $id = $db->deleteDB(self::getClass(), $field, $id);
        if ($id)
            return $id;
        else
            return null;
    }

    /**
     * Metodo che verifica se esiste una determinata categoria dati il campo e l'id
     * @param $field
     * @param $id
     * @return bool|null
     */
    public static function exist($field, $id) {
        $db = parent::getInstance();
        $id = $db->existDB(self::getClass(), $field, $id);
        if ($id)
            return $id;
        else
            return null;
    }

    /**
     * Metodo che cerca una determinata categoria nel DB
     * @param $field
     * @param $id
     * @return array|null
     */
    public static function search($field, $id) {
        $db = parent::getInstance();
        $id = $db->searchDB(self::getClass(), $field, $id);
        if ($id)
            return $id;
        else
            return null;
    }

    /**
     * Metodo che carica una categoria dal DB sulla base di un dato attributo
     * @param $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return array|ECategoria
     */
    public static function load($field='', $criterio='', $id=''){
        $Fclass= self::class;
        $db=parent::getInstance();
        $result= $db->loadDB($Fclass,$field='', $criterio='', $id='');
        return $result;
    }
    public static function loadByField($parametri = array(), string $ordinamento, string $limite) {
        $categoria = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(), $parametri, $ordinamento, $limite);
        $rows_number = $db->getRowNum(static::getClass(), $parametri, $ordinamento, $limite);
        if (($result != null) && ($rows_number == 1)) {
            $categoria = new ECategoria($result['idCate'], $result['categoria']);
        }
        else {
            if (($result != null) && ($rows_number > 1)) {
                $categoria = array();
                for ($i = 0; $i < count($result); $i++) {
                    $categoria[] = new ECategoria($result[$i]['idCate'], $result[$i]['categoria']);
                }
            }
        }
        return $categoria;
    }
}