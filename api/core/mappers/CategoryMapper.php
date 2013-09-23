<?php
/**
 * CategoryMapper class
 *
 * PHP version 5
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2013
 * @link      www.levitarmouse.com
 */

/**
 * CategoryMapper class
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2013
 * @link      www.levitarmouse.com
 */
class CategoryMapper extends MappedEntityMapper
{
    protected static $instance = null;

    protected $sMainTable           = 'prp_category';
    protected $sInternalIdFieldName = 'categoryId';

//    protected $sSequenceName    = 'SRV_STB_SEQ';

    protected $aFieldMapping = array(
                            'categoryId'   => 'CATEGORY_ID',
                            'categoryName' => 'CATEGORY_NAME',
                            'group'        => 'GROUP',
                            'stats'        => 'STATS',
                            'disabled'     => 'DISABLED',
                            'userId'       => 'USER_ID',
                                    );


    protected $aFieldMappingRead = array(
                                    );

    protected $aFieldMappingWrite = array(

                                    );

    protected $aFieldMappingUniqueKey = array(
                            'CATEGORY_ID' => 'categoryId',
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
     * @return CategoryMapper
     */
    public static function getInstance($db)
    {
        if (self::$instance === null)
        {
            return new CategoryMapper($db);
        }

        return self::$instance;
    }
}