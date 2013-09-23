<?php
/**
 * SessionMapper class
 *
 * PHP version 5
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * SessionMapper class
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class SessionMapper extends MappedEntityMapper
{
    protected static $instance = null;

    protected $sMainTable           = 'prp_session';
    protected $sInternalIdFieldName = 'sessionId';

//    protected $sSequenceName    = 'SRV_STB_SEQ';

    protected $aFieldMapping = array(
                            'sessionId'         => 'SESSION_ID',
                            'userId'            => 'USER_ID',
                            'phpSessionId'      => 'PHP_SESSION_ID',
                            'ip'                => 'IP',
                            'token'             => 'TOKEN',
                            'lastUpdate'        => 'LAST_UPDATE',
                            'userAgent'         => 'USER_AGENT',
                                    );


    protected $aFieldMappingRead = array(
                                    );

    protected $aFieldMappingWrite = array(

                                    );

    protected $aFieldMappingUniqueKey = array(
                            'SESSION_ID'            => 'sessionId',
                                    );

    /**
     * __construct
     *
     * @param object $db DbConnection
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
     * @param object $db DbConnection
     *
     * @return StbSpaceMapper
     */
    public static function getInstance($db)
    {
        if (self::$instance === null)
        {
            return new SessionMapper($db);
        }

        return self::$instance;
    }
}