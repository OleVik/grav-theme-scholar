<?php
/**
 * Scholar Theme, Content Interface
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\Content
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\Content;

/**
 * Content Interface
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\Content\ContentInterface
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
interface ContentInterface
{
    /**
     * Create menu-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Reserved collection-mode for handling child-pages
     * @param integer $depth Reserved placeholder for recursion depth
     *
     * @return array Page-structure with children
     */
    public static function buildMenu(string $route, $mode = false, $depth = 0);

    /**
     * Manipulate content and build list from headings
     *
     * @param string $content HTML-content
     * @param bool   $itemize Assign indices to tags
     *
     * @return object [content, headings]
     */
    public static function pageNavigation(string $content, bool $itemize = false): object;

    /**
     * Remove given HTML tags
     *
     * @param string $content HTML-content
     * @param mixed  $tags    Tags to strip, comma-separated
     *
     * @return string Manipulated HTML, UTF-8 encoded
     */
    public static function stripHTML(string $content, $tags);

    /**
     * Wrap HTML tags
     *
     * @param string $content    HTML-content
     * @param string $wrapperTag Tag to wrap around matches
     * @param array  $targetTags HTML tags to wrap
     *
     * @see https://stackoverflow.com/a/10683463
     *
     * @return string Manipulated HTML
     */
    public static function wrapHTML(string $content, string $wrapperTag, array $targetTags): string;
    
    /**
     * Extract headings from HTML
     *
     * @param array $data [title => [href, level]]
     *
     * @return string HTML ordered list
     */
    public static function buildList($data): string;

    /**
     * Get inner HTML of a DOM node
     *
     * @param \DOMNode $node DOMDocument node
     * @param boolean  $wrap Include target tag
     *
     * @see https://stackoverflow.com/a/53740544
     *
     * @return string Inner DOM node
     */
    public static function getInnerHTML(\DOMNode $node, $wrap = true): string;

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
     *
     * @see https://alanwhipple.com/2011/05/25/php-truncate-string-preserving-html-tags-words/
     */
    public static function truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true): string;
}
