<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Reset ClassFinder back to normal.
        ClassFinder::setAppRoot(null);
    }
    /**
     * @dataProvider classFinderDataProvider
     */
    public function testClassFinder($namespace, $expected, $message)
    {
        try {
            ClassFinder::enableExperimentalFilesSupport();
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }


    public function classFinderDataProvider()
    {
        return array(
            array(
                'TestApp1\FilesClasses',
                array(
                    'TestApp1\FilesClasses\Bam',
                    'TestApp1\FilesClasses\Wam',
                    'TestApp1\FilesClasses\Fam',
                    'TestApp1\FilesClasses\Cam',
                    'TestApp1\FilesClasses\Lam',
                ),
                'ClassFinder should be able to find 1st party classes included from `files` listed in composer.json.'
            ),
            array(
                'TestApp1\FilesClasses\MoreClasses',
                array(
                    'TestApp1\FilesClasses\MoreClasses\Pham',
                    'TestApp1\FilesClasses\MoreClasses\Slam'
                ),
                'ClassFinder should be able to find 1st party classes included from `files` listed in composer.json.'
            ),
            array(
                'HaydenPierce\Files',
                array(
                    'HaydenPierce\Files\z',
                    'HaydenPierce\Files\z2',
                    'HaydenPierce\Files\a',
                    'HaydenPierce\Files\a2',
                    'HaydenPierce\Files\b',
                    'HaydenPierce\Files\b2'
                ),
                'ClassFinder should be able to find 3rd party classes included from `files` listed in composer.json of those projects.'
            )
        );
    }

    public function testFilesSupportRequiresEnabling()
    {
        ClassFinder::disableExperimentalFilesSupport(); // Disabling FilesSupport should cause no files to be found.

        $this->assertFalse(ClassFinder::namespaceHasClasses('TestApp1\FilesClasses'));
    }

}
