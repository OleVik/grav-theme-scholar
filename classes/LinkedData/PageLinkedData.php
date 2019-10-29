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

use Grav\Common\Page\Page;
use Grav\Common\Language\Language;

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
     * @param string   $orderBy  Property to order by.
     * @param string   $orderDir Direction to order.
     */
    public function __construct(Language $Language, $orderBy = 'date', $orderDir = 'desc')
    {
        $this->data = array();
        $this->language = $Language;
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
        if (!empty(parent::getAuthor($header))) {
            $data['author'] = parent::getAuthor($header);
        }
        if (!empty(parent::getImage($header, $page->media()->all()))) {
            $data['image'] = parent::getImage($header, $page->media()->all());
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
        $schema = parent::getType($page->template());
        if ($page->language() !== $this->language->getDefault()) {
            $data['translationOfWork'] = [
                '@type' => key($schema),
                'url' => $page->canonical(false),
                'inLanguage' => $this->language->getDefault()
            ];
        }
        if ($slave) {
            return parent::getSchema(
                $data,
                key($schema)
            );
        } else {
            $this->data = parent::getSchema(
                $data,
                key($schema)
            );
        }
        if ($schema[key($schema)] === true) {
            $collections = parent::getCollections($header);
            foreach ($collections as $collection) {
                if (count($page->collection($collection)) < 1) {
                    continue;
                }
                $schemaCollection = ['@type' => 'ItemList', 'itemListElement' => array()];
                foreach ($page->collection($collection) as $item) {
                    $schemaCollection['itemListElement'][] = parent::buildSchema($item, true);
                }
                $this->data['mainEntity'][] = $schemaCollection;
            }
        }
    }
}
