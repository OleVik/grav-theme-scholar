<?php
namespace Grav\Theme\Scholar\API;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Utils;
use Grav\Common\Page\Page;
use Grav\Common\Page\Media;
use Grav\Common\Page\Header;
use RocketTheme\Toolbox\Event\Event;

require __DIR__ . '/../vendor/autoload.php';
use Spatie\SchemaOrg\Schema;

/**
 * LinkedData API
 */
class LinkedData
{
    public $data;
    public $index;

    /**
     * Initialize class
     *
     * @param string $orderBy  Property to order by.
     * @param string $orderDir Direction to order.
     */
    public function __construct($orderBy = 'date', $orderDir = 'desc')
    {
        $this->data = array();
        $this->index = array();
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
    }

    /**
     * Create Schema-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Placeholder for operation-mode, private.
     * @param integer $depth Placeholder for recursion depth, private.
     *
     * @return array Page-structure with children and media
     */
    public function buildTree($route, $mode = false, $depth = 0)
    {
        $page = Grav::instance()['page'];
        $depth++;
        $mode = '@page.self';
        if ($depth > 1) {
            $mode = '@page.children';
        }
        $pages = $page->evaluate([$mode => $route]);
        $pages = $pages->published()->order($this->orderBy, $this->orderDir);
        foreach ($pages as $page) {
            $route = $page->rawRoute();
            $date = $page->date();
            $date = \DateTime::createFromFormat('U', $date)->format('Y-m-d H:i:s');
            $header = $page->find($route)->header();
            $header = $page->toArray($header)['header'];
            $header['date'] = $date;
            $header['url'] = $page->url(true, true, true);
            if ($page->children() !== null) {
                $this->buildTree($route, $mode, $depth);
            }
            if (!isset($header['image'])) {
                if (!empty($page->media()->images())) {
                    $header['image'] = key($page->media()->images());
                }
            }
            if (count($page->children()) < 1) {
                $this->data[$route] = $header;
            }
        }
    }

    /**
     * Create Schema-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Placeholder for operation-mode, private.
     * @param integer $depth Placeholder for recursion depth, private.
     *
     * @return array Page-structure with children and media
     */
    public function buildIndex($route, $mode = false, $depth = 0)
    {
        $page = Grav::instance()['page'];
        $depth++;
        $mode = '@page.self';
        if ($depth > 1) {
            $mode = '@page.children';
        }
        $pages = $page->evaluate([$mode => $route]);
        $pages = $pages->published()->order($this->orderBy, $this->orderDir);
        foreach ($pages as $page) {
            $route = $page->rawRoute();
            $date = $page->date();
            $date = \DateTime::createFromFormat('U', $date)->format('c');
            $header = $page->find($route)->header();
            $header = $page->toArray($header)['header'];
            $header['date'] = $date;
            $header['url'] = $page->url(true, true, true);
            if ($page->children() !== null) {
                $this->buildIndex($route, $mode, $depth);
            }
            if (!isset($header['image'])) {
                if (!empty($page->media()->images())) {
                    $header['image'] = key($page->media()->images());
                }
            }
            if (count($page->children()) < 1) {
                $this->index[] = (object) $header;
            }
        }
    }

    public function buildSchema(Page $page)
    {
        $date = $page->date();
        $date = \DateTime::createFromFormat('U', $date)->format('Y-m-d H:i:s');
        $header = (array) $page->header();
        $data = [
            'name' => $page->title(),
            'datePublished' => $date,
            'url' => $page->url()
        ];
        
        // dump(Grav::instance()['config']->get('themes.scholar.schema'));
        $c = self::filterRecursive(
            Grav::instance()['config']->get('themes.scholar.schema'),
            function ($value) {
                if (is_array($value) && array_key_exists('name', $value) && 'blog' === $value['name']) {
                    dump($value['schema']);
                    return $value['schema'];
                }
                return false;
            }
        );
        $this->data = $c;
        return;

        if (isset($header['taxonomy'])) {
            $taxonomy = TaxonomyMap::pluralize($header['taxonomy']);
            if (isset($taxonomy['categories'])) {
                if (is_array($taxonomy['categories'])) {
                    $taxonomy['categories'] = implode(",", $taxonomy['categories']);
                }
                $data['articleSection'] = $taxonomy['categories'];
            }
            if (isset($taxonomy['tags'])) {
                if (is_array($taxonomy['tags'])) {
                    $taxonomy['tags'] = implode(",", $taxonomy['tags']);
                }
                $data['keywords'] = $taxonomy['tags'];
            }
        }
        $this->data = self::getSchema(
            $data,
            'Article'
        );
    }

    public static function determineType(string $template): string
    {
        $config = Grav::instance()['config']->get('themes.scholar.schema');
    }

    public static function filterRecursive(array $array, callable $callback): array
    {
        foreach ($array as $k => $v) {
            $res = call_user_func($callback, $v);
            if (false === $res) {
                unset($array[$k]);
            } else {
                if (is_array($v)) {
                    $array[$k] = self::filterRecursive($v, $callback);
                }
            }
        }
        return $array;
    }

    /**
     * Build Schema/JsonLD data
     *
     * @param array   $options Page data.
     * @param string  $type    Type of Schema.
     * @param boolean $script  Return as JavaScript, default false.
     *
     * @return array|string
     */
    public static function getSchema(array $options, string $type, $script = false)
    {
        $Schema = Schema::$type();
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $value = implode(",", $value);
            }
            $Schema->$key((string) $value);
        }
        if ($script) {
            $Schema = $Schema->toScript();
            $Schema = str_replace('<script type="application/ld+json">', '', $Schema);
            $Schema = str_replace('</script>', '', $Schema);
            return $Schema;
        }
        return $Schema->toArray();
    }

    /**
     * Aggregate Schema/JsonLD data
     *
     * @return string
     */
    public function getSchemas()
    {
        $data = array();
        foreach ($this->data as $route => $params) {
            if (isset($params['type'])) {
                $type = $params['type'];
                unset($params['type']);
                $data[] = LinkedData::getSchema($params, $type, true);
            } else {
                $data[] = LinkedData::getSchema($params, 'Event', true);
            }
        }
        $return = '';
        foreach ($data as $schema) {
            $return .= $schema . "\n";
        }
        return $return;
    }
}