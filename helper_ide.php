<?php
include_once __DIR__ . '/syLibs/autoload.php';
ini_set('display_errors', 'On');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

class IDETool {
    use \Traits\SimpleTrait;

    public static function generator(){
        $option = strtolower(\Tool\Tool::getClientOption(1, true, ''));
        switch ($option) {
            case 'rdkafka':
                $generator = new \IDE\DeclaredRdKafka();
                $generator->createHelper();
                break;
            case 'seaslog':
                $generator = new \IDE\DeclaredSeasLog();
                $generator->createHelper();
                break;
            case 'yaf':
                $generator = new \IDE\DeclaredYaf();
                $generator->createHelper();
                break;
            case 'yac':
                $generator = new \IDE\DeclaredYac();
                $generator->createHelper();
                break;
            case 'yaconf':
                $generator = new \IDE\DeclaredYaconf();
                $generator->createHelper();
                break;
            default:
                self::help();
                break;
        }
    }

    private static function help(){
        echo '显示帮助: php helper_ide.php -h' . PHP_EOL;
        echo '生成扩展帮助文件: php helper_ide.php xxx' . PHP_EOL;
        echo '    xxx:PHP扩展名称,可以通过命令[php -m]获取' . PHP_EOL;
    }
}

IDETool::generator();