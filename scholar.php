<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Theme;

class Scholar extends Theme
{
    /**
     * Initialize plugin and subsequent events
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onThemeInitialized' => ['onThemeInitialized', 0]
        ];
    }

    /**
     * Register events and route with Grav
     * 
     * @return void
     */
    public function onThemeInitialized()
    {
        /* Check if Admin-interface */
        if (!$this->isAdmin()) {
            $this->enable(
                [
                    'onPageInitialized' => ['onPageInitialized', 0]
                ]
            );
        }
    }

    public function onPageInitialized()
    {
        $assets = $this->grav['assets'];
        $config = $this->config();
        if ($config['style']) {
            $style = $config['style'];
            $current = self::fileFinder(
                $style,
                '.css',
                'theme://css/styles',
                'theme://css'
            );
            $assets->addCss($current, 101);
        }
        if ($config['debug']) {
            $debug = self::fileFinder(
                'tota11y',
                '.min.js',
                'theme://js'
            );
            $assets->addJs($debug, 100);
        }
    }

    /**
     * Search for a file in multiple locations
     *
     * @param string $file         Filename.
     * @param string $ext          File extension.
     * @param array  ...$locations List of paths.
     * 
     * @return string
     */
    public static function fileFinder($file, $ext, ...$locations)
    {
        $return = false;
        foreach ($locations as $location) {
            if (file_exists($location . '/' . $file . $ext)) {
                $return = $location . '/' . $file . $ext;
                break;
            }
        }
        return $return;
    }
}
?>
