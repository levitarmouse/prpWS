<?php
/**
 * CategoryDTO
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * CategoryDTO
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class CategoryDTO
{
    public $oDb;
    public $categoryId;
    public $userId;
    public $categoryName;

    function __construct($oDb, $categoryId = '', $userId = '', $categoryName = '')
    {
        $this->oDb = $oDb;
        $this->categoryId = $categoryId;
        $this->userId = $userId;
        $this->categoryName = $categoryName;
    }
}