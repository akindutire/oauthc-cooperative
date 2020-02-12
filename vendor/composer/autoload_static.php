<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita201138208421f238683f8a68246bb87
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'src\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'src\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'src\\oauthcoop\\config\\Config' => __DIR__ . '/../..' . '/src/oauthcoop/config/config.php',
        'src\\oauthcoop\\controller\\Admin' => __DIR__ . '/../..' . '/src/oauthcoop/controller/Admin.php',
        'src\\oauthcoop\\controller\\Authentication' => __DIR__ . '/../..' . '/src/oauthcoop/controller/Authentication.php',
        'src\\oauthcoop\\controller\\Home' => __DIR__ . '/../..' . '/src/oauthcoop/controller/Home.php',
        'src\\oauthcoop\\controller\\Staff' => __DIR__ . '/../..' . '/src/oauthcoop/controller/Staff.php',
        'src\\oauthcoop\\migration\\create_feedback_entity' => __DIR__ . '/../..' . '/src/oauthcoop/migration/2020-02-06-1581004521$create_feedback_entity.php',
        'src\\oauthcoop\\model\\Feedback' => __DIR__ . '/../..' . '/src/oauthcoop/model/Feedback.php',
        'src\\oauthcoop\\route\\Api' => __DIR__ . '/../..' . '/src/oauthcoop/route/Api.php',
        'src\\oauthcoop\\route\\Web' => __DIR__ . '/../..' . '/src/oauthcoop/route/Web.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita201138208421f238683f8a68246bb87::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita201138208421f238683f8a68246bb87::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita201138208421f238683f8a68246bb87::$classMap;

        }, null, ClassLoader::class);
    }
}