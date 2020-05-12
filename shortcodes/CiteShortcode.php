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
 * Create link overlay for slide
 *
 * Class PresentationPlugin
 *
 * @category Extensions
 * @package  Grav\Plugin
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-theme-scholar
 */
class CiteShortcode extends Shortcode
{
    /**
     * Initialize shortcode
     *
     * @return string
     */
    public function init()
    {
        $this->shortcode->getHandlers()->add(
            'cite',
            function (ShortcodeInterface $sc) {
                return $this->blockquoteCiteRenderer($sc);
            }
        );
    }

    /**
     * Render a sidenote in HTML
     *
     * @param ShortcodeInterface $sc Accessor to Thunder\Shortcode
     *
     * @return string
     */
    public function blockquoteCiteRenderer(ShortcodeInterface $sc)
    {
        $link = $sc->getParameter('link', $sc->getParameter('href', null));
        $content = $sc->getParameter('content', $sc->getContent());
        $output = $this->twig->processTemplate(
            'partials/components/cite.html.twig',
            [
                'link' => $link,
                'content' => $content,
                'id' => 'sn-' . $sc->getNamePosition()
            ]
        );
        return $output;
    }
}
