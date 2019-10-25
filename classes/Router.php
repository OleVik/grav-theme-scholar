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
use Grav\Theme\Scholar\API\Source;
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
        
        // unset($this->grav['page']);
        $page = $this->dispatch($path);

        switch ('/' . basename($path)) {
            case $searchRoute:
                break;
            case $dataRoute:
                break;
            case $printRoute:
                $page->parent(
                    $this->grav['pages']->find(
                        str_replace('/' . basename($path), '', $path)
                    )
                );
                $collection = $page->parent()->collection('print');
                $collectionRoot = $collection->current();
                $content = '';
                foreach ($collection as $item) {
                    $Source = new Source($item, $this->grav['pages']);
                    $raw = $item->rawMarkdown();
                    foreach (array_keys($item->media()->all()) as $mediaItem) {
                        $raw = str_replace(
                            $mediaItem,
                            $Source->render($mediaItem)['page']->route() . '/' . $mediaItem,
                            $raw
                        );
                    }
                    $content .= $raw;
                }
                $page->title($collectionRoot->title());
                $page->slug(basename($path));
                // $page->content($content);
                $page->rawMarkdown($content);
                $page->template($collectionRoot->template());
                $this->grav['pages']->addPage($page, $path);
                // dump($page);

                // header(
                //     'Content-type: ' . Utils::getMimeByExtension(
                //         $collectionRoot->templateFormat()
                //     )
                // );
                // echo $content;
                // exit();
                break;
        }
    }

    public function dispatch2($path)
    {
        $page = $this->grav['pages']->dispatch($path);
        if (!$page) {
            $page = new Page;
            $page->init(new \SplFileInfo(__DIR__ . '/../pages/print.md'));
            $page->slug(basename($path));
            $page->template('article');
            $this->grav['pages']->addPage($page, $path);
        }
        return $page;
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
        $content = $this->grav['twig']->processTemplate(
            $template . '.html.twig', 
            [
                'page' => $Page
            ]
        );
        return $content;
    }
}
