# Market Hub
Progetto per l'esame di Programmazione Web

Prerequisiti:
  1) Avere installato sul proprio dispositivo xampp (https://www.apachefriends.org/it/download.html) con versione di PHP almeno pari a 8.0
  2) Aver configurato xampp con il file config.inc.php impostando il campo password con una password vuota

Modalità di installazione:
  1) Scaricare la cartella ZIP da questa repository di GitHub
  2) Spostare la cartella markethub in xampp/htdocs(opt/lampp/htdocs per sistemi Unix-like)
  3) Aprire xampp e, dalla dashboard, avviare Apache Server e MySQL Server
  4) Una volta fatto l'accesso in phpMyAdmin importare il database tramite il file (già presente in cartella) "markethub.sql"
  5) Si apra l'applicazione web tramite browser all'indirizzo localhost/markethub
  
N.B.: Se viene visualizzato l'errore 500 su sistemi Unix-like, eseguire questi comandi:
  1) sudo chown -R nome_utente_sistema:daemon /opt/lampp/htdocs(al posto di nome_utente_sistema va messo il proprio nome sul sistema in uso)
  2) sudo chmod -R 775 /opt/lampp/htdocs
  
  Questo può accadere perché PHP utilizza un demone per interagire con il filesystem e lasciare proprietario e gruppo di base (root:root) può dare problemi 
