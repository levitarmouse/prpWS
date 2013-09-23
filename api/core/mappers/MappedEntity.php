<?php
/**
 * MappedEntity class
 *
 * PHP version 5
 */

/**
 * MappedEntity class
 */
abstract class MappedEntity
{
    const NO_CREATED    = 'NO_CREATED';     // No existe en la DB
    const ALREADY_EXISTS = 'ALREADY_EXISTS'; // Ya existe en la DB
    const CREATE_OK     = 'CREATE_OK';      // Se creó en la DB
    const CREATE_FAILED  = 'CREATE_FAILED';  // Falló la creación en la DB
    const UPDATE_OK     = 'UPDATE_OK';      // Se modificó en la DB
    const UPDATE_FAILED  = 'UPDATE_FAILED';  // Falló la modificación en la DB
    const REMOVAL_OK    = 'REMOVAL_OK';    // Se eliminó en la DB
    const REMOVAL_FAILED = 'REMOVAL_FAILED'; // Falló la eliminación en la DB

    protected $oMapper;
//
//    abstract function defineMapper();

    protected $exists;
    protected $aListChange;
    protected $hasChanges;
    protected $aData;
    private   $_isLoading;
    public    $objectStatus;

    protected $oLogger;
    public    $oDb;

    function __construct($oDb)
    {
        $this->oDb          = $oDb;
        $this->aData        = array();
        $this->aListChange  = array();
        $this->exists       = false;
        $this->hasChanges   = false;
        $this->_isLoading   = false;
        $this->objectStatus = self::NO_CREATED;

        $this->oLogger     = Outlet::getInstanceOf('Logger', '', 'Logger');
    }

    public function exists()
    {
        return $this->exists;
    }

    /**
     * Inicializa los atributos de la clase desde el ResultSet
     * pasado como parametro.
     *
     * @param type $aRsValues
     *
     * @return type
     */
    private function _initClassAttribs($aRsValues, $aFieldMapping)
    {
        $this->exists = false;

        if (is_array($aRsValues) && count($aRsValues) > 0)
        {
            foreach ($aFieldMapping as $sAttrib => $sField)
            {
                if (isset($aRsValues[$sField]))
                {
                    $this->aData[$sAttrib] = $aRsValues[$sField];
                }
            }
            $this->exists = true;
            $this->objectStatus = self::ALREADY_EXISTS;
        }

        return;
    }

    public function getNextId()
    {
        return $this->oMapper->getNextId();
    }

    public function __get($sAttrib)
    {
        if (isset($this->aData[$sAttrib]))
        {
            return $this->aData[$sAttrib];
        }
        return null;
    }

    public function __set($sAttrib, $sValue)
    {
        $oldValue = (isset($this->aData[$sAttrib])) ? $this->aData[$sAttrib] : null;
        $newValue = $sValue;
        $this->detectChanges($sAttrib, $oldValue, $newValue);
        $this->aData[$sAttrib] = $sValue;
    }

    protected function init($aRsValues)
    {
        $aFieldMapping = $this->oMapper->getFieldMapping();
        $this->_isLoading = true;
        $this->_initClassAttribs($aRsValues, $aFieldMapping);
        $this->_isLoading = false;

        $this->loadRelated();

        return;
    }

    public function initByResultSet($aRsValues)
    {
        $this->init($aRsValues);
        return;
    }

    public function loadById(LoadByIdDTO $dto)
    {
        $iId     = $dto->id;
        $aRs = $this->oMapper->getById($iId);
        $this->init($aRs);
        return;
    }

    public function loadByName(LoadByNameDTO $dto)
    {
        $sName   = $dto->name;
        $iUserId = $dto->userId;
        $aRs   = $this->oMapper->getByName($sName, $iUserId);
        $this->init($aRs);
        return;
    }

    public function loadByParams(Array $aParams)
    {
//        $sName = $dto->sName;
        $aRs   = $this->oMapper->getByParams($aParams);
        $this->init($aRs);
        return;
    }

    public function loadRelated()
    {
        return;
    }

    /**
     * create
     *
     * @return none
     */
    public function create()
    {
        $iResult = 0;
        $aValues = $this->getValues();

        if (is_array($aValues) && count($aValues > 0))
        {
            $iResult = $this->oMapper->create($aValues);

            if ($iResult) {
                $this->objectStatus = self::CREATE_OK;
            } else {
                $this->objectStatus = self::CREATE_FAILED;
        }
        }

        return ($bResult) ? '' : 'MAPPED_ENTITY_FAILED_TO_CREATE_['.get_class($this).']_INSTANCE_THROUGH_MAPPEDENTITY';
    }

    /**
     * modify
     *
     * @return none
     */
    public function modify()
    {
        $iResult = 0;
        $aWhere  = array();

        $aUniqueKey = $this->oMapper->getFieldMappingUniqueKey();
        if (is_array($aUniqueKey) && count($aUniqueKey) > 0) {
            try {
                foreach ($aUniqueKey as $sField => $sAttrib) {
                    if ($this->$sAttrib === null) {
                        throw new Exception('MAPPED_ENTITY_ERROR_COULD_NOT_DETERMINE_CONDITION_FOR_MODIFICATION');
                    }
                    $aWhere[$sField] = $this->$sAttrib;
                }

                $aValues = $this->getValues(true);

                if (is_array($aValues) && count($aValues) > 0)
                {
                    $iResult = $this->oMapper->modify($aValues, $aWhere);

                    if ($iResult) {
                        $this->objectStatus = self::UPDATE_OK;
                    } else {
                        $this->objectStatus = self::UPDATE_FAILED;
                }
            }
            }
            catch (Exception $e) {
                $iResult = $e->getMessage();
            }
        }
        return ($iResult) ? '' : 'MAPPED_ENTITY_FAILED_TO_MODIFY_['.get_class($this).']_INSTANCE_THROUGH_MAPPEDENTITY';
    }

    public function remove()
    {
        // TODO Deberían mandarse los authorization con flag 0 al sacar los canales de vod?????
        $iResult = 0;

        $aUniqueKey = $this->oMapper->getFieldMappingUniqueKey();
        if (is_array($aUniqueKey) && count($aUniqueKey) > 0) {
            try {
                foreach ($aUniqueKey as $sField => $sAttrib) {
                    if ($this->$sAttrib === null) {
                        throw new Exception('MAPPED_ENTITY_ERROR_COULD_NOT_DETERMINE_CONDITION_FOR_REMOVAL');
                    }
                    $aWhere[$sField] = $this->$sAttrib;
                }

                $iResult = $this->oMapper->remove($aWhere);
            }
            catch (Exception $e) {
                $iResult = $e->getMessage();
            }
        }

        if ($iResult) {
            $this->objectStatus = self::REMOVAL_OK;
        } else {
            $this->objectStatus = self::REMOVAL_FAILED;
    }
    }

    protected function getValues($bOnlyChanges = false)
    {
        $aValues = array();
        // Devuelve los attribs de la clase en un array asociativo
        // donde la key es el nombre del campo en la DB y el valor es el attr
        if ($bOnlyChanges)
        {
            if (is_array($this->aListChange)) {
                // Solo devuelve los campos sobre los que hubo cambios
                foreach ($this->aListChange as $sAttrName => $aChanges)
                {
                    $aFieldMapping = $this->oMapper->getFieldMapping();
                    if (isset($aFieldMapping[$sAttrName]))
                    {
                        // Devuelve el nuevo valor de los campos
                        $aValues[$aFieldMapping[$sAttrName]] = $aChanges['newValue'];
                    }
                }
            }
        }
        else
        {
            // Devuelve todos los campos, es para el caso de un insert
            $aFieldMapping = $this->oMapper->getFieldMapping();
            foreach ($aFieldMapping as $sAttrib => $sField)
            {
                $aValues[$sField] = $this->aData[$sAttrib];
            }
        }
        return $aValues;
    }

    protected function detectChanges($sAttrib, $oldValue, $newValue)
    {
        $bWasChanged = false;
        if (!$this->_isLoading) {
            if (isset($this->oMapper)) {
            if (array_key_exists($sAttrib, $this->oMapper->getFieldMapping())) {
                    if ( ($newValue === 0 || $newValue === '0') &&
                         ($oldValue === '' || $oldValue === null) ) {
                        $bWasChanged = true;
                    }
                    elseif ( ($newValue === '' || $newValue === null) &&
                             ($oldValue === 0 || $oldValue === '0') ) {
                        $bWasChanged = true;
                    }
                    elseif ($oldValue != $newValue) {
                        $bWasChanged = true;
                    }
                    if ($bWasChanged) {
                    $this->hasChanges |= true;
                    $this->aListChange[$sAttrib] = array('oldValue' => $oldValue, 'newValue' => $newValue);
                    $this->oLogger->logDetectChanges(get_class($this).'.'.$sAttrib.
                                                     " | old value -> [{$oldValue}] | new value -> [{$newValue}]");
                    unset($oChange);
                }
            }
        }
        }
        return;
    }

    public function hasChanges($sAttrName = '')
    {
        if ($sAttrName != '')
        {
            if ($this->hasChanges)
            {
                return array_key_exists($sAttrName, $this->getListChange());
            }
        }
        return $this->hasChanges;
    }

    public function getOldValueFor($sAttrib)
    {
        if (is_array($this->aListChange) && isset($this->aListChange[$sAttrib]))
        {
            return $this->aListChange[$sAttrib]['oldValue'];
        }
        return $this->aData[$sAttrib];
    }

    public function getListChange()
    {
        return $this->aListChange;
    }

    protected function initAdditionalAttribs()
    {
        $aFields = $this->aAdditionalAttribs;

        if (is_array($aFields) && count($aFields) > 0)
        {
            foreach ($aFields as $sAttr => $aSource)
            {
                $sFromTable  = $aSource['table'];
                $sFromFields = $aSource['field'];
                $sWhereField = $aSource['id'];
                $sWhereValue = $this->$sWhereField;
                $value = $this->oTvTopology->getField($sFromTable, $sFromFields, $sWhereField, $sWhereValue);
                $this->$sAttr = $value;
            }
        }
    }

    public function isBeingUsed($sField, $sValue, $bAutoExclude = true)
    {
        $id = $this->oMapper->getAttribAsUniqueKey();

        if ($bAutoExclude) {
            return $this->oMapper->isBeingUsed($sField, $sValue, $this->$id);
        }
        else {
            return $this->oMapper->isBeingUsed($sField, $sValue);
        }
    }

    public function getMapper(){
        return $this->oMapper;
}

    public function getAttribs($bAsObject = false, $bAsXml = false)
    {
        $mReturn = $this->aData;
        if ($bAsObject) {
            $mReturn = $this->_arrayToObject($mReturn);
        }
        else if ($bAsXml) {
            $mReturn = $this->_arrayToXML($mReturn);
        }
        return $mReturn;
    }

    private function _arrayToObject($aArray = null)
    {
        $obj = new stdClass();
        ksort($aArray, SORT_STRING);
        if (is_array($aArray) && count($aArray) > 0) {
            foreach ($aArray as $sAttrib => $sValue) {
                $obj->$sAttrib = $sValue;
            }
        }
        $obj->objectStatus = $this->objectStatus;
        return $obj;
    }

    private function _arrayToXML($aArray = null)
    {
        ksort($aArray, SORT_STRING);
        $xml = '';
        if (is_array($aArray)) {
            foreach ($aArray as $sAttrib => $sValue) {
                $xml .= "<{$sAttrib}>{$sValue}</{$sAttrib}>\n";
            }
        }
        return $xml;
    }
}
