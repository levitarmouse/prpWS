<?php
/**
 * TvConfigMapper class
 *
 * PHP version 5
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * TvConfigMapper class
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class TvConfigMapper extends Mapper
{
    protected static $instance = null;

    /**
     * __construct
     *
     * @param type $db Db Connection
     *
     * @return none
     */
    protected function __construct($db)
    {
        parent::__construct($db);
    }

    /**
     * getInstance
     *
     * @param type $db Db Connection
     *
     * @return TVControllerMapper
     */
    public static function getInstance($db)
    {
        if (self::$instance === null)
        {
            return new TvConfigMapper($db);
        }
        return self::$_instance;
    }

    /**
     * Obtiene valor de la constante de la DB
     *
     * @param string $constante constante
     *
     * @return string
     */
    function getGlobalParameter($constante)
    {
        $sql = "SELECT valor
                  FROM iw_constantes
                 WHERE parametroaplicacion = :constante
               ";
        $aBindings = array("constante" => $constante);

        $aResult = $this->select($sql, $aBindings);

        if (is_array($aResult) && count($aResult) > 0) {
            return $aResult[0]['VALOR'];
        }
        return '';
    }

}

?>
