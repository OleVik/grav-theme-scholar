How Testing Works
-----------------

Testing ClassFinder is a bit of an ordeal because the project is intimately related to autoloading.

Testing works by creating entire "sample applications" in the testing directory. These applications
include their own composer.json and attempt to simulate a real application that `composer require`d 
this project. Autoloading is accomplished by requiring in an older version of `haydenpierce/class-finder`.
*Autoloading is the only reason `"haydenpierce/class-finder": "0.0.1"` exists in the test app's composer.json file*.

Tests can be run with this command (tested on Windows 10):
```
"vendor/bin/phpunit" "./test/app1/src/ClassFinderTest.php"
```

This starts off with a bootstrap script that copies in the existing src directory over the initially required
`haydenpierce/class-finder/src` files. Therefore, blowing away any changes made there. This makes debugging
kind of annoying because running xdebug on the test files will bring you into the test files. If you make changes directly
to the test files, they will immediately be blown away on the next run of the tests. Don't make the same
mistake I've made dozens of times.

If you're running the tests for the first time, you will encounter an error. This is because the test app
is itself a composer application that must be installed. Currently, the necessary command to install will be output
the first time you run the tests.
