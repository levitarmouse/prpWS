<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OperationRouterDTO
 *
 * @author gabriel
 */
class OperationRouterDTO {

    public $oDb;
    public $oSession;
    public $sXml;
    public $aXml;

    function __construct($oDb, $oSession, $sXml, $aXml = array()) {
        $this->oDb = $oDb;
        $this->oSession = $oSession;
        $this->sXml = $sXml;
        $this->aXml = $aXml;
    }
}