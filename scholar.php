<?php
/**
 * Scholar Theme
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav
 * @subpackage Scholar
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-theme-scholar
 */
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Theme;
use Grav\Common\Taxonomy;
use Grav\Common\Utils;
use Grav\Common\Inflector;
use Grav\Common\Page\Page;
use Grav\Common\Page\Media;
use Grav\Framework\File\YamlFile;
use Grav\Framework\File\Formatter\YamlFormatter;
use Grav\Plugin\Taxonomylist;
use RocketTheme\Toolbox\Event\Event;
use Scholar\API\Content;
// use Scholar\API\Data;
use Grav\Theme\Scholar\API\TaxonomyMap;
use Grav\Theme\Scholar\API\LinkedData;
use Grav\Theme\Scholar\API\Utilities;
use Grav\Theme\Scholar\API\Router;

/**
 * Scholar Theme
 *
 * Class Scholar
 *
 * @category Extensions
 * @package  Grav\Theme
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class Scholar extends Theme
{
    /**
     * Register intial event and libraries
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        include __DIR__ . '/vendor/autoload.php';
        return [
            'onThemeInitialized' => ['onThemeInitialized', 0],
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0]
        ];
    }

    /**
     * Initialize the theme and events
     *
     * @return void
     */
    public function onThemeInitialized()
    {
        if ($this->config->get('themes.scholar.enabled') != true) {
            return;
        }
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->startTimer('scholar', 'Scholar');
        }
        $this->schemas();
        $this->contentTypes();
        // dump($this->grav['config']->get('system.pages.types'));
        // dump(Utils::getSupportPageTypes());
        /* if ($this->isAdmin() && $this->config->get('plugins.admin')) {
            $this->enable(
                [
                    'onGetPageTemplates' => ['onGetPageTemplates', 0],
                    'onTwigSiteVariables' => ['twigBaseUrl', 0],
                    'onAssetsInitialized' => ['onAdminPagesAssetsInitialized', 0]
                ]
            );
        } */
        $this->enable(
            [
                'onPagesInitialized' => ['handleAPI', 0],
                'onPageInitialized' => ['onPageInitialized', 0],
                'onPageContentProcessed' => ['onPageContentProcessed', 0],
                'onTwigExtensions' => ['onTwigExtensions', 0],
                'onTwigTemplatePaths' => ['templates', 0],
                'onTwigSiteVariables' => ['transportTaxonomyTranslations', 0],
                'onGetPageTemplates' => [
                    ['onGetPageTemplates', 0]
                ],
            ]
        );
        $this->schemas();
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->stopTimer('scholar');
        }
    }

    /**
     * Register templates dynamically
     *
     * @return void
     */
    public function templates()
    {
        $locator = $this->grav['locator'];
        foreach ($this->config->get('themes.scholar.components') as $component) {
            $this->grav['twig']->twig_paths[] = $locator->findResource(
                'theme://components/' . $component
            );
        }
    }

    /**
     * Register custom content types
     *
     * @return void
     */
    public function contentTypes(): void
    {
        $contentTypes = $this->grav['config']->get('system.pages.types');
        $contentTypes[] = 'print';
        $this->grav['config']->set(
            'system.pages.types',
            $contentTypes
        );
    }

    /**
     * Register Schemas dynamically
     *
     * @return void
     */
    public function schemas(): void
    {
        $locator = $this->grav['locator'];
        $formatter = new YamlFormatter;
        $target = Utilities::fileFinder(
            'schema.yaml',
            [
                'theme://components',
                'user://themes/scholar/components'
            ]
        );
        $file = $locator->findResource(
            $target,
            true,
            true
        );
        $YamlFile = new YamlFile(
            $file,
            $formatter
        );
        $data = $YamlFile->load();
        if (isset($data['default'])) {
            $this->grav['config']->set('theme.schema.default', $data['default']);
        }
        if (isset($data['types'])) {
            foreach ($data['types'] as $schema => $data) {
                $this->grav['config']->set('theme.schema.types.' . $schema, $data);
            }
        }
        foreach ($this->config->get('themes.scholar.components') as $component) {
            $target = Utilities::fileFinder(
                'schema.yaml',
                [
                    'theme://components/' . $component,
                    'user://themes/scholar/components/' . $component
                ]
            );
            $file = $locator->findResource(
                $target,
                true,
                true
            );
            if (file_exists($file)) {
                $YamlFile = new YamlFile(
                    $file,
                    $formatter
                );
                foreach ($YamlFile->load() as $schema => $data) {
                    $this->grav['config']->set('theme.schema.types.' . $schema, $data);
                }
            }
        }
    }

    public function onGetPageTemplates(Event $event)
    {
        $types = $event->types;
        $types->register('search');
    }

    /**
     * Handle API
     *
     * @return void
     */
    public function handleAPI(Event $event)
    {
        // $grav = $this->grav;
        // dump($grav);
        $uri = $this->grav['uri'];
        // dump($uri);
        // $page = $this->grav['page'];
        // dump($page);
        // $pages = $this->grav['pages'];
        // dump($pages);
        // $config = $this->grav['config'];
        // dump($config);
        $Router = new Router($this->grav);
        // if ($uri->path() == $this->config->get('themes.scholar.routes.search')) {
        //     $this->handleSearchPage($event);
        //     // Router::handleSearch($this->grav['pages'], $this->config);
        // } elseif ($uri->path() == $this->config->get('themes.scholar.routes.data')) {
        //     $this->handleDataAPI();
        // }
    }

    /**
     * Handle Data API
     *
     * @return void
     */
    public function handleDataAPI()
    {
        try {
            include __DIR__ . '/classes/Data.php';
            $ld = new Data();
            $ld->buildIndex('/learn');
            header('Content-Type: application/json');
            header("allow-control-access-origin: * ");
            header('HTTP/1.1 200 OK');
            echo json_encode($ld->index);
        } catch (\Exception $e) {
            echo $e;
        }
        exit();
    }

    public function onPageInitialized()
    {
        // dump('$this->grav[\'page\']->taxonomy()');
        // dump((array) $this->grav['page']->taxonomy());
        // dump(Utils::arrayFlattenDotNotation((array) $this->grav['page']->taxonomy()));

        // $ld = new LinkedData();
        // $ld->buildSchema($this->grav['page']);
        // dump($ld->data);
    }

    public function onPageContentProcessed()
    {
        // $contentTypes = $this->grav['config']->get('system.pages.types');
        // $contentTypes[] = 'print';
        // $this->grav['config']->set(
        //     'system.pages.types',
        //     $contentTypes
        // );
        // dump($this->grav['config']->get('system.pages.types'));
    }

    /**
     * Get Taxonomy
     *
     * @param string  $type  Type of taxonomy to retrieve
     * @param boolean $array Output as array
     *
     * @return void
     */
    public static function getTaxonomy($type = false, $array = false)
    {
        $taxonomylist = new Taxonomylist();
        if ($type && $array) {
            $taxonomies = array();
            foreach (array_keys($taxonomylist->get()[$type]) as $taxonomy) {
                array_push($taxonomies, ['text' => $taxonomy, 'value' => $taxonomy]);
            }
            return $taxonomies;
        }
        if ($type) {
            return $taxonomylist->get()[$type];
        } else {
            return $taxonomylist->get();
        }
    }

    /**
     * Creates page-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Reserved collection-mode for handling child-pages
     * @param integer $depth Reserved placeholder for recursion depth
     *
     * @return array Page-structure with children
     */
    public static function buildTree(string $route, $mode = false, $depth = 0)
    {
        $page = Grav::instance()['page'];
        $depth++;
        $mode = '@page.self';
        if ($depth > 1) {
            $mode = '@page.children';
        }
        $pages = $page->evaluate([$mode => $route]);
        $pages = $pages->published()->order('date', 'desc');
        $paths = array();
        foreach ($pages as $page) {
            $route = $page->rawRoute();
            $paths[$route]['depth'] = $depth;
            $paths[$route]['title'] = $page->title();
            $paths[$route]['route'] = $route;
            $paths[$route]['template'] = $page->template();
            if (!empty($paths[$route])) {
                $children = self::buildTree($route, $mode, $depth);
                if (!empty($children)) {
                    $paths[$route]['children'] = $children;
                }
            }
            $media = new Media($page->path());
            foreach ($media->all() as $filename => $file) {
                $paths[$route]['media'][$filename] = $file->items()['type'];
            }
        }
        if (!empty($paths)) {
            return $paths;
        } else {
            return null;
        }
    }

    /**
     * Add taxonomy to Twig variables
     *
     * @return void
     */
    public function transportTaxonomyTranslations()
    {
        $this->grav['twig']->twig_vars['site_taxonomy'] = $this->config->get('site.taxonomies');
        $language = $this->grav['language']->getActive() ?? 'en';
        $dateFormats = $this->config->get('system.pages.dateformat');
        $dateFormatsJS = function () use ($dateFormats) {
            $return = '';
            foreach ($dateFormats as $key => $value)
                $return .= $key . ': "' . $value . '", ';
            return rtrim($return, ", ");
        };
        $locator = $this->grav['locator'];
        $formatter = new YamlFormatter;
        $file = new YamlFile($locator->findResource('theme://languages.yaml', true, true), $formatter);
        $translationStrings = array();
        foreach (array_keys(Utils::arrayFlattenDotNotation($file->load())) as $key) {
            $key = str_replace('en.', '', $key);
            $translationStrings[$key] = $this->grav['language']->translate([$key]);
        }
        if ($this->grav['page']->template() == 'search') {
            $searchRoute = $this->grav['page']->url(true, true, true);
        } else {
            $searchRoute = $this->grav['uri']->rootUrl(true) . $this->config->get('themes.scholar.routes.search');
        }
        $this->grav['assets']->addInlineJs(
            'const systemLanguage = "' . $language . '";' . "\n" .
            'const systemDateformat = {' . $dateFormatsJS() . '};' . "\n" .
            'const siteTaxonomy = [\'' . implode("','", $this->config->get('site.taxonomies')) . '\'];' . "\n" .
            'const searchRoute = "' . $searchRoute . '";' . "\n" .
            'const ScholarTranslation = ' . json_encode(Utils::arrayUnflattenDotNotation($translationStrings)['THEME_SCHOLAR']) . ';'
        );
    }

    /**
     * Add Twig Extensions
     *
     * @return void
     */
    public function onTwigExtensions()
    {
        // include_once __DIR__ . '/twig/PageNavigationExtension.php';
        // $this->grav['twig']->twig->addExtension(new PageNavigationExtension());
        // include_once __DIR__ . '/twig/ScholarMenuExtension.php';
        // $this->grav['twig']->twig->addExtension(new ScholarMenuExtension());
        // include_once __DIR__ . '/twig/ScholarUtilitiesExtension.php';
        // $this->grav['twig']->twig->addExtension(new ScholarUtilitiesExtension());
        // include_once __DIR__ . '/twig/TruncateWordsExtension.php';
        // $this->grav['twig']->twig->addExtension(new TruncateWordsExtension());
        // include_once __DIR__ . '/twig/StripHTMLExtension.php';
        // $this->grav['twig']->twig->addExtension(new StripHTMLExtension());
        // include_once __DIR__ . '/twig/SectionWrapperExtension.php';
        // $this->grav['twig']->twig->addExtension(new SectionWrapperExtension());
        // include_once __DIR__ . '/twig/TaxonomyMapExtension.php';
        // $this->grav['twig']->twig->addExtension(new TaxonomyMapExtension());
        include_once __DIR__ . '/twig/ScholarTwigExtensions.php';
        $this->grav['twig']->twig->addExtension(new ScholarTwigExtensions());
    }
    
    /**
     * Initialize shortcodes
     *
     * @return void
     */
    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/shortcodes');
        $locator = $this->grav['locator'];
        foreach ($this->config->get('themes.scholar.components') as $component) {
            $this->grav['shortcode']->registerAllShortcodes(
                'theme://components/' . $component . '/shortcodes'
            );
        }
    }
}
