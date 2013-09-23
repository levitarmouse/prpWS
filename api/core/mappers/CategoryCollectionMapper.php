<?php
/**
 * CategoryCollectionMapper class
 *
 * PHP version 5
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * CategoryCollectionMapper class
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class CategoryCollectionMapper extends MappedCollectionMapper
{
    protected static $instance = null;

    /**
     * __construct
     *
     * @param object $db DbConnection
     *
     * @return none
     */
    protected function __construct($db)
    {
        parent::__construct($db, 'Category.ini');
    }

    /**
     * getInstance
     *
     * @param object $db DbConnection
     *
     * @return CategoryCollectionMapper
     */
    public static function getInstance($db)
    {
        if (self::$instance === null)
        {
            return new CategoryCollectionMapper($db);
        }

        return self::$instance;
    }

    public function getCollection($userId)
    {
//        $userId = $dto->userId;
        $sSql = "SELECT ";

        $iCount = count($this->aFieldMapping);
        $i = 0;
        foreach ($this->aFieldMapping as $attrib => $field) {
            $sSql .= chr(96).$field.chr(96);
            if ($i < $iCount-1) {
                $sSql .= ', ';
            }
            $i++;
        }

        $sSql .= '  FROM '.chr(96).$this->getDbTableName().chr(96);
        $sSql .= ' WHERE 1 = 1 ';
        $sSql .= '   AND user_id = :user_id';

        $aBnd = array('user_id' => $userId);

        $aResult = $this->select($sSql, $aBnd);

        return $aResult;
    }

    public function getNext()
    {

    }

}