<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define('ROOT_PATH', '/var/www/prpWS/');
require_once ROOT_PATH.'/includes/config.php';
require_once ROOT_PATH.'/includes/autoloader.php';

/**
 * Description of prpWS
 *
 * @author gabriel
 */
class prpWSPurchase
{
    protected $oDb;

    /**
     *
     * @param string $sUser      User Name
     * @param string $sSessionId SessionId
     *
     * @return array $return Token
     */
    public function hello($sUser, $sSessionId)
    {
        $this->oDb = new DB();
        $oDTO = new UserDTO($this->oDb, $sUser);
        if ($sUser == '' || $sSessionId == '') {
            $oDTO->sUserName = 'guest';
            $oDTO->iUserId   = 0;
        }

        $oUser = new User($oDTO);

        if ($sSessionId == '') {
            $sSessionId = session_id();
            session_regenerate_id();
            session_destroy();
            session_start();
        }

        $oDTO = new SessionDTO($this->oDb, $sSessionId,
                               $oUser->userId,
                               $_SERVER['REMOTE_ADDR']);
        $oSession = new Session($oDTO);

        if ($oUser->exists()) {
            if (!$oSession->exists()) {

                $oSession->userId         = $oUser->userId;
                $oSession->phpSessionId   = session_id();
                $oSession->ip             = $_SERVER['REMOTE_ADDR'];
                $oSession->lastUpdate     = Config::SYSDATE_STRING;
                $oSession->userAgent      = $_SERVER['HTTP_USER_AGENT'];
                $oSession->create();
                $sMessage = 'Bienvenido';
            }
            else {
                $sMessage = 'otra vez sopa';
            }
        }
        else {
            $sMessage = 'el usuario no existe';
        }

        $oResponse = new HelloReponse();

        $oResponse->token     = $oSession->token;
        $oResponse->sessionId = $oSession->phpSessionId;
        $oResponse->message   = $sMessage;
        return $oResponse;
    }

    /**
     * hash
     *
     * @param string $string Salt
     *
     * @return string $string Hash
     */
    public function hash($string)
    {
        return md5($string);
    }

    /**
     * login
     *
     * @param string $userName  User name
     * @param string $passToken Pass token
     *
     * @return LoginReponse $oResponse
     */
	public function login($userName, $passToken){

        $this->oDb = new DB();
        $oResponse = new LoginReponse();

        $userName = utf8_decode($userName);

        $separador = Config::getSeparador();
        list($sUserPassToken, $sSessionId) = explode($separador, $passToken);

        try {
            if ($userName == '') {
                throw new Exception('NICK_NAME_OR_PASSTOKEN_EMPTY');
            }

            $oUserDTO = new UserDTO($this->oDb, $userName, $sUserPassToken);
            $oUser = new User($oUserDTO);

            if ($oUser->exists()) {
                $oDTO = new SessionDTO($this->oDb, $sSessionId,
                                       0,
                                       $_SERVER['REMOTE_ADDR']);
                $oSession = new Session($oDTO);

                if ($oSession->exists()) {
                    if ($oSession->exists()) {

                        /**
                         * La validación del login espera:
                         *
                         * password_eviada = al md5 del string resultante de
                         * concatenar la password del usuario +
                         * el sessionId
                         *
                         * De ese modo la password del usuario nunca viaja por red,
                         * lo que viaja es md5(password.sessionId)
                         */
                        $sVerif = md5($oUser->password.$oSession->phpSessionId);
                        if ($sVerif == $sUserPassToken) {
                            $oSession->userId = $oUser->userId;
    //                        $oSession->token  = $sNewToken;
                            $oSession->modify();

                            $oResponse->hello = utf8_encode('Hello '.$oUser->realName.'!');
                            $oResponse->token = $oSession->phpSessionId;
                        }
                    }
                    else {
                        $oResponse->hello = 'Goodbye';
                    }
                }
                else {
                    $oResponse->hello = 'Goodbye';
                }
            }
            else {
                error_log("el usuario [{$userName}] no existe\n", 3, 'logs/prpWs-logger-messages.log');
                $oResponse->hello = 'Goodbye';
            }
        }
        catch (Exception $e) {
            $oResponse->hello = $e->getMessage();
            $oResponse->token = '';
        }

        return $oResponse;
	}

    /**
     * expense
     *
     * @param string $userName User Name
     * @param string $token    Token
     * @param string $xml      Request Information
     *
     * @return array identificador de error
     */
    public function getCategories($userName, $token, $xml)
    {
        $result;
        $this->oDb = new DB();
        $oValidSession = $this->_validate($userName, $token);

        if ($oValidSession && $oValidSession->exists()) {
            $result = $this->_route(__FUNCTION__, $oValidSession, $xml);
        }

        return $result;
    }

    private function _route($handler, $oSession, $xml)
    {
        $this->oDb = new DB();
        $oDTO = new OperationRouterDTO($this->oDb, $oSession, $xml);

        $oRouter = new OperationRouter($oDTO);

        $routeResult = $oRouter->handle($handler);

        return $routeResult;
    }

    /**
     * expense
     *
     * @param string $userName User Name
     * @param string $sessionId    Token
     * @param string $xml      Request Information
     *
     * @return string identificador de error
     */
    private function _validate($userName, $sessionId)
    {
        $this->oDb = new DB();
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        $iUserId  = '';

        $oSessionDTO = new SessionDTO($this->oDb, $sessionId, $iUserId, $remoteIp);
        $oSession = new Session($oSessionDTO);

        if ($oSession->exists()) {
            $oSession->lastUpdate = Config::SQL_SYSDATE_STRING;
            $bResult = $oSession->modify();

            return $oSession;
        }
        else {
            return null;
        }
    }

    /**
     * expense
     *
     * @param string $userName User Name
     * @param string $token    Token
     * @param string $xml      Request Information
     *
     * @return string identificador de error
     */
    public function expense($userName, $token, $xml)
    {
        $this->oDb = new DB();
        $userName    = $userName;
        $aToken      = explode(Config::SEPARADOR, $token);
        $sOpToken    = $aToken[0];
        $iUserId     = $aToken[1];
        $sHttpSessionId = $aToken[2];
        $remoteIp = $_SERVER['REMOTE_ADDR'];

        $oSessionDTO = new SessionDTO($this->oDb, '', $iUserId, $sHttpSessionId, $remoteIp);
        $oSession = new Session($oSessionDTO);

        if ($oSession->exists()) {
            $oSession->lastUpdate = Config::SQL_SYSDATE_STRING;

            $oDTO = new OperationRouterDTO($this->oDb, $oSession, $xml);

            $oRouter = new OperationRouter($oDTO);

            $sMessage = $oRouter->handle();


            $oSession->modify();
        }
        else {
            return 'Debes ingresar primero para poder operar';
        }

        return $sMessage;
    }
}