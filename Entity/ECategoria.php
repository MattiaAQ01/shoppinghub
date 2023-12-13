<?php

/**
 * La classe ECategoria contiene tutti gli attributi e i metodi relativi alle categorie di annunci
 * Gli attributi sono:
 * categoria: nome Categoria
 * idCate: id Categoria
 * @access public
 * @author BPT
 * @package Entity
 */

class ECategoria implements JsonSerializable
{
    /**
     * @var mixed nome Categoria
     */
    private  $categoria;
    /**
     * @var mixed id Categoria
     */
    private  $idCate;

    //-----------------------------COSTRUTTORE--------------------------

    public function __construct($idCate=null,$categoria=null)
    {
        $this->idCate = $idCate;
        $this->categoria = $categoria;
    }

    //--------------METODI GET E SET------------------------------------

    /**
     * @return mixed nome categoria
     */




    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param mixed $categoria nome categoria
     */
    public function setCategoria($categoria): void
    {
        $this->categoria = $categoria;
    }

    /**
     * @return mixed id Categoria
     */
    public function getIdCate()
    {
        return $this->idCate;
    }

    /**
     * @param mixed id Categoria
     */
    public function setIdCate($idCate): void
    {
        $this->idCate = $idCate;
    }

    public function jsonSerialize()
    {
        return
            [
                'categoria' => $this->getCategoria(),
                'idCate' => $this->getIdCate()
            ];
    }

}