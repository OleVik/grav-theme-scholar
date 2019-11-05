Unknown Namespace
-----------------

__This exception only occurs in versions 0.3.x and lower.__

Example PHP:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Example `composer.json`:
```
{
  "autoload": {
    "psr-4": {
      "Acme\\": "src/",
    },
  }
}
```


Results in this exception:

> Unknown namespace 'Acme\Foo\Bar'

This exception occurs when the provided namespace isn't declared or isn't accessible based on items are _are_ declared 
in `composer.json`. In the given example, `Acme` is declared to map to `src/` in `composer.json`, so PSR4 would mandate
that `src/Foo/Bar` is a valid path for a directory. However, that directory could not be located, and therefore the
provided namespace is unknown. 

If you discover that this exception is raised and Composer _can_ autoload classes found in the namespace, please submit 
an issue.
