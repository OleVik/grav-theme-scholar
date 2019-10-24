<?php
/**
 * Scholar Theme, Router API
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\API
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-theme-scholar
 */

namespace Grav\Theme\Scholar\API;

use Grav\Common\Grav;
use Grav\Common\Utils;
use Grav\Common\Page\Page;
use Grav\Theme\Scholar\API\Utilities;

/**
 * Router API
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\API
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-theme-scholar
 */
class Router
{
    /**
     * Initialize Router
     *
     * @param Grav $Grav Grav-instance
     */
    public function __construct(Grav $Grav)
    {
        $this->grav = $Grav;
        $path = $this->grav['uri']->path();

        $searchRoute = $this->grav['config']->get('theme.routes.search')
            ?? $this->grav['config']->get('themes.scholar.routes.search')
            ?? '/search';
        $dataRoute = $this->grav['config']->get('theme.routes.data')
            ?? $this->grav['config']->get('themes.scholar.routes.data')
            ?? '/data';
        $printRoute = $this->grav['config']->get('theme.routes.print')
            ?? $this->grav['config']->get('themes.scholar.routes.print')
            ?? '/print';
        
        $Page = $this->dispatch($path);

        switch ('/' . basename($path)) {
            case $searchRoute:
                break;
            case $dataRoute:
                break;
            case $printRoute:
                $parent = $this->dispatch(
                    str_replace('/' . basename($path), '', $path)
                );
                $content = '';
                foreach ($parent->collection('print') as $item) {
                    $content .= $item->content();
                }
                $Page->title($parent->title());
                $Page->content($content);
                $new = $this->handlePrint($Page, $parent->template());
                // $Page->content($new);
                // dump($new);
                $format = $parent->templateFormat();
                header('Content-type: ' . Utils::getMimeByExtension($format));
                echo $new;
                exit();
                break;
        }
    }

    /**
     * Create content type if missing
     *
     * @param string $route Route to dispatch
     *
     * @return Page Page-instance
     */
    public function dispatch(string $route): Page
    {
        $page = $this->grav['pages']->dispatch($route);
        if (!$page) {
            $source = Utilities::fileFinder(
                basename($route) . '.md',
                [
                    'theme://pages',
                    'user://themes/scholar/pages'
                ]
            );
            $file = $this->grav['locator']->findResource(
                $source,
                true,
                true
            );
            $page = new Page();
            $page->init(
                new \SplFileInfo($file)
            );
            $this->grav['pages']->addPage($page, $route);
        }
        return $page;
    }

    /**
     * Handle Search Page
     *
     * @return void
     */
    public function handleSearch(): void
    {
        // dump('handleSearch');
        $page = $this->grav['pages']->dispatch($this->grav['config']->get('themes.scholar.routes.search', '/search'));
        if (!$page) {
            $page = new Page();
            $page->init(new \SplFileInfo(__DIR__ . '/../pages/search.md'));
            $page->slug(basename($this->grav['config']->get('themes.scholar.routes.search', '/search')));
            $this->grav['pages']->addPage($page, $this->grav['config']->get('themes.scholar.routes.search', '/search'));
        }
    }

    /**
     * Handle Print
     *
     * @param Page   $Page     Page-instance
     * @param string $template Template-name
     *
     * @return void
     */
    public function handlePrint(Page $Page, string $template)
    {
        // dump('handlePrint');
        
        $content = $this->grav['twig']->processTemplate(
            $template . '.html.twig', 
            [
                'page' => $Page
            ]
        );
        return $content;
    }
}
