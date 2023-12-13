<?php
/**
 * La classe FUtente fornisce query per gli oggetti EUtente
 * @author BPT
 * @package Foundation
 */
class FUtente extends FDatabase
{
    /**
     * @var string tabella con cui opera
     */
    private static $table = "utente";
    /**
     * Classe foundation
     */
    private static $class = "FUtente";
    /**
     * Valori della tabella
     */
    private static $values = '(:nome, :cognome, :idUser, :email, :password, :dataIscrizione, :dataFineBan, :ban, :admin, :vemail, :codice)';
    /**
     * Costruttore
     */
    public function __construct(){}


    /**
     * Questo metodo lega gli attributi dell'utente da inserire con i parametri della insert
     * @param PDOStatement $stmt
     * @param EUtente $user Utente in cui i dati devono essere inseriti nel DB
     */
    public static function bind($stmt, EUtente $user)
    {
        $stmt->bindValue(':idUser',$user->getIdUser(), PDO::PARAM_INT);
        $stmt->bindValue(':nome', $user->getNome(), PDO::PARAM_STR);
        $stmt->bindValue(':cognome', $user->getCognome(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':dataIscrizione', $user->getDataIscrizione(), PDO::PARAM_STR);
        $stmt->bindValue(':dataFineBan', $user->getDataFineBan(), PDO::PARAM_STR);
        $stmt->bindValue(':ban', $user->isBan(), PDO::PARAM_BOOL);
        $stmt->bindValue(':admin', $user->getAdmin(), PDO::PARAM_BOOL);
        $stmt->bindValue(':vemail', $user->getDataVerificaEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':codice', $user->getCodice(), PDO::PARAM_INT);


    }

    /**
     * Questo metodo restituisce il nome della tabella per la costruzione della query
     * @return string $table nome della tabella
     */
    public static function getTable(): string {
        return self::$table;
    }
    /**
     * Questo metodo restituisce il nome della classe per la costruzione della query
     * @return string $class nome della classe
     */
    public static function getClass(): string {
        return self::$class;
    }

    /**
     * Questo metodo restituisce l'insieme dei valori per la costruzione della Query
     */
    public static function getValues() :string {
        return self::$values;
    }

    /**
     * Metodo che permette la store di un utente
     * @param $utente utente da salvare
     * @return null
     */
    public static function store($utente){

        $db = parent::getInstance();
        $id = $db->storeDB(self::$class, $utente);
        $utente->setIdUser($id);
    }

    /**
     * Metodo che può aggiornare i campi di un utente
     * @param $val valore della primary key da usare come riferimento per la riga
     * @param $newValue nuovo valore da assegnare
     * @param $field campo in cui si vuole modificare il valore
     * @param $pk
     * @return true se esiste il mezzo, altrimenti false
     */
    public static function update($field, $newValue, $pk, $val){
        $db = parent::getInstance();
        $result = $db->updateDB(self::getClass(),$field,$newValue,$pk,$val);
        if($result) return true;
        else return false;
    }

    /**
     * Metodo che permette la delete sul DB in base all'id
     * @param $field campo da considerare quando viene passat l'id
     * @param $id id dell'oggetto da eliminare dal DB
     * @return true|false
     */
    public static function delete($field,$id){
        $db = parent::getInstance();
        $result = $db->deleteDB(self::getClass(),$field,$id);
        if($result != null) return true;
        else return false;
    }

    /**
     * Metodo che permette di verificare se esiste un utente nel DB
     * @param $id valore della riga di cui si vuole verificare l'esistenza
     * @param $field colonna su cui eseguire la verifica
     * @return true|false
     */
    public static function exist($field,$id){
        $db = parent::getInstance();
        $result = $db->existDB(self::getClass(),$field,$id);
        if ($result != null) return true;
        else return false;
    }

    /**
     * Metodo che cerca un determinato oggetto nel DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed
     */
    public static function search($parametri=array(),$ordinamento='',$limite=''){
        $db = parent::getInstance();
        $result = $db->searchDB(self::$class,$parametri,$ordinamento,$limite);
        return $result;
    }

    /**
     * Metodo che permette il caricamento di un utente, posto che sia loggato
     * @param email dell'utente
     * @param pass password dell'utente
     * @return mixed
     */
    public static function loadLogin($user,$pass){
        $utente = null;
        $db = FDatabase::getInstance();
        $result = $db->checkIfLogged($user,$pass);
        if(isset($result)){
            $utente = self::loadByField(array(['email', '=', $result['email']]));
        }
        return $utente;
    }

    /**
     * Metodo che prende determinate righe dal DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return mixed
     */
    public static function getRows($parametri = array(),$ordinamento = '',$limite = ''){
        $db = parent::getInstance();
        $result = $db->getRowNum(self::$class,$parametri,$ordinamento,$limite);
        return $result;
    }

    /**
     * Metodo che permette la load di uno o più utenti da DB
     * @param array $parametri
     * @param string $ordinamento
     * @param string $limite
     * @return object $utente -> utenti risultanti dalla query
     */
    public static function loadByField($parametri = array(), $ordinamento = '', $limite = ''){
        $utente = null;
        $db = parent::getInstance();
        $result = $db->searchDB(static::getClass(),$parametri, $ordinamento,$limite);

        if(count($parametri)>0){
            $rows_number = $db->getRowNum(static::getClass(), $parametri,$ordinamento,$limite);
        } else{
            $rows_number = $db->getRowNum(static::getClass());
        }
        if(($result != null) && ($rows_number == 1)){
            $utente = new EUtente($result['nome'],$result['cognome'], $result['email'],  $result['dataIscrizione'], $result['dataFineBan'],$result['ban'],$result['password'],$result['admin'],$result['idUser'],$result['vemail'],$result['codice']);
         }
        else{
            if(($result != null) && ($rows_number > 1)){
                $utente = array();
                for($i = 0; $i < count($result) ;$i++){
                    $utente[] = new EUtente($result[$i]['nome'],$result[$i]['cognome'], $result[$i]['email'],  $result[$i]['dataIscrizione'], $result[$i]['dataFineBan'],$result[$i]['ban'],$result[$i]['password'],$result[$i]['admin'],$result[$i]['idUser'],$result[$i]['vemail'],$result[$i]['codice']);

                }
            }
        }
        return $utente;
    }
}







