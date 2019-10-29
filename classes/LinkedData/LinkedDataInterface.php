<?php
/**
 * Scholar Theme, Linked Data Interface
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\LinkedData
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\LinkedData;

use Grav\Common\Page\Page;

/**
 * Linked Data Interface
 *
 * @category LinkedData
 * @package  Grav\Theme\Scholar\LinkedData\LinkedDataInterface
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
interface LinkedDataInterface
{
    /**
     * Create Schema-structure
     *
     * @param Page $page  Page-instance
     * @param bool $slave If true
     *
     * @return void
     */
    public function buildSchema(Page $page, bool $slave = false);

    /**
     * Get Page author
     *
     * @param array $header Page header
     *
     * @return array Author data
     */
    public static function getAuthor(array $header): array;

    /**
     * Get Page image
     *
     * @param array $header Page header
     * @param array $media  Page media
     *
     * @return array Page Image
     */
    public static function getImage(array $header, array $media): array;
    /**
     * Search for named collections in Page FrontMatter
     *
     * @param array $header Page Header
     *
     * @return array
     */
    public static function getCollections(array $header): array;

    /**
     * Determine Schema type and whether iterable
     *
     * @param string $template Template name to search for
     *
     * @return array ['Template name (string)' => 'Iterable (bool)']
     */
    public static function getType(string $template): array;

    /**
     * Build Schema/JsonLD data
     *
     * @param array   $options Page data.
     * @param string  $type    Type of Schema.
     * @param boolean $script  Return as JavaScript, default false.
     *
     * @return array|string
     */
    public static function getSchema(array $options, string $type, $script = false);

    /**
     * Aggregate Schema/JsonLD data
     *
     * @return string
     */
    public function getSchemas(): string;
}
