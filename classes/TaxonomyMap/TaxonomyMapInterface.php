<?php
/**
 * Scholar Theme, Taxonomy Map Interface
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\TaxonomyMap
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\TaxonomyMap;

/**
 * Taxonomy Map Interface
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\TaxonomyMap\TaxonomyMapInterface
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
interface TaxonomyMapInterface
{
    /**
     * Instantiate class
     */
    public function __construct();

    /**
     * Get Taxonomy
     *
     * @param string $type  Type of taxonomy to retrieve
     * @param bool   $array Output as array
     *
     * @return array Taxonomy map
     */
    public function get(string $type = null, bool $array = null): array;

    /**
     * Get Page Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public function getPage(string $route);

    /**
     * Get Page Descendants Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public function getDescendants(string $route): array;

    /**
     * Pluralize taxonomy names
     *
     * @param array   $list   Associated array of taxonomy entries
     * @param boolean $unique Remove duplicates
     *
     * @return array Taxonomy map
     */
    public static function pluralize(array $list, bool $unique = null): array;

    /**
     * Limit the length of an array
     *
     * @param array   $list   Array to slice
     * @param integer $length Maximum length
     *
     * @return array Taxonomy map
     */
    public static function limit(array $list, int $length = 10): array;

    /**
     * Limit the length of an array
     *
     * @param array   $list      Array to filter
     * @param integer $threshold Minimum value
     *
     * @return array Taxonomy map
     */
    public static function threshold(array $list, int $threshold = 10): array;
}
