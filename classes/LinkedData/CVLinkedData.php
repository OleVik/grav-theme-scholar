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

use Grav\Common\Inflector;
use Grav\Common\Page\Interfaces\PageInterface as Page;
use Grav\Common\Language\Language;

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
     */
    public function __construct(Language $Language)
    {
        $this->data = array();
        $this->language = $Language;
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
            '@id' => $header['basics']['name'] ? '#' . Inflector::hyphenize($header['basics']['name']) : '#' . $page->slug(),
            'datePublished' => $date,
            'url' => $page->url(true, true, true),
            'inLanguage' => $page->language() ?? $this->language->getDefault()
        ];
        if (isset($header['basics']['name'])) {
            $data['name'] = $header['basics']['name'];
        }
        if (isset($header['basics']['label'])) {
            $data['jobTitle'] = $header['basics']['label'];
        }
        if (isset($header['basics']['email'])) {
            $data['email'] = $header['basics']['email'];
        }
        if (isset($header['basics']['phone'])) {
            $data['telephone'] = $header['basics']['phone'];
        }
        if (isset($header['basics']['url'])) {
            $data['url'] = $header['basics']['url'];
        }
        if (isset($header['basics']['description'])) {
            $data['description'] = $header['basics']['description'];
        }
        if (isset($header['basics']['location']) && is_array($header['basics']['location'])) {
            $data['address'] = array('@type' => 'PostalAddress');
            if (isset($header['basics']['location']['address'])) {
                $data['address']['streetAddress'] = $header['basics']['location']['address'];
            }
            if (isset($header['basics']['location']['postal_code'])) {
                $data['address']['postal_code'] = $header['basics']['location']['postal_code'];
            }
            if (isset($header['basics']['location']['city'])) {
                $data['address']['addressLocality'] = $header['basics']['location']['city'];
            }
            if (isset($header['basics']['location']['region'])) {
                $data['address']['addressRegion'] = $header['basics']['location']['region'];
            }
        } elseif (isset($header['basics']['location']) && is_string($header['basics']['location'])) {
            $data['homeLocation'] = $header['basics']['location'];
        }
        if (isset($header['basics']['profiles']) && is_array($header['basics']['profiles'])) {
            $data['contactPoint'] = array();
            foreach ($header['basics']['profiles'] as $profile) {
                $contactPoint = array('@type' => 'ContactPoint');
                if (isset($profile['network']) && is_string($profile['network'])) {
                    $contactPoint['contactType'] = $profile['network'];
                }
                if (isset($profile['username']) && is_string($profile['username'])) {
                    $contactPoint['identifier'] = $profile['username'];
                }
                if (isset($profile['url']) && is_string($profile['url'])) {
                    $contactPoint['url'] = $profile['url'];
                }
                if (count(array_keys($contactPoint)) > 1) {
                    $data['contactPoint'][] = $contactPoint;
                }
            }
        }
        $data['alumniOf'] = array();
        if (isset($header['work'])) {
            $work = self::alumni($header, $data, 'work');
            if (isset($work['worksFor'])) {
                $data['worksFor'] = $work['worksFor'];
            }
            if (isset($work['alumniOf'])) {
                $data['alumniOf'] = array_merge($data['alumniOf'], $work['alumniOf']);
            }
        }
        if (isset($header['volunteer'])) {
            $volunteer = self::alumni($header, $data, 'volunteer');
            if (isset($volunteer['alumniOf'])) {
                $data['alumniOf'] = array_merge($data['alumniOf'], $volunteer['alumniOf']);
            }
        }
        if (isset($header['education'])) {
            $education = self::alumni($header, $data, 'education');
            if (isset($education['alumniOf'])) {
                $data['alumniOf'] = array_merge($data['alumniOf'], $education['alumniOf']);
            }
        }
        if (isset($header['awards'])) {
            $awards = self::award($header, $data, 'awards');
            if (!empty($awards)) {
                $data['award'] = $awards;
            }
        }
        if (isset($header['competencies'])) {
            $competencies = self::competency($header, $data, 'competencies');
            if (!empty($competencies)) {
                $data['knowsAbout'] = $competencies;
            }
        }
        if (isset($header['languages'])) {
            $languages = self::competency($header, $data, 'languages');
            if (!empty($languages)) {
                $data['knowsLanguage'] = $languages;
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
                    $schemaCollection['itemListElement'][] = $this->buildSchema($item, true);
                }
                $this->data['mainEntity'][] = $schemaCollection;
            }
        }
    }

    /**
     * Parse Alumni-data
     *
     * @param array  $header Page-header
     * @param array  $data   Data-container
     * @param string $type   Which header-property to parse
     *
     * @return void|array Alumni-data
     */
    public static function alumni(array $header, array $data, string $type)
    {
        $return = array();
        if (is_array($header[$type]) && count($header[$type]) > 0) {
            if ($type == 'work') {
                if (isset($header[$type][0]) && !empty($header[$type][0])) {
                    $worksFor = array('@type' => 'Organization');
                    if (isset($header[$type][0]['name']) && is_string($header[$type][0]['name'])) {
                        $worksFor['name'] = $header[$type][0]['name'];
                    }
                    if (isset($header[$type][0]['url']) && is_string($header[$type][0]['url'])) {
                        $worksFor['url'] = $header[$type][0]['url'];
                    }
                }
                $return['worksFor'] = $worksFor;
                unset($header[$type][0]);
            }
            if (count($header[$type]) >= 1) {
                foreach ($header[$type] as $item) {
                    $Organization = array('@type' => 'Organization');
                    if (isset($item['name']) && is_string($item['name'])) {
                        $Organization['name'] = $item['name'];
                    }
                    if (isset($item['url']) && is_string($item['url'])) {
                        $Organization['url'] = $item['url'];
                    }
                    if (isset($item['title']) && is_string($item['title'])) {
                        $Organization['employee'] = array(
                            '@type' => 'Person',
                            'sameAs' => $data['@id'],
                            'hasOccupation' => ['@type' => 'OrganizationRole']
                        );
                        $Organization['employee']['hasOccupation']['roleName'] = $item['title'];
                        if (isset($item['start_date']) && is_string($item['start_date'])) {
                            $Organization['employee']['hasOccupation']['start_date'] = $item['start_date'];
                        }
                        if (isset($item['end_date']) && is_string($item['end_date'])) {
                            $Organization['employee']['hasOccupation']['end_date'] = $item['end_date'];
                        }
                        if (isset($item['description']) && is_string($item['description'])) {
                            $Organization['employee']['hasOccupation']['description'] = $item['description'];
                        }
                    }
                    $return['alumniOf'][] = $Organization;
                }
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return;
    }

    /**
     * Parse Award-data
     *
     * @param array  $header Page-header
     * @param array  $data   Data-container
     * @param string $type   Which header-property to parse
     *
     * @return void|string Awards
     */
    public static function award(array $header, array $data, string $type)
    {
        $award = '';
        if (is_array($header[$type]) && count($header[$type]) > 0) {
            foreach ($header[$type] as $item) {
                if (isset($item['title']) && is_string($item['title'])) {
                    $award .= $item['title'];
                }
                if (isset($item['date']) && is_string($item['date'])) {
                    $award .= ' (' . $item['date'] . ')';
                }
                if (isset($item['name']) && is_string($item['name'])) {
                    $award .= ': ' . $item['name'];
                }
                if (isset($item['description']) && is_string($item['description'])) {
                    $award .= ', ' . $item['description'];
                }
                $award .= '; ';
            }
        }
        if (!empty($award)) {
            return rtrim($award, '; ');
        }
        return;
    }

    /**
     * Parse Skills-data
     *
     * @param array  $header Page-header
     * @param array  $data   Data-container
     * @param string $type   Which header-property to parse
     *
     * @return void|string Skills
     */
    public static function competency(array $header, array $data, string $type)
    {
        $competency = '';
        if (is_array($header[$type]) && count($header[$type]) > 0) {
            foreach ($header[$type] as $item) {
                if (isset($item['title']) && is_string($item['title'])) {
                    $competency .= $item['title'];
                }
                if (isset($item['date']) && is_string($item['date'])) {
                    $competency .= ' (' . $item['date'] . ')';
                }
                if (isset($item['level']) && is_string($item['level'])) {
                    $competency .= ' (' . $item['level'] . ')';
                }
                if (isset($item['name']) && is_string($item['name'])) {
                    $competency .= ': ' . $item['name'];
                }
                if (isset($item['description']) && is_string($item['description'])) {
                    $competency .= ', ' . $item['description'];
                }
                if (isset($item['keywords']) && is_array($item['keywords']) && count($item['keywords']) > 0) {
                    $keywords = '';
                    foreach ($item['keywords'] as $keyword) {
                        $keywords .= $keyword . ', ';
                    }
                    $competency .= ': ' . rtrim($keywords, ', ');
                }
                $competency .= '; ';
            }
            if (!empty($competency)) {
                return rtrim($competency, '; ');
            }
        }
        return;
    }
}
