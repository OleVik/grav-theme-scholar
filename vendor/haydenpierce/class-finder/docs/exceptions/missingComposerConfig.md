Missing composer.json
---------------------

Example PHP:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Results in this exception:

> Could not locate composer.json. You can get around this by setting ClassFinder::$appRoot manually.

ClassFinder requires a composer.json to load autoloading settings. In this situation, ClassFinder wasn't able to 
find it when recursively searching for it. You will need to tell ClassFinder where the root of your application is - this
is the directory that contains the composer.json configuration and where classes will be searched out of.

```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

ClassFinder::setAppRoot(realpath(__DIR__ . '../../app/')); // This is suggesting that the app root is really someone else.
$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

This is an exotic situation and shouldn't apply to most projects.

If this information doesn't resolve the issue, please feel free to submit an issue.
