<?php

/**
 * La classe EFotoUtente è un estensione della classe EFoto, e associa le foto agli utenti
 * Gli attributi sono:
 * idUser: id utente Foto
 */

class EFotoUtente extends EFoto implements JsonSerializable
{
    /**
     * @var  id utente
     */
    private  $idUser;

    //--------------------------------COSTRUTTORE----------------------------------------------

    public function __construct( $idFoto,  $nomeFoto,  $size, $tipo, $foto,$idUser)
    {
        parent::__construct($idFoto, $nomeFoto, $size, $tipo, $foto);
         $this->idUser = $idUser;
    }

    //-------------------------------METODI GET E SET-------------------------------------------

    /**
     * @return  id utente
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param  $idUser id utente
     */
    public function setIdUser($idUser): void
    {
        $this->idUser = $idUser;
    }

    public function jsonSerialize ()
    {
        return
            [
                'id'   => $this->getIdFoto(),
                'Foto' => $this->getNomeFoto(),
                'size'   => $this->getSize(),
                'tipo'  =>  $this->getTipo(),
                'data'  =>  $this->getFoto(),
            ];
    }



}