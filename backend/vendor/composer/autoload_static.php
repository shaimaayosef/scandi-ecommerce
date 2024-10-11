<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67a08d337a763db525ba5a40886f4af7
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GraphQL\\' => 8,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GraphQL\\' => 
        array (
            0 => __DIR__ . '/..' . '/webonyx/graphql-php/src',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/../..' . '/src',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67a08d337a763db525ba5a40886f4af7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67a08d337a763db525ba5a40886f4af7::$prefixDirsPsr4;
            $loader->fallbackDirsPsr0 = ComposerStaticInit67a08d337a763db525ba5a40886f4af7::$fallbackDirsPsr0;
            $loader->classMap = ComposerStaticInit67a08d337a763db525ba5a40886f4af7::$classMap;

        }, null, ClassLoader::class);
    }
}