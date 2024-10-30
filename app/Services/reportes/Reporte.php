<?php 

abstract class Reporte 
{
    protected $caratula;
    protected $cuerpo;

    public function setCaratula($caratula): void
    {
        $this->caratula = $caratula;
    }

    

}