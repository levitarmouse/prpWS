<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExpenseItemManager
 *
 * @author gabriel
 */
class ExpenseItemManager
{
    public function getCategories($dto)
    {
        $oDb = $dto->oSession->oDb;
        $obj = new CategoryCollection(new CategoryDTO($oDb));

        $oDTO = new LoadByIdDTO($dto->oSession->userId);
//        $oDTO->userId = $dto->oSession->userId;
        $categories = $obj->getCollection($oDTO);

        $sXml = $obj->getAsXML();

        return $sXml;
    }

}