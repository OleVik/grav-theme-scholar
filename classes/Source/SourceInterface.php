<?php
/**
 * Scholar Theme, Source Interface
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\Source
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\Source;

use Grav\Common\Page\Page;
use Grav\Common\Page\Pages;

/**
 * Source Interface
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\Source\SourceInterface
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-imgcaptions
 */
interface SourceInterface
{
    /**
     * Instantiate class
     *
     * @param Page  $page  Page-instance
     * @param Pages $pages Pages-instance
     */
    public function __construct(Page $page, Pages $pages);

    /**
     * Determine origin of image
     *
     * @param string $source Image src-attribute
     * @param string $prefix Optional prefix to Page location
     *
     * @return array Image source, filename, and optionally Page
     */
    public function render(string $source, string $prefix = '');
}
