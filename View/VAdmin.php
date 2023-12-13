<?php

/**
 * La classe VAdmin si occupa dell'input-output per l'Admin
 * @author BPT
 * @package View
 */
class VAdmin
{
    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * Funzione che inizializza e configura smarty
     */
    public function __construct() {
        $this->smarty = StartSmarty::configuration();
    }


    /**
     * Metodo che restituisce la data fino a quando bannare l'utente
     * @return mixed data di iscrizione utente
     */
    function getDate() {
        return $_POST['date'];
    }

    /**
     * Metodo che restituisce l'email dell'utente da bannare/riattivare
     * Inviato con metodo POST
     * @return string contenente l'email dell'utente
     */
    function getEmail() {
        $value = null;
        if (isset($_POST['email']))
            $value = $_POST['email'];
        return $value;
    }

    /**
     * Metodo che restituisce l'id della recensione da eliminare
     * Inviato con il metodo POST
     * @return string contenente l'email dell'utente
     */
    function getId() {
        $value = null;
        if (isset($_POST['valore']))
            $value = $_POST['valore'];
        return $value;
    }

    /**
     * Metodo che permette di visualizzare l'homepage dell'admin
     * @param $utente utente
     * @param $list array degli utenti
     * @param $immagine array delle immagini utente
     * @return void
     * @throws SmartyException
     */
    function homeAdmin($utente, $list, $fotoUtente) {
        $this->smarty->assign('userLogged', 'admin');
        
        $this->smarty->assign('utente', $utente);
        $this->smarty->assign('list', $list);
        $this->smarty->assign('foto_utente', $fotoUtente);

        $this->smarty->display('admin.tpl');
    }

    /**
     * Metodo che permette di visualizzare il profilo di un utente dall'admin
     * @param $utente utente
     * @param $immagine immagine utente
     * @return void
     * @throws SmartyException
     */
    public function profilo_utente($annunci,$utente,$immagini, $fotoUtente, $fotoAutori, $idutente,$categoria,$autori,$foto_recensori,$recensione,$utente_del_profilo,$fotoUtenteVisualizzante){
        $this->smarty->assign('userLogged', 'admin');
        $this->smarty->assign('utentedp',$utente_del_profilo);

        $this->smarty->assign('utente',$utente);
        $this->smarty->assign('foto_utente', $fotoUtente);
        $this->smarty->assign('foto_utente_visualizzante', $fotoUtenteVisualizzante);                              
        $this->smarty->assign('annuncio', $annunci);
        $this->smarty->assign('immagini', $immagini);
        $this->smarty->assign('fotoAutori', $fotoAutori);
        $this->smarty->assign('idutente', $idutente);
        $this->smarty->assign('categoria',$categoria);
        $this->smarty->assign('autori',$autori);
        $this->smarty->assign('foto_recensori',$foto_recensori);
        $this->smarty->assign('recensione',$recensione);
        $this->smarty->assign('udp',$utente_del_profilo);
        $this->smarty->display('utente.tpl');

    }
    public function profilo_admin($annunci,$utente,$immagini, $fotoUtente, $fotoAutori, $idutente,$categoria,$autori,$foto_recensori,$recensione,$utente_del_profilo,$fotoUtenteVisualizzante){
        $this->smarty->assign('userLogged', 'admin');

        $this->smarty->assign('utente',$utente);
        $this->smarty->assign('foto_utente', $fotoUtente);
        $this->smarty->assign('idutente', $idutente);
        $this->smarty->display('profilo_admin.tpl');

    }    
}