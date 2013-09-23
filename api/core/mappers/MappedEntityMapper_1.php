<?php
/**
 * MappedEntityMapper class
 *
 * PHP version 5
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */

/**
 * MappedEntityMapper class
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */
abstract class MappedEntityMapper extends Mapper
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

    public function getNextId()
    {
        return $this->getSeqNextVal($this->sSequenceName);
    }

    /**
     * getById
     *
     * @param type $iId           Identificador
     * @param type $iControllerId ControllerId
     *
     * @return type
     */
    public function getById($iId, $iControllerId = '')
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

            if ($iControllerId != '') {
                $aBnd['CONTROLLERID'] = $iControllerId;
                $sWhere .= ' AND CONTROLLERID = :CONTROLLERID ';
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
                return $aResult[0];
            }
        }
        return array();
    }

    /**
     * getById
     *
     * @param type $iId Vod Space Id
     *
     * @return type
     */
    public function getByCrmId($sCrmId, $iControllerId = '', $iCompanyId = '', $sControllerType = '')
    {
        $sMainTable          = $this->getDbTableName();
        $sCrmIdFieldName     = $this->getDbFieldCrmId();
        $sCompanyIdFieldName = $this->getDbFieldCompanyId();
        $sControllerTypeFieldName = $this->getDbFieldControllerType();
        $sControllerIdFieldName = 'controllerid';

        if ($sMainTable != '' && $sCrmIdFieldName != '') {

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
            $sWhere = " WHERE {$sCrmIdFieldName} = :CRMID ";

            $aBnd = array('CRMID'        => $sCrmId);

            if ($iControllerId != '') {
                $sWhere .= " AND {$sControllerIdFieldName} = :CONTROLLERID ";
                $aBnd['CONTROLLERID'] = $iControllerId;
            }

            if ($iCompanyId != '') {
                $sWhere .= " AND {$sCompanyIdFieldName} = :COMPANYID ";
                $aBnd['COMPANYID'] = $iCompanyId;
            }

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
                return $aResult[0];
            }
        }
        return array();
    }
    /**
     * getByExternalId
     *
     * @param type $iId Vod Space Id
     *
     * @return type
     */
    public function getByExternalId($iExternalId, $iControllerId = '')
    {
        $sMainTable           = $this->getDbTableName();
        $sExternalIdFieldName = $this->getDbFieldExternalId();

        if ($sMainTable != '' && $sExternalIdFieldName != '') {

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
            $sWhere = " WHERE {$sExternalIdFieldName} = :EXTERNALID ";

            $aBnd = array('EXTERNALID'        => $iExternalId);

            if ($iControllerId != '') {
                $aBnd['CONTROLLERID'] = $iControllerId;
                $sWhere .= ' AND CONTROLLERID = :CONTROLLERID ';
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
                return $aResult[0];
            }
        }
        return array();
    }

    public function getByESPV($idEmpresa, $idServicio, $idProducto, $idVenta)
    {
        $sMainTable  = $this->getDbTableName();
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

            $sFrom = " FROM {$sMainTable}
                      WHERE 1 = 1
                        AND IDEMPRESA  = :IDEMPRESA
                        AND IDSERVICIO = :IDSERVICIO
                        AND IDPRODUCTO = :IDPRODUCTO
                        AND IDVENTA    = :IDVENTA ";

            $aBnd = array('IDEMPRESA'  => $idEmpresa,
                          'IDSERVICIO' => $idServicio,
                          'IDPRODUCTO' => $idProducto,
                          'IDVENTA'    => $idVenta,
                         );

            $sSql .= $sFrom;

            // Logging
            foreach ($aBnd as $field => $value) {
                $sLogValues .= $field.'->['.$value.'] ';
            }
            $this->oLogger->logDbChanges("select from {$sMainTable} where {$sLogValues}", 'SELECT');

            $aResult = $this->select($sSql, $aBnd);

            $this->oLogger->logDbChanges("result: ".serialize($aResult));

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult[0];
            }
        }
        return array();
    }

    public function getByESPVC($idEmpresa, $idServicio, $idProducto, $idVenta, $idCliente)
    {
        $sMainTable  = $this->getDbTableName();
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

            $sFrom = " FROM {$sMainTable}
                      WHERE 1 = 1
                        AND IDEMPRESA  = :IDEMPRESA
                        AND IDSERVICIO = :IDSERVICIO
                        AND IDPRODUCTO = :IDPRODUCTO
                        AND IDVENTA    = :IDVENTA
                        AND IDCLIENTE  = :IDCLIENTE ";

            $aBnd = array('IDEMPRESA'  => $idEmpresa,
                          'IDSERVICIO' => $idServicio,
                          'IDPRODUCTO' => $idProducto,
                          'IDVENTA'    => $idVenta,
                          'IDCLIENTE'  => $idCliente,
                         );

            $sSql .= $sFrom;

            // Logging
            foreach ($aBnd as $field => $value) {
                $sLogValues .= $field.'->['.$value.'] ';
            }
            $this->oLogger->logDbChanges("select from {$sMainTable} where {$sLogValues}", 'SELECT');

            $aResult = $this->select($sSql, $aBnd);

            $this->oLogger->logDbChanges("result: ".serialize($aResult));

            if (is_array($aResult) && isset($aResult[0]))
            {
                return $aResult[0];
            }
        }
        return array();
    }

    /**
     * getByStbId
     *
     * @param type $iStbId Vod Space Id
     *
     * @return type
     */
    public function getByStbId($iStbId)
    {
        $sMainTable  = $this->getDbTableName();
        if ($sMainTable != '') {

            $sSql  = "SELECT ROWNUM ";
            foreach ($this->aFieldMapping as $classAttrib => $dbField) {
                $sSql .= ", {$dbField} ";
            }

            $sFrom = " FROM {$sMainTable}
                    WHERE STBID = :ID ";

            $aBnd = array('ID'  => $iStbId);

            $sSql .= $sFrom;

            // Logging
            $this->oLogger->logDbChanges("select from {$sMainTable} where STBID->[{$iStbId}]", 'SELECT');

            $aResult = $this->select($sSql, $aBnd);

            $this->oLogger->logDbChanges("result: ".serialize($aResult));

            if (is_array($aResult) && isset($aResult[0])) {
                return $aResult[0];
            }
        }
        return array();
    }

    public function getByESPVP($idEmpresaPadre, $idServicioPadre, $idProductoPadre, $idVentaPadre)
    {

    }

    public function create($aValues)
    {
        $sLogValues = '';
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

                if ($value === TvConfig::TV_SYSDATE_STRING || $value === TvConfig::TV_SQL_SYSDATE_STRING) {
                    $value = "SYSDATE";
                }
                elseif ($value === TvConfig::TV_ENABLED) {
                    $value = "0";
                }
                elseif ($value === TvConfig::TV_DISABLED) {
                    $value = "1";
                }
                elseif ($value === TvConfig::TV_SQL_EMPTY_STRING) {
                    $value = "''";
                }
                elseif ($value === TvConfig::TV_NULL_STRING || $value === TvConfig::TV_SQL_NULL_STRING) {
                    $value = "null";
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
            $this->oLogger->logDbChanges("insert {$sMainTable} values {$sLogValues}", 'INSERT');

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
        if ($value === TvConfig::TV_SYSDATE_STRING   || $value === TvConfig::TV_SQL_SYSDATE_STRING ||
            $value === TvConfig::TV_SQL_EMPTY_STRING || $value === TvConfig::TV_EMPTY_STRING ||
            $value === TvConfig::TV_NULL_STRING      || $value === TvConfig::TV_ANY_STRING ||
            $value === TvConfig::TV_DISABLED         || $value === TvConfig::TV_ENABLED
                ) {
            return true;
        }
        return false;
    }

    public function modify($aValues, $aWhere)
    {
        $sLogValues = $sLogWhere = '';
        $sMainTable  = $this->getDbTableName();
        if (count($aWhere) > 0 && count($aValues) > 0 && $sMainTable != '') {

            $bFirst = true;
            foreach ($aValues as $field => $value) {

                $aBnd[$field] = $value;

                $setExpresion = $originalValue = null;
                if ($value !== null) {
                    if (is_array($this->aFieldMappingWrite) ) {
                        if (isset($this->aFieldMappingWrite[$field])) {
                            $setExpresion = $this->aFieldMappingWrite[$field];
                        }
                    }
                }

                if ($value === TvConfig::TV_SYSDATE_STRING || $value === TvConfig::TV_SQL_SYSDATE_STRING) {
                    $value = "SYSDATE";
                    unset($aBnd[$field]);
                }
                elseif ($value === TvConfig::TV_ENABLED) {
                    $value = "0";
                    unset($aBnd[$field]);
                }
                elseif ($value === TvConfig::TV_DISABLED) {
                    $value = "1";
                    unset($aBnd[$field]);
                }
                elseif ($value === TvConfig::TV_EMPTY_STRING || $value === TvConfig::TV_SQL_EMPTY_STRING) {
                    $value = "''";
                    unset($aBnd[$field]);
                }
                elseif ($value === TvConfig::TV_NULL_STRING || $value === TvConfig::TV_SQL_NULL_STRING || $value === null ) {
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
            $this->oLogger->logDbChanges("update {$sMainTable} set {$sLogValues} where {$sLogWhere}", 'UPDATE');

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
            $this->oLogger->logDbChanges("delete from {$sMainTable} where {$sLogWhere}", 'DELETE');

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