<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Inflector;
use Symfony\Component\DomCrawler\Crawler;

class SectionWrapperExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'SectionWrapperExtension';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('section_wrapper', [$this, 'wrapHTML']),
        ];
    }

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
    public function wrapHTML(string $content, string $wrapperTag, array $targetTags)
    {
        $doc = new \DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        $xpath = new \DOMXpath($doc);
        libxml_clear_errors();
        $segments = array();
        $wrapper = null;
        $elements = $xpath->query('//' . implode(' | //', $targetTags));
        foreach ($elements as $element) {
            $nodes = array($element);
            for ($next = $element->nextSibling; $next && $next->nodeName != $element->nodeName; $next = $next->nextSibling) {
                $nodes[] = $next;
            }
            $wrapper = $doc->createElement($wrapperTag);
            $element->parentNode->replaceChild($wrapper, $element);
            foreach ($nodes as $node) {
                $wrapper->appendChild($node);
            }
            $segments[] = $element->nodeValue;
        }
        $node = $doc->getElementsByTagName('body')[0];
        return self::innerHTML($node, false);
    }
    
    /**
     * Extract headings from HTML
     *
     * @param array $docata Array of title => [href, level]
     *
     * @return string HTML ordered list
     */
    public static function buildList($docata)
    {
        if (empty($docata)) {
            return '';
        }
        $output = '<ol>';
        $keys = array_keys($docata);
        foreach (array_keys($keys) as $index) {
            $title = current($keys);
            $properties = $docata[$title];
            $href = $docata[$title]['href'];
            $level = $docata[$title]['level'];
            $nextLevel = $docata[next($keys)]['level'] ?? null;
        
            if ($nextLevel > $level) {
                $output .= '<li><a href="#' . $href . '">' . $title . '</a><ol>';
            } else {
                $output .= '<li><a href="#' . $href . '">' . $title . '</a></li>';
            }
            if ($nextLevel < $level) {
                $output .= '</ol></li>';
            }
        }
        $output .= '</ol>';
        return $output;
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
