<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit263d57e2dc5b8da8ff83c473a8fd43ba
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '25072dd6e2470089de65ae7bf11d3109' => __DIR__ . '/..' . '/symfony/polyfill-php72/bootstrap.php',
        'f598d06aa772fa33d905e87be6398fb1' => __DIR__ . '/..' . '/symfony/polyfill-intl-idn/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php72\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Intl\\Idn\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Shoplemo\\' => 9,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Php72\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php72',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Intl\\Idn\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-intl-idn',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Shoplemo\\' => 
        array (
            0 => __DIR__ . '/..' . '/shoplemo/php-sdk/source',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'Twocheckout' => 
            array (
                0 => __DIR__ . '/..' . '/2checkout/2checkout-php/lib',
            ),
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );


    public static $classMap = array (
        'CoinpaymentsAPI' => __DIR__ . '/..' . '/coinpaymentsnet/coinpayments-php/src/CoinpaymentsAPI.php',
        'CoinpaymentsCurlRequest' => __DIR__ . '/..' . '/coinpaymentsnet/coinpayments-php/src/CoinpaymentsCurlRequest.php',
        'CoinpaymentsValidator' => __DIR__ . '/..' . '/coinpaymentsnet/coinpayments-php/src/CoinpaymentsValidator.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit263d57e2dc5b8da8ff83c473a8fd43ba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit263d57e2dc5b8da8ff83c473a8fd43ba::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit263d57e2dc5b8da8ff83c473a8fd43ba::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit263d57e2dc5b8da8ff83c473a8fd43ba::$classMap;

        }, null, ClassLoader::class);
    }
}