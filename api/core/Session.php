<?php
/**
 * Subscriber class
 *
 * PHP version 5
 */

/**
 * Subscriber class
 */
class Session extends MappedEntity
{
    protected $oMapper;

    public function __construct(SessionDTO $dto)
    {
        parent::__construct($dto->oDb);

        $iUserId    = $dto->iUserId;
        $sIp        = $dto->sIp;
        $iSessionId = $dto->iSessionId;

        $this->oMapper = SessionMapper::getInstance($this->oDb);

        try {
            if ($iSessionId != '') {
                $aParams = array(//'user_id'        => $iUserId,
                                 'ip'             => $sIp,
                                 'php_session_id' => $iSessionId);
                $this->loadByParams($aParams);
            }
//            else if ($iUserId !== '' && $sIp !== '') {
//                $aParams = array('user_id'         => $iUserId,
//                                 'http_session_id' => $sHttpSessionId,
//                                 'ip'              => $sIp);
//                $this->loadByParams($aParams);
//            }
        }
        catch (Exception $e) {
            $msg = $e->getMessage();
        }
    }
}