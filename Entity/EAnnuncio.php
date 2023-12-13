<?php

class EAnnuncio implements JsonSerializable
{
    private  $titolo;
    private  $descrizione;
    private  $prezzo;
    private  $idFoto;
    private  $data;
    private $idAnnuncio;
    private  $idVenditore;
    private  $idCompratore;
    private  $categoria;
    private  $ban;
    private $acquistato;

    /**
     * @param  $titolo
     * @param  $descrizione
     * @param float $prezzo
     * @param  $idFoto
     * @param  $data
     * @param  $idAnnuncio
     * @param  $idVenditore
     * @param  $idCompratore
     * @param  $categoria
     * @param $acquistato
     */
    public function __construct( $titolo,  $descrizione,  $prezzo,  $data,   $idVenditore,  $idCompratore,  $categoria,  $ban, $idAnnuncio=null, $acquistato)
    {
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->prezzo = $prezzo;
        $this->data = $data;
        $this->idVenditore = $idVenditore;
        $this->idCompratore = $idCompratore;
        $this->categoria = $categoria;
        $this->ban = $ban;
        $this->idAnnuncio = $idAnnuncio;
        $this->acquistato = $acquistato;
    }



    /**
     * @return 
     */
    public function getIdVenditore()
    {
        return $this->idVenditore;
    }

    /**
     * @param  $idVenditore
     */
    public function setIdVenditore( $idVenditore): void
    {
        $this->idVenditore = $idVenditore;
    }

    /**
     * @return 
     */
    public function getIdCompratore()
    {
        return $this->idCompratore;
    }

    /**
     * @param  $idCompratore
     */
    public function setIdCompratore( $idCompratore): void
    {
        $this->idCompratore = $idCompratore;
    }

    /**
     * @return 
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * @param  $descrizione
     */
    public function setDescrizione( $descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    /**
     * @return float
     */
    public function getPrezzo()
    {
        return $this->prezzo;
    }

    /**
     * @param float $prezzo
     */
    public function setPrezzo($prezzo): void
    {
        $this->prezzo = $prezzo;
    }
    /**
     * @return 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  $data
     */
    public function setData( $data): void
    {
        $this->data = $data;
    }

    /**
     * @return 
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * @param  $titolo
     */
    public function setTitolo( $titolo): void
    {
        $this->titolo = $titolo;
    }

    /**
     * @return 
     */
    public function getIdAnnuncio()
    {
        return $this->idAnnuncio;
    }

    /**
     * @param  $idAnnuncio
     */
    public function setIdAnnuncio($idAnnuncio): void
    {
        $this->idAnnuncio = $idAnnuncio;
    }

    /**
     * @return 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param  $categoria
     */
    public function setCategoria( $categoria): void
    {
        $this->categoria = $categoria;
    }

    /**
     * @return mixed
     */
    public function isBan()
    {
        return $this->ban;
    }

    /**
     * @param  $ban
     */
    public function setBan( $ban): void
    {
        $this->ban = $ban;
    }

    /**
     * @return mixed
     */
    public function isAcquistato()
    {
        return $this->acquistato;
    }

    /**
     * @param mixed $acquistato
     */
    public function setAcquistato($acquistato): void
    {
        $this->acquistato = $acquistato;
    }

    public function jsonSerialize()
    {
        return
            [
                'titolo'   => $this->getTitolo(),
                'descrizione' => $this->getDescrizione(),
                'prezzo'   => $this->getPrezzo(),
                'idFoto'   => $this->getIdFoto(),
                'data'   => $this->getData(),
                'idAnnuncio'   => $this->getIdAnnuncio(),
                'idVenditore'   => $this->getIdVenditore(),
                'idCompratore'   => $this->getIdCompratore(),
                'categoria'   => $this->getCategoria()
            ];

    }
}

