<?php

/**
 * La classe CAnnunci viene utilizzata per eseguire tutte le operazioni
 * CRUD (create/read/delete/update) sugli annunci
 * @author BPT
 * @package Control
 */
class CAnnunci
{

    /**
     * Metodo che permette la visualizzazione di/degli annuncio/annunci trovato/i
     * in base ai parametri di ricerca e, se non sono presenti annunci nella
     * categoria cercata o non sono stati trovati annunci inerenti la ricerca,
     * viene mostrata una pagina con degli annunci
     * @param $cerca
     * @param $index
     * @return void
     */
    static function esploraAnnunci($cerca=null, $index=null){


        $annunci_per_pagina = 5;
        if ($cerca == null && isset($_COOKIE['searchOn'])) {
            if ($_COOKIE['searchOn'] == 1) self::searchOff();
        }

        if ($index == null) $new_index = 1; 
        else $new_index = $index;

        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
        if (isset($_COOKIE['annuncio_ricerca'])) $data = unserialize($_COOKIE['annuncio_ricerca']);   //se settato, fai la deserializzazione del cookie, contiene il valore di "titolo" e "idAnnuncio"
        
        // entra il primo if nel primo caricamento della sezione esplora annunci  
        if (!isset($_COOKIE['annuncio_ricerca']) || !is_array($data)) {         //se il cookie non è settato oppure $data non è un array recupera il numero di righe della tabella annunci
            $num_annunci = $pm::getRows('FAnnuncio',array(['acquistato','=','0']));
            
        } elseif(in_array('no_categoria',$data) || in_array('no_ricerca',$data)){       //se la ricerca per titolo o categoria non restituisce risultati recupera il numero di righe della tabella annunci
            $num_annunci = $pm::getRows('FAnnuncio',array(['acquistato','=','0']));
        } else {
            if (isset($data['titolo']) || isset($data['idAnnuncio'])){          //se la condizione è verificata imposta il numero degli annunci a 1
                $num_annunci = 1;
            } elseif (is_array($data[0])){                                      //oppure imposta il numero degli annunci in base alla size di $data    
                $num_annunci = sizeof($data);
            }
        }

        $utente = unserialize($session->readValue('utente'));
        if(!isset($utente)) $utente=null;
        if($utente!=null){
            $fotoUtenteVisualizzante = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));
        }
        else {$fotoUtenteVisualizzante=null;}
        $immagini = array();
        $categorie = $pm::load('FCategoria');
        $annunci_pag = array();
        if ($num_annunci % $annunci_per_pagina != 0){                                   //calcolo delle pagine necessarie per il display di tutti gli annunci
            $page_number = floor($num_annunci / $annunci_per_pagina + 1);
        } else {
            $page_number = $num_annunci / $annunci_per_pagina;
        }
        if (!isset($_COOKIE['annuncio_ricerca']) || (in_array('no_categoria',$data) || in_array('no_ricerca',$data))) {           //caso in cui la ricerca non produce risultati
            if ($new_index * $annunci_per_pagina <= $num_annunci) {             //calcolo se serve un'ulteriore pagina per gli annunci
                $annunci = $pm::load('FAnnuncio',array(['acquistato','=','0']));
                if ($annunci_per_pagina * $new_index > count($annunci)) $annPag = (count($annunci) % $annunci_per_pagina) + $annunci_per_pagina*($new_index-1);
                else $annPag = $annunci_per_pagina * $new_index;
                for ($i = ($new_index - 1) * $annunci_per_pagina; $i < $annPag; $i++) {                                 //calcolo quali annunci mettere in questa pagina
                    $annunci_pag[] = $annunci[$i];                                                                      //array contenente gli annunci nella pagina corrente


                }
            } else {
                $limite = $num_annunci % $annunci_per_pagina + ($new_index-1)  * $annunci_per_pagina;           
                $annunci = $pm::load('FAnnuncio',array(['acquistato','=','0']));
                if(!is_array($annunci)) $annunci=array($annunci);
                for ($i = ($new_index - 1) * $annunci_per_pagina; $i < $limite; $i++) {
                    $annunci_pag[] = $annunci[$i];


                }
            }

            if (is_array($annunci_pag)) {                                                   //caricamento foto degli annunci della pagina
                for ($i = 0; $i < sizeof($annunci_pag); $i++) {                                                 
                    $immagini[$i] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_pag[$i]->getIdAnnuncio()]));
                    if(!is_array($immagini[$i])) $immagini[$i]=array($immagini[$i]);

                }
            } else {                                                                        //caricamento foto del singolo annuncio della pagina            
                $immagini = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_pag[0]->getIdAnnuncio()]));
                if(!is_array($immagini)) $immagini=array($immagini);

            }
        }
        //con categoria o ricerca
        else {
            if ($new_index * 5 <= $num_annunci){                                //Se l'indice moltiplicato per 5 è minore o uguale al numero totale di annunci, viene eseguito un ciclo per caricare gli annunci sulla pagina corrente.
                for ($i = ($new_index - 1)*$annunci_per_pagina; $i <$annunci_per_pagina * $new_index ; $i++) {
                    $annunci_pag = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $data[$i]['idAnnuncio']]));
                }
            } else {                                                    //Se è impostato il titolo in $data carica un singolo annuncio,se $data è un array ciclo per caricare gli annunci basandosi sull'ID dell'annuncio
                if (isset($data['titolo'])){
                    $annuncio_pag = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $data['idAnnuncio']]));
                    $annunci_pag[] =$annuncio_pag;
                } else if (is_array($data[0])){
                    $limite= (count($data) % $annunci_per_pagina) + $annunci_per_pagina * ($new_index-1);
                    for ($i =($new_index - 1)*$annunci_per_pagina; $i <$limite ; $i++) {
                        $annunci_pag[] = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $data[$i]['idAnnuncio']]));
                    }
                }
            }
            if (is_array($annunci_pag)) {                               //caricamento foto degli annunci della pagina 
                for ($i = 0; $i < sizeof($annunci_pag); $i++) {
                    $immagini[$i] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_pag[$i]->getIdAnnuncio()]));
                    if(!is_array($immagini[$i])) $immagini[$i]=array($immagini[$i]);
                }
            } else {                                                    //caricamento foto del singolo annuncio della pagina
                $immagini = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_pag->getIdAnnuncio()]));
                if(!is_array($immagini)) $immagini=array($immagini);

            }
        }
        $view = new VAnnunci();

        $cerca = 'cerca';                                   //showAllErr viene richiamato se la ricerca non da risultati
        if(isset($data)){
            if(in_array('no_categoria',$data) || in_array('no_ricerca',$data)) $view->showAllErr($annunci_pag, $page_number, $new_index, $num_annunci, $immagini, $cerca, $data[0], $data[1], $categorie);
            else $view->showAll($annunci_pag, $page_number, $new_index, $num_annunci, $immagini, $cerca, $categorie,$fotoUtenteVisualizzante);
        }
        else $view->showAll($annunci_pag, $page_number, $new_index, $num_annunci, $immagini, $cerca, $categorie,$fotoUtenteVisualizzante);
    }


    /**
     * Metodo che svuota il cookie che viene attivato nel momento in cui viene fatta una ricerca
     * @return void
     */
    static function searchOff() {
        setcookie('searchOn', 0);
        setcookie('annuncio_ricerca', '');
        header('Location: /markethub/Annunci/esploraAnnunci');
    }

    /**
     * Metodo che permette la visualizzazione della pagina
     * che contiene più informazioni riguardanti l'annuncio
     * in questione
     * @param int $id
     * @return void
     */
    static function infoAnnuncio($id) {

        $view = new VAnnunci();
        $pm = USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        $utente = unserialize($session->readValue('utente'));     //costruisce l'oggetto utente prendendolo dalla sessione
        if(!isset($utente)) $utente=null;
        if($utente!=null){
            $fotoUtenteVisualizzante = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));
        }
        else {$fotoUtenteVisualizzante=null;}
        $session->setValue('idAnnuncio', $id);  //setta in sessione l'id dell'annuncio che stiamo visualizzando
        $annuncio = $pm::load('FAnnuncio', array(['idAnnuncio','=',$id]));  //carica, tramite id, l'annuncio dal db
        $autore = $pm::load('FUtente', array(['idUser','=',$annuncio->getIdVenditore()]));  //carica, tramite id, il venditore
        $foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio','=',$id]));  //carica,tramite id, la foto dell'annuncio
        if(!is_array($foto)) $foto= array($foto);
        $fotoUtente = $pm::load('FFotoUtente', array(['idUser','=',$autore->getIdUser()])); //carica,tramite id, la foto del venditore
        if(!isset($fotoUtente)) $fotoUtente = null;
        $categoria = $pm::load('FCategoria',array(['idCate','=',$annuncio->getCategoria()])); //carica,tramite id, la categoria dell'annuncio
        $tutteCategorie = $pm::loadAll('FCategoria');
        $view->showInfo($annuncio, $autore, $utente, $foto, $fotoUtente,$categoria,$tutteCategorie, $fotoUtenteVisualizzante);
    }


    /**
     * Metodo che salva un COOKIE con idAnnuncio e Titolo degli annunci risultanti da una ricerca per categoria o per parole chiavi 
     * @param $categoria
     * @return void
     */
    static function cerca($categoria = null){
        $pm = USingleton::getInstance('FPersistentManager');
        if(isset($categoria)) $categoria=$pm::load('FCategoria',array(['idCate','=',$categoria])); //controllo per la ricerca filtrata per categoria
        if($categoria != null){
            $annunci = $pm::load('FAnnuncio', array(['categoria', '=', $categoria->getIdCate()])); //carica da db gli annunci di una determinata categoria 
            if($annunci != null){
                if (is_array($annunci)){
                    for($i = 0; $i < sizeof($annunci); $i++){
                        $array[$i]['titolo'] = $annunci[$i]->getTitolo();                       //mette in un array i valori di Titolo per gli annunci che corrispondono ai criteri di ricerca
                        $array[$i]['idAnnuncio'] = $annunci[$i]->getIdAnnuncio();               //mette in un array i valori di IdAnnuncio per gli annunci che corrispondono ai criteri di ricerca
                    }
                }
                else {
                    $array['titolo'] = $annunci->getTitolo();                                   //mette in una variabile il valore di Titolo per l'annuncio che corrisponde ai criteri di ricerca
                    $array['idAnnuncio'] = $annunci->getIdAnnuncio();                           //mette in una variabile il valore di IdAnnuncio per l'annuncio che corrisponde ai criteri di ricerca
                }
                $data = serialize($array);                                                      //serializzazione dei dati raccolti
                setcookie('annuncio_ricerca', $data);                                           //settaggio valori nel cookie
                setcookie('searchOn', 1);                                                       //settaggio cookie di ricerca
            }
            else{
                $data = serialize(['no_categoria', $categoria->getCategoria()]);                //caso in cui la categoria scelta non contenga annunci
                setcookie('annuncio_ricerca', $data);
                setcookie('searchOn', 1);
            }
            header('Location: /markethub/Annunci/esploraAnnunci/cerca');
        }
        else {
            $j = 0;
            $array = null;
            $parametro = VAnnunci::getTestoRicerca();                                   //prende la ricerca dell'utente
            $parametro = strtoupper($parametro);                                        //lo porta in maiuscolo
            $allPostTitleAndId = $pm::loadDefCol('FAnnuncio', array('titolo', 'idAnnuncio'));       //prende tutti i titoli e gli Id di tutti gli annunci
            if (isset($allPostTitleAndId[0]) && is_array($allPostTitleAndId[0])) {
                for ($i = 0; $i < sizeof($allPostTitleAndId); $i++) {                               //ciclo dentro $allPostTitleAndId
                    if (is_int(strpos($allPostTitleAndId[$i]['titolo'], $parametro))){              //vede se un titolo risponde alla ricerca fatta dall'utente vedendo se $parametro è presente in uno o più titoli
                        $array[$j]['titolo'] = $allPostTitleAndId[$i]['titolo'];                    //inserisce nell'array dei risultati titolo e idAnnuncio
                        $array[$j]['idAnnuncio'] = $allPostTitleAndId[$i]['idAnnuncio'];
                        $j += 1;
                    }
                }
            } elseif (isset($allPostTitleAndId['titolo'])){
                if (is_int(strpos($allPostTitleAndId['titolo'], $parametro))){
                    $array = $allPostTitleAndId;
                }
            }
            $data = serialize($array);                                              //serializzazione dei dati raccolti
            if($array == null){
                $data = serialize(['no_ricerca', $parametro]);                      //caso in cui la ricerca per titolo non restituisca risultati
            }
            setcookie('annuncio_ricerca', $data);                                      //settaggio valori nel cookie
            setcookie('searchOn', 1);                                                  //settaggio cookie ricerca
            header('Location: /markethub/Annunci/esploraAnnunci/cerca');
        }
    }

    /**
     * Metodo che rimanda alla view che permette la modifica di un annuncio
     * @param $idAnnuncio
     * @return void
     */
    static function modificaAnnuncio($idAnnuncio) {
        $pm = USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        $utente = unserialize($session->readValue('utente'));
        $annuncio = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));                            //carica l'annuncio da modificare
        if (CUtente::isLogged() && $utente->getIdUser() == $annuncio->getIdVenditore()) {                       //controllo per vedere se effettivamente sei l'autore dell'annuncio
            $foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annuncio->getIdAnnuncio()]));         //caricamento foto annuncio
            $categoria = $pm::load('FCategoria', array(['idCate', '=', $annuncio->getCategoria()]));            //caricamento categoria annuncio
            $view = new VAnnunci();
            $view->modificaAnnuncio($annuncio, $foto,$categoria);
        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }

    /**
     * Metodo che invia al DB i dati aggiornati in seguito alla
     * modifica di un annuncio
     * @param $idAnnuncio
     * @param $idFotoVecchia
     * @return void
     */
    static function confermaModifiche($idAnnuncio) {
        $pm=USingleton::getInstance('FPersistentManager');
        if (CUtente::isLogged()) {
            $annuncio = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));
            //$Foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));
            
            $titolo = VAnnunci::getTitoloAnnuncio();                                //metodi per la raccolta dei nuovi parametri dell'annuncio 
            $descrizione = VAnnunci::getDescrizioneAnnuncio();
            $categoria = VAnnunci::getCategoriaAnnuncio();
            $prezzo = VAnnunci::getPrezzoAnnuncio();
            $idFotoNuova = self::modificaFoto($idAnnuncio);


            $pm::update('titolo', $titolo, 'idAnnuncio', $idAnnuncio, 'FAnnuncio');                           //metodi per l'aggiornamento dei parametri dell'annuncio nel db
            $pm::update('descrizione', $descrizione, 'idAnnuncio', $idAnnuncio, 'FAnnuncio');
            $pm::update('categoria', $categoria, 'idAnnuncio', $idAnnuncio, 'FAnnuncio');
            $pm::update('prezzo', $prezzo, 'idAnnuncio', $idAnnuncio, 'FAnnuncio');
            header('Location: /markethub/Annunci/infoAnnuncio?idAnnuncio=' . $idAnnuncio);                    //header che riporta alla pagina di visualizzazione dell'annuncio modificato
        } else {
            header('Location: /markethub/Utente/login');
        }
    }


    /**
     * Metodo che permette la pubblicazione di un annuncio
     * @return void
     */
    static function pubblicaAnnuncio() {
        $pm=USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        if (CUtente::isLogged()) {

            $utente = unserialize($session->readValue('utente'));
            $annuncio = new EAnnuncio(VAnnunci::getTitoloAnnuncio(),  VAnnunci::getDescrizioneAnnuncio(),  VAnnunci::getPrezzoAnnuncio(), date('Y/m/d'),$utente->getIdUser(),  null,  VAnnunci::getCategoriaAnnuncio(),  0,null, 0);   //metodi per la raccolta dei parametri dell'annuncio
            $pm::store($annuncio);          //metodo per l'inserimento del nuovo annuncio nel db (id autoincrementale, settato nell'oggetto dalla store stessa)
            self::upload($annuncio->getIdAnnuncio());           //richiamo al metodo per il caricamento della foto dell'annuncio appena creato
            header('Location: /markethub/Annunci/infoAnnuncio?idAnnuncio=' . $annuncio->getIdAnnuncio());       //header che riporta alla pagina dell'annuncio appena creato
        }
        else {
            header('Location: /markethub/Utente/login');
        }
    }


    /**
     * Metodo che permette il caricamento di una foto durante
     * la creazione o modifica di un annuncio
     *
     */
    static function upload($idAnnuncio) {
        $pm = USingleton::getInstance('FPersistentManager');
        for($i=0;$i<count($_FILES['file']['name']);$i++) {          //conto le foto caricate dall'utente
            $result = false;
            $maxSize = 600000;
            $result = is_uploaded_file($_FILES['file']['tmp_name'][$i]);
            if (!$result) {
                return false;
            } else {
                $size = $_FILES['file']['size'][$i];
                if ($size > $maxSize) {
                    return false;
                }
                $type = $_FILES['file']['type'][$i];
                $nome = $_FILES['file']['name'][$i];
                $foto = file_get_contents($_FILES['file']['tmp_name'][$i]);
                $foto = addslashes($foto);
                $fotoCaricata = new EFotoAnnuncio($idFoto = null, $nome, $size, $type, $foto, $idAnnuncio);
                $pm::storeMedia($fotoCaricata, $_FILES['file']['tmp_name'][$i]);
            }
        }
    }

    static function modificaFoto($idAnnuncio)
    {
        $pm = USingleton::getInstance('FPersistentManager');
        for($i=0;$i<count($_FILES['file']['name']);$i++) {
            $result = false;
            $max_size = 600000;
            $result = is_uploaded_file($_FILES['file']['tmp_name'][$i]);
            if ($result == false){return;}
            $fotoVecchia = $pm::load('FFotoAnnuncio',array(['idAnnuncio','=',$idAnnuncio]));
            if(isset($fotoVecchia)) $pm::delete('idAnnuncio',$idAnnuncio, 'FFotoAnnuncio');
            if (!$result) {
                //echo "Impossibile eseguire l'upload.";
                return false;
            } else {
                $size = $_FILES['file']['size'][$i];
                if ($size > $max_size) {
                    //echo "Il file è troppo grande.";
                    return false;
                }
                $type = $_FILES['file']['type'][$i];
                $nome = $_FILES['file']['name'][$i];
                $foto = file_get_contents($_FILES['file']['tmp_name'][$i]);
                $foto = addslashes($foto);
                $image = new EFotoAnnuncio($idFoto = null, $nome, $size, $type, $foto,$idAnnuncio);
                $pm::storeMedia($image, $_FILES['file']['tmp_name'][$i]);
            
               }
        }
    }


    /**
     * Metodo che permette la cancellazione di un annuncio
     * @param $idAnnuncio
     * @param $idFoto
     * @return void
     */
    static function cancellaAnnuncio($idAnnuncio) {
        $pm = USingleton::getInstance("FPersistentManager");
        $session = USingleton::getInstance("USession");
        $utente = unserialize($session->readValue("utente"));
        $foto=$pm::load('FFotoAnnuncio',array(['idAnnuncio','=',$idAnnuncio]));      //caricamento foto annuncio
        if (CUtente::isLogged()) {
            $annuncio = $pm::load("FAnnuncio", array(['idAnnuncio', '=', $idAnnuncio]));        //caricamento annuncio
            if ($annuncio->getIdVenditore() == $utente->getIdUser()){
                $pm::delete('idAnnuncio', $idAnnuncio, "FAnnuncio");                 //cancellazione annuncio       
                $pm::delete('idAnnuncio', $idAnnuncio, "FRecensione");               //cancellazione recensioni legate all'annuncio
                if(is_array($foto)) {
                    for ($i = 0; $i < sizeof($foto); $i++) {
                        $pm::delete('idAnnuncio', $foto[$i]->getIdAnnuncio(), "FFotoAnnuncio"); //cancellazione foto legate all'annuncio
                    }
                }
                else  $pm::delete('idAnnuncio', $foto->getIdAnnuncio(), "FFotoAnnuncio");



                header("Location: /markethub/Utente/profiloLoggato");                //header che riporta al profilo utente 
            } else {
                header("Location: /markethub/Utente/profiloLoggato");
            }
        } else {
            header("Location: /markethub/Utente/login");
        }
    }

    /**
     * Metodo che rimanda alla schermata di acquisto di un annuncio
     * @param $idAnnuncio
     * @return void
     */
    static function schermataAcquisto($idAnnuncio) {
        $pm = USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        $utente = unserialize($session->readValue('utente'));
        if (CUtente::isLogged()) {
            $annuncio = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));        //caricamento annuncio 
            if ($utente->getIdUser() != $annuncio->getIdVenditore()) {                          //controllo che l'acquirente sia diverso dal venditore
                $view = new VAnnunci();
                $view->schermataAcquisto($utente, $annuncio);                                   //vista per l'acquisto
            } else {
                header('Location: /markethub/Annunci/infoAnnuncio?idAnnuncio=' . $idAnnuncio);  //se venditore e acquirente sono la stessa persona torna alle informazioni dell'annuncio 
            }
        } else {
            header("Location: /markethub/Utente/login");
        }
    }

    static function acquistoCompletato($idAnnuncio, $idCompratore) {
        $pm = USingleton::getInstance('FPersistentManager');
        $session = USingleton::getInstance('USession');
        $utente = unserialize($session->readValue('utente'));
        if (CUtente::isLogged()) {
            $annuncio = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));            //caricamento annuncio
            $foto = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $idAnnuncio]));            //caricamento foto annuncio
            if (!is_array($foto)) $foto = array($foto);
            $foto = $foto[0];
            if ($utente->getIdUser() != $annuncio->getIdVenditore()) {
                $pm::update('acquistato', 1, 'idAnnuncio', $idAnnuncio, 'FAnnuncio');               //setta il valore di acquistato a 1 in quanto l'oggetto è stato comprato
                $pm::update('idCompratore', $idCompratore,'idAnnuncio', $idAnnuncio,'FAnnuncio');   //setta l'id corrispondente dell'acquirente
                $view = new VAnnunci();
                $view->acquistoCompletato($utente->getNome(), $annuncio->getTitolo(), $foto);       //vista post acquisto
            } else {
                header('Location to: /markethub/Annunci/infoAnnuncio?idAnnuncio=' . $idAnnuncio);
            }
        } else {
            header("Location: /markethub/Utente/login");
        }
    }
}
