<?php
final class SyFrameLoader {
    /**
     * @var \SyFrameLoader
     */
    private static $instance = null;
    private $classMap = [];
    /**
     * swift mailer未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $swiftMailerStatus = true;
    /**
     * smarty未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $smartyStatus = true;
    private $smartyRootClasses = [];

    private function __construct() {
        $this->classMap = [
            'Twig' => 'loadTwigFile',
            'Smarty' => 'loadSmartyFile',
            'SmartyBC' => 'loadSmartyFile',
            'PHPExcel' => 'loadPhpExcelFile',
            'Resque' => 'loadResqueFile',
            'Swift' => 'loadSwiftMailerFile',
        ];

        $this->smartyRootClasses = [
            'smarty' => 'smarty.php',
            'smartybc' => 'smartybc.php',
        ];
    }

    private function __clone() {
    }

    /**
     * @return \SyFrameLoader
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function loadTwigFile(string $className) : string {
        return SY_ROOT . '/syLibs/Template/' . str_replace('_', '/', $className) . '.php';
    }

    private function loadSmartyFile(string $className) : string {
        if ($this->smartyStatus) {
            $smartyLibDir = SY_ROOT . '/syLibs/Template/Smarty/libs/';
            define('SMARTY_DIR', $smartyLibDir);
            define('SMARTY_SYSPLUGINS_DIR', $smartyLibDir . '/sysplugins/');

            $this->smartyStatus = false;
        }

        $lowerClassName = strtolower($className);
        if(isset($this->smartyRootClasses[$lowerClassName])){
            $file = SMARTY_DIR . $this->smartyRootClasses[$lowerClassName];
        } else {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($className) . '.php';
        }

        return $file;
    }

    private function loadPhpExcelFile(string $className) : string {
        return SY_ROOT . '/syLibs/Excel/' . str_replace('_', '/', $className) . '.php';
    }

    private function loadResqueFile(string $className) : string {
        return SY_ROOT . '/syLibs/Queue/' . str_replace('_', '/', $className) . '.php';
    }

    private function loadSwiftMailerFile(string $className) : string {
        if($this->swiftMailerStatus){ //加载swift mailer依赖文件
            require_once SY_ROOT . '/syLibs/Mailer/Swift/depends/cache_deps.php';
            require_once SY_ROOT . '/syLibs/Mailer/Swift/depends/mime_deps.php';
            require_once SY_ROOT . '/syLibs/Mailer/Swift/depends/message_deps.php';
            require_once SY_ROOT . '/syLibs/Mailer/Swift/depends/transport_deps.php';
            require_once SY_ROOT . '/syLibs/Mailer/Swift/depends/preferences.php';

            $this->swiftMailerStatus = false;
        }

        return SY_ROOT . '/syLibs/Mailer/' . str_replace('_', '/', $className) . '.php';
    }

    private function loadSyLibFile(string $className) : string {
        return SY_ROOT . '/syLibs/' . $className . '.php';
    }

    /**
     * 加载文件
     * @param string $className 类名
     * @return bool
     */
    public function loadFile(string $className) : bool {
        $pos = strpos($className, '_');
        $prefix = $pos ? substr($className, 0, $pos) : $className;
        $funcName = $this->classMap[$prefix] ?? 'loadSyLibFile';
        $file = $this->$funcName($className);
        if(is_file($file) && is_readable($file)){
            require_once $file;
            return true;
        }

        return false;
    }
}

/**
 * 类自动加载
 * @param string $className 类全名
 * @return bool
 */
function syAutoload(string $className) {
    $trueName = str_replace([
        '\\',
        "\0",
    ], [
        '/',
        '',
    ], $className);
    return SyFrameLoader::getInstance()->loadFile($trueName);
}
spl_autoload_register('syAutoload');