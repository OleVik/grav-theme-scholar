<?php
/**
 * Scholar Theme, Linked Data API
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\API
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
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
 * Linked Data API
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\API
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
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
     * Create Schema-structure
     *
     * @param Page $page  Page-instance
     * @param bool $slave If true
     *
     * @return void
     */
    public function buildSchema(Page $page, bool $slave = false)
    {
        $date = $page->date();
        $date = \DateTime::createFromFormat('U', $date)->format('Y-m-d H:i:s');
        $header = (array) $page->header();
        $data = [
            'name' => $page->title(),
            'datePublished' => $date,
            'url' => $page->url(true, true, true)
        ];
        if (!empty(self::getAuthor($header))) {
            $data['author'] = self::getAuthor($header);
        }
        if (!empty(self::getImage($header, $page->media()->all()))) {
            $data['image'] = self::getImage($header, $page->media()->all());
        }
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
        $schema = self::getType($page->template());
        if ($slave) {
            return self::getSchema(
                $data,
                key($schema)
            );
        } else {
            $this->data = self::getSchema(
                $data,
                key($schema)
            );
        }
        if ($schema[key($schema)] === true) {
            $collections = self::getCollections($header);
            foreach ($collections as $collection) {
                if (count($page->collection($collection)) < 1) {
                    continue;
                }
                $schemaCollection = ['@type' => 'ItemList', 'itemListElement' => array()];
                foreach ($page->collection($collection) as $item) {
                    $schemaCollection['itemListElement'][] = self::buildSchema($item, true);
                }
                $this->data['mainEntity'][] = $schemaCollection;
            }
        }
    }

    public static function getAuthor(array $header): array
    {
        $data = array();
        if (isset($header['author']) && is_string($header['author'])) {
            $data['@type'] = 'Person';
            $data['name'] = $header['author'];
        } elseif (Grav::instance()['config']->get('site.author.name') !== null
            && is_string(Grav::instance()['config']->get('site.author.name'))
        ) {
            $data['@type'] = 'Person';
            $data['name'] = Grav::instance()['config']->get('site.author.name');
        }
        return $data;
    }

    public static function getImage(array $header, array $media): array
    {
        $data = array();
        if (isset($header['image']) && is_string($header['image'])) {
            $data['@type'] = 'ImageObject';
            $data['url'] = $media[$header['image']]->url();
        }
        return $data;
    }

    /**
     * Search for named collections in Page FrontMatter
     *
     * @param array $header Page Header
     *
     * @return array
     */
    public static function getCollections(array $header): array
    {
        $collections = self::filterRecursive(
            $header,
            function ($value) {
                if (is_array($value)
                    && array_key_exists('items', $value)
                    && is_array($value['items'])
                    && !empty($value['items'])
                    && is_string(key($value['items']))
                    && is_string($value['items'][key($value['items'])])
                ) {
                    return true;
                }
                return false;
            }
        );
        return array_keys($collections);
    }

    /**
     * Determine Schema type and whether iterable
     *
     * @param string $template Template name to search for
     *
     * @return array ['Template name (string)' => 'Iterable (bool)']
     */
    public static function getType(string $template): array
    {
        $schemaConfig = Grav::instance()['config']->get('theme.schema');
        $schema = self::filterRecursive(
            $schemaConfig['types'],
            function ($value) use ($template) {
                if (is_array($value) && array_key_exists('name', $value) && $value['name'] !== $template) {
                    return false;
                }
                return true;
            }
        );
        $iterable = false;
        if (isset($schema[$template]['children'])) {
            $iterable = true;
        }
        return [$schema[$template]['schema'] ?? $schemaConfig['default'] => $iterable];
    }

    /**
     * Filters the elements of an array recursively, using a given callable
     *
     * Callable function must return a boolean, whether to accept or remove the value
     *
     * @param array    $array    Array to search
     * @param callable $callback Function to call
     *
     * @return array
     *
     * @link https://github.com/lingtalfi/Bat/blob/master/ArrayTool.md#filterrecursive
     */
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
            $Schema->$key($value);
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
            $data[] = self::getSchema($params, $params['@type'], true);
        }
        $return = '';
        foreach ($data as $schema) {
            $return .= $schema . "\n";
        }
        return $return;
    }
}