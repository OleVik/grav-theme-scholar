<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Theme;

class ScholarUtilitiesExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'ScholarUtilitiesExtension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('root_template', [$this, 'rootTemplate']),
            new \Twig_SimpleFunction('file_exists', [$this, 'fileExists']),
            new \Twig_SimpleFunction('rawcontent', [$this, 'getFileContent']),
        ];
    }
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('unwrap_p', [$this, 'unwrapParagraph']),
        ];
    }

    public function rootTemplate(string $route)
    {
        return Scholar::getRootTemplate($route);
    }

    public function fileExists(string $file)
    {
        $file = Grav::instance()['locator']->findResource($file, true, true);
        return file_exists($file);
    }

    public function unwrapParagraph(string $content)
    {
        return str_replace(['<p>', '</p>'], '', $content);
    }

    /**
     * Get the raw contents of a file
     *
     * @param string $path Absolute path to file
     *
     * @return string
     *
     * @see https://github.com/gebeer/grav-plugin-twig-getfilecontents/
     */
    public function getFileContent(string $path)
    {
        return file_get_contents($path);
    }
}
