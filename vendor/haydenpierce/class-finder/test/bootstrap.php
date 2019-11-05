<?php

/**
 * Glossary:
 * test classes - Classes that ClassFinder will attempt to find.
 * test app - a directory structure containing a composer.json. This directory structure is intended to simulate an application
 * that can autoload classes with psr-4.
 *
 * Due to the nature of the this component, the ClassFinder class must have access to the classes in the test app.
 * For this reason, we copy in ClassFinder and ensure that it can be autoloaded with the test classes.
 */

// Find test apps

function findTestApps($rootDir) {
    $testAppPaths = scandir(__DIR__);

    $testAppPaths = array_filter($testAppPaths, function($path) {
        return substr($path, 0, 3) === 'app';
    });

    $testAppPaths = array_map(function($path) {
        return realpath(__DIR__  . '/' . $path);
    }, $testAppPaths);

    return $testAppPaths;
}

// https://stackoverflow.com/a/3349792/3000068
function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

// https://stackoverflow.com/a/2050909/3000068
function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function copyInCurrentClasses($testApp) {
    $classFinderSource = realpath(__DIR__ . '/../src');
    $classFinderPath = $testApp . '/vendor/haydenpierce/class-finder/src';
    $classFinderPath = str_replace('\\', '/', $classFinderPath);

    if (is_dir($classFinderPath)) {
        deleteDir($classFinderPath);
    }

    recurse_copy($classFinderSource, $classFinderPath);

    $composerSource = realpath(__DIR__ . '/../composer.json');
    $composerPath =  $testApp . '/vendor/haydenpierce/class-finder/composer.json';
    copy($composerSource, $composerPath);
}

$testApps = findTestApps(__DIR__);

foreach($testApps as $testApp) {

    $autoloaderPath = $testApp . '/vendor/autoload.php';

    $autoloaderExists = false;
    if (!file_exists($autoloaderPath)) {
        echo "No autoloader detected.\n";
        echo "Running composer install for $testApp...\n";

        // TODO: Programically run composer install?
        // https://stackoverflow.com/a/45831624/3000068
        // I ran into a problem getting composer to run via shell_exec() - Stack Overflow suggests it could be xdebug related.
        // I couldn't figure out how to set an environment variable before running a command on Windows.
        // This should be capable of Windows and Unix systems.
        echo "FAILED.\n";

        echo "************\n";
        echo "* SOLUTION *\n";
        echo "************\n";
        echo "You will need to run the following command to get this up and running:\n";
        echo "composer install --working-dir=\"$testApp\"\n";
        exit;
    }

    copyInCurrentClasses($testApp);
}
