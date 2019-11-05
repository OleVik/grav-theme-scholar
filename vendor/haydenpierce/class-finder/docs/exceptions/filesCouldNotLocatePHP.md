Could not locate PHP interrupter.
---------------------------------

Example PHP:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

ClassFinder::enableExperimentalFilesSupport();
$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Results in this exception:

> Could not locate PHP interrupter.

When running ClassFinder with support for autoloaded classes in `files`, ClassFinder must execute the included file in a
shell to determine any defined classes in it. To do this, ClassFinder must determine the location of the PHP interrupter.
If you're using PHP 5.4 or newer, `PHP_BINARY` should be set accurately and is used to determine the location of `php`.
On PHP 5.3, ClassFinder attempts to find it via `which` (or `where` on Windows). If ClassFinder _still_ can't find it, this
exception is thrown.

Providing an explicit path to the PHP interrupter is not supported. Please open an issue that includes your use case, 
if you need this.

Possible Solution 1
-------------------

Upgrade to a newer version of PHP that includes the `PHP_BINARY` constant.

Possible Solution 2
-------------------

Add `php` to your `$PATH` so it can be discovered via `which` or `where`. 