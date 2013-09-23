<?php
/**
 * MappedEntityMapper class
 *
 * PHP version 5
 *
 * @package   miplaner
 * @author    Gabriel Prieto <info@miplaner.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */

/**
 * MappedEntityMapper class
 *
 * @package   miplaner
 * @author    Gabriel Prieto <info@miplaner.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */
abstract class MappedEntityMapper extends Mapper
{
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

    public function getDbFieldNameId()
    {
        return (isset($this->sCrmIdDbFieldName)) ? $this->sCrmIdDbFieldName : '';
    }

    public function getDbFieldInternalId()
    {
        return (isset($this->sInternalIdFieldName)) ? $this->sInternalIdFieldName : '';
    }

    public function getDbFieldName()
    {
        return (isset($this->sNameDbFieldName)) ? $this->sNameDbFieldName : '';
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

    public function getNextId()
    {
        return $this->getSeqNextVal($this->sSequenceName);
    }

    /**
     * getById
     *
     * @param integer $iId ObjectId
     *
     * @return type
     */
    public function getById($iId)
    {
        $sMainTable   = $this->getDbTableName();
        $sIdFieldName = $this->getDbFieldInternalId();

        if ($sMainTable != '' && $sIdFieldName != '') {

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
            $sWhere = " WHERE {$sIdFieldName} = :ID ";

            $aBnd = array('ID' => $iId);

            $sSql .= $sFrom . $sWhere;

            $aResult = $this->select($sSql, $aBnd);

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult[0];
            }
        }
        return array();
    }

    /**
     * getById
     *
     * @param string  $iId Vod Space Id
     * @param integer $iId Vod Space Id
     *
     * @return type
     */
    public function getByName($sName, $userId = '')
    {
        $sMainTable     = $this->getDbTableName();
        $sNameFieldName = $this->getDbFieldName();
//        $sObjectType    = $this->getDbFieldCompanyId();
//        $sUserNickName  = 'controllerid';

        if ($sMainTable != '' && $sNameFieldName != '') {

            $sSql  = "SELECT 1";

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
            $sWhere = " WHERE {$sNameFieldName} = :{$sNameFieldName} ";

            $aBnd = array($sNameFieldName => $sName);

            $sSql .= $sFrom . $sWhere;

            $aResult = $this->select($sSql, $aBnd);

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult[0];
            }
        }
        return array();
    }

    public function getByParams($aParams)
    {
        $sMainTable     = $this->getDbTableName();

        if ($sMainTable != '' && is_array($aParams)) {

            $sSql  = "SELECT 1";

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

            foreach ($aParams as $field => $value) {
                $sWhere .= "AND {$field} = :{$field} ";
                $aBnd[$field] = $value;
            }


            $sSql .= $sFrom . $sWhere;

            $aResult = $this->select($sSql, $aBnd);

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult[0];
            }
        }
        return array();
    }

    public function create($aValues)
    {
        $sLogValues = $sLogWhere = '';
        $sMainTable  = $this->getDbTableName();
        $iResult = false;
        if ($sMainTable != '') {
            $aBnd    = array();
            $iValues = count($aValues);
            $bFirst  = true;

            $sFields = $sValues = '';

            foreach ($aValues as $field => $value)
            {
                $bValueIsAConstant = $this->_isAConstant($value);
                $valueExpresion = null;
                if (is_array($this->aFieldMappingWrite) ) {
                    if (isset($this->aFieldMappingWrite[$field]) && !$bValueIsAConstant) {
                        $valueExpresion = $this->aFieldMappingWrite[$field];
                    }
                }

                if ($value === Config::SYSDATE_STRING || $value === Config::SQL_SYSDATE_STRING) {
                    $value = "now()";
                }
                elseif ($value === Config::ENABLED) {
                    $value = "0";
                }
                elseif ($value === Config::DISABLED) {
                    $value = "1";
                }
                elseif ($value === Config::SQL_EMPTY_STRING || $value === Config::EMPTY_STRING) {
                    $value = "''";
                }
                elseif ($value === '') {
                    $value = "''";
                }
                elseif ($value === null) {
                    $value = '';
                }

                $sFields .= (!$bFirst) ? ', ' .$field :     $field.'';

                if ($valueExpresion !==  null) {
                    $sValues .= (!$bFirst) ? ', '.$valueExpresion : $valueExpresion;
                    $aBnd[$field] = $value;
                }
                else {
                    if ($bValueIsAConstant) {
                        $sValues .= (!$bFirst) ? ', '.$value : $value;
                    }
                    else {
                        $sValues .= (!$bFirst) ? ', :'.$field : ':'.$field;
                        $aBnd[$field] = $value;
                    }
                }

                // Logging
                $sLogValues .= $field.'->['.$value.'] ';

                $bFirst = false;
            }

            // Logging
            $this->oLogger->logDebug("insert {$sMainTable} values {$sLogValues}");

            $sSql = "
                INSERT INTO {$this->getDbTableName()}
                      ({$sFields})
               VALUES ({$sValues})";

            $iResult = $this->insert($sSql, $aBnd, $sMainTable);
            $this->oLogger->logDebug("insert ending with: ({$iResult})");
        }
        return $iResult;
    }

    private function _isAConstant($value)
    {
        if ($value === Config::SYSDATE_STRING   || $value === Config::SQL_SYSDATE_STRING ||
            $value === Config::SQL_EMPTY_STRING || $value === Config::EMPTY_STRING ||
            $value === Config::NULL_STRING      || $value === Config::ANY_STRING ||
            $value === Config::DISABLED         || $value === Config::ENABLED
                ) {
            return true;
        }
        return false;
    }

    public function modify($aValues, $aWhere)
    {
        $sLogValues = $sLogWhere = $sSetters = '';
        $sMainTable  = $this->getDbTableName();
        if (count($aWhere) > 0 && count($aValues) > 0 && $sMainTable != '') {

            $bFirst = true;
            foreach ($aValues as $field => $value) {

                $aBnd[$field] = $value;

                $setExpresion = $originalValue = null;
                if (is_array($this->aFieldMappingWrite) ) {
                    if (isset($this->aFieldMappingWrite[$field])) {
                        $setExpresion = $this->aFieldMappingWrite[$field];
                    }
                }

                if ($value === Config::SYSDATE_STRING || $value === Config::SQL_SYSDATE_STRING) {
                    $value = "now()";
                    unset($aBnd[$field]);
                }
                elseif ($value === Config::ENABLED) {
                    $value = "0";
                    unset($aBnd[$field]);
                }
                elseif ($value === Config::DISABLED) {
                    $value = "1";
                    unset($aBnd[$field]);
                }
                elseif ($value === Config::EMPTY_STRING || $value === Config::SQL_EMPTY_STRING) {
                    $value = "''";
                    unset($aBnd[$field]);
                }
                elseif ($value === Config::NULL_STRING || $value === null ) {
                    $value = 'null';
                    unset($aBnd[$field]);
                }
                elseif ($value === '') {
                    $value = "''";
                    unset($aBnd[$field]);
                }
                else {
                    $originalValue = $value;
                    $value = ":{$field}";
                }

                if ($setExpresion === null) {
                    $setExpresion = $value;
                }

                $sSetters .= ($bFirst)
                              ?   "{$field} = {$setExpresion}"
                              : ", {$field} = {$setExpresion}";

                $bFirst = false;

                // Logging
                $sLogValues .= $field.'->['.(($originalValue) ? $originalValue : $value).'] ';
            }

            $sWhere  = ' 1 = 1 ';
            if (is_array($aWhere) && count($aWhere) > 0) {
                foreach ($aWhere as $field => $value) {
                    $aBnd[$field] = $value;
                    $sWhere .= " AND {$field} = :{$field}";

                    // Logging
                    $sLogWhere .= $field.'->['.$value.'] ';
                }
            }
            else {
                return false;
            }

            // Logging
            $this->oLogger->logDebug("update {$sMainTable} set {$sLogValues} where {$sLogWhere}");

            $sSql = "
                UPDATE {$sMainTable}
                   SET {$sSetters}
                 WHERE {$sWhere}";

            $iResult = $this->update($sSql, $aBnd, $sMainTable);
            $this->oLogger->logDebug("update ending with: ({$iResult})");
            return $iResult;
        }

        return false;
    }

    public function remove($aWhere)
    {
        $sLogWhere = '';
        $sMainTable  = $this->getDbTableName();
        if (is_array($aWhere) && count($aWhere) > 0 && $sMainTable != '') {

            $sSql = "
                DELETE
                  FROM {$sMainTable} ";

            $sWhere  = ' WHERE 1 = 1 ';

            foreach ($aWhere as $field => $value) {
                $aBnd[$field] = $value;
                $sWhere .= " AND {$field} = :{$field}";
                // Logging
                $sLogWhere .= $field.'->['.$value.'] ';
            }

            // Logging
            $this->oLogger->logDebug("delete from {$sMainTable} where {$sLogWhere}");

            $sSql .= ' '.$sWhere;

            $iResult = $this->delete($sSql, $aBnd, $sMainTable);
            $this->oLogger->logDebug("delete ending with: ({$iResult})");
            return $iResult;
        }

        return false;
    }

    public function isBeingUsed($sField, $sValue, $iExcludeId = '')
    {
        if ($iExcludeId) {
            return $this->valueAlreadyExists($this->sMainTable,
                                             $sField,
                                             $sValue,
                                             $this->sInternalIdFieldName,
                                             $iExcludeId);
        }
        else {
            return $this->valueAlreadyExists($this->sMainTable,
                                             $sField,
                                             $sValue,
                                             $this->sInternalIdFieldName);
        }
    }

    public function valueAlreadyExists($sTableName, $sFieldName, $sValue, $sExcludeField = '', $iExcludeId = '')
    {
        // TODO
        // escapar parametros
        $bResult = false;

        $sSql = <<<EOQ
            SELECT count(*) as Q
              FROM {$sTableName}
             WHERE {$sFieldName} = :value
EOQ;
        $aBinding = array('value' => $sValue);

        if ($iExcludeId)
        {
            $sSql .= " AND {$sExcludeField} <> :excludeid ";
            $aBinding['excludeid'] = $iExcludeId;
        }

        $aRecords = $this->select($sSql, $aBinding);
        if($aRecords[0]['Q'] > 0) {
            $bResult = true;
        }

        return $bResult;
    }
}