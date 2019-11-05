<?php
/**
 * Scholar Theme, Router Interface
 *
 * PHP version 7
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\Router
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-scholar
 */

namespace Grav\Theme\Scholar\Router;

use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Page\Collection;

/**
 * Router  Interface
 *
 * @category API
 * @package  Grav\Theme\Scholar\Router\RouterInterface
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-theme-scholar
 */
interface RouterInterface
{
    /**
     * Initialize Router
     *
     * @param Grav $Grav Grav-instance
     */
    public function __construct(Grav $Grav);

    /**
     * Create content type if missing
     *
     * @param string $route Route to dispatch
     *
     * @return Page Page-instance
     */
    public function dispatch(string $route): Page;

    /**
     * Handle embed output
     *
     * @param Page $Page Page-instance
     *
     * @return void
     */
    public function handleEmbed(Page $Page): void;

    /**
     * Handle data output
     *
     * @param Page $Page Page-instance
     *
     * @return void
     */
    public function handleData(Page $Page): void;

    /**
     * Handle raw content
     *
     * @param Page       $Page       Page-instance
     * @param Collection $Collection Collection-instance
     * @param string     $template   Template-override
     *
     * @return Page Page-instance
     */
    public function handleRawContent(Page $Page, Collection $Collection, string $template = ''): Page;

    /**
     * Handle processed content
     *
     * @param Page   $Page     Page-instance
     * @param string $template Template-override
     *
     * @return void
     */
    public function handleProcessedContent(Page $Page, string $template = ''): void;

    /**
     * Creates Pages-structure recursively
     *
     * @param string  $route Route to page
     * @param string  $mode  Reserved collection-mode for handling child-pages
     * @param integer $depth Reserved placeholder for recursion depth
     *
     * @return array Page-structure
     */
    public function iteratePages($route, $mode = false, $depth = 0): array;
}
