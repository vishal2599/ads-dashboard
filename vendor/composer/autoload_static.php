<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita4a2a57cf058c08c8c8aa0465ab98e6f
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AdvDashboard\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AdvDashboard\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita4a2a57cf058c08c8c8aa0465ab98e6f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita4a2a57cf058c08c8c8aa0465ab98e6f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita4a2a57cf058c08c8c8aa0465ab98e6f::$classMap;

        }, null, ClassLoader::class);
    }
}
