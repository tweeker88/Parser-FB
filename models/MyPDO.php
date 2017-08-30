<?php

abstract class MyPDO{

    public $dbo;

    public function __construct($dns)
    {
        return $this->dbo = $dns;
    }
}