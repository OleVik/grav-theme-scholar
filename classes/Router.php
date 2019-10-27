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
use Grav\Common\Page\Collection;
use Grav\Theme\Scholar\API\Content;
use Grav\Theme\Scholar\API\Source;
use Grav\Theme\Scholar\API\LinkedData;
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
        $this->searchRoute = $this->grav['config']->get('theme.routes.search')
            ?? $this->grav['config']->get('themes.scholar.routes.search')
            ?? '/search';
        $this->embedRoute = $this->grav['config']->get('theme.routes.embed')
            ?? $this->grav['config']->get('themes.scholar.routes.embed')
            ?? '/embed';
        $this->dataRoute = $this->grav['config']->get('theme.routes.data')
            ?? $this->grav['config']->get('themes.scholar.routes.data')
            ?? '/data';
        $this->printRoute = $this->grav['config']->get('theme.routes.print')
            ?? $this->grav['config']->get('themes.scholar.routes.print')
            ?? '/print';

        $path = $this->grav['uri']->path();
        if (\in_array(
            '/' . basename($path),
            [
                $this->searchRoute,
                $this->embedRoute,
                $this->dataRoute,
                $this->printRoute
            ]
        )
        ) {
            $page = $this->dispatch($path);
            $page->parent(
                $this->grav['pages']->find(
                    str_replace('/' . basename($path), '', $path)
                )
            );
        } else {
            return;
        }
        if ('/' . basename($path) == $this->embedRoute) {
            $this->handleEmbed($page);
        } elseif ('/' . basename($path) == $this->dataRoute) {
            $this->handleData($page);
        } elseif ('/' . basename($path) == $this->printRoute) {
            $template = $page->parent()->template();
            if (isset($page->parent()->header()->print['template'])) {
                $template = $page->parent()->header()->print['template'];
            }
            if (isset($page->parent()->header()->print['items'])) {
                $collection = $page->parent()->collection('print')->published();
                if ($collection
                    && isset($page->parent()->header()->print['process'])
                    && $page->parent()->header()->print['process'] === true
                ) {
                    $this->handleProcessedContent($page, $template);
                } elseif ($collection) {
                    $page = $this->handleRawContent($page, $collection, $template);
                }
            } else {
                $page = $page->parent();
            }
            $this->grav['pages']->addPage($page, $path);
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
        }
        return $page;
    }

    /**
     * Handle embed output
     *
     * @param Page $Page Page-instance
     *
     * @return void
     */
    public function handleEmbed(Page $Page): void
    {
        header(
            'Content-type: ' . Utils::getMimeByExtension(
                $Page->parent()->templateFormat()
            )
        );
        echo $Page->parent()->content();
        exit();
    }

    /**
     * Handle data output
     *
     * @param Page $Page Page-instance
     *
     * @return void
     */
    public function handleData(Page $Page): void
    {
        $ld = new LinkedData($this->grav['language']);
        $Page->title($Page->parent()->title());
        $ld->buildSchema($Page->parent());
        $data = [
            'encodingFormat' => Utils::getMimeByExtension(
                $Page->parent()->templateFormat()
            ),
            'header' => (array) $Page->parent()->header(),
            'content' => $Page->parent()->content()
        ];
        $data = array_merge($ld->data, $data);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Handle raw content
     *
     * @param Page       $Page       Page-instance
     * @param Collection $Collection Collection-instance
     * @param string     $template   Template-override
     *
     * @return Page Page-instance
     */
    public function handleRawContent(Page $Page, Collection $Collection, string $template = ''): Page
    {
        $content = '';
        foreach ($Collection as $item) {
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
        $Page->title($Page->parent()->title());
        $Page->rawMarkdown($content);
        $Page->template($Page->parent()->template());
        if (strlen($template) > 0) {
            $Page->template($template);
        }
        return $Page;
    }

    /**
     * Handle processed content
     *
     * @param Page   $Page     Page-instance
     * @param string $template Template-override
     *
     * @return void
     */
    public function handleProcessedContent(Page $Page, string $template = ''): void
    {
        $collection = $this->iteratePages($Page->parent()->route());
        $content = '';
        foreach ($collection as $item) {
            if (strlen($item['title']) > 0) {
                $content .= '<h' . $item['depth'] .'>' .
                $item['title'] . '</h' . $item['depth'] . '>';
            }
            $content .= $item['content'];
        }
        $Page->title($Page->parent()->title());
        $Page->setRawContent($content);
        if (strlen($template) < 1) {
            $template = $Page->parent()->template();
        }
        $content = $this->grav['twig']->processTemplate(
            $template . '.html.twig', 
            [
                'page' => $Page
            ]
        );
        header(
            'Content-type: ' . Utils::getMimeByExtension(
                $Page->parent()->templateFormat()
            )
        );
        echo $content;
        exit();
    }

    /**
     * Creates Pages-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Reserved collection-mode for handling child-pages
     * @param integer $depth Reserved placeholder for recursion depth
     *
     * @return array Page-structure
     */
    public function iteratePages($route, $mode = false, $depth = 0): array
    {
        $page = $this->grav['page'];
        $depth++;
        $mode = '@page.self';
        if ($depth > 1) {
            $mode = '@page.children';
        }
        $collection = $page->evaluate([$mode => $route])->order(
            $page->header()->print['order']['by'] ?? 'folder',
            $page->header()->print['order']['dir'] ?? 'asc'
        );
        $index = array();
        foreach ($collection as $page) {
            $index[$page->route()] = [
                'depth' => $depth,
                'title' => $page->title(),
                'content' => $page->content()
            ];
            $children = $this->iteratePages($page->route(), $mode, $depth);
            $index = array_merge($index, $children);
        }
        if (!empty($index)) {
            return $index;
        } else {
            return [];
        }
    }
}
