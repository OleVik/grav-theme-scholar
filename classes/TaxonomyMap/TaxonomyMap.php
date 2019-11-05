<?php
/**
 * Scholar Theme, Taxonomy Map
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\TaxonomyMap
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar\TaxonomyMap;

use Grav\Common\Grav;

/**
 * Taxonomy Map
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\TaxonomyMap\TaxonomyMap
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class TaxonomyMap
{
    /**
     * Instantiate class
     */
    public function __construct()
    {
        $this->grav = Grav::instance();
        $taxonomy = $this->grav['taxonomy']->taxonomy();
        $taxonomy = self::pluralize($taxonomy);
        foreach ($taxonomy as $key => $values) {
            $this->taxonomy[$key] = $values;
        }
    }

    /**
     * Get Taxonomy
     *
     * @param string $type  Type of taxonomy to retrieve
     * @param bool   $array Output as array
     *
     * @return array Taxonomy map
     */
    public function get(string $type = null, bool $array = null): array
    {
        if (empty($this->taxonomy)) {
            return array();
        }
        if ($type && $array) {
            $taxonomies = array();
            foreach (array_keys($this->taxonomy[$type]) as $taxonomy) {
                array_push($taxonomies, ['text' => $taxonomy, 'value' => $taxonomy]);
            }
            return $taxonomies;
        }
        if ($type) {
            return $this->taxonomy[$type];
        } else {
            return $this->taxonomy;
        }
    }

    /**
     * Get Page Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public function getPage(string $route)
    {
        $page = $this->grav['pages']->find($route);
        return self::pluralize($page->taxonomy(), true);
    }

    /**
     * Get Page Descendants Taxonomy
     *
     * @param string $route Route to Page
     *
     * @return array Taxonomy map
     */
    public function getDescendants(string $route): array
    {
        if ($route == '/') {
            $return = array();
            foreach ($this->taxonomy as $key => $entries) {
                foreach ($entries as $name => $items) {
                    $return[$key][$name] = count($items);
                }
                array_multisort(array_values($return[$key]), SORT_DESC, array_keys($return[$key]), SORT_ASC, $return[$key]);
            }
            return $return;
        } else {
            $mode = '@page.descendants';
        }
        $pages = $this->grav['page']->evaluate([$mode => $route]);
        $return = array();
        foreach ($pages->published() as $page) {
            foreach (array_keys($page->taxonomy()) as $key) {
                $return[$key] = array();
                foreach ($page->taxonomy()[$key] as $value) {
                    if (!in_array($value, $return[$key])) {
                        $return[$key][] = $value;
                    }
                }
            }
        }
        $return = self::pluralize($return);
        return $return;
    }

    /**
     * Pluralize taxonomy names
     *
     * @param array   $list   Associated array of taxonomy entries
     * @param boolean $unique Remove duplicates
     *
     * @return array Taxonomy map
     */
    public static function pluralize(array $list, bool $unique = null): array
    {
        if (isset($list['category']) && isset($list['categories'])) {
            $list['categories'] = array_merge_recursive($list['category'], $list['categories']);
            unset($list['category']);
        } elseif (isset($list['category']) && !isset($list['categories'])) {
            $list['categories'] = $list['category'];
            unset($list['category']);
        }
        if (isset($list['tag']) && isset($list['tags'])) {
            $list['tags'] = array_merge_recursive($list['tag'], $list['tags']);
            unset($list['tag']);
        } elseif (isset($list['tag']) && !isset($list['tags'])) {
            $list['tags'] = $list['tag'];
            unset($list['tag']);
        }
        if ($unique) {
            foreach ($list as $key => $values) {
                $list[$key] = array_unique($values);
            }
        }
        return $list;
    }

    /**
     * Limit the length of an array
     *
     * @param array   $list   Array to slice
     * @param integer $length Maximum length
     *
     * @return array Taxonomy map
     */
    public static function limit(array $list, int $length = 10): array
    {
        return array_slice($list, 0, $length, true);
    }

    /**
     * Limit the length of an array
     *
     * @param array   $list      Array to filter
     * @param integer $threshold Minimum value
     *
     * @return array Taxonomy map
     */
    public static function threshold(array $list, int $threshold = 10): array
    {
        return array_filter(
            $list,
            function ($value) use ($threshold) {
                return $value >= $threshold;
            }
        );
    }
}
