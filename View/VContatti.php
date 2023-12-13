<?php

/**
 * Classe che porta alla pagina con i nostri contatti
 * e allo scopo di questa web app
 * @author BPT
 * @package View
 */
class VContatti
{
    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * Costruttore che configura/inizializza smarty
     */
    public function __construct(){
        $this->smarty = StartSmarty::configuration();
    }

    /**
     * Metodo che mostra la schermata con le nostre info e obiettivi
     * @return void
     * @throws SmartyException
     */
    public function contact($fotoUtente){
        if(CAdmin::isLogged())$this->smarty->assign('userLogged', 'admin');
        elseif (CUtente::isLogged())  $this->smarty->assign('userLogged', 'loggato');
        else $this->smarty->assign('userLogged', 'nouser');

        $this->smarty->assign('foto_utente', $fotoUtente);


        $this->smarty->display('about_us.tpl');
    }
    public function contactNotLogged(){
        if(CAdmin::isLogged())$this->smarty->assign('userLogged', 'admin');
        elseif (CUtente::isLogged())  $this->smarty->assign('userLogged', 'loggato');
        else $this->smarty->assign('userLogged', 'nouser');
        
        $this->smarty->display('about_us.tpl');
    }
}