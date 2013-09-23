<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MappedCollection
 *
 * @author gprieto
 */
abstract class MappedCollection
{
    protected $oMapper;
    protected $aRsCollection;
    protected $aObjCollection;

    function __construct($oDb)
    {
        $this->oDb          = $oDb;
        $this->aRsCollection  = array();
        $this->oLogger     = Outlet::getInstanceOf('Logger', null, 'TvLogger');
    }

    public function loadById(LoadByIdDTO $dto)
    {
        $id = $dto->id;

        $aRs = $this->oMapper->getById($id);

        $this->aRsCollection = $aRs;
//        $this->init($aRs);
        return $this->aRsCollection;
    }

    public function getAsXML()
    {
        $i = 0;
        $sXml  = "\n<collection>\n";

        /** @var $obj MappedEntity **/
        foreach ($this->aObjCollection as $obj) {
            $sXml .= '<element_'.$i.">\n";
            $sXml .= $obj->getAttribs(false, true);
            $sXml .= '</element_'.$i.">\n";
            $i++;
        }
        $sXml .= "\n</collection>\n";
        return $sXml;
    }
}