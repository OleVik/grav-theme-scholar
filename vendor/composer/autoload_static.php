<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit785cb74a939a47c9971b9fe8f0e7ca59
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Spatie\\SchemaOrg\\' => 17,
        ),
        'G' => 
        array (
            'Grav\\Theme\\Scholar\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Spatie\\SchemaOrg\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/schema-org/src',
        ),
        'Grav\\Theme\\Scholar\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExtra\\Sorter' => 
            array (
                0 => __DIR__ . '/..' . '/phpextra/sorter/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit785cb74a939a47c9971b9fe8f0e7ca59::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit785cb74a939a47c9971b9fe8f0e7ca59::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit785cb74a939a47c9971b9fe8f0e7ca59::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
