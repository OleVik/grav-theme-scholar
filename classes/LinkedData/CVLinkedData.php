<?php
/**
 * Scholar Theme, Linked Data for CV
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
use Grav\Theme\Scholar\API\TaxonomyMap;

/**
 * Linked Data for CV
 *
 * @category LinkedData
 * @package  Grav\Theme\Scholar\LinkedData\CVLinkedData
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class CVLinkedData extends AbstractLinkedData
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
        $date = \DateTime::createFromFormat('U', $page->date())->format('Y-m-d H:i:s');
        $header = (array) $page->header();
        $data = [
            'name' => $page->title(),
            'datePublished' => $date,
            'url' => $page->url(true, true, true),
            'inLanguage' => $page->language() ?? $this->language->getDefault()
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
        if ($page->language() !== $this->language->getDefault()) {
            $data['translationOfWork'] = [
                '@type' => key($schema),
                'url' => $page->canonical(false),
                'inLanguage' => $this->language->getDefault()
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
                    $schemaCollection['itemListElement'][] = self::buildSchema($item, true);
                }
                $this->data['mainEntity'][] = $schemaCollection;
            }
        }
    }
}
