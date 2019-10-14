<?php
/**
 * Scholar Theme
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav
 * @subpackage Scholar
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-theme-scholar
 */
namespace Grav\Plugin\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Create a sidenote for Tufte CSS
 *
 * Class ScholarTheme
 *
 * @category Extensions
 * @package  Grav\Plugin
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-theme-scholar
 */
class NoteShortcode extends Shortcode
{
    /**
     * Initialize shortcode
     *
     * @return string
     */
    public function init()
    {
        $this->shortcode->getHandlers()->add(
            'note',
            function (ShortcodeInterface $sc) {
                return $this->noteRenderer($sc, 'sidenote');
            }
        );
        $this->shortcode->getHandlers()->add(
            'sidenote',
            function (ShortcodeInterface $sc) {
                return $this->noteRenderer($sc, 'sidenote');
            }
        );
        $this->shortcode->getHandlers()->add(
            'marginnote',
            function (ShortcodeInterface $sc) {
                return $this->noteRenderer($sc, 'marginnote');
            }
        );
    }

    /**
     * Parse tag for attributes
     *
     * @param string $tag HTML-tag
     *
     * @return array Associative array of attributes with values
     */
    protected static function attributes(string $tag)
    {
        preg_match_all(
            '/(\S+)\s*=\s*[\"\']?((?:.(?![\"\']?\s+(?:\S+)=|[>\"\']))?[^\"\']*)[\"\']?/im',
            $tag,
            $attributes,
            PREG_SET_ORDER
        );
        $assoc = array();
        foreach ($attributes as $attribute) {
            $assoc[$attribute[1]] = $attribute[2];
        }
        return $assoc;
    }

    /**
     * Render a sidenote in HTML
     *
     * @param ShortcodeInterface $sc   Accessor to Thunder\Shortcode
     * @param string             $type Type of note
     *
     * @return string
     */
    public function noteRenderer(ShortcodeInterface $sc, string $type)
    {
        $content = $sc->getParameter('content', $sc->getContent());
        preg_match_all(
            "/<p>\s*?(<a .*<img.*<\/a>|<img.*)?\s*<\/p>/",
            $content,
            $paragraphWrappers,
            PREG_SET_ORDER
        );
        if (count($paragraphWrappers) > 0) {
            foreach ($paragraphWrappers as $wrapper) {
                $content = str_replace($wrapper[0], $wrapper[1], $content);
            }
            $wrap = true;
        }
        preg_match_all(
            '/<figure[^>]*>\s*(?:<a[^>]*>\s*)*(?\'img\'<img[^>]*>\s*)*(?:<\/a>\s*)*(?:<figcaption[^>]*>(?\'title\'.*)<\/figcaption>\s*)<\/figure>/mi',
            $content,
            $figureWrappers,
            PREG_SET_ORDER
        );
        if (count($figureWrappers) > 0) {
            foreach ($figureWrappers as $wrapper) {
                $content = str_replace($wrapper[0], $wrapper['img'], $content);
                if (isset($wrapper['title']) && !empty($wrapper['title'])) {
                    $title = $wrapper['title'];
                }
            }
            $wrap = true;
        }
        $prefix = $type == 'sidenote' ? 'sn' : 'mn';
        $output = $this->twig->processTemplate(
            'partials/tufte/note.html.twig',
            [
                'type' => $type,
                'wrap' => $wrap ?? null,
                'title' => $title ?? null,
                'content' => $content,
                'id' => $prefix . '-' . $sc->getNamePosition()
            ]
        );
        return $output;
    }
}
