<?php

namespace HaydenPierce\ClassFinder\UnitTest\PSR4;

use HaydenPierce\ClassFinder\PSR4\PSR4Namespace;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PSR4NamespaceTest extends \PHPUnit_Framework_TestCase
{
    /** @var vfsStreamDirectory */
    private $root;

    public function setUp()
    {
        $structure = $this->getTestStructure();
        $this->root = vfsStream::setup('root', null, $structure);
    }

    public function getTestStructure()
    {
        return array(
            'Baz' => array(
                'Foo' => array(
                    'Fooa.php' => $this->getClassFileContents('PSR4\\Foo', 'Fooa'),
                    'Foob.php' => $this->getClassFileContents('PSR4\\Foo', 'Foob')
                ),
                'Bar.php' => $this->getClassFileContents('PSR4', 'Bar'),
                'Barb.php' => $this->getClassFileContents('PSR4', 'Barb')
            )
        );
    }

    public function getClassFileContents($namespace, $className)
    {
        $template = <<<EOL
<?php 

namespace %s

class %s
{
}
EOL;

        return sprintf($template, $namespace, $className);
    }

    public function testCountMatchingNamespaceSegments()
    {
        $namespace = new PSR4Namespace('MyPSR4Root\\Foot\\', array($this->root->getChild('Baz')->path()));

        $this->assertEquals(1, $namespace->countMatchingNamespaceSegments('MyPSR4Root'));
        $this->assertEquals(2, $namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot'));
        $this->assertEquals(2, $namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot\\Baz'), 'countMatchingNamespaceSegments should only report matches against the registered namespace root. It should not attempt to resolve segments after the registered root.');
        $this->assertEquals(2, $namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot\\Baz\\Foo'), 'countMatchingNamespaceSegments should only report matches against the registered namespace root. It should not attempt to resolve segments after the registered root.');
        $this->assertEquals(0, $namespace->countMatchingNamespaceSegments('Cactus'));
        $this->assertEquals(0, $namespace->countMatchingNamespaceSegments('Cactus\\Foot'));
    }

    public function testIsAcceptableNamespace()
    {
        $namespace = new PSR4Namespace('MyPSR4Root\\Foot\\', array($this->root->getChild('Baz')->path()));

        $this->assertFalse($namespace->isAcceptableNamespace('MyPSR4Root'), 'MyPSR4Root cannot use the directory mapping for MyPSR4Root\\Foot because it does not include the Foot segment.');
        $this->assertFalse($namespace->isAcceptableNamespace('MyPSR4Root\\Cactus'));
        $this->assertTrue($namespace->isAcceptableNamespace('MyPSR4Root\\Foot'));
        $this->assertTrue($namespace->isAcceptableNamespace('MyPSR4Root\\Foot\\Baz'), 'Longer namespaces are acceptable because we can resolve the additional segments');
        $this->assertTrue($namespace->isAcceptableNamespace('MyPSR4Root\\Foot\\Baz\\Foo'), 'countMatchingNamespaceSegments should only report matches against the registered namespace root. It should not attempt to resolve segments after the registered root.');
        $this->assertFalse($namespace->isAcceptableNamespace('Cactus'));
        $this->assertFalse($namespace->isAcceptableNamespace('Cactus\\Foot'));
    }

    public function testKnowsNamespace()
    {
        $namespace = $this->getMockBuilder('\HaydenPierce\ClassFinder\PSR4\PSR4Namespace')
            ->setConstructorArgs(array(
                'MyPSR4Root\\Foot\\',
                array($this->root->getChild('Baz')->path())
            ))
            ->setMethods(array(
                'normalizePath'
            ))
            ->getMock();

        $root = $this->root;
        $namespace->method('normalizePath')->willReturnCallback(function($directory, $relativePath) use ($root) {
            return 'vfs://' . $directory . '/' . $relativePath;
        });

        $this->assertTrue($namespace->knowsNamespace('MyPSR4Root'));
        $this->assertTrue($namespace->knowsNamespace('MyPSR4Root\\Foot'));
        $this->assertTrue($namespace->knowsNamespace('MyPSR4Root\\Foot\\Foo'), 'countMatchingNamespaceSegments should only report matches against the registered namespace root. It should not attempt to resolve segments after the registered root.');
        $this->assertFalse($namespace->knowsNamespace('MyPSR4Root\\Foot\\Cactus'), 'countMatchingNamespaceSegments should only report matches against the registered namespace root. It should not attempt to resolve segments after the registered root.');
        $this->assertFalse($namespace->knowsNamespace('Cactus'));
        $this->assertFalse($namespace->knowsNamespace('Cactus\\Foot'));
    }
}
