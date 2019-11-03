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

use Grav\Common\Theme;
use Grav\Common\Utils;
use Grav\Framework\File\YamlFile;
use Grav\Framework\File\Formatter\YamlFormatter;
use Grav\Theme\Scholar\Utilities;
use Grav\Theme\Scholar\Router;

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
        /* if ($this->isAdmin() && $this->config->get('plugins.admin')) {
            $this->enable(
                [
                    'onTwigSiteVariables' => ['twigBaseUrl', 0],
                    'onAssetsInitialized' => ['onAdminPagesAssetsInitialized', 0]
                ]
            );
        } */
        $this->enable(
            [
                'onPagesInitialized' => ['onPagesInitialized', 0],
                'onPageInitialized' => ['onPageInitialized', 0],
                'onTwigExtensions' => ['onTwigExtensions', 0],
                'onTwigTemplatePaths' => ['templates', 0],
                'onTwigSiteVariables' => ['transportTaxonomyTranslations', 0]
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
        foreach ($this->config->get('theme.components') as $component) {
            $this->grav['twig']->twig_paths[] = $locator->findResource(
                'theme://components/' . $component
            );
        }
    }

    /**
     * Register Schemas dynamically
     *
     * @return void
     */
    public function schemas()
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

    /**
     * Handle API
     *
     * @return void
     */
    public function onPagesInitialized()
    {
        $Router = new Router($this->grav);
        // dump($this->grav['page']->translatedLanguages(true));
    }

    /**
     * Handle current Page
     *
     * @return void
     */
    public function onPageInitialized()
    {
        if ($this->grav['config']->get('theme.linkeddata.enabled')) {
            $call = $this->grav['config']->get(
                'theme.linkeddata.default',
                'themes.scholar.linkeddata.default'
            );
            if ($this->grav['page']->template() == 'cv') {
                $call = $this->grav['config']->get('theme.linkeddata.cv', $call);
            }
            if (isset($this->grav['page']->header()->theme)
                && !empty($this->grav['page']->header()->theme)
            ) {
                $this->grav['config']->set(
                    'theme',
                    array_merge(
                        $this->grav['config']->get('theme'),
                        $this->grav['page']->header()->theme
                    )
                );
            }
            $ld = new $call($this->grav['language']);
            $ld->buildSchema($this->grav['page']);
            $this->grav['assets']->addInlineJs(
                $call::getSchema(
                    $ld->data,
                    key($call::getType($this->grav['page']->template())),
                    true
                ),
                ['type' => 'application/ld+json']
            );
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
            foreach ($dateFormats as $key => $value) {
                $return .= $key . ': "' . $value . '", ';
            }
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
     * Initialize Twig Extensions
     *
     * @return void
     */
    public function onTwigExtensions()
    {
        include_once __DIR__ . '/twig/ScholarTwigExtensions.php';
        $this->grav['twig']->twig->addExtension(new ScholarTwigExtensions());
    }
    
    /**
     * Initialize Shortcodes
     *
     * @return void
     */
    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/shortcodes');
        $locator = $this->grav['locator'];
        foreach ($this->config->get('theme.components') as $component) {
            $this->grav['shortcode']->registerAllShortcodes(
                'theme://components/' . $component . '/shortcodes'
            );
        }
    }
}
