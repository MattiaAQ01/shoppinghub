<?php

require_once 'StartSmarty.php';  // carica il file StartSmarty.php contenentela configurazione di Smarty.
require_once 'autoload.php';   // carica il file per l'autoloading delle classi.

$fcontroller = new CFrontController(); // inizializza un oggetto di tipo CFrontController responsabile di gestire le richieste HTTP in arrivo al server.
$fcontroller->run($_SERVER['REQUEST_URI']); // metodo run di CFrontController che prende l'URI della richiesta.






