Version 0.4.0
----------

* [#16](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/16) Don't throw if a namespace contains no classes. Thanks, Benedikt Franke
* [#16](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/16) Add method `Classfinder::namespaceHasClasses` to detect if a namespace is empty. Thanks, Benedikt Franke

Version 0.3.3
-------------

* ([Original Merge Request](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/12)) via ([Merge Request](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/13)) Fixed a bug that caused an exception to be thrown when the `exec` function was disabled by PHP configuration - even when files support was explicitly disabled. Thanks, incraigulous.


Version 0.3.2
-------------

* [#11](https://gitlab.com/hpierce1102/ClassFinder/issues/11) ([Merge Request](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/10)) Fixed a bug that caused "Access Denied" errors when some directories were missing some permissions. Thanks, Leonardo Losoviz.


Version 0.3.1
-------------

* [#8](https://gitlab.com/hpierce1102/ClassFinder/issues/8) ([Merge Request](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/8)) Fixed a bug that caused notices to be raised when a composer.json doesn't include an `autoload` configuration. Thanks, walid.ammar and TheFehr.

Version 0.3.0 
-------------

* [#4](https://gitlab.com/hpierce1102/ClassFinder/issues/4) Warnings will no longer appear if a classmap configuration is present, but empty. [Contributed by](https://gitlab.com/hpierce1102/ClassFinder/merge_requests/6) rotespferd. 
* Automatically locating `composer.json` should be more consistent and no longer immediately fallback to `/`.
* Internal - Fix an issue where forking the project resulting in broken CI jobs.



Version 0.3.0 Beta
------------------

* [#3](https://gitlab.com/hpierce1102/ClassFinder/issues/3) Added support for "recursive mode". Invoking `ClassFinder::getClassesInNamespace()` 
in this mode will result in classes in subnamespaces being turned up.

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = ClassFinder::getClassesInNamespace('TestApp1\Foo', ClassFinder::RECURSIVE_MODE);

/**
 * array(
 *   'TestApp1\Foo\Bar',
 *   'TestApp1\Foo\Baz',
 *   'TestApp1\Foo\Foo',
 *   'TestApp1\Foo\Box\Bar',
 *   'TestApp1\Foo\Box\Baz',
 *   'TestApp1\Foo\Box\Foo',
 *   'TestApp1\Foo\Box\Lon\Bar',
 *   'TestApp1\Foo\Box\Lon\Baz',
 *   'TestApp1\Foo\Box\Lon\Foo',
 * )
 */
var_dump($classes);
```

* Added **experimental** support for classes that have been included via `files` entries in `composer.json`.  Including this feature
is a significant drain on performance, so it must be explicitly enabled.

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

ClassFinder::enableExperimentalFilesSupport();

$classes = ClassFinder::getClassesInNamespace('TestApp1\Foo');
```

* PSR4 and Classmap features can now be disabled. Disabling autoloading features that you don't need will probably improve performance.

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

ClassFinder::disablePSR4Support();
ClassFinder::disableClassmapSupport();

$classes = ClassFinder::getClassesInNamespace('TestApp1\Foo');
```

Version 0.2.0
-------------

* Added support for finding classes declared via `classmap`.
* Exceptions will no longer be thrown when PSR4 can't find a registered namespace (because it could be a valid class
declared in a `classmap`)

Example composer.json that is now supported:
```
  ...
  "autoload": {
    ...
    "classmap": [ "src/foo/", "src/bar/" ]
  }
  ...
```


Version 0.1.2
-------------

* Fixed composer.json so that it can be correctly installed on PHP 7+.

Version 0.1.1
-------------

* Fixed a Linux specific bug that caused absolute paths to fail to resolve and erroneously throw exceptions. If you were
affected by this bug, you would see errors like `Unknown namespace Acme\Whatever. Checked for files in , but that directory did not exist. [...]`
when that namespace does indeed exist.
* Support for PHP 5.3 is now under testing harness and should work now. 

Version 0.1.0
-------------

* Vastly improved PSR4 support
    * Loading classes from Composer packages is now supported.
    * Namespaces that map to multiple directories is now supported.
    * Fixed a bug where ClassFinder would use a more generic (and therefore _wrong_) namespace over a better one. 
    (Selecting `Acme`, when `Acme\Foo` is a better choice)
* Manually overriding the AppRoot is now done with a static method instead of a static property

Mapping a namespace to multiple directories:
```
    ...
    "autoload": {
        "psr-4": {
            "Acme\\Foo\\": [ "src/", "srcButDifferent/" ]
        }
    }
    ...
```

Old overriding app root: 
```
ClassFinder::appRoot = '/home/hpierce/whatevs'; 
```

New overriding app root:
```
ClassFinder::setAppRoot('/home/hpierce/whatevs'); 
```
