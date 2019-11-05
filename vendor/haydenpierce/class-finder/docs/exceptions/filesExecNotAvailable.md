FilesFinder requires that exec() is available.
----------------------------------------------

Example PHP:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

ClassFinder::enableExperimentalFilesSupport();
$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Results in this exception:

> FilesFinder requires that exec() is available. Check your php.ini to see if it is disabled.

When running ClassFinder with support for autoloaded classes in `files`, ClassFinder must execute the included file in a
shell to determine any defined classes in it. `exec()` is used to accomplish this. In some environments, hosts may 
intentionally disable the use `exec()` as a security or performance precaution. 

Possible Solution 1
-------------------

Disable `files` support.

The majority of users won't need `files` support. If you've copy / pasted a snippet (including from documentation here) 
that enabled it, you should remove it and see if you're part of the 99% that doesn't need this feature. You may also
want to explicitly disable it:

```
ClassFinder::disableExperimentalFilesSupport();
$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Possible Solution 2
-------------------

Ensure `exec()` is available to PHP.

Find your `php.ini` file and look for a configuration value called `disabled_functions`:

```
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

Remove exec from the list and restart your webserver.


