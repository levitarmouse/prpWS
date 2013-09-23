<?php
/**
 * UserMapper class
 *
 * PHP version 5
 */

/**
 * UserMapper class
 */
class UserMapper extends MappedEntityMapper
{
    protected static $instance = null;

    protected $sMainTable           = 'prp_user';
    protected $sInternalIdFieldName = 'USER_ID';
    protected $sNameDbFieldName     = 'USER_NAME';

    protected $sSequenceName    = 'IW_CLIENTE_SEQ';

    protected $aFieldMapping = array(
                            'userId'       => 'USER_ID',       //    int       {null}
                            'realName'     => 'REAL_NAME',     //    varchar   50
                            'userName'     => 'USER_NAME',     //    varchar   20
                            'mail'         => 'MAIL',          //    varchar   100
                            'password'     => 'PASSWORD',      //    varchar   100
                            'image'        => 'IMAGE',         //    varchar   256
                            'disable'      => 'DISABLE',      //    tinyint   {null}
                            'themeId'      => 'THEME_ID',      //    int       {null}
                            'logued'       => 'LOGUED',        //    tinyint   {null}
                            'creationDate' => 'CREATION_DATE', //    bigint    {null}
                            'lastLogin'    => 'LAST_LOGIN',    //    bigint    {null}
                            'token'        => 'TOKEN'          //    varchar   255
                                    );

    protected $aFieldMappingRead = array(
//                            'FECHAALTA'        => " TO_CHAR(FECHAALTA,        'YYYY/MM/DD HH24:MI:SS')  AS FECHAALTA ",
//                            'FECHAMODIF'       => " TO_CHAR(FECHAMODIF,       'YYYY/MM/DD HH24:MI:SS')  AS FECHAMODIF ",
//                            'FECHABAJA'        => " TO_CHAR(FECHABAJA,        'YYYY/MM/DD HH24:MI:SS')  AS FECHABAJA ",
//                            'FECHAVENCIMIENTO' => " TO_CHAR(FECHAVENCIMIENTO, 'YYYY/MM/DD HH24:MI:SS')  AS FECHAVENCIMIENTO ",
//                            'LASTBILLINGDATE'  => " TO_CHAR(LASTBILLINGDATE,  'YYYY/MM/DD HH24:MI:SS')  AS LASTBILLINGDATE ",
//                            'LASTLOGIN'        => " TO_CHAR(LASTLOGIN,        'YYYY/MM/DD HH24:MI:SS')  AS LASTLOGIN ",
                                   );

    protected $aFieldMappingWrite = array(
//                            'FECHAALTA'        => " TO_DATE(:FECHAALTA,        'YYYY/MM/DD HH24:MI:SS') ",
//                            'FECHAMODIF'       => " TO_DATE(:FECHAMODIF,       'YYYY/MM/DD HH24:MI:SS') ",
//                            'FECHABAJA'        => " TO_DATE(:FECHABAJA,        'YYYY/MM/DD HH24:MI:SS') ",
//                            'FECHAVENCIMIENTO' => " TO_DATE(:FECHAVENCIMIENTO, 'YYYY/MM/DD HH24:MI:SS') ",
//                            'LASTBILLINGDATE'  => " TO_DATE(:LASTBILLINGDATE,  'YYYY/MM/DD HH24:MI:SS') ",
//                            'LASTLOGIN'        => " TO_DATE(:LASTLOGIN,        'YYYY/MM/DD HH24:MI:SS') ",
                                   );

    protected $aFieldMappingUniqueKey = array(
                            'USER_ID'        => 'userId',
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
        ;
    }

    /**
     * getInstance
     *
     * @param object $db DbConnection
     *
     * @return UserMapper
     */
    public static function getInstance($db)
    {
        if (self::$instance === null)
        {
            return new UserMapper($db);
        }

        return self::$instance;
    }
}