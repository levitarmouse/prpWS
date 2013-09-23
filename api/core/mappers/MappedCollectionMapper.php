<?php
/**
 * MappedCollectionMapper class
 *
 * PHP version 5
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */

/**
 * MappedCollectionMapper class
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */
class MappedCollectionMapper extends Mapper
{
    protected $sCompanyIdFieldName = '';
    protected $sCrmIdDbFieldName = '';
    protected $sExternalIdDbFieldName = '';
    protected $sInternalIdFieldName = '';
    protected $sMainTable = '';
    protected $sSequenceName = '';
    protected $sControllerTypeFieldName = '';
    protected $iCountFileds;
    protected $aFieldMapping = array();
    protected $aFieldMappingUniqueKey = array();

    /**
     * __construct
     *
     * @param type $db DbConnection
     *
     * @return none
     */
    protected function __construct($db, $sConfigFile = '')
    {
        parent::__construct($db);

        if ($sConfigFile != '') {
            $this->_loadConfig($sConfigFile);
        }
    }

    private function _loadConfig($sConfigFileName)
    {
        $filename = $sConfigFileName;
        $aConfig = parse_ini_file($filename, true);

        foreach ($aConfig as $section => $aParameters) {
            if ($section == 'tableInfo' || $section == 'mainFields') {
                foreach ($aParameters as $attrib => $value) {
                    $this->$attrib = $value;
                }
            }
            if ($section == 'fieldMapping') {
                foreach ($aParameters as $index => $value) {
                    $this->aFieldMapping[$index] = $value;
                }
            }
            if ($section == 'fieldMappingUniqueKey') {
                foreach ($aParameters as $index => $value) {
                    $this->aFieldMappingUniqueKey[$index] = $value;
                }
            }
        }
    }

    public function getDbFieldCompanyId()
    {
        return (isset($this->sCompanyIdFieldName)) ? $this->sCompanyIdFieldName : '';
    }

    public function getDbFieldCrmId()
    {
        return (isset($this->sCrmIdDbFieldName)) ? $this->sCrmIdDbFieldName : '';
    }

    public function getDbFieldExternalId()
    {
        return (isset($this->sExternalIdDbFieldName)) ? $this->sExternalIdDbFieldName : '';
    }

    public function getDbFieldInternalId()
    {
        return (isset($this->sInternalIdFieldName)) ? $this->sInternalIdFieldName : '';
    }

    public function getDbTableName()
    {
        return (isset($this->sMainTable)) ? $this->sMainTable : '';
    }

    public function getDbTableSequenceName()
    {
        return (isset($this->sSequenceName)) ? $this->sSequenceName : '';
    }

    public function getFieldMappingUniqueKey()
    {
        return (isset($this->aFieldMappingUniqueKey)) ? $this->aFieldMappingUniqueKey : array();
    }

    public function getDbFieldControllerType()
    {
        return (isset($this->sControllerTypeFieldName)) ? $this->sControllerTypeFieldName : '';
    }

    public function getAttribAsUniqueKey()
    {
        $sAttrib = '';
        if (is_array($this->aFieldMappingUniqueKey)) {
            $aCopy = $this->aFieldMappingUniqueKey;
            array_flip($aCopy);

            $sAttrib = $aCopy[$this->sInternalIdFieldName];
        }
        return $sAttrib;
    }

    public function getById($id)
    {
        $sMainTable               = $this->getDbTableName();
//        $sControllerTypeFieldName = $this->getDbFieldControllerType();

        if ($sMainTable != '') {

            $sSql  = "SELECT ";
            $i = 0;
            $iCountFields = count($this->aFieldMapping);
            foreach ($this->aFieldMapping as $classAttrib => $dbField) {

                $sSql .= " `{$dbField}` ";

//                if (isset($this->aFieldMappingRead)) {
//                    if (array_key_exists($dbField, $this->aFieldMappingRead)) {
//                        $sFields .= ' '.$this->aFieldMappingRead[$dbField].' ';
//                    }
//                }
                if ($i < $iCountFields - 1) {
                    $sSql .= ", ";
                }
                $i ++;
            }
            $sFrom  = " FROM {$sMainTable} ";
            $sWhere = " WHERE 1 = 1 ";

            $aBnd = array();
//            if ($sControllerTypeFieldName != '') {
//                $sWhere .= " AND {$sControllerTypeFieldName} = :CONTROLLERTYPE ";
//                $aBnd['CONTROLLERTYPE'] = $sControllerType;
//            }

            $sSql .= $sFrom . $sWhere;

            // Logging
            foreach ($aBnd as $field => $value) {
                $sLogValues .= $field.'->['.$value.'] ';
            }
//            $this->oLogger->logDbChanges("select from {$sMainTable} where {$sLogValues}", 'SELECT');

            $aResult = $this->select($sSql, $aBnd);

//            $this->oLogger->logDbChanges("result: ".serialize($aResult));

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult;
            }
        }
        return array();
    }


    public function getByControllerType($sControllerType)
    {
        $sMainTable          = $this->getDbTableName();
        $sControllerTypeFieldName = $this->getDbFieldControllerType();

        if ($sMainTable != '') {

            $sSql  = "SELECT ROWNUM";

            foreach ($this->aFieldMapping as $classAttrib => $dbField) {

                $sTemp = " {$dbField} ";

                if (isset($this->aFieldMappingRead)) {
                    if (array_key_exists($dbField, $this->aFieldMappingRead)) {
                        $sTemp = ' '.$this->aFieldMappingRead[$dbField].' ';
                    }
                }
                $sSql .= ", {$sTemp} ";
            }
            $sFrom  = " FROM {$sMainTable} ";
            $sWhere = " WHERE 1 = 1 ";

            $aBnd = array();
            if ($sControllerTypeFieldName != '') {
                $sWhere .= " AND {$sControllerTypeFieldName} = :CONTROLLERTYPE ";
                $aBnd['CONTROLLERTYPE'] = $sControllerType;
            }

            $sSql .= $sFrom . $sWhere;

            // Logging
            foreach ($aBnd as $field => $value) {
                $sLogValues .= $field.'->['.$value.'] ';
            }
            $this->oLogger->logDbChanges("select from {$sMainTable} where {$sLogValues}", 'SELECT');

            $aResult = $this->select($sSql, $aBnd);

            $this->oLogger->logDbChanges("result: ".serialize($aResult));

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult;
            }
        }
        return array();
    }
}