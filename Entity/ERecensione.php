<?php

/**
 * La classe ERecensione contiene tutti gli attributi e i metodi relativi alle recensioni
 * Gli attributi sono:
 * commento: contenuto testuale Recensione
 * valutazione: valutazione annuncio
 * idRecensione: id univoco Recensione
 * idAnnuncio: id Annuncio recensito
 * dataPubblicazione: data pubblicazione Recensione
 * autore: autore Recensione
 * @access public
 * @author BPT
 * @package Entity
 */

class ERecensione implements JsonSerializable
{
    /**
     * commento Recensione
     * @var ||null
     */
    private  $commento;
    /**
     * valutazione annuncio
     * @var ||null
     */
    private  $valutazione;
    /**
     * id Recensione
     * @var 
     */
    private   $idRecensione;
    /**
     * id annuncio recensito
     * @var ||null
     */
    private  $idAnnuncio;
    /**
     * data pubblicazione Recensione
     * @var DateTime||null
     */
    private   $dataPubblicazione;
    /**
     * autore Recensione
     * @var ||null
     */
    private  $autore;
    /**
     * id dell'utente recensito
     * @var mixed|null
     */
    private  $idRecensito;

    //---------------------------------------------------------------------------COSTRUTTORE----------------------------------------------------

    public function __construct($commento=null, $valutazione=null,  $dataPubblicazione=null,$autore, $idRecensione=null,$idRecensito=null)
    {
        $this->commento = $commento;
        $this->valutazione = $valutazione;
        $this->dataPubblicazione = $dataPubblicazione;
        $this->autore = $autore;
        $this->idRecensione=$idRecensione;
        $this->idRecensito=$idRecensito;
    }

    //----------------------------------------------------------------METODI GET E SET-----------------------------------------------------

    /**
     * @return  commento Recensione
     */
    public function getCommento()
    {
        return $this->commento;
    }


    /**
     * @param  $commento commento Recensione
     */
    public function setCommento( $commento): void
    {
        $this->commento = $commento;
    }

    /**
     * @return |null
     */
    public function getIdRecensione()
    {
        return $this->idRecensione;
    }

    /**
     * @param |null $idRecensione
     */
    public function setIdRecensione( $idRecensione): void
    {
        $this->idRecensione = $idRecensione;
    }

    /**
     * @return DateTime
     */
    public function getDataPubblicazione()
    {
        return $this->dataPubblicazione;
    }

    /**
     * @param DateTime $dataPubblicazione
     */
    public function setDataPubblicazione($dataPubblicazione): void
    {
        $this->dataPubblicazione = $dataPubblicazione;
    }

    /**
     * @return int|null $idRecensito
     */
    public function getIdRecensito()
    {
        return $this->idRecensito;
    }

    /**
     * @param int|null $idRecensito
     */
    public function setIdRecensito( $idRecensito): void
    {
        $this->idRecensito = $idRecensito;
    }

    /**
     * @return DateTime| data pubblicazione Recensione
     */
    public function getDataPubb()
    {
        return $this->dataPubblicazione;
    }

    /**
     * @param DateTime $data data pubblicazione Recensione
     */
    public function setDataPubb(DateTime $dataPubblicazione): void
    {
        $this->dataPubblicazione = $dataPubblicazione;
    }

    /**
     * @return  id annuncio recensito
     */
    public function getIdUser()
    {
        return $this->idAnnuncio;
    }

    /**
     * @param  $idAnnuncio id annuncio recensito
     */
    public function setIdAnnuncio( $idAnnuncio): void
    {
        $this->idAnnuncio = $idAnnuncio;
    }

    /**
     * @return  valutazione annuncio
     */
    public function getValutazione(){
        return $this->valutazione;
    }

    /**
     * @param  $valutazione valutazione annuncio
     */
    public function setValutazione($valutazione): void
    {
        $this->valutazione = $valutazione;
    }

    /**
     * @return  autore Recensione
     */
    public function getAutore()
    {
        return $this->autore;
    }

    /**
     * @param  $autore autore recensione
     */
    public function setAutore($autore): void
    {
        $this->autore = $autore;
    }

    public function jsonSerialize()
    {
        return
            [
                'commento'   => $this->getCommento(),
                'valutazione' => $this->getValutazione(),
                'idRecensione'   => $this->getIdRecensione(),
                'idAnnuncio'   => $this->getIdAnnuncio(),
                'dataPubblicazione' => $this->getDataPubb(),
                'autore'   => $this->getAutore()
            ];
    }
}