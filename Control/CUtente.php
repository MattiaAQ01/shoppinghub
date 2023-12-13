<?php

/**
 * La classe CUtente gestisce le interazioni dell'utente con la web app
 * e permette la personalizzazione dei dati personali
 * @author BPT
 * @package Control
 */
class CUtente
{

    /**
     * Metodo che gestisce il login da parte di un utente
     * @return void
     */
    static function confermaMail()
    {
        $view = new VUtente();
        $pm = USingleton::getInstance("FPersistentManager");
        $utente = $pm->loadLogin($_POST['email'], md5($_POST['password']));
        if ($utente->getCodice() == $_POST['codice']) {
            $session = USingleton::getInstance("USession");
            $dati = serialize($utente);


            $pm->update('vemail', date('Y-m-d'), 'idUSer', $utente->getIdUser(), 'FUtente');
            $session->setValue('vemail', $utente->getDataVerificaEmail());
            header('Location: /markethub/Utente/login');

        } else {
            $view->loginError(0, 'Codice errato');
        }

    }


    static function login()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (static::isLogged()) {
                $pm = USingleton::getInstance("FPersistentManager");
                $view = new VUtente();
                $view->loginOk();

            } else {
                $view = new VUtente();
                $view->showFormLogin();

            }
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            static::verifica();

        }
    }


    /**
     * Metodo che verifica se un utente è bannato oppure è un admin
     * @return void
     */
    static function verifica()
    {   date_default_timezone_set('Europe/Rome');
        $timezone = date_default_timezone_get();
        $view = new VUtente();
        $pm = USingleton::getInstance("FPersistentManager");
        $utente = $pm->loadLogin(VUtente::getEmail(), md5(VUtente::getPassword()));
        if ($utente != null) {
            if ($utente->getDataVerificaEmail() != null) {
                if ($utente->isBan() == 1) {
                if (strtotime($utente->getDataFineBan()) <= strtotime($timezone)) {$pm::update('ban', 0, 'idUser', $utente->getIdUser(), 'FUtente');
                    $pm::update('dataFineBan', null, 'idUser', $utente->getIdUser(), 'FUtente'); 
                    $utente = $pm->loadLogin(VUtente::getEmail(), md5(VUtente::getPassword()));}}                
                    if ($utente->isBan() != 1) {
                        if (USession::sessionStatus() == PHP_SESSION_NONE) {
                            $session = USingleton::getInstance("USession");
                            //  $pm->update('vemail', date('Y-m-d'), 'idUSer', $utente->getIdUser(), 'FUtente');
                            $dati = serialize($utente);
                            $admin = $utente->getAdmin();
                            $session->setValue("admin", $admin);
                            $session->setValue("utente", $dati);
                            //  $session->setValue('vemail', $utente->getDataVerificaEmail());
                            if ($admin == 1) {
                                header("Location: /markethub/Admin/homeAdmin");
                            } else {
                                header("Location: /markethub/");
                            }
                        }


                    } else {
                        $view->loginError(1, 'bannato', $utente->getDataFineBan());
                    }

                } else {
                    $view->verifyPage(VUtente::getEmail(), VUtente::getPassword());
                }

            } else {
                $view->loginError(0, 'errore');
            }
        }


    /**
     * Metodo che verifica se un utente è loggato
     * @return bool
     */
    static function isLogged()
    {
        $identified = false;
        if (isset($_COOKIE['PHPSESSID'])) {
            if (USession::sessionStatus() == PHP_SESSION_NONE) {
                USingleton::getInstance("USession");
            }
        }
        if (isset($_SESSION['utente'])) {
            $identified = true;
        }

        return $identified;
    }

    /**
     * Metodo cbe distrugge tutti i cookie o sessID legati ad un utente
     * al momento del logout
     * @return void
     */
    static function logout()
    {
        $session = USingleton::getInstance("USession");
        $session->unsetSession();
        $session->destroySession();
        setcookie('PHPSESSID', ''); //elimina il cookie lato client
        header('Location: /markethub/');
    }

    /**
     * Metodo che gestisce la registrazione di un utente
     * @return void
     */
    static function registration()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $view = new VUtente();
            if (self::isLogged()) {
                $view->loginOk();
            }
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            self::verifyRegistration();
        }
    }

    /**
     * Metodo che, dopo aver verificato l'esistenza da parte di un utente,
     * registra i suoi dati sul DB
     * @return void
     */
    static function verifyRegistration()
    {
        $pm = USingleton::getInstance("FPersistentManager");
        $session = USingleton::getInstance("USession");
        $utente = unserialize($session->readValue('utente'));
        $regexEmail = preg_match("/^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,3})$/", VUtente::getEmail());
        $verifyEmail = $pm::exist("email", VUtente::getEmail(), "FUtente");
        $verifyPassword = preg_match("/^\S*(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=\S*[\W])[a-zA-Z\d]{8,}\S*$/", VUtente::getPassword());
        $view = new VUtente();
        if ($verifyEmail) {
            $view->registrationError("emailEsistente");
        } elseif (!$regexEmail) {
            $view->registrationError("emailRegex");
        } elseif (!$verifyPassword) {
            $view->registrationError("password");
        } else {
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            echo'<script>alert("Si prega di controllare la mail e di fare il login con il codice di verifica")</script>';
            $view->mailer($_POST['email'], $_POST['nome'], $verification_code);


            $utente = new EUtente(VUtente::getNome(), VUtente::getCognome(), VUtente::getEmail(),  date("Y/m/d"), null, 0, md5(VUtente::getPassword()), 0, null, null, $verification_code);
            $pm::store($utente);
            header("Location: /markethub/Utente/login");

        }

    }


    /**
     * Metodo che rimanda alla view col template riguardante il profilo
     * dell'utente e la modifica dei suoi dati
     * @param $id
     * @return void
     */

    static function profiloLoggato($id = null)
    {
        $view = new VUtente();
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
        if ($id == null) {
            $utente = unserialize($session->readValue('utente'));
        } else {
            $utente = $pm::load('FUtente', array(['idUser', '=', $id]));
        }
        
        
        if (isset($_SESSION['utente'])) $utente_del_profilo = unserialize($_SESSION['utente']);

        if (CUtente::isLogged() || $id != null) {
            $fotoUtenteVisualizzante = $pm::load('FFotoUtente', array(['idUser', '=', $utente_del_profilo->getIdUser()]));
            $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));
            $annuncio = $pm::load('FAnnuncio', array(['idVenditore', '=', $utente->getIdUser()],['acquistato','=','0']));
            $categoria = $pm::loadAll('FCategoria');
            $recensione = $pm::load('FRecensione', array(['idRecensito', '=', $utente->getIdUser()]));
            //recensione
            if ($recensione != null) {
                if (!is_array($recensione)) $recensione = array($recensione);
                if (is_array($recensione)) {
                    for ($i = 0; $i < sizeof($recensione); $i++) {
                        $autori[$i] = $pm::load('FUtente', array(['idUser', '=', $recensione[$i]->getAutore()]));
                        $foto_recensori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori[$i]->getIdUser()]));
                    }
                } else {
                    $autori = $pm::load('FUtente', array(['idUser', '=', $recensione->getAutore()]));
                    $foto_recensori = $pm::load('FFotoUtente', array(['idUser', '=', $autori->getIdUser()]));
                }

            }

            //Annuncio
            if ($annuncio != null) {
                if (is_array($annuncio)) {
                    for ($i = 0; $i < sizeof($annuncio); $i++) {
                        $foto[$i] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio[$i]->getIdAnnuncio()]));
                        if (!is_array($foto[$i])) {
                            $foto[$i] = array($foto[$i]);
                        }
                        $autori_annunci[$i] = $pm::load('FUtente', array(['idUser', '=', $annuncio[$i]->getIdVenditore()]));
                        $foto_autori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci[$i]->getIdUser()]));
                        if (!is_array($foto_autori[$i])) $foto_autori[$i] = array($foto_autori[$i]);


                    }


                } else {
                    $foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio->getIdAnnuncio()]));
                    if (!is_array($foto)) $foto = array(array($foto));
                    $autori_annunci = $pm::load('FUtente', array(['idUser', '=', $annuncio->getIdVenditore()]));
                    $foto_autori = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci->getIdUser()]));
                    if (!is_array($foto_autori)) $foto_autori = array($foto_autori);


                }
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;
                if (!is_array($annuncio) && isset($annuncio)) $annuncio = array($annuncio);
                if (!is_array($foto[0])) $foto = array($foto);
                if (!isset($utente_del_profilo)) $utente_del_profilo = null;
                

                $view->profiloLoggato($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione, $utente_del_profilo,$fotoUtenteVisualizzante);
            } else {
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;
                if (!isset($utente_del_profilo)) $utente_del_profilo = null;
                $view->profiloLoggato($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione, $utente_del_profilo,$fotoUtenteVisualizzante);
            }

        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }

    static function profiloNonLoggato($id = null)
    {
        $view = new VUtente();
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');

        $utente = $pm::load('FUtente', array(['idUser', '=', $id]));

        if (CUtente::isLogged() || $id != null) {
            $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));
            $annuncio = $pm::load('FAnnuncio', array(['idVenditore', '=', $utente->getIdUser()],['acquistato','=','0']));
            $categoria = $pm::loadAll('FCategoria');
            $recensione = $pm::load('FRecensione', array(['idRecensito', '=', $utente->getIdUser()]));
            //recensione
            if ($recensione != null) {
                if (!is_array($recensione)) $recensione = array($recensione);
                if (is_array($recensione)) {
                    for ($i = 0; $i < sizeof($recensione); $i++) {
                        $autori[$i] = $pm::load('FUtente', array(['idUser', '=', $recensione[$i]->getAutore()]));
                        $foto_recensori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori[$i]->getIdUser()]));
                    }
                } else {
                    $autori = $pm::load('FUtente', array(['idUser', '=', $recensione->getAutore()]));
                    $foto_recensori = $pm::load('FFotoUtente', array(['idUser', '=', $autori->getIdUser()]));
                }

            }

            //Annuncio
            if ($annuncio != null) {
                if (is_array($annuncio)) {
                    for ($i = 0; $i < sizeof($annuncio); $i++) {
                        $foto[$i] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio[$i]->getIdAnnuncio()]));
                        if (!is_array($foto[$i])) {
                            $foto[$i] = array($foto[$i]);
                        }
                        $autori_annunci[$i] = $pm::load('FUtente', array(['idUser', '=', $annuncio[$i]->getIdVenditore()]));
                        $foto_autori[$i] = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci[$i]->getIdUser()]));
                        if (!is_array($foto_autori[$i])) $foto_autori[$i] = array($foto_autori[$i]);


                    }


                } else {
                    $foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio->getIdAnnuncio()]));
                    if (!is_array($foto)) $foto = array(array($foto));
                    $autori_annunci = $pm::load('FUtente', array(['idUser', '=', $annuncio->getIdVenditore()]));
                    $foto_autori = $pm::load('FFotoUtente', array(['idUser', '=', $autori_annunci->getIdUser()]));
                    if (!is_array($foto_autori)) $foto_autori = array($foto_autori);


                }
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;
                if (!is_array($annuncio) && isset($annuncio)) $annuncio = array($annuncio);
                if (!is_array($foto[0])) $foto = array($foto);
        
                

                $view->profiloNonLoggato($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione);
            } else {
                if (!isset($foto)) $foto = null;
                if (!isset($foto_autori)) $foto_autori = null;
                if (!isset($autori)) $autori = null;
                if (!isset($foto_recensori)) $foto_recensori = null;

                $view->profiloNonLoggato($annuncio, $utente, $foto, $fotoUtente, $foto_autori, $id, $categoria, $autori, $foto_recensori, $recensione);
            }

        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }

    /**
     * Metodo che permette l'upload di una foto al momento della
     * creazione dell'utente o modifica del profilo
     * @return mixed
     *
     */
    static function upload($idUser)
    {
        $pm = USingleton::getInstance('FPersistentManager');
        $result = false;
        $max_size = 600000;
        $result = is_uploaded_file($_FILES['file']['tmp_name']);
        if ($result == false){return;}
        $fotoVecchia = $pm::load('FFotoUtente',array(['idUser','=',$idUser]));
        if(isset($fotoVecchia)) $pm::delete('idUser',$idUser, 'FFotoUtente');
        if (!$result) {
            //echo "Impossibile eseguire l'upload.";
            return false;
        } else {
            $size = $_FILES['file']['size'];
            if ($size > $max_size) {
                //echo "Il file è troppo grande.";
                return false;
            }
            $type = $_FILES['file']['type'];
            $nome = $_FILES['file']['name'];
            $immagine = file_get_contents($_FILES['file']['tmp_name']);
            $immagine = addslashes($immagine);
            $image = new EFotoUtente($id = 0, $nome, $size, $type, $immagine,$idUser);
            $pm::storeMedia($image, $_FILES['file']['tmp_name']);
        }
    }

        /**
         * Metodo che aggiorna il DB con i nuovi dati dell'utente
         * forniti a seguito delle modifiche
         * @return void
         */
        static function modificaP()
        {
            $pm = USingleton::getInstance("FPersistentManager");
            $session = USingleton::getInstance("USession");
            $view = new VUtente();
            if (CUtente::isLogged()) {
                $utente = unserialize($session->readValue('utente'));
                $idFoto = self::upload($utente->getIdUser());
                $nome = VUtente::getNome();
                $cognome = VUtente::getCognome();
                $email = VUtente::getEmail();
                $password = md5(VUtente::getPassword());

                if ($nome != $utente->getNome()) {
                    $pm::update('nome', $nome, 'idUser', $utente->getIdUser(), "FUtente");
                    $utente->setNome($nome);
                }

                if ($cognome != $utente->getCognome()) {
                    $pm::update('cognome', $cognome, 'idUser', $utente->getIdUser(), "FUtente");
                    $utente->setCognome($cognome);
                }

                if ($email != $utente->getEmail()) {
                    $pm::update('email', $email, 'idUser', $utente->getIdUser(), "FUtente");
                    $utente->setEmail($email);
                    $pm::update('vemail', null, 'idUser', $utente->getIdUser(), "FUtente");
                    $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                    $pm::update('codice', $verification_code, 'idUser', $utente->getIdUser(), "FUtente");
                    VUtente::mailer($email, $utente->getNome(), $verification_code);
                    CUtente::logout();

                }
                if ($password != md5($utente->getPassword())) {
                    $pm::update('password', $password, 'idUser', $utente->getIdUser(), "FUtente");
                    $utente->setPassword($password);
                }

                $session->destroyValue('utente');

                if ($idFoto != false) {
                    $session->setValue('utente', serialize($utente));

                    header("Location: /markethub/Utente/profiloLoggato");
                } else {
                    if($utente->getAdmin() == 0){
                    $session->setValue('utente', serialize($utente));

                    header("Location: /markethub/Utente/profiloLoggato");}
                    if($utente->getAdmin() == 1){
                        $session->setValue('utente', serialize($utente));
    
                        header("Location: /markethub/Admin/profiloAdmin");}
                }
            } else {
                header("Location: /markethub/");
            }
        }

        /**
         * Funzione invocata quando un utente scrive una recensione su un oggetto acquistato
         * @param $id
         * @return void
         */
        public static function scriviRecensione()
        {
            $pm = USingleton::getInstance('FPersistentManager');
            if (CUtente::isLogged()) {

                $commento = VUtente::getCommento();
                $valutazione = VUtente::getValutazione();
                $dataPubblicazione = date('Y-m-d');
                $idRecensito = VUtente::getIdUser();
                $autore = unserialize($_SESSION['utente']);
                $recensione = new ERecensione($commento, $valutazione, $dataPubblicazione, $autore->getIdUser(), null, $idRecensito);
                $pm::store($recensione);
                header('Location: /markethub/Utente/profiloLoggato?id=' . $idRecensito);
            } else {
                header('Location: /markethub/');
            }
        }


        /**
         * Funzione invocata quando un utente decide di cancellare la propria recensione
         * @param $id
         * @return void
         */
        static function cancellaRecensione($id, $profilo)
        {
            $pm = USingleton::getInstance("FPersistentManager");
            $session = USingleton::getInstance("USession");
            $utente = unserialize($session->readValue("utente"));
            if ($utente != null) {
                $recensione = $pm::load("FRecensione", array(['idRecensione', '=', $id]));
                if ($recensione != null && $recensione->getAutore() == $utente->getIdUser()) {
                    $pm::delete('idRecensione', $id, "FRecensione");
                    header("Location: /markethub/Utente/profiloLoggato/" . $profilo);

                } else {
                    header("Location: /markethub/Utente/profiloLoggato");
                }
            } else {
                header("Location: /markethub/");
            }
        }

        public static function storico(){
            $pm = USingleton::getInstance("FPersistentManager");
            $session = USingleton::getInstance("USession");
            $view = new VUtente();
            if(CUtente::isLogged()){
                $utente = unserialize($session->readValue("utente"));
                $annunci= $pm::load('FAnnuncio',array(['idCompratore','=',$utente->getIdUser()]));
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

}