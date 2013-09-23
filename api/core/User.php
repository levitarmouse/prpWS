<?php
/**
 * Subscriber class
 *
 * PHP version 5
 */

/**
 * Subscriber class
 */
class User extends MappedEntity
{
    protected $oMapper;

    public function __construct(UserDTO $dto)
    {
        parent::__construct($dto->oDb);

        $sUser   = $dto->sUserName;
        $sPass   = $dto->sUserPass;
        $iUserId = $dto->iUserId;

        $this->oMapper     = UserMapper::getInstance($this->oDb);

        try {
            if ($sUser != '') {
                $oDTO = new LoadByNameDTO($sUser, $iUserId);
                $this->loadByName($oDTO);
            }
        }
        catch (Exception $e) {
            $msg = $e->getMessage();
        }
    }
}