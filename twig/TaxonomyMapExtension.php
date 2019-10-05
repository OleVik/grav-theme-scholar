<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Theme;
use Grav\Theme\Scholar\API\TaxonomyMap;

class TaxonomyMapExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'TaxonomyExtension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('taxonomy_map', [$this, 'getTaxonomyMap']),
            new \Twig_SimpleFunction('taxonomy_map_page', [$this, 'getPageTaxonomyMap']),
            new \Twig_SimpleFunction('taxonomy_map_descendants', [$this, 'getDescendantsTaxonomyMap'])
        ];
    }
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('limit', [$this, 'limit']),
            new \Twig_SimpleFilter('threshold', [$this, 'threshold'])
        ];
    }

    public function getTaxonomyMap()
    {
        include __DIR__ . '/../vendor/autoload.php';
        $TaxonomyMap = new TaxonomyMap();
        return $TaxonomyMap->get();
    }

    public function getPageTaxonomyMap(string $route)
    {
        include __DIR__ . '/../vendor/autoload.php';
        $TaxonomyMap = new TaxonomyMap();
        return $TaxonomyMap->getPage($route);
    }

    public function getDescendantsTaxonomyMap(string $route)
    {
        include __DIR__ . '/../vendor/autoload.php';
        $TaxonomyMap = new TaxonomyMap();
        return $TaxonomyMap->getDescendants($route);
    }

    public function limit(array $list, int $length = 10)
    {
        include __DIR__ . '/../vendor/autoload.php';
        return TaxonomyMap::limit($list, $length);
    }

    public function threshold(array $list, int $threshold = 10)
    {
        include __DIR__ . '/../vendor/autoload.php';
        return TaxonomyMap::threshold($list, $threshold);
    }
}
