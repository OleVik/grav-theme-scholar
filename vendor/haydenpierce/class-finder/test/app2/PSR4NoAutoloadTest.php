<?php

namespace TestApp2;

require_once __DIR__ . '/vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class PSR4NoAutoloadTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Reset ClassFinder back to normal.
        ClassFinder::setAppRoot(null);
    }

    /**
     * @dataProvider getClassesInNamespaceDataProvider
     */
    public function testGetClassesInNamespace($namespace, $expected, $message)
    {


        try {
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }

    public function getClassesInNamespaceDataProvider()
    {
        return array(
            array(
                'HaydenPierce\SandboxApp',
                array(
                    'HaydenPierce\SandboxApp\Foy'
                ),
                'ClassFinder should be able to find 3rd party classes'
            ),
            array(
                'HaydenPierce\SandboxApp\Foo\Bar',
                array(
                    'HaydenPierce\SandboxApp\Foo\Bar\Barc',
                    'HaydenPierce\SandboxApp\Foo\Bar\Barp'
                ),
                'ClassFinder should be able to find 3rd party classes multiple namespaces deep.'
            ),
            array(
                'HaydenPierce\SandboxAppMulti',
                array(
                    'HaydenPierce\SandboxAppMulti\Zip',
                    'HaydenPierce\SandboxAppMulti\Zop',
                    'HaydenPierce\SandboxAppMulti\Zap',
                    'HaydenPierce\SandboxAppMulti\Zit'
                ),
                'ClassFinder should be able to find 3rd party classes when a provided namespace root maps to multiple directories (Example: "HaydenPierce\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            )
        );
    }

    public function testGetClassesInNamespaceRecursively()
    {
        $namespace = 'HaydenPierce';
        $expected = array(
            'HaydenPierce\SandboxApp\Foy',
            'HaydenPierce\SandboxApp\Fob\Soz',
            'HaydenPierce\SandboxApp\Foo\Larc',
            'HaydenPierce\SandboxApp\Foo\Bar\Barc',
            'HaydenPierce\SandboxApp\Foo\Bar\Barp',
            'HaydenPierce\SandboxAppMulti\Zip',
            'HaydenPierce\SandboxAppMulti\Zop',
            'HaydenPierce\SandboxAppMulti\Zap',
            'HaydenPierce\SandboxAppMulti\Zit'
        );
        $message = 'ClassFinder should be able to find 3rd party classes';

        ClassFinder::disableClassmapSupport();

        try {
            $classes = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        // ClassFinder has the ability to find itself. This ability, while intended, is incontinent for tests
        // because of the 'HaydenPierce' test case. Whenever ClassFinder would be updated, we would need to update the
        // test. To prevent the flakiness, we just remove ClassFinder's classes.
        $classes = array_filter($classes, function($class) {
            return strpos($class, 'HaydenPierce\ClassFinder') !== 0;
        });

        ClassFinder::enableClassmapSupport();

        $this->assertEquals($expected, $classes, $message);
    }
}