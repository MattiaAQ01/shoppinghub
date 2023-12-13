<?php

/**
 * Classe che gestisce le richieste HTTP in arrivo al server
 * @author BPT
 * @package Control
 */
class CFrontController
{

    /**
     * Metodo che riceve la richiesta, determina quale azione deve 
     * essere eseguita e quale controller deve gestirla in base all'URI ricevuto 
     * e reindirizza l'utente ad un metodo di un controllore e successivamente 
     * ad una view che genererà un template lato client.
     * @param $path
     * @return void
     */
    public function run($path){

        $method = $_SERVER['REQUEST_METHOD'];  // contiene il metodo di richiesta HTTP utilizzato per accedere alla pagina  GET, POST, PUT, DELETE o altri metodi HTTP standard).

        if (strpos($path, '?') != false){   // ? indica l'inizio dei parametri GET.
            $url = explode('?', $path);       // L'URI viene diviso in due parti, $url[0] e $url[1] usando il carattere ? come punto di separazione.
            $resource = explode('/', $url[0]);       // La parte dell'URI relativa alla risorsa, prima del ?, viene ulteriormente suddivisa in segmenti in base al carattere /.                                      
            $params = explode('&', $url[1]);    // i parametri GET da estrarre dall'URI vengono divisi in singoli parametri separati usando il carattere &. 
        } else {
            $resource = explode('/', $path);
        }

        array_shift($resource);
        array_shift($resource);

        if ($resource[0] != 'api'){

            $controller = 'C' . $resource[0];  // ad esempio, se l'URI è /utente, costruirebbe il nome del controller come 'CUtente'.
            $dir = 'Control';   
            $elementDir = scandir($dir); // Scandisce il contenuto della directory 'Control' e restituisce un array con i nomi dei file presenti in essa.   

            if (in_array($controller . ".php", $elementDir)) { // controlla se $controller con l'aggiunta dell'estensione '.php' è presente nell'array $elementDir.
                if (isset($resource[1])) {  // controlla se esiste un secondo segmento nell'URI dopo il nome del controller che rappresenta la funzione o l'azione da richiamare.
                    $function = $resource[1];
                    if (method_exists($controller, $function)) {

                        $param =array();  

                        if (isset($url[1])){
                            for ($i = 0; $i < count($params); $i++){
                                $array = explode('=', $params[$i]);
                                $param[] = $array[1];   //$array[1] contiene la parte destra del segno '=', valore che viene aggiunto all'array $param.
                            }
                            if(count($param)==2) $controller::$function($param[0],$param[1]); //chiama la funzione del controller con i due parametri;
                            elseif (count($param)==1)$controller::$function($param[0]);
                            else $controller::$function();
                        } else {     //se non ci sono parametri nell'URI
                            for ($i = 2; $i < count($resource); $i++) {
                                $param[] = $resource[$i];
                            }
                            $num = (count($param));
                            if ($num == 0) $controller::$function();
                            else if ($num == 1) $controller::$function($param[0]);
                            else if ($num == 2) $controller::$function($param[0], $param[1]);
                        }


                    }
                    else {
                        if (CUtente::isLogged()){
                            CRicerca::blogHome();
                        }
                        CRicerca::blogHome();  // che l'utente sia loggato oppure no.
                    }
                }
            } else {
                if (CUtente::isLogged()){

                    CRicerca::blogHome();
                } else {
                    CRicerca::blogHome();
                }
            }
        } else {
            if (CUtente::isLogged()){

                CRicerca::blogHome();

            } else {
                CRicerca::blogHome();
            }
        }
    }


}