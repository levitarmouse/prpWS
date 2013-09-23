<?php
/**
 * TvMapper class
 *
 * PHP version 5
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */

/**
 * TvMapper class
 *
 * @package   IntrawayWS
 * @author    Gabriel Prieto <gabriel.prieto@intraway.com>
 * @copyright 2012 Intraway Corporation
 * @link      www.intraway.com
 */
class Mapper
{
    static $iCountSelect  = 0;
    static $iCountExecute = 0;
    static $iCountUpdate  = 0;
    static $iCountDelete  = 0;
    static $iCountInsert  = 0;

    public $oDb;
    public $oLogger;

    public function getById($iId) {}
    public function getByCrmId($sCrmId, $iControllerId = '', $iCompanyId = '', $sControllerType = '') {}
    public function getByStbId($iStbId) {}
    public function getByESPV($idEmpresa, $idServicio, $idProducto, $idVenta) {}
    public function getByESPVC($idEmpresa, $idServicio, $idProducto, $idVenta, $idCliente) {}
    public function getByESPVP($idEmpresaPadre, $idServicioPadre, $idProductoPadre, $idVentaPadre) {}

//    public abstract function create($aValues);
//    public abstract function update($iIdEmpresa, $iIdServicio, $iIdProducto, $iIdVenta, $aValues = array());
//    public abstract function remove($iIdEmpresa, $iIdServicio, $iIdProducto, $iIdVenta, $aValues = array());

    /**
     * __construct
     *
     * @param type $db DbConnection
     *
     * @return none
     */
    protected function __construct($db)
    {
        $this->oDb = $db;
        $this->oLogger = Outlet::getInstanceOf('Logger', null, 'TvLogger');
    }

    /**
     *
     * @return type
     */
    public function getNextId()
    {
        return $this->getSeqNextVal($this->sSequenceName);
    }

    /**
     * getSeqNextVal
     *
     * @param type $db       DbConnection
     * @param type $sSecName Sequence Name
     *
     * @return type
     */
    public function getSeqNextVal($sSecName)
    {
        $iResult = null;
        $sSql = <<<EOQ
            SELECT {$sSecName}.nextval AS nextval
              FROM dual
EOQ;
        $aBinding = array();

        $aResult = $this->select($sSql, $aBinding);

        if (count($aResult) == 1)
        {
            $iResult = $aResult[0]['NEXTVAL'];
        }
        return $iResult;
    }

    /**
     * getSysdate
     * Devuelve la fecha actual y la fecha luego de aplicar los modificadores pasados por parámetro.
     * Los modificadores son, el formato de las fechas a devolver y valores para agregar o quitar.
     *
     * @param type $sFormat          Formato a devolver
     * @param type $iYearsToAppend   Años para agregar
     * @param type $iMonthsToAppend  Meses para agregar
     * @param type $iWeeksToAppend   Semanas para agregar
     * @param type $iDaysToAppend    Días para agregar
     * @param type $iHoursToAppend   Horas para agregar
     * @param type $iMinutesToAppend Minutos para agregar
     * @param type $iSecondsToAppend Segundos para agregar
     *
     * @return type
     */
    public function getSysdate($sFormat = '', $bGetAsSeconds = false,
                               $iYearsToAppend = 0,  $iMonthsToAppend = 0, $iWeeksToAppend = 0, $iDaysToAppend = 0,
                               $iHoursToAppend = 0, $iMinutesToAppend = 0, $iSecondsToAppend = 0)
    {
        $iYearsToAppend   = ($iYearsToAppend !== null)   ? $iYearsToAppend : 0;
        $iMonthsToAppend  = ($iMonthsToAppend !== null)  ? $iMonthsToAppend : 0;
        $iWeeksToAppend   = ($iWeeksToAppend !== null)   ? $iWeeksToAppend : 0;
        $iDaysToAppend    = ($iDaysToAppend !== null)    ? $iDaysToAppend : 0;
        $iHoursToAppend   = ($iHoursToAppend !== null)   ? $iHoursToAppend : 0;
        $iMinutesToAppend = ($iMinutesToAppend !== null) ? $iMinutesToAppend : 0;
        $iSecondsToAppend = ($iSecondsToAppend !== null) ? $iSecondsToAppend : 0;

        $sFormat = ($sFormat == '') ? 'YYYYMMDDHH24MISS' : $sFormat;

        $sSql = <<<EOQ
            SELECT TO_CHAR(SYSDATE, '$sFormat') AS ACTUAL,
                   TO_CHAR(ADD_MONTHS(sysdate + :years*365 + :weeks*7 + :days + :hours/24 + :minutes/1440 + :seconds/86400, :months),
                           '$sFormat') AS FECHA,
                   TO_CHAR(ADD_MONTHS(sysdate + :years*365 + :weeks*7 + :days + :hours/24 + :minutes/1440 + :seconds/86400, :months),
                           'YYYY;MM;DD;HH24;MI;SS') AS TO_EXPLODE
              FROM DUAL
EOQ;
        $aBinding = array(
                          'years'   => $iYearsToAppend,
                          'months'  => $iMonthsToAppend,
                          'weeks'   => $iWeeksToAppend,
                          'days'    => $iDaysToAppend,
                          'hours'   => $iHoursToAppend,
                          'minutes' => $iMinutesToAppend,
                          'seconds' => $iSecondsToAppend,
                         );

        $aResult = $this->select($sSql, $aBinding);

        if (count(func_get_args()) == 0) {
            $sReturn = $aResult[0]['ACTUAL'];
        }
        else {
            if ($bGetAsSeconds) {
                list($yy, $mm, $dd, $hh, $mi, $ss) = explode(';', $aResult[0]['TO_EXPLODE']);
                $sReturn = mktime($hh, $mi, $ss, $mm, $dd, $yy);
            }
            else {
                $sReturn = $aResult[0]['FECHA'];
            }
        }

        return $sReturn;
    }

    public function getDateAsTimestamp($sDate = '', $sFormat = '')
    {
        if ($sDate == '') {
            $iReturn = $this->getSysdate('', true);
        }
        else {
            $sSql = <<<EOQ
                SELECT TO_CHAR(TO_DATE(:sDate, :sFormat), 'YYYY;MM;DD;HH24;MI;SS') AS TO_EXPLODE
                  FROM DUAL
EOQ;
            $aBinding = array('sDate' => $sDate, 'sFormat' => $sFormat);
            $aResult = $this->select($sSql, $aBinding);
            list($yy, $mm, $dd, $hh, $mi, $ss) = explode(';', $aResult[0]['TO_EXPLODE']);
            $iReturn = mktime($hh, $mi, $ss, $mm, $dd, $yy);
        }
        return $iReturn;
    }

    public function isDateInTheFuture($sDate, $sFormat = 'YYYYMMDDHH24MISS')
    {
        return ($this->getSysdateDiff($sDate, $sFormat) >= 0);
    }

    /**
     * getSysdate
     *
     * @param type $db DbConnection
     *
     * @return type
     */
    public function getSysdateDiff($sDate, $sFormat = '')
    {
        $sFormat = ($sFormat == '') ? 'DD/MM/YYYY HH24:MI:SS' : $sFormat;

        $sSql = <<<EOQ
               SELECT (TO_DATE(:datestring, :format) - SYSDATE ) AS TIMEDIFF
                 FROM DUAL
EOQ;
        $aBinding = array('datestring' => $sDate,
                          'format'     => $sFormat);
        $aResult = $this->select($sSql, $aBinding);

        $fDiff = $aResult[0]['TIMEDIFF'];
        return $fDiff;
    }

    /**
     *
     * @param type $sSql
     * @param type $aBnd
     *
     * @return type
     */
    public function execute($sSql, $aBnd)
    {
        $iResult = 0;
        try {
            if (!$this->oDb) {
                throw new Exception(__CLASS__.' DbConection not present');
            }
            else {
                $iResult = $this->oDb->sqlExecForBinding($sSql, $aBnd);
                self::$iCountExecute ++;
            }
        }
        catch (Exception $e) {
            $this->oLogger->logDebug($e->getMessage());
            die;
        }
        return $iResult;
    }

    /**
     *
     * @param type $sSql
     * @param type $aBnd
     *
     * @return type
     */
    public function insert($sSql, $aBnd)
    {
        $iResult = 0;
        try {
            if (!$this->oDb) {
                throw new Exception(__CLASS__.' DbConection not present');
            }
            else {
                $iResult = $this->oDb->sqlExecForBinding($sSql, $aBnd);
                self::$iCountInsert ++;
            }
        }
        catch (Exception $e) {
            $this->oLogger->logDebug($e->getMessage());
            die;
        }
        return $iResult;
    }

    /**
     *
     * @param type $sSql
     * @param type $aBnd
     *
     * @return type
     */
    public function update($sSql, $aBnd)
    {
        $iResult = 0;
        try {
            if (!$this->oDb) {
                throw new Exception(__CLASS__.' DbConection not present');
            }
            else {
                $iResult = $this->oDb->sqlExecForBinding($sSql, $aBnd);
                self::$iCountUpdate ++;
            }
        }
        catch (Exception $e) {
            $this->oLogger->logDebug($e->getMessage());
            die;
        }
        return $iResult;
    }

    /**
     *
     * @param type $sSql
     * @param type $aBnd
     *
     * @return type
     */
    public function delete($sSql, $aBnd)
    {
        $iResult = 0;
        try {
            if (!$this->oDb) {
                throw new Exception(__CLASS__.' DbConection not present');
            }
            else {
                $iResult = $this->oDb->sqlExecForBinding($sSql, $aBnd);
                self::$iCountDelete ++;
            }
        }
        catch (Exception $e) {
            $this->oLogger->logDebug($e->getMessage());
            die;
        }
        return $iResult;
    }

    public function getFieldMapping()
    {
        return $this->aFieldMapping;
    }

    /**
     *
     * @param type $sSql
     * @param type $aBnd
     *
     * @return type
     */
    public function select($sSql, $aBnd)
    {
        $aResult = null;
        try {
            if (!$this->oDb) {
                throw new Exception(__CLASS__.' DbConection not present');
            }
            else {
                $aResult = $this->oDb->sqlOpenForBinding($sSql, $aBnd);
                self::$iCountSelect ++;
            }
        }
        catch (Exception $e) {
            $this->oLogger->logDebug($e->getMessage());
            die;
        }
        return $aResult;
    }

    /**
     * _validateDateTime
     *
     * @param type $datetime
     * @param type $bDayFirst
     *
     * @return boolean
     */
    public function validateDateTime($datetime, $bDayFirst=false)
    {
        $bValidDate = false;

        $aDate = explode(" ", $datetime);

        if (count($aDate) == 2) {

            $aDay = explode("/", $aDate[0]);

            if (count($aDay) == 3 ) {

                if ($bDayFirst) {
                    list($day, $month, $year) = $aDay;
                } else {
                    list($month, $day, $year) = $aDay;
                }

                $aTime = explode(":", $aDate[1]);
                if (count($aTime) == 2) {
                    list($hour, $minute) = $aTime;

                    $bDate = @checkdate((int)$month, (int)$day, (int)$year);

                    if (is_numeric($hour) && is_numeric($minute) ) {
                        $bHour = ( ( ($hour >= 0 && $hour < 24) && ($minute >= 0 && $minute < 60) ) &&
                                   ( ((int)$hour == $hour ) && ((int)$minute) == $minute )           ) ? true : false;
                    } else {
                        $bHour = false;
                    }

                    if ($bDate == true && $bHour == true) {
                        $bValidDate = true;
                    }
                }
            }
        }

        return $bValidDate;
    }
}