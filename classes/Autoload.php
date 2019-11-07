<?php
/**
 * Scholar Theme, Autoload
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\Autoload
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar;

/**
 * Autoload
 *
 * @category API
 * @package  Grav\Theme\Scholar\Autoload\Autoload
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class Autoload
{
    /**
     * Initialize Autoload-helper
     *
     * @param string $Namespace  Vendor namespace
     * @param array  $ClassNames List of classes to instantiate
     */
    public function __construct(string $Namespace, array $ClassNames)
    {
        foreach ($ClassNames as $name) {
            $class = new \ReflectionClass($Namespace . '\\' . $name);
            $class->newInstanceWithoutConstructor();
        }
    }
}
