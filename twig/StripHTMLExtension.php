<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Inflector;

class StripHTMLExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'StripHTMLExtension';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('strip_html_tags', [$this, 'stripHTML']),
        ];
    }

    /**
     * Remove given HTML tags
     *
     * @param string $content HTML-content
     * @param mixed  $tags    Tags to strip, comma-separated
     *
     * @return string Manipulated HTML, UTF-8 encoded
     */
    public function stripHTML(string $content, $tags)
    {
        if (strlen($content) < 1) {
            return;
        }
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $doc = new \DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        foreach ($tags as $element) {
            $elements = $doc->getElementsByTagName($element);
            for ($i = $elements->length; --$i >= 0;) {
                $target = $elements->item($i);
                $target->parentNode->removeChild($target);
            }
        }
        $node = $doc->getElementsByTagName('body')[0];
        return self::innerHTML($node, false);
    }

    /**
     * Get innerHTML of a DOM node
     *
     * @param \DOMNode $node DOMDocument node
     * @param boolean  $wrap Include target tag
     *
     * @see https://stackoverflow.com/a/53740544
     *
     * @return void
     */
    public static function innerHTML(\DOMNode $node, $wrap = true)
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($node, true));
        $html = trim($doc->saveHTML());
        if ($wrap) {
            return $html;
        }
        return preg_replace('@^<' . $node->nodeName . '[^>]*>|</' . $node->nodeName . '>$@', '', $html);
    }
}
