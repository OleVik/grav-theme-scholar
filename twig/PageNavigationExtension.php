<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Inflector;
use Symfony\Component\DomCrawler\Crawler;

class PageNavigationExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'PageNavigationExtension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('page_navigation', [$this, 'build']),
        ];
    }

    /**
     * Manipulate headings and build list
     *
     * @param string $content HTML-content
     *
     * @return array [Manipulated HTML, HTML Ordered List]
     */
    public function build($content)
    {
        include __DIR__ . '/../vendor/autoload.php';
        $headings = array();
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);
        $replacements = $crawler->filter('h1,h2,h3,h4,h5,h6')->each(
            function (Crawler $node, $i) use (&$content, &$headings) {
                $level = (int) str_replace('h', '', $node->nodeName());
                $id = Inflector::hyphenize($node->text());
                $old = '<' . $node->nodeName() . '>' . $node->text() . '</' . $node->nodeName() . '>';
                $new = '<' . $node->nodeName() . '><a name="' . $id . '" href="#' . $id . '">' . $node->text() . '</a>' . '</' . $node->nodeName() . '>';
                $content = str_replace($old, $new, $content);
                $headings[$node->text()] = ['href' => $id, 'level' => $level];
            }
        );
        $headings = self::buildList($headings);
        return (object) ['content' => $content, 'headings' => $headings];
    }
    
    /**
     * Extract headings from HTML
     *
     * @param array $data Array of title => [href, level]
     *
     * @return string HTML ordered list
     */
    public static function buildList($data)
    {
        if (empty($data)) {
            return '';
        }
        $output = '<ol>';
        $keys = array_keys($data);
        foreach (array_keys($keys) as $index) {
            $title = current($keys);
            $properties = $data[$title];
            $href = $data[$title]['href'];
            $level = $data[$title]['level'];
            $nextLevel = $data[next($keys)]['level'] ?? null;
        
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
}
