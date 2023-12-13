<?php

/**
 * La classe CRicerca si occupa del caricamento degli annunci nella homepage
 * @author BPT
 * @package Control
 */
class CRicerca

{
    /**
     * Metodo utilizzato per il caricamento degli annunci nella home 
     * @return void
     */
    public static function blogHome($id = null){
        $vSearch = new VRicerca();
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
        if ($id == null) {
            $utente = unserialize($session->readValue('utente'));
        } else {
            $utente = $pm::load('FUtente', array(['idUser', '=', $id]));
        }
        

        if (CUtente::isLogged() || $id != null) {
        $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()])); // se l'utente é loggato, recupera la foto utente 
   
        $annunci_da_mostrare =$pm::getRows('FAnnuncio',array(['acquistato','=','0'])); // recupera il numero di annunci non acquistati
        if ($annunci_da_mostrare == 0 && $fotoUtente != null) {$vSearch->showAlertAnnunciVuotiLogged($fotoUtente); return;} // se non ci sono annunci non ancora acquistati e l'utente ha una foto mostra l'alert
        if ($annunci_da_mostrare == 0 && $fotoUtente == null) {$vSearch->showAlertAnnunciVuotiNoFoto(); return;} // se non ci sono annunci non ancora acquistati e l'utente non ha una foto mostra l'alert
        
        $numAnnunci = $annunci_da_mostrare; //altrimenti...
        if($annunci_da_mostrare>6) $numAnnunci=6; // vincolo per avere non più di 6 annunci nella home
        
if($numAnnunci!=0) { // non sarà mai zero perché la casistica é studiata sopra
    for ($i = 0; $i < $numAnnunci; $i++) {
        
        $annunci_da_mostrare = $pm::load('FAnnuncio',array(['acquistato','=','0'])); // qui carica tutti gli annunci non ancora acquistati
        if ($numAnnunci == 1){ $annuncio_home = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $annunci_da_mostrare->getIdAnnuncio()]));
        $annunci_home[] = $annuncio_home;}
        else $annunci_home[] = $pm::load('FAnnuncio',array(['idAnnuncio', '=', $annunci_da_mostrare[$i]->getIdAnnuncio()]));
        $annunci_foto[] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_home[$i]->getIdAnnuncio()]));
        if (!is_array($annunci_foto[$i])) $annunci_foto[$i] = array($annunci_foto[$i]);


    }
    $vSearch->showHome($annunci_home, $annunci_foto, $fotoUtente);
} else     $vSearch->showHome(null, null, null);  // non entra mai perché si ferma prima

    }

    else {
   
        $annunci_da_mostrare =$pm::getRows('FAnnuncio',array(['acquistato','=','0']));
        if ($annunci_da_mostrare == 0) {$vSearch->showAlertAnnunciVuotiNoFoto(); return;}
        
        $numAnnunci = $annunci_da_mostrare;
        if($annunci_da_mostrare>6) $numAnnunci=6;
        
if($numAnnunci!=0) {
    for ($i = 0; $i < $numAnnunci; $i++) {
        
        $annunci_da_mostrare = $pm::load('FAnnuncio',array(['acquistato','=','0'])); 
        if ($numAnnunci == 1){ $annuncio_home = $pm::load('FAnnuncio', array(['idAnnuncio', '=', $annunci_da_mostrare->getIdAnnuncio()]));
        $annunci_home[] = $annuncio_home;}
        else $annunci_home[] = $pm::load('FAnnuncio',array(['idAnnuncio', '=', $annunci_da_mostrare[$i]->getIdAnnuncio()]));
        $annunci_foto[] = $pm::load('FFotoAnnuncio', array(['idAnnuncio', '=', $annunci_home[$i]->getIdAnnuncio()]));
        if (!is_array($annunci_foto[$i])) $annunci_foto[$i] = array($annunci_foto[$i]);

    }
    $vSearch->showHomeNotLogged($annunci_home, $annunci_foto);
} else     $vSearch->showHomeNotLogged(null, null);

    }

}
}