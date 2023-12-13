<?php

/**
 * La classe EFotoAnnuncio è un'estensione della classe EFoto, e associa le foto all'annuncio
 * Gli attributi sono:
 * idAnn: id annuncio Foto
 * @access public
 * @author BPT
 * @package Entity
 */

class EFotoAnnuncio extends EFoto implements JsonSerializable
{
    /**
     * @var  id Annuncio
     */
   private  $idAnnuncio;

   //-----------------------------COSTRUTTORE------------------------------------------------

    public function __construct( $idFoto,  $nomeFoto,  $size, $tipo, $foto,$idAnnuncio)
    {
        parent::__construct($idFoto, $nomeFoto, $size, $tipo, $foto);
        $this->idAnnuncio = $idAnnuncio;
    }

    //-------------------------METODI GET E SET-----------------------------------------------

    /**
     * @return  id annuncio
     */
    public function getIdAnnuncio()
    {
        return $this->idAnnuncio;
    }
    /**
     * @param  $idAnnuncio id annuncio
     */
    public function setIdAnn($idAnnuncio): void
    {
        $this->idAnnuncio = $idAnnuncio;
    }

    public function jsonSerialize()
    {
    return
        [
            'id'   => $this->getIdFoto(),
            'nFoto' => $this->getNomeFoto(),
            'size'   => $this->getSize(),
            'tipo'  =>  $this->getTipo(),
            'foto'  =>  $this->getFoto(),
        ];
    }
}