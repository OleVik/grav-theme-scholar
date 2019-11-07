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
use Grav\Common\Utils;
use Grav\Framework\File\YamlFile;
use Grav\Framework\File\Formatter\YamlFormatter;
use Grav\Theme\Scholar\Content;
use Grav\Theme\Scholar\LinkedData;
use Grav\Theme\Scholar\Router;
use Grav\Theme\Scholar\Source;
use Grav\Theme\Scholar\TaxonomyMap;
use Grav\Theme\Scholar\Timer;
use Grav\Theme\Scholar\Utilities;
use Grav\Theme\Scholar\Autoload;

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
        $this->autoload();
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->startTimer('scholar', 'Scholar');
        }
        if ($this->isAdmin() && $this->config->get('plugins.admin')) {
            // $this->enable(
            //     [
            //         'onTwigSiteVariables' => ['twigBaseUrl', 0],
            //         'onAssetsInitialized' => ['onAdminPagesAssetsInitialized', 0]
            //     ]
            // );
        }
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
     * Get enabled components
     *
     * @return array
     */
    public static function getComponentsBlueprint(): array
    {
        $componentFolders = Utilities::foldersFinder(
            [
                'theme://components',
                'user://themes/scholar/components'
            ]
        );
        $components = array();
        foreach (array_unique($componentFolders) as $component) {
            $components[] = [
                'text' => ucfirst($component),
                'value' => $component
            ];
        }
        return $components;
    }
    /**
     * Handle API
     *
     * @return void
     */
    public function onPagesInitialized()
    {
        $Router = self::getInstance(
            $this->config->get(
                'theme.api.router',
                'Router\Router'
            ),
            $this->grav
        );
        // dump($this->grav['page']->translatedLanguages(true));
    }

    /**
     * Handle current Page
     *
     * @return void
     */
    public function onPageInitialized()
    {
        if ($this->grav['config']->get('theme.linked_data')) {
            $call = self::getInstance(
                $this->grav['config']->get(
                    'theme.api.linked_data.default',
                    'LinkedData\PageLinkedData'
                ),
                $this->grav['language'],
                $this->grav['config']
            );
            if ($this->grav['page']->template() == 'cv') {
                $call = self::getInstance(
                    $this->grav['config']->get(
                        'theme.api.linked_data.cv',
                        'LinkedData\CVLinkedData'
                    ),
                    $this->grav['language'],
                    $this->grav['config']
                );
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
            // $ld = new $call($this->grav['language']);
            $call->buildSchema($this->grav['page']);
            $this->grav['assets']->addInlineJs(
                $call::getSchema(
                    $call->data,
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
     * Register APIs
     *
     * @return void
     */
    public function autoload()
    {
        new Autoload(
            self::FQCN(),
            Utils::arrayFlatten($this->config->get('theme.api'))
        );
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
            $target = Utilities::folderFinder(
                $component,
                [
                    'theme://components',
                    'user://themes/scholar/components'
                ]
            );
            $this->grav['twig']->twig_paths[] = $locator->findResource($target);
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

    /**
     * Get Fully Qualified Class Name
     *
     * @return string FQCN
     */
    public static function FQCN(): string
    {
        return __CLASS__;
    }

    /**
     * Get class instance
     *
     * @param string $class   Class name
     * @param mixed  ...$args Class arguments
     *
     * @return mixed Class instance
     */
    public static function getInstance(string $class, ...$args)
    {
        $caller = '\\' . self::FQCN() . '\\' . $class;
        return new $caller(...$args);
    }

    /**
     * Get class names for blueprints
     *
     * @param string $key Needle to search for
     *
     * @return array Blueprint-friendly list of class names
     */
    public static function getClassNames(string $key)
    {
        $regex = '/Grav\\\\Theme\\\\Scholar\\\\(?<api>.*)/i';
        $classes = preg_grep($regex, get_declared_classes());
        $matches = preg_grep('/' . $key . '/i', $classes);
        $options = [
            '' => 'None'
        ];
        foreach ($matches as $match) {
            if (Utils::contains($match, 'Abstract') || Utils::contains($match, 'Interface')) {
                continue;
            }
            $match = str_replace(self::FQCN() . '\\', '', $match);
            $options[$match] = $match;
        }
        return $options;
    }
}
