<?php

class Config
{
    const SQL_SYSDATE_STRING = 'SQL_SYSDATE_STRING';
    const SYSDATE_STRING = 'SYSDATE_STRING';
    const SQL_EMPTY_STRING = 'SQL_EMPTY_STRING';
    const EMPTY_STRING = 'EMPTY_STRING';
    const NULL_STRING = 'NULL_STRING';
    const ANY_STRING = 'ANY_STRING';
    const ENABLED = 'ENABLED';
    const DISABLED = 'DISABLED';
    const SEPARADOR = '|';

    protected static $aMethodHandlers = array(
                                                'getCategories' => 'ExpenseItemManager',
                                             );

    public function __construct()
    {
        ;
    }

    static function getSeparador()
    {
        return self::SEPARADOR;
        return md5(self::SEPARADOR);
    }

    public static function getMethodHandler($methodName)
    {
        $sHandlerName = '';
        $aMethodHandlers = self::$aMethodHandlers;
        if (is_array($aMethodHandlers)) {
            if (array_key_exists($methodName, $aMethodHandlers)) {
                $sHandlerName = trim(self::$aMethodHandlers[$methodName]);
            }
        }
        return $sHandlerName;
    }
}


