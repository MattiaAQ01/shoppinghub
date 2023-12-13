<?php
if(file_exists('Config.inc.php')) require_once 'Config.inc.php';

/**
 * La classe FDatabase viene utilizzata per la connessione al DB
 * @author BPT
 * @package Foundation
 */
class FDatabase
{
    /**
     * Variabile che stabilisce ls connessione con il db
     * @var $db PDO
     */
    private $db;
    /**
     * Classe foundation
     * @var string
     */
    private static $class = "FDatabase";

    /**
     * Costruttore privato, accessibile solo con il metodo getInstance()
     */
    public function __construct()
    {
        if (!$this->existConn()) {
            try {
                $this->db = new PDO("mysql:host=127.0.0.1;dbname=" . $GLOBALS['database'], $GLOBALS['username'], $GLOBALS['password']);
            } catch (PDOException $e) {
                print 'Attenzione errore: '. $e->getMessage();
            }
        }
    }

    /**
     * Metodo che restituisce l'unica istanza dell'oggetto.
     * @return FDataBase l'istanza dell'oggetto.
     */
    public static function getInstance(){
        if (USingleton::getInstance(self::$class) == null) {
            USingleton::getInstance(self::$class);
        }
        return USingleton::getInstance(self::$class);
    }

    /**
     * Verifica l'esistenza della connessione con il database
     * @return bool
     */
    public function existConn(): bool {
        if($this->db != null){
            return true;
        } else
            return false;
    }

    /**
     * Chiude la connessione con il database
     * @return void
     */
    public function closeConn(){
        USingleton::stopInstance(self::$class);
    }

    /**
     * Questa funzione carica in $result il risultato di una query. Può produrre sia risultati singoli
     * che array di risultati (se le righe prodotte sono maggiori di una)
     * @param $class
     * @param $field
     * @param $id
     * @return array|mixed|null
     */
    public function loadDB($class, $field='', $criterio='', $id='')
    {
        try {
            if ($field == '' || $id == '' || $criterio == ''){
                $query = "SELECT * FROM " . $class::getTable() . ";";
            } else {
                $query = "SELECT * FROM " . $class::getTable() . " WHERE " . $field . $criterio . "'" . $id . "';";
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 0) {
                $result = null;        //nessuna riga interessata. return null
            } elseif ($num == 1) {                          //nel caso in cui una sola riga fosse interessata
                $result = $stmt->fetch(PDO::FETCH_ASSOC); //ritorna una sola riga
            } else {
                $result = array();                         //nel caso in cui piu' righe fossero interessate
                $stmt->setFetchMode(PDO::FETCH_ASSOC);   //imposta la modalità di fetch come array associativo
                while ($row = $stmt->fetch())
                    $result[] = $row;
            }
            // $this->closeDbConnection();
            return $result;
        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Verifica l'accesso di un utente, controllando le email e password siano presenti nel DB
     * @param $email
     * @param $pass
     * @return mixed|null
     */
    public function checkIfLogged($email, $pass){
        try {
            $class = 'FUtente';
            $query = 'SELECT * FROM ' . $class::getTable() . " WHERE email ='" . $email . "' AND password ='" . $pass . "';";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 0) {
                $result = null;
            } else {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return $result;
        } catch (PDOException $e){
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Metodo utilizzato per salvare una nuova tupla sul DB
     * @param $object
     * @param $class
     * @return bool|mixed
     */
    public function storeDB($class, $object){

        try {
            $this->db->beginTransaction();
            $query = "INSERT INTO `" . $class::getTable() . "` " . str_replace(array(':', ',', ')'), array('`', '`,', '`)'), $class::getValues()) . " VALUES " . $class::getValues();
            $stmt = $this->db->prepare($query);
            $class::bind($stmt, $object);
            $stmt->execute();
            $id = $this->db->lastInsertId(); // ritorna l'id inserito, che é quello dell'oggetto appen
            $this->db->commit();
            $this->closeConn();
            return $id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Metodo utilizzato per salvare una foto di un utente o annuncio sul DB
     * @param $class
     * @param $obj
     * @param $nome_file
     * @return false|string|null
     */
    public function storeMediaDB($class , $obj, $nome_file) {
        try {
            $this->db->beginTransaction();
            $query = "INSERT INTO `" . $class::getTable() . "` " . str_replace(array(':', ',', ')'), array('`', '`,', '`)'), $class::getValues()) . " VALUES " . $class::getValues();
            $stmt = $this->db->prepare($query);
            $class::bind($stmt, $obj, $nome_file);
            $stmt->execute();
            $id = $this->db->lastInsertId();
            $this->db->commit();
            $this->closeConn();
            return $id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Metodo utilizzato per aggiornare dei valori sul DB
     * @param $class
     * @param $field
     * @param $newvalue
     * @param $pk
     * @param $id
     * @return bool
     */
    public function updateDB ($class, $field, $newvalue, $pk, $id)
    {
        try {
            $this->db->beginTransaction();
            $query = "UPDATE " . $class::getTable() . ' SET ' . $field . '="' . addslashes($newvalue) . '" WHERE ' . $pk . '="' . $id . '";';
            //var_dump($query);
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $this->db->commit();
            $this->closeConn();
            return true;
        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Questa funzione serve a rimuovere i dati di una determinata istanza di un oggetto dal DB
     * @param $object
     * @return bool
     */
    public function deleteDB ($class, $field, $id)
    {
        try {
            $result = null;
            $this->db->beginTransaction();
            $esiste = $this->existDB($class, $field, $id);
            if ($esiste) {
                $query = "DELETE FROM " . $class::getTable() . " WHERE " . $field . "='" . $id . "';";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $this->db->commit();
                $this->closeConn();
                $result = true;
            }

        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
        }
        return $result;
    }

    /**
     * Metodo utilizzato per verificare l'esistenza di una tupla sul DB
     * @param $class
     * @param $field
     * @param $id
     * @return array|false|mixed|void|null
     */
    public function existDB ($class, $field, $id)
    {
        try {
            $query = "SELECT * FROM " . $class::getTable() . " WHERE " . $field . "='" . $id . "'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) == 1) return $result[0];  //rimane solo l'array interno
            else if (count($result) > 1) return $result;  //resituisce array di array
            $this->closeConn();

        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Cerca all'interno del DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return array|false
     */
    public function searchDB($class, $parametri = array(), $ordinamento = '', $limite = ''){
        $filtro = '';
        try {
            for ($i = 0; $i < sizeof($parametri); $i++) {
                if ($i > 0) $filtro .= ' AND';
                $filtro .= ' `' . $parametri[$i][0] . '` ' . $parametri[$i][1] . ' \'' . $parametri[$i][2] . '\'';
            }
            $query = 'SELECT * ' .
                'FROM `' . $class::getTable() . '` ';

            if ($filtro != '')
                $query .= 'WHERE ' . $filtro . ' ';
            if ($ordinamento != '')
                $query .= 'ORDER BY ' . $ordinamento . ' ' . 'DESC ';
            if ($limite != '')
                $query .= 'LIMIT ' . $limite . ' ';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $numRow = $stmt->rowCount();
            if ($numRow == 0){
                $result = null;
            } elseif ($numRow == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

            } else {
                $result = array();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                while ($row = $stmt->fetch()) $result[] = $row;
            }
            return $result;
        } catch (PDOException $e){
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Questo metodo verifica quante righe sono state prodotte da una determinata query
     * @param $class
     * @param $field
     * @param $id
     * @return int|null
     */
    public function getRowNum($class, $parametri = array(), $ordinamento = '', $limite = ''){
        $filtro = '';
        try {
            for ($i = 0; $i < sizeof($parametri); $i++) {
                if ($i > 0) $filtro .= ' AND';
                $filtro .= ' `' . $parametri[$i][0] . '` ' . $parametri[$i][1] . ' \'' . $parametri[$i][2] . '\'';
            }
            $query = 'SELECT * ' .
                'FROM `' . $class::getTable() . '` ';
            if ($filtro != '')
                $query .= 'WHERE ' . $filtro . ' ';
            if ($ordinamento != '')
                $query .= 'ORDER BY ' . $ordinamento . ' ' ;
            if ($limite != '')
                $query .= 'LIMIT ' . $limite . ' ';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            $this->closeConn();
            return $num;

        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }
    /**
     * Funzione utilizzata per ritornare tutte le recensioni presenti sul DB,
     * utilizzata nella pagina admin
     * @param $query query da eseguire
     * @return array|null
     */
    public function getAllRev ()
    {
        try {
            $query = "SELECT * FROM recensione;";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 0) {
                $result = null;
            } elseif ($num == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $result = array();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                while ($row = $stmt->fetch())
                    $result[] = $row;
            }
            return array($result, $num);
        } catch (PDOException $e) {
            echo "Attenzione errore: " . $e->getMessage();
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Metodo che carica tutti i valori di un determinato attributo di una tabella dal DB
     * @param $class
     * @param $columns
     * @param $order
     * @param $limite
     * @return array|mixed|null
     */
    public function loadDefColDB($class, $columns, $order = '', $limite = '') {
        $cols  = '';
        try {
            for ($i = 0; $i < count($columns); $i++) {
                if ($i > 0) $cols .= ', ';
                $cols .= $columns[$i];
            }
            $query = 'SELECT ' . $cols . ' FROM ' . $class::getTable();
            if ($order != '') {
                $query .= ' ORDER BY ' . $order . 'DESC';
            }

            if ($limite != '') {
                $query .= ' LIMIT ' . $limite;
            }
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $numRow = $stmt->rowCount();
print($query);
            if ($numRow == 0) {
                $result = null;
            } elseif ($numRow == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $result = array();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                while ($row = $stmt->fetch()) $result[] = $row;
            }
            return $result;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

}