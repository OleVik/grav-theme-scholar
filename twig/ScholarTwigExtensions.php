<?php
/**
 * Scholar Theme, Twig Extensions
 *
 * PHP version 7
 *
 * @category API
 * @package  Grav\Theme
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Theme\Scholar\LinkedData\AbstractLinkedData;

/**
 * Scholar Theme, Twig Extensions
 *
 * @category Extensions
 * @package  Grav\Theme\ScholarTwigExtensions
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class ScholarTwigExtensions extends \Twig_Extension
{
    /**
     * Get Extension-name
     *
     * @return void
     */
    public function getName()
    {
        return 'ScholarTwigExtensions';
    }

    /**
     * Get Extension-functions
     *
     * @return void
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('root_template', [$this, 'rootTemplate']),
            new \Twig_SimpleFunction('file_exists', [$this, 'fileExists']),
            new \Twig_SimpleFunction('rawcontent', [$this, 'getFileContents']),
            new \Twig_SimpleFunction('schema_type', [$this, 'getSchemaType']),
            new \Twig_SimpleFunction('page_navigation', [$this, 'pageNavigation']),
            new \Twig_SimpleFunction('scholar_menu_route', [$this, 'menuRoute']),
            new \Twig_SimpleFunction('scholar_menu', [$this, 'menu']),
            new \Twig_SimpleFunction('taxonomy_map', [$this, 'getTaxonomyMap']),
            new \Twig_SimpleFunction('taxonomy_map_page', [$this, 'getPageTaxonomyMap']),
            new \Twig_SimpleFunction('taxonomy_map_descendants', [$this, 'getDescendantsTaxonomyMap'])
        ];
    }

    /**
     * Get Extension-filters
     *
     * @return void
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('unwrap_p', [$this, 'unwrapParagraph']),
            new \Twig_SimpleFilter('section_wrapper', [$this, 'sectionWrapper']),
            new \Twig_SimpleFilter('strip_html_tags', [$this, 'stripHTML']),
            new \Twig_SimpleFilter('limit', [$this, 'limit']),
            new \Twig_SimpleFilter('threshold', [$this, 'threshold']),
            new \Twig_SimpleFilter(
                'truncate_words',
                array($this, 'truncate'),
                array(
                    'is_safe' => array('html')
                )
            )
        ];
    }

    /**
     * Determine root Page template
     *
     * @param string $route Route to Page
     *
     * @return string|bool Template name if true, otherwise false
     */
    public static function rootTemplate(string $route)
    {
        $page = Grav::instance()['page']->find($route);
        $parent = $page->topParent();
        if ($parent) {
            return $parent->template();
        }
        return false;
    }

    /**
     * Check file existence
     *
     * @param string $path Path to file
     *
     * @return boolean File existence
     */
    public static function fileExists(string $path): bool
    {
        $path = Grav::instance()['locator']->findResource($path, true, true);
        return file_exists($path);
    }

    /**
     * Remove paragraphs
     *
     * @param string $content Content to process
     *
     * @return string Processed content
     */
    public static function unwrapParagraph(string $content): string
    {
        return str_replace(['<p>', '</p>'], '', $content);
    }

    /**
     * Get the raw contents of a file
     *
     * @param string $path Absolute path to file
     *
     * @return string File contents
     */
    public static function getFileContents(string $path)
    {
        return file_get_contents($path);
    }

    /**
     * Determine Schema type and whether iterable
     *
     * @param string $template Template name to search for
     *
     * @return string Template name
     */
    public static function getSchemaType(string $template): string
    {
        return key(AbstractLinkedData::getType($template));
    }

    /**
     * Manipulate content and build list from headings
     *
     * @param string $content HTML-content
     * @param bool   $itemize Assign indices to tags
     *
     * @return object [content, headings]
     */
    public static function pageNavigation(string $content, bool $itemize): object
    {
        $Content = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.content',
                'Content\Content'
            )
        );
        return $Content::pageNavigation($content, $itemize);
    }

    /**
     * Get menu route
     *
     * @param string $route Page route
     * @param string $type  Condition
     *
     * @return string|bool Page route if true, otherwise false
     */
    public static function menuRoute(string $route, string $type = '')
    {
        $page = Grav::instance()['page']->find($route);
        if ($page->template() == $type) {
            return $page->rawRoute();
        } else {
            return self::getMenuRoute($page->parent()->rawRoute(), $type);
        }
        return false;
    }

    /**
     * Create menu-structure recursively
     *
     * @param string $route Route to page
     *
     * @return string Rendered template
     */
    public static function menu(string $route): string
    {
        $Content = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.content',
                'Content\Content'
            )
        );
        return Grav::instance()['twig']->processTemplate(
            'partials/docs/menu.html.twig',
            [
                'pages' => $Content::buildMenu($route)
            ]
        );
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
        $Content = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.content',
                'Content\Content'
            )
        );
        return $Content::wrapHTML($content, $wrapper, $targets);
    }

    /**
     * Remove given HTML tags
     *
     * @param string $content HTML-content
     * @param mixed  $tags    Tags to strip, comma-separated
     *
     * @return string Manipulated HTML, UTF-8 encoded
     */
    public static function stripHTML(string $content, $tags)
    {
        if (empty($tags)) {
            return $content;
        }
        $Content = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.content',
                'Content\Content'
            )
        );
        return $Content::stripHTML($content, $tags);
    }

    /**
     * Get Taxonomy
     *
     * @return array Taxonomy map
     */
    public static function getTaxonomyMap(): array
    {
        $TaxonomyMap = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
        return $TaxonomyMap->get();
    }

    /**
     * Get Page Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public static function getPageTaxonomyMap(string $route): array
    {
        $TaxonomyMap = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
        return $TaxonomyMap->getPage($route);
    }

    /**
     * Get Page Descendants Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public static function getDescendantsTaxonomyMap(string $route): array
    {
        $TaxonomyMap = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
        return $TaxonomyMap->getDescendants($route);
    }

    /**
     * Limit the length of an array
     *
     * @param array   $list   Array to slice
     * @param integer $length Maximum length
     *
     * @return array Taxonomy map
     */
    public static function limit(array $list, int $length = 10)
    {
        $TaxonomyMap = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
        return $TaxonomyMap::limit($list, $length);
    }

    /**
     * Limit the length of an array
     *
     * @param array   $list      Array to filter
     * @param integer $threshold Minimum value
     *
     * @return array Taxonomy map
     */
    public static function threshold(array $list, int $threshold = 10)
    {
        $TaxonomyMap = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
        return $TaxonomyMap::threshold($list, $threshold);
    }

    /**
     * Truncates a string up to a number of characters
     * while preserving whole words and HTML tags
     *
     * @param string  $text         String to truncate.
     * @param integer $length       Length of returned string, including ellipsis.
     * @param string  $ending       Ending to be appended to the trimmed string.
     * @param boolean $exact        If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     *
     * @return string Truncated string.
     */
    public static function truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {
        $Content = Scholar::getInstance(
            Grav::instance()['config']->get(
                'theme.api.content',
                'Content\Content'
            )
        );
        return $Content::truncate($text, $length, $ending, $exact, $considerHtml);
    }
}
