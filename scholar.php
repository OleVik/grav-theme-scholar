<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Theme;
use Grav\Common\Utils;
use Grav\Common\Inflector;
use Grav\Common\Page\Page;
use Grav\Common\Page\Media;
use Grav\Framework\File\YamlFile;
use Grav\Framework\File\Formatter\YamlFormatter;
use Grav\Plugin\Taxonomylist;
use RocketTheme\Toolbox\Event\Event;
// use Scholar\API\Content;
// use Scholar\API\Data;
use Grav\Theme\Scholar\API\TaxonomyMap;
use Grav\Theme\Scholar\API\LinkedData;

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
        // dump(new \SplFileInfo(GRAV_ROOT . '/system/pages/notfound.md'));
        // dump(new \SplFileInfo(__FILE__));
        // dump(new \SplFileObject(__FILE__));
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->startTimer('scholar', 'Scholar');
        }
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
                // 'onPageInitialized' => ['pagePreCache', 0],
                'onPagesInitialized' => ['handleSearchPage', 0],
                // 'onPagesInitialized' => ['handleAPI', 0],
                // 'onPageNotFound' => ['handleSearchPage', 0],
                'onPageInitialized' => ['onPageInitialized', 0],
                'onTwigExtensions' => ['onTwigExtensions', 0],
                'onTwigTemplatePaths' => ['templates', 0],
                'onTwigSiteVariables' => ['transportTaxonomyTranslations', 0],
                'onGetPageTemplates' => [
                    ['onGetPageTemplates', 0]
                ],
                // 'onAssetsInitialized' => ['onAssetsInitialized', 0],
                // 'onShutdown' => ['onShutdown', 0]
            ]
        );
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->stopTimer('scholar');
        }
    }

    public function templates() {
        // dump($this->config->get('themes.scholar'));
        $locator = $this->grav['locator'];
        foreach ($this->config->get('themes.scholar.schema') as $component) {
            $this->grav['twig']->twig_paths[] = $locator->findResource('theme://templates/components/' . $component['name']);
        }
        // dump($this->grav['twig']->twig_paths);
        
        // $array = Utils::arrayFilterRecursive(
        //     (array) $this->config->get('themes.scholar.schema'),
        //     function ($k, $v) {
        //         return $v['name'] == 'book';
        //     }
        // );
        // dump($array);
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
        $uri = $this->grav['uri'];
        $page = $this->grav['page'];
        dump($page->template());
        $config = $this->config->get('themes.scholar');
        if ($uri->path() == $config['routes']['search']) {
            $this->handleSearchPage($event);
        } elseif ($uri->path() == $config['routes']['data']) {
            $this->handleDataAPI();
        }
    }

    /**
     * Handle Search Page
     *
     * @return void
     */
    public function handleSearchPage(Event $event)
    {
        $pages = $this->grav['pages'];
        $page = $pages->dispatch($this->config->get('themes.scholar.routes.search', '/search'));
        if (!$page) {
            $page = new Page();
            $page->init(new \SplFileInfo(__DIR__ . '/pages/search.md'));
            $page->slug(basename($this->config->get('themes.scholar.routes.search', '/search')));
            $pages->addPage($page, $this->config->get('themes.scholar.routes.search', '/search'));
        }

        // $output = $this->grav['twig']->processTemplate(
        //     'search.html.twig',
        //     [
        //         'content' => 'contentio',
        //         'page' => $page ?? null
        //     ]
        // );
        // echo $output;
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
        // dump((array) $this->grav['page']->header());
        $ld = new LinkedData();
        $ld->buildSchema($this->grav['page']);
        dump($ld->data);
        // $taxonomy = new TaxonomyMap();
        // dump(Grav::instance()['taxonomy']);
        // Grav::instance()['debugger']->addMessage(Grav::instance()['taxonomy']);
        // Grav::instance()['debugger']->addMessage(self::getTaxonomy());
        // Grav::instance()['debugger']->addMessage(self::getTaxonomy('tags'));
        // Grav::instance()['debugger']->addMessage(self::getTaxonomy('tags', true));
        // Grav::instance()['debugger']->addMessage('result: ' . self::getDocsRoot($route));
    }

    public static function getMenuRoute(string $route, string $type)
    {
        $page = Grav::instance()['page']->find($route);
        if ($page->template() == $type) {
            return $page->rawRoute();
        } else {
            return self::getMenuRoute($page->parent()->rawRoute(), $type);
        }
        return false;
    }

    public static function getRootTemplate(string $route)
    {
        $page = Grav::instance()['page']->find($route);
        $parent = $page->topParent();
        if ($parent) {
            return $parent->template();
        }
        return false;
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
        include_once __DIR__ . '/twig/PageNavigationExtension.php';
        $this->grav['twig']->twig->addExtension(new PageNavigationExtension());
        include_once __DIR__ . '/twig/ScholarMenuExtension.php';
        $this->grav['twig']->twig->addExtension(new ScholarMenuExtension());
        include_once __DIR__ . '/twig/ScholarUtilitiesExtension.php';
        $this->grav['twig']->twig->addExtension(new ScholarUtilitiesExtension());
        include_once __DIR__ . '/twig/TruncateWordsExtension.php';
        $this->grav['twig']->twig->addExtension(new TruncateWordsExtension());
        include_once __DIR__ . '/twig/StripHTMLExtension.php';
        $this->grav['twig']->twig->addExtension(new StripHTMLExtension());
        include_once __DIR__ . '/twig/SectionWrapperExtension.php';
        $this->grav['twig']->twig->addExtension(new SectionWrapperExtension());
        include_once __DIR__ . '/twig/TaxonomyMapExtension.php';
        $this->grav['twig']->twig->addExtension(new TaxonomyMapExtension());
    }
    
    /**
     * Initialize shortcodes
     *
     * @return void
     */
    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/shortcodes');
    }
}
