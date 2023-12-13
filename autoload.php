<?php

// my_autoloader funzione che gestisce il caricamento automatico delle classi in base alla loro convenzione di denominazione. 
// prende il nome della classe come argomento, controlla il primo carattere del nome e in base ad esso include il file corrispondente.

function my_autoloader($className) {

    $firstLetter = $className[0];
    switch ($firstLetter) {
        case 'E':
            include_once ('Entity/' . $className . '.php');
            break;

        case 'F':
            include_once ('Foundation/' . $className . '.php');
            break;

        case 'V':
            include_once ('View/' . $className . '.php');
            break;

        case 'C':
            include_once ('Control/' . $className . '.php');
            break;

        case 'I':
            include_once ($className . '.php');
            break;

        case 'U':
            include_once ('Foundation/Utility/' . $className . '.php');
            break;
    }
}

//spl_autoload_register('my_autoloader') registra questa funzione come l'autoloader personalizzato da utilizzare quando PHP cerca di caricare una classe non inclusa.

spl_autoload_register('my_autoloader');

?>  