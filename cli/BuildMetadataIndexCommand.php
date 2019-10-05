<?php
/**
 * Scholar Theme, Metadata Index Builder
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

namespace Grav\Plugin\Console;

use Grav\Common\Grav;
use Grav\Common\GravTrait;
use Grav\Console\ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Grav\Framework\Cache\Adapter\FileStorage;
use Scholar\API\Data;

/**
 * Metadata Index Builder
 *
 * API for storing Pages metadata as JSON
 *
 * @category   API
 * @package    Grav\Theme\Scholar
 * @subpackage Grav\Theme\Scholar\API
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-theme-scholar
 */
class BuildMetadataIndexCommand extends ConsoleCommand
{
    /**
     * Command definitions
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName("index")
            ->setDescription("Generates and stores Pages metadata.")
            ->setHelp('The <info>index</info>-command generates and stores data.')
            ->addArgument(
                'route',
                InputArgument::REQUIRED,
                'The route to the page'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Overrides target-option from theme-config'
            )
            ->addOption(
                'echo',
                'e',
                InputOption::VALUE_NONE,
                'Outputs result directly'
            );
    }

    /**
     * Build and save metadata index
     *
     * @return void
     */
    protected function serve()
    {
        $config = Grav::instance()['config']->get('themes.scholar');
        $locator = Grav::instance()['locator'];
        $route = $this->input->getArgument('route');
        $target = $this->input->getArgument('target');
        $echo = $this->input->getOption('echo');
        if (!empty($target)) {
            $config['cache'] = $target;
        }
        $type = $this->input->getArgument('type');
        $this->output->writeln('<info>Generating metadata index</info>');
        try {
            include __DIR__ . '/../classes/Data.php';
            $ld = new Data();
            $ld->buildIndex($route);
            if ($echo) {
                echo json_encode($ld->index, JSON_PRETTY_PRINT);
            } else {
                $target = array(
                    'persist' => $locator->findResource('user://') . '/data/scholar',
                    'transient' => $locator->findResource('cache://') . '/scholar'
                );
                $location = $target[$config['cache']];
                $Storage = new FileStorage($location);
                if ($Storage->doHas($file)) {
                    $Storage->doDelete($file);
                }
                $Storage->doSet($file, $data, 0);
                $this->output->writeln('<info>Saved to ' . $location . '/' . $file . '.</info>');
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
