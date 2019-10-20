<?php
/**
 * Scholar Theme, Page Navigation Twig-Extension
 *
 * PHP version 7
 *
 * @category API
 * @package  Grav\Theme\Scholar
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme;

use Grav\Theme\Scholar\API\Content;

/**
 * Scholar Theme, Page Navigation Twig-Extension
 *
 * @category Extensions
 * @package  Grav\Theme
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class PageNavigationExtension extends \Twig_Extension
{
    /**
     * Get Extension-name
     *
     * @return void
     */
    public function getName()
    {
        return 'PageNavigationExtension';
    }

    /**
     * Get Extension-functions
     *
     * @return void
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('page_navigation', [$this, 'pageNavigation']),
        ];
    }

    /**
     * Manipulate content and build list from headings
     *
     * @param string $content HTML-content
     * @param bool   $itemize Assign indices to tags
     *
     * @return object [content, headings]
     */
    public function pageNavigation(string $content, bool $itemize): object
    {
        return Content::pageNavigation($content, $itemize);
    }
}
