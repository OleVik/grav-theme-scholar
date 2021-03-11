<?php

/**
 * Scholar Theme, Linked Data for Page
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

use Grav\Theme\Scholar;
use Grav\Common\Page\Interfaces\PageInterface as Page;
use Grav\Common\Language\Language;
use Grav\Common\Config\Config;

/**
 * Linked Data for Page
 *
 * @category LinkedData
 * @package  Grav\Theme\Scholar\LinkedData\PageLinkedData
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class PageLinkedData extends AbstractLinkedData
{
    public $data;

    /**
     * Initialize class
     *
     * @param Language $Language Language-instance.
     * @param Config   $Config   Config-instance.
     */
    public function __construct(Language $Language, Config $Config)
    {
        $this->data = array();
        $this->Language = $Language;
        if (empty($this->Language->getLanguages())) {
            $this->Language->setLanguages(['en']);
        }
        if (!$this->Language->getDefault()) {
            $this->Language->setDefault('en');
        }
        $this->TaxonomyMap = Scholar::getInstance(
            $Config->get(
                'theme.api.taxonomy_map',
                'TaxonomyMap\TaxonomyMap'
            )
        );
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
        $date = \DateTime::createFromFormat('U', $page->date())->format('Y-m-d H:i:s');
        $header = (array) $page->header();
        $data = [
            'name' => $page->title(),
            'datePublished' => $date,
            'url' => $page->url(true, true, true),
            'inLanguage' => $page->language(),
            'accessibilityAPI' => 'ARIA',
            'accessibilityFeature' => [
                'highContrastDisplay/CSSEnabled',
                'bookmarks'
            ],
            'accessibilityControl' => [
                'fullKeyboardControl',
                'fullMouseControl',
                'fullTouchControl'
            ]
        ];
        if (!empty(self::getAuthor($header))) {
            $data['author'] = self::getAuthor($header);
        }
        if (!empty(self::getImage($header, $page->media()->all()))) {
            $data['image'] = self::getImage($header, $page->media()->all());
        }
        if (isset($header['taxonomy'])) {
            $taxonomy = $this->TaxonomyMap::pluralize($header['taxonomy']);
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
        if ($page->language() !== $this->Language->getDefault()) {
            $data['translationOfWork'] = [
                '@type' => key($schema),
                'url' => $page->canonical(false),
                'inLanguage' => $this->Language->getDefault()
            ];
        }
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
                    $schemaCollection['itemListElement'][] = $this->buildSchema($item, true);
                }
                $this->data['mainEntity'][] = $schemaCollection;
            }
        }
    }
}
