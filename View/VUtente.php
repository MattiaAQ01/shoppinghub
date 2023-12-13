<?php

require_once 'StartSmarty.php';
require_once 'PhpMailerStart.php';

class VUtente
{
    private $smarty;
    private $phpmailer;

    public function __construct() {
        $this->smarty = StartSmarty::configuration();
        $this->phpmailer=PhpMailerStart::configuration();
    }

    static function getEmail(){
        return $_POST['email'];
    }
    static function getPassword(){
        return $_POST['password'];
    }
    static function getNome(){
        return $_POST['nome'];
    }
    static function getCognome(){
        return $_POST['cognome'];
    }





    public function showFormLogin(){
        $this->smarty->display('./smarty/libs/templates/login.tpl');
    }

    public function verifyPage($email,$password){
        $this->smarty->assign('email',$email);
        $this->smarty->assign('password',$password);
        $this->smarty->display('./smarty/libs/templates/verify.tpl');
    }

    public function loginOk(){
        $this->smarty->display('./smarty/libs/templates/index.tpl');
    }

    public function loginError($ban=0, $error='', $fine_ban=''){
        $this->smarty->assign('ban', $ban);
        $this->smarty->assign('fine_ban', $fine_ban);
        $this->smarty->assign('error', $error);

        $this->smarty->display('./smarty/libs/templates/login.tpl');
    }

    public function registrationError($error){
        switch ($error){
            case 'emailEsistente':
                $this->smarty->assign('emailExist', 'errorExist');
                break;
            case 'emailRegex':
                $this->smarty->assign('emailRegex', "errorRegex");
                print('ciao');
                break;
            case 'password':
                $this->smarty->assign('password', "errorPassword");
                break;
        }
        $this->smarty->display('./smarty/libs/templates/login.tpl');
    }

    static function mailer($email, $nome, $verification_code){
          $mail = new PHPMailer\PHPMailer\PHPMailer();

                //Enable verbose debug output
                $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;

                //Send using SMTP
                $mail->isSMTP();

                //Set the SMTP server to send through
                $mail->Host = 'smtp.gmail.com';

                //Enable SMTP authentication
                $mail->SMTPAuth = true;

                //SMTP username
                $mail->Username = 'matteopaolino9@gmail.com';

                //SMTP password
                $mail->Password = 'afjvazuaqzukcyrx';

                //Enable TLS encryption;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

                //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('matteopaolino9@gmail.com', 'markethub');

                //Add a recipient
                $mail->addAddress($email, $nome);
                //Set email format to HTML
                $mail->isHTML(true);


                $mail->Subject = 'Email verification';
                $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';

                $mail->send();
}

    public function profiloLoggato($annunci,$utente,$immagini, $fotoUtente, $fotoAutori, $idutente,$categoria,$autori,$foto_recensori,$recensione,$utente_del_profilo,$fotoUtenteVisualizzante){
        if(CAdmin::isLogged())$this->smarty->assign('userLogged', 'admin');
        else if (CUtente::isLogged()) $this->smarty->assign('userLogged', 'logged');
        

        $this->smarty->assign('utente',$utente);
        $this->smarty->assign('annuncio', $annunci);
        $this->smarty->assign('immagini', $immagini);
        $this->smarty->assign('foto_utente', $fotoUtente);
        $this->smarty->assign('foto_utente_visualizzante', $fotoUtenteVisualizzante);
        $this->smarty->assign('fotoAutori', $fotoAutori);
        //$this->smarty->assign('facebook', $facebook);
        //$this->smarty->assign('instagram', $instagram);
        $this->smarty->assign('idutente', $idutente);
        $this->smarty->assign('categoria',$categoria);
        $this->smarty->assign('autori',$autori);
        $this->smarty->assign('foto_recensori',$foto_recensori);
        $this->smarty->assign('recensione',$recensione);
        $this->smarty->assign('udp',$utente_del_profilo);
        $this->smarty->display('profilo_privato.tpl');

    }

    public function profiloNonLoggato($annunci,$utente,$immagini, $fotoUtente, $fotoAutori, $idutente,$categoria,$autori,$foto_recensori,$recensione){
    
        $this->smarty->assign('userLogged', 'nouser');

        $this->smarty->assign('utente',$utente);
        $this->smarty->assign('annuncio', $annunci);
        $this->smarty->assign('immagini', $immagini);
        $this->smarty->assign('foto_utente', $fotoUtente);
        $this->smarty->assign('fotoAutori', $fotoAutori);
        //$this->smarty->assign('facebook', $facebook);
        //$this->smarty->assign('instagram', $instagram);
        $this->smarty->assign('idutente', $idutente);
        $this->smarty->assign('categoria',$categoria);
        $this->smarty->assign('autori',$autori);
        $this->smarty->assign('foto_recensori',$foto_recensori);
        $this->smarty->assign('recensione',$recensione);
        $this->smarty->display('profilo_privato.tpl');

    }


    public function modificaProfilo($utente){
        if(CAdmin::isLogged())$this->smarty->assign('userLogged', 'admin');
        else if (CUtente::isLogged()) $this->smarty->assign('userLogged', 'logged');
        else $this->smarty->assign('userLogged', 'nouser');
        $this->smarty->assign('utente', $utente);
        $this->smarty->assign('nome', $utente->getNome());
        $this->smarty->assign('cognome', $utente->getCognome());
        $this->smarty->assign('password', $utente->getPassword());
        $this->smarty->assign('email', $utente->getEmail());

        //  $this->smarty->assign('immagine_utente', $foto);


        $this->smarty->display('profilo_privato.tpl');
    }

    //Recensione

    /**
     * Metodo che restituisce il commento della recensione che si vuole scrivere
     * Inviato con metodd POST
     * @return string
     */
    static function getCommento()
    {
        return strtoupper($_POST['commento']);
    }
    static function getIdUser()
    {
        return $_POST['idUser'];
    }
    /**
     * Metodo che restituisce la valutazione della recensione che si vuole valutare
     * Inviato con metodo POST
     * @return string
     */
    static function getValutazione()
    {
        return strtoupper($_POST['rate']);
    }

    public function paginaRecensione($autori,$immagine,$recensione,$utente){

        $this->smarty->assign('autori',$autori);
        $this->smarty->assign('immagine',$immagine);
        $this->smarty->assign('recensione',$recensione);
        $this->smarty->assign('utente',$utente);


        $this->smarty->display('./smarty/libs/templates/recensione.tpl');
    }
    /**
     * Metodo per recuperare i filtri inseriti dall'amministratore
     * @return array con i filtri
     */
    public function recuperaFiltri(){
        $filtri = array();
        if(isset($_POST['last'])){
            $filtri['last'] = $_POST['last'];
        } else {
            $filtri['last'] = null;
        }

        if(isset($_POST['parola'])){
            $filtri['parola'] = $_POST['parola'];
        } else{
            $filtri['parola'] = null;
        }
        return $filtri;
    }

    /**
     * Metodo che recupera l'array di idRecensione da bannare inviati con la form
     * @return array|mixed array di id inseriti
     */
    public function recuperaRecensioni(){
        $arrayid=array();
        if(isset($_POST['recensione'])){
            $arrayid = $_POST['recensione']; //recupero array di id recensioni inviati con la form
        }
        return $arrayid;
    }

    /**
     * Funzione per mostrare le recensioni recuperati secondo i filtri
     * @param $recensione recensione da mostrare
     */
    public function mostraRecensione($rec, $arrrecensioni){
        //comunico a smarty di mostrare le recensioni

        $this->smarty->assign('com',$rec);
        $this->smarty->assign('commenti',$arrrecensioni);
        $this->smarty->display('BannaRecensione.tpl');

    }
    public function storico($annuncio,$utente,$immagini){
        if(CAdmin::isLogged())$this->smarty->assign('userLogged', 'admin');
       else if (CUtente::isLogged()) $this->smarty->assign('userLogged', 'logged');
       else $this->smarty->assign('userLogged', 'nouser');

        $this->smarty->assign('annuncio',$annuncio);
        $this->smarty->assign('utente',$utente);
        $this->smarty->assign('immagini',$immagini);
        $this->smarty->display('storico.tpl');
    }

}
