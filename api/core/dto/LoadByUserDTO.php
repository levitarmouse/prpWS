<?php
/**
 * LoadByCrmIdDTO class
 *
 * PHP version 5
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */

/**
 * LoadByCrmIdDTO class
 *
 * @package   prpWS
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright Levitarmouse.com 2012
 * @link      www.levitarmouse.com
 */
class LoadByUserDTO
{
    public $name;
    public $userId;

    public function __construct($name, $userId)
    {
        $this->name   = $name;
        $this->userId = $userId;
    }
}