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
class SessionDTO
{
    public $oDb;
    public $iSessionId;
    public $iUserId;
    public $sIp;
    public $sPhpSessionId;

    function __construct($oDb, $iSessionId = '', $iUserId = '',
                         $sIp = '', $sPhpSessionId = '') {
        $this->oDb = $oDb;
        $this->iSessionId     = $iSessionId;
        $this->iUserId        = $iUserId;
        $this->sIp            = $sIp;
        $this->sPhpSessionId  = $sPhpSessionId;
    }
}