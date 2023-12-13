<?php
/**
 * La classe CAdmin permette all'admin di effettuare delle operazioni di ban/riattivazione
 * su utenti e annunci e di eliminare le recensioni
 * @author BPT
 * @package Control
 */
class CAdmin
{
    /**
     * Metodo che mostra la schermata con tutti gli utenti all'admin
     * @return void
     */
    static function homeAdmin() {

        $view = new VAdmin();
        $session = USingleton::getInstance('USession'); // USingleton::getInstance("USession") gestisce l'unicità dell'istanza della classe USession, se un'istanza dell'oggetto è già stata creata la restituisce, altrimenti crea una nuova istanza e la restituisce
        $utente = unserialize($session->readValue('utente')); //unserialize() converte la stringa serializzata nell'oggetto originale
        if (CAdmin::isLogged()) {
            $pm = USingleton::getInstance('FPersistentManager');
            $list = $pm::load('FUtente',   array(['idUser', '!=', $utente->getIdUser()]));  // richiesta di caricamento di tutti gli utenti a meno di quello che ha l'id uguale all'id dell'utente recuperato dalla sessione, cioé l'admin
            $fotoUtente = $pm::load('FFotoUtente',  array(['idUser', '=', $utente->getIdUser()])); // richiesta di caricamento con vincolo id uguale a quello dell'admin loggato
            $view->homeAdmin($utente, $list, $fotoUtente);
        } else {
            header('Location: /markethub/');
        }
    }

    /**
     * Metodo che visualizza il profilo di un utente strutturato per l'admin
     * @param $id
     * @return void
     */
    static function profiloUtente($id = null) // null valore predefinito
    {
        $view = new VAdmin();
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
      
        $utente = $pm::load('FUtente', array(['idUser', '=', $id])); //entra questo, così ho l'utente recuperato dal db che é quello che viene viualizzato, e l'admin recuperato dalla sessione che visualizza
        
        if (isset($_SESSION['utente'])) $utente_del_profilo = unserialize($session->readValue('utente')); // admin recuperato dalla sessione

        if (CAdmin::isLogged() || $id != null) {
            $fotoUtenteVisualizzante = $pm::load('FFotoUtente', array(['idUser', '=', $utente_del_profilo->getIdUser()])); //foto dell'admin
            $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()])); //foto utente del profilo visualizzato
            $annuncio = $pm::load('FAnnuncio', array(['idVenditore', '=', $utente->getIdUser()])); //annunci dell'utente visualizzato, che sarà un singolo elemento o un array di elementi
            $categoria = $pm::loadAll('FCategoria');
            $recensione = $pm::load('FRecensione', array(['idRecensito', '=', $utente->getIdUser()])); //recensioni fatte a quell'utente che viene visualizzato
            
            if ($recensione != null) {
                if (!is_array($recensione)) $recensione = array($recensione);
                
                    for ($i = 0; $i < sizeof($recensione); $i++) {
                        $autori[$i] = $pm::load('FUtente', array(['idUser', '=', $recensione[$i]->getAutore()])); // mette nell'array $autori gli utenti che hanno l'id uguale all'id dell'autore della recensione o delle recensioni fatte all'utente visualizzato
                        $foto_recensori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori[$i]->getIdUser()])); // recupera le foto degli utenti che hanno l'id uguale a quello degli autori dell'array $autori
                    } //per ogni recensione ne estrae l'autore, e per ogni autore ne estrae la foto
              

            }


            if ($annuncio != null) {
                if (!is_array($annuncio)) $annuncio = array($annuncio);
                    for ($i = 0; $i < sizeof($annuncio); $i++) {
                        $foto[$i] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio[$i]->getIdAnnuncio()])); //per ogni annuncio dell'utente, ne estrae la foto e la mette nell'array $foto[$i]
                        if (!is_array($foto[$i])) {
                            $foto[$i] = array($foto[$i]); // $foto[$i] é array anche con un solo elemento, quindi anche se ho un solo annuncio con una sola foto
                        }
                        $autori_annunci[$i] = $pm::load('FUtente', array(['idUser', '=', $annuncio[$i]->getIdVenditore()])); //potrei farlo $autori_annunci = $pm::load('FUtente', array(['idUser', '=', $annuncio[0]->getIdVenditore()])); ma per convenzione continuo ad usare array
                        $foto_autori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci[$i]->getIdUser()])); //potrei farlo $foto_autore = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci->getIdUser()()])); ma per convenzione continuo ad usare array
                        if (!is_array($foto_autori[$i])) $foto_autori[$i] = array($foto_autori[$i]);


                    }


                
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;
                if (!isset($utente_del_profilo)) $utente_del_profilo = null;

                $view->profilo_utente($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione, $utente_del_profilo,$fotoUtenteVisualizzante);
            } else {
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;
                if (!isset($utente_del_profilo)) $utente_del_profilo = null;
                $view->profilo_utente($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione, $utente_del_profilo,$fotoUtenteVisualizzante);
            }

        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }


    
    /**
     * Metodo che permette all'admin di visualizzare il suo profilo
     * @param $id
     * @return void
     */

    static function profiloAdmin($id = null)
    {
        $view = new VAdmin(); 
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
        
        $utente = unserialize($session->readValue('utente')); // recupero dell'oggetto Utente dalla sessione

       


        if (CAdmin::isLogged()) {  // isLogged() controlla se si é loggati come Admin
            $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));{
            $annuncio = null;
            $categoria = null;
            $recensione = null;
            $fotoUtenteVisualizzante = null;

            $foto = null;
            $foto_autori = null;
            $autori = null;
            $foto_recensori = null;
            $utente_del_profilo = null;
            $view->profilo_admin($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione, $utente_del_profilo,$fotoUtenteVisualizzante);
            } //nome, cognome ed email recuperati dalla view

        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }



    /**
     * Metodo che permette il ban di un utente da parte dell'admin
     * @param $id
     * @return void
     */
    static function bannaUtente($id) {
        $view = new VAdmin();
        if (CAdmin::isLogged()) {
            $pm = USingleton::getInstance('FPersistentManager');
            $date = $view->getDate(); // ritorna la data impostata dall'admin
            date_default_timezone_set('Europe/Rome'); // imposta il fuso orario
            $timezone = date_default_timezone_get(); // recupero il fusso orario
            try {
                if (strtotime($date) > strtotime($timezone)) {  // strtotime() restituisce il numero di secondi passati tra il primo gennaio del 1970 e la data specificata nel formato d/m/Y
                    $pm::update('dataFineBan', $date, 'idUser', $id, 'FUtente'); // aggiorna i dati nel db associati all'utente con quell'id
                    $pm::update('ban', 1, 'idUser', $id, 'FUtente');
                    header('Location: /markethub/Admin/profiloUtente/'.$id); // rimanda alla pagina dell'utente con quell'id richiamando profiloUtente
                }
            } catch (Exception $e) {
                echo ('Data antecedente a quella corrente: '.$e->getMessage());
                header('Location: /markethub/Admin/profiloUtente?id='.$id); 
            }
        }
        else {
            header('Location: /markethub/');
        }
    }

    /*
     * Metodo che permette la riattivazione di un utente da parte dell'admin
     * @param $id
     * @return void
     
    static function rimuoviBan($id) {
        $session = USingleton::getInstance('USession');
        $admin = unserialize($session->readValue('utente'));
        if ($admin != null && $admin->getAdmin() == 1) {
            $pm = USingleton::getInstance('FPersistentManager');
            $pm::update('ban', 0, 'idUser', $id, 'FUtente');
            $pm::update('dataFineBan', null, 'idUser', $id, 'FUtente');
            header('Location: /markethub/Admin/profiloUtente/'.$id);
        } else {
            header('Location: /markethub/');
        }
    }
    */

  

    /**
     * Metodo che permette l'eliminazione di una recensione scurrile da parte dell'admin
     * @param $id
     * @return void
     */
    /**
     * Funzione invocata quando un utente scrive una recensione 
     * @param $id
     * @return void
     */
    public static function scriviRecensione()
    {
        $pm = USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        if (CAdmin::isLogged()) {
            $utente_recensitore = unserialize($session->readValue("utente"));
        }

        if ($utente_recensitore != null) {

            $commento = VUtente::getCommento(); 
            $valutazione = VUtente::getValutazione();
            $dataPubblicazione = date('Y-m-d'); // restituisce la data corrente nel formato anno-mese-giorno
            $idRecensito = VUtente::getIdUser();
            $autore = unserialize($_SESSION['utente']);
            $recensione = new ERecensione($commento, $valutazione, $dataPubblicazione, $autore->getIdUser(), null, $idRecensito);
            $pm::store($recensione);
            header('Location: /markethub/Admin/profiloUtente?id=' . $idRecensito);  // rimanda alla pagina dell'utente con quell'id richiamando profiloUtente
        } else {
            header('Location: /markethub/');
        }
    }


    /**
     * Metodo che verifica se si é loggati come admin
     * @return void
     */
    static function isLogged()
    {
        $identified = false;
        if (isset($_COOKIE['PHPSESSID'])) {  // controlla che il cookie con l'id per la sessione sia settato 
            if (USession::sessionStatus() == PHP_SESSION_NONE) { // se però non c'é una sessione attiva sul server
                USingleton::getInstance("USession"); // crea una nuova istanza eseguendo session_start() costruttore di USession
            }
        }
        if (isset($_SESSION['utente'])) { // se nell'array superglobale $_SESSION la chiave utente ha un valore associato
            if(unserialize($_SESSION['utente'])->getAdmin()==1)   $identified = true; //recupero dell'oggetto Utente e verifica sul booleano admin
        }

        return $identified;
    }


    public static function storico($id){
        $pm = USingleton::getInstance("FPersistentManager");
        $view = new VUtente();
        if(CAdmin::isLogged()){
            $utente = $pm::load('FUtente',array(['idUser','=',$id])); // utente di cui visualizzo lo storico
            $annunci= $pm::load('FAnnuncio',array(['idCompratore','=',$utente->getIdUser()])); // annunci che hanno l'id del compratore uguale all'id dell'utente di cui visualizzo lo storico
            if(isset($annunci)) {

                if (!is_array($annunci)) $annunci = array($annunci);
                for ($i = 0; $i < count($annunci); $i++) {
                    $immagini[] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci[$i]->getIdAnnuncio()]));
                    if (!is_array($immagini[$i])) $immagini[$i] = array($immagini[$i]);
                }
                $view->storico($annunci,$utente,$immagini);
            }
            else  $view->storico($annunci=null,$utente,$immagini=null);



        }
        else header("Location: /markethub/");

    }


    
    /**
     * Metodo che consente di cancellare una recensione qualsiasi
     * @param $id, $profilo
     * @return void
     */
    static function cancellaRecensione($id, $profilo){ // prende id della recensione e del profilo dalla quale viene rimossa
        $pm = USingleton::getInstance("FPersistentManager");
        if (CAdmin::isLogged()) {
            $recensione = $pm::load("FRecensione", array(['idRecensione', '=', $id])); // carica la recensione con l'id uguale all'id passato
            if ($recensione != null) {
                $pm::delete('idRecensione', $id, "FRecensione");
                header("Location: /markethub/Admin/profiloUtente?id=" . $profilo);  // rimanda alla pagina dell'utente con quell'id richiamando profiloUtentes

            } else {
                header("Location: /markethub/Admin/profiloUtente");
            }
        } else {
            header("Location: /markethub/Admin/profiloUtente");
        }
    }


    static function cancellaAnnuncio($idAnnuncio, $profilo) { 
        $pm = USingleton::getInstance("FPersistentManager");
        if (CAdmin::isLogged()) {
            $annuncio = $pm::load("FAnnuncio", array(['idAnnuncio', '=', $idAnnuncio])); // carica l'annuncio con l'id uguale all'id passato
            if ($annuncio != null){ // per come strutturato, non sarà mai null
                $pm::delete('idAnnuncio', $idAnnuncio, "FAnnuncio");
    
                header("Location: /markethub/Admin/profiloUtente?id=".$profilo);  // rimanda alla pagina dell'utente con quell'id richiamando profiloUtente
            } else { // per come strutturato, non sarà mai null
                header("Location: /markethub/Admin/profiloUtente");
            }
        } else {
            header("Location: /markethub/Admin/profiloUtente");
        }
    }
















}



