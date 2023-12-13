<?php

/**
 * La classe CContatti viene utilizzata per legare la home
 * ai nostri contatti e inf
 * @author BPT
 * @package Control
 */
class CContatti
{

    /**
     * Metodo che porta alla view che visualizza la schermata con i nostri contatti
     * @return void
     * @throws SmartyException
     */
    static function chiSiamo($id = null){
        
        $view = new VUtente();
        $session = USingleton::getInstance('USession');
        $pm = USingleton::getInstance('FPersistentManager');
       
        $utente = unserialize($session->readValue('utente'));
       
        

        if (CUtente::isLogged() || $id != null) {
            $fotoUtente = $pm::load('FFotoUtente', array(['idUser', '=', $utente->getIdUser()]));
            $view = new VContatti();
            $view->contact($fotoUtente);
        }
        else {
            $view = new VContatti();
            $view->contactNotLogged();
        }
    }
}