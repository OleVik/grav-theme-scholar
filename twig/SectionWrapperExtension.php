<?php
/**
 * Scholar Theme, Section Wrapper Twig-Extension
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
 * Scholar Theme, Section Wrapper Twig-Extension
 *
 * @category Extensions
 * @package  Grav\Theme
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class SectionWrapperExtension extends \Twig_Extension
{
    /**
     * Get Extension-name
     *
     * @return void
     */
    public function getName()
    {
        return 'SectionWrapperExtension';
    }

    /**
     * Get Extension-functions
     *
     * @return void
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('section_wrapper', [$this, 'sectionWrapper']),
        ];
    }

    /**
     * Wrap HTML tags
     *
     * @param string $content HTML-content
     * @param string $wrapper Tag to wrap around matches
     * @param array  $targets HTML tags to wrap
     *
     * @return string Manipulated HTML
     */
    public static function sectionWrapper(string $content, string $wrapper, array $targets)
    {
        return Content::wrapHTML($content, $wrapper, $targets);
    }
}
