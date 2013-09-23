<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DTO
 *
 * @author gabriel
 */
class DTO
{
    protected $aData;

    public function __get($attrib)
    {
        return $this->aData[$attrib];
    }

    public function __set($attrib, $value)
    {
        $this->aData[$attrib] = $value;
        return true;
    }
}