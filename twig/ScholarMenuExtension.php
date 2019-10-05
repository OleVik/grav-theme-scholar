<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Theme;

class ScholarMenuExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'ScholarMenuExtension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('scholar_menu_route', [$this, 'menuRoute']),
            new \Twig_SimpleFunction('scholar_menu', [$this, 'menu'])
        ];
    }

    public function menuRoute(string $route, string $type = 'docs')
    {
        return Scholar::getMenuRoute($route, $type);
    }

    public function menu(string $route)
    {
        return Grav::instance()['twig']->processTemplate(
            'partials/docs/menu.html.twig',
            [
                'pages' => Scholar::buildTree($route)
            ]
        );
    }
}