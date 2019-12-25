<?php
/**
 * Scholar Theme, Linked Data Abstract
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\LinkedData
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\LinkedData;

use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Language\Language;
use Spatie\SchemaOrg\Schema;
use Grav\Theme\Scholar\Utilities;
use Grav\Theme\Scholar\LinkedData\LinkedDataInterface;

/**
 * Linked Data Abstract
 *
 * @category LinkedData
 * @package  Grav\Theme\Scholar\LinkedData\AbstractLinkedData
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
abstract class AbstractLinkedData implements LinkedDataInterface
{
    /**
     * Create Schema-structure
     *
     * @param Page $page  Page-instance
     * @param bool $slave If true
     *
     * @return void
     */
    abstract public function buildSchema(Page $page, bool $slave = false);

    /**
     * Get Page author
     *
     * @param array $header Page header
     *
     * @return array Author data
     */
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

    /**
     * Get Page image
     *
     * @param array $header Page header
     * @param array $media  Page media
     *
     * @return array Page Image
     */
    public static function getImage(array $header, array $media): array
    {
        $data = array();
        if (isset($header['image'])
            && is_string($header['image'])
            && !empty($header['image'])
            && isset($media[$header['image']])
        ) {
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
        $collections = Utilities::filterRecursive(
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
        $schema = Utilities::filterRecursive(
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
    public function getSchemas(): string
    {
        $data = array('@context' => 'http://schema.org');
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
