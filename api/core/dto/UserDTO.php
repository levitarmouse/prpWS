<?php

/*
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * Description of UserDTO
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class UserDTO
{
    public $oDb;
    public $sUserName;
    public $sUserPass;
    public $iUserId;

    function __construct($oDb, $sUserName = '', $sUserPass = '', $iUserId = '')
    {
        $this->oDb = $oDb;
        $this->sUserName = $sUserName;
        $this->sUserPass = $sUserPass;
        $this->iUserId   = $iUserId;
    }

}

?>
