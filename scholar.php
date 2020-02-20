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
use Grav\Common\Inflector;
use Grav\Framework\File\YamlFile;
use Grav\Framework\File\Formatter\YamlFormatter;
use Grav\Theme\Scholar\Content;
use Grav\Theme\Scholar\LinkedData;
use Grav\Theme\Scholar\Router;
use Grav\Theme\Scholar\Source;
use Grav\Theme\Scholar\TaxonomyMap;
use Grav\Theme\Scholar\Timer;
use Grav\Theme\Scholar\Autoload;
use Grav\Theme\Scholar\Utilities;

use RocketTheme\Toolbox\Event\Event;
use Grav\Plugin\StaticGeneratorPlugin as StaticGenerator;

/**
 * Scholar Theme
 *
 * Class Scholar
 *
 * @category Extensions
 * @package  Grav\Theme
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-theme-scholar
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
            'onThemeInitialized' => ['onThemeInitialized', 0]
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
        if ($this->config->get('system.pages.type') == "flex") {
            return;
        }
        $this->autoload();
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->startTimer('scholar', 'Scholar');
        }
        if ($this->isAdmin() && $this->config->get('plugins.admin')) {
            $this->enable(
                [
                    'onGetPageBlueprints' => ['onGetPageBlueprints', 0]
                ]
            );
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
        if (isset($this->grav['shortcode'])) {
            $this->registerShortcodes();
        }
        if ($this->config->get('system.debugger.enabled')) {
            $this->grav['debugger']->stopTimer('scholar');
        }
    }

    /**
     * Get configuration blueprints
     *
     * @param string $path   Path to blueprint
     * @param string $prefix Optional key-prefix
     *
     * @return array Associative, nested array of settings
     */
    public static function getConfigBlueprintFields(string $path, string $prefix = ''): array
    {
        $config = Grav::instance()['config'];
        $locator = Grav::instance()['locator'];
        $formatter = new YamlFormatter;
        $file = new YamlFile($locator->findResource($path, true, true), $formatter);
        $return = array();
        foreach ($file->load() as $name => $data) {
            foreach ($data as $key => $property) {
                if (Utils::contains($key, '@')) {
                    $key = str_replace(['data-', '@'], '', $key);
                    if (is_string($property)) {
                        $data[$key] = call_user_func_array(
                            $property,
                            []
                        );
                    } elseif (is_array($property)) {
                        $data[$key] = call_user_func_array(
                            $property[0],
                            array_slice($property, 1, count($property)-1, true)
                        );
                    }
                }
            }
            if (!isset($data['name'])) {
                $data['name'] = $prefix . $name;
            }
            if (!isset($data['default']) && $config->get('theme.' . $name)) {
                $data['default'] = $config->get('theme.' . $name);
            }
            $return [$prefix . $name] = $data;
        }
        return $return;
    }

    /**
     * Get styles
     *
     * @return array Associative array of styles
     */
    public static function getStylesBlueprint(): array
    {
        include __DIR__ . '/vendor/autoload.php';
        $stylesFolders = Utils::arrayMergeRecursiveUnique(
            Utilities::filesFinder('theme://css/styles', ['css']),
            Utilities::filesFinder('user://themes/scholar/css/styles', ['css'])
        );
        $styles = array();
        foreach (array_unique($stylesFolders) as $style) {
            $name = $style->getBasename('.' . $style->getExtension());
            $styles[$name] = Inflector::titleize($name);
        }
        return $styles;
    }

    /**
     * Get enabled components
     *
     * @return array
     */
    public static function getComponentsBlueprint(): array
    {
        include __DIR__ . '/vendor/autoload.php';
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
     * Get Highlighter themes
     *
     * @return array Associative array of styles
     */
    public static function getHighlighterThemeBlueprint(): array
    {
        include __DIR__ . '/vendor/autoload.php';
        $stylesFolders = Utils::arrayMergeRecursiveUnique(
            Utilities::filesFinder('theme://css/highlighter', ['css']),
            Utilities::filesFinder('user://themes/scholar/css/highlighter', ['css'])
        );
        $styles = array();
        foreach (array_unique($stylesFolders) as $style) {
            $name = $style->getBasename('.' . $style->getExtension());
            $name = str_replace(['enlighterjs.', '.min'], '', $name);
            $styles[$name] = Inflector::titleize($name);
        }
        return $styles;
    }

    /**
     * Register blueprints
     *
     * @param Event $event Instance of RocketTheme\Toolbox\Event\Event.
     *
     * @return void
     */
    public function onGetPageBlueprints(Event $event)
    {
        foreach ($this->config->get('themes.scholar.components') as $component) {
            $componentFolder = Utilities::folderFinder(
                'blueprints',
                [
                    'theme://components/' . $component,
                    'user://themes/scholar/components/' . $component
                ]
            );
            if ($componentFolder) {
                $folder = $this->grav['locator']->findResource(
                    $componentFolder,
                    true,
                    true
                );
                if (is_dir($folder)) {
                    $event->types->scanBlueprints($folder);
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
        if ($this->grav['config']->get('theme.router')) {
            $Router = self::getInstance(
                $this->config->get(
                    'theme.api.router',
                    'Router\Router'
                ),
                $this->grav
            );
        }
    }

    /**
     * Handle current Page
     *
     * @return void
     */
    public function onPageInitialized()
    {
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
        if ($this->grav['config']->get('theme.debug')) {
            $this->grav['assets']->addJs(
                $this->grav['locator']->findResource(
                    'theme://node_modules/@khanacademy/tota11y/dist/tota11y.min.js',
                    false,
                    true
                )
            );
        }
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
            $searchRoute = $this->grav['uri']->rootUrl(true) .
            $this->config->get('themes.scholar.routes.search');
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
    public function registerShortcodes()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/shortcodes');
        foreach ($this->config->get('theme.components') as $component) {
            $path = 'theme://components/' . $component . '/shortcodes';
            if (is_dir($this->grav['locator']->findResource($path))) {
                $this->grav['shortcode']->registerAllShortcodes($path);
            }
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
        $language = Grav::instance()['language'];
        $regex = '/Grav\\\\Theme\\\\Scholar\\\\(?<api>.*)/i';
        $classes = preg_grep($regex, get_declared_classes());
        $matches = preg_grep('/' . $key . '/i', $classes);
        $options = [
            '' => $language->translate(['THEME_SCHOLAR.GENERIC.NONE'])
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
