<?php
include_once __DIR__ . '/syLibs/autoload.php';
ini_set('display_errors', 'On');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

class MysqlTool {
    use \Traits\SimpleTrait;

    private static function handlePath(){
        $needStr1 = \Tool\Tool::getClientOption('-path', false, '');
        $needStr2 = preg_replace([
            '/\s+/',
            '/\/+/'
        ], [
            '',
            '/',
        ], $needStr1);
        $needStr3 = trim($needStr2);
        $length = strlen($needStr3);
        if($length == 0){
            $path = '';
        } else {
            $path = substr($needStr3, ($length - 1), 1) == '/' ? $needStr3 : $needStr3 . '/';
        }

        return $path;
    }

    /**
     * 转换表名或数据库名
     * @param string $name 表名或数据库名
     * @return string
     */
    private static function transferName(string $name) : string {
        $str1 = strtolower($name);
        $str2 = preg_replace([
            '/[^0-9a-z]+/',
            '/\s+/'
        ], [
            ' ',
            ' ',
        ], $str1);
        $str3 = ucwords(trim($str2));

        return str_replace(' ', '', $str3);
    }

    public static function generator(){
        $option = \Tool\Tool::getClientOption(1, true);
        switch ($option) {
            case 'entities' :
                self::createDbEntities([
                    'db' => trim(\Tool\Tool::getClientOption('-db', false, '')),
                    'path' => self::handlePath(),
                    'prefix' => trim(\Tool\Tool::getClientOption('-prefix', false, '')),
                    'suffix' => trim(\Tool\Tool::getClientOption('-suffix', false, '')),
                ]);
                break;
            case 'entity' :
                self::createDbEntity([
                    'db' => trim(\Tool\Tool::getClientOption('-db', false, '')),
                    'table' => trim(\Tool\Tool::getClientOption('-table', false, '')),
                    'path' => self::handlePath(),
                    'prefix' => trim(\Tool\Tool::getClientOption('-prefix', false, '')),
                    'suffix' => trim(\Tool\Tool::getClientOption('-suffix', false, '')),
                ]);
                break;
            default :
                self::help();
                break;
        }
    }

    private static function help(){
        echo '显示帮助: php helper_mysql.php -h' . PHP_EOL;
        echo '生成数据库下所有的实体类: php helper_mysql.php entities -db xxx -path /xxx -prefix xxx -suffix xxx' . PHP_EOL;
        echo '    -db:数据库名' . PHP_EOL;
        echo '    -path:存放实体类文件的目录' . PHP_EOL;
        echo '    -prefix:实体类文件前缀' . PHP_EOL;
        echo '    -suffix:实体类文件后缀' . PHP_EOL;
        echo '生成数据库下指定的实体类: php helper_mysql.php entity -db xxx -table xxx -path /xxx -prefix xxx -suffix xxx' . PHP_EOL;
        echo '    -db:数据库名' . PHP_EOL;
        echo '    -table:表名' . PHP_EOL;
        echo '    -path:存放实体类文件的目录' . PHP_EOL;
        echo '    -prefix:实体类文件前缀' . PHP_EOL;
        echo '    -suffix:实体类文件后缀' . PHP_EOL;
    }

    /**
     * 创建数据库下所有的实体类
     */
    private static function createDbEntities(array $configs){
        if(!$configs['db']){
            exit('数据库名不能为空' . PHP_EOL);
        }

        $tables = \DesignPatterns\Singletons\MysqlSingleton::getInstance()->getDbTables($configs['db']);
        foreach ($tables as $eTable) {
            self::createDbEntity([
                'db' => $configs['db'],
                'table' => $eTable,
                'path' => $configs['path'],
                'prefix' => $configs['prefix'],
                'suffix' => $configs['suffix'],
            ]);

            echo 'create entity ' . $eTable . ' success' . PHP_EOL;
        }
        echo 'create all entities success' . PHP_EOL;
    }

    /**
     * 创建数据库下指定的实体类
     */
    private static function createDbEntity(array $configs){
        if(!$configs['db']){
            exit('数据库名不能为空' . PHP_EOL);
        } else if(!$configs['table']){
            exit('数据库表名不能为空' . PHP_EOL);
        }

        $fields = \DesignPatterns\Singletons\MysqlSingleton::getInstance()->getTableFields($configs['table'], $configs['db']);

        $fileName = $configs['prefix'] . self::transferName($configs['table']) . $configs['suffix'];
        $primaryKey = 'id';
        $filedStr = '';
        foreach ($fields as $eField) {
            if (strpos($eField['Type'], 'int') !== false) {
                $varType = 'int';
                if(is_null($eField['Default'])){
                    if($eField['Key'] == 'PRI'){
                        $primaryKey = $eField['Field'];
                        $default = 'null';
                    } else {
                        $default = $eField['Null'] == 'NO' ? '0' : 'null';
                    }
                } else {
                    $default = (int)$eField['Default'];
                }
            } else if (strpos($eField['Type'], 'float') !== false) {
                $varType = 'double';
                if(is_null($eField['Default'])){
                    if($eField['Key'] == 'PRI'){
                        $primaryKey = $eField['Field'];
                        $default = 'null';
                    } else {
                        $default = $eField['Null'] == 'NO' ? '0.00' : 'null';
                    }
                } else {
                    $default = (double)$eField['Default'];
                }
            } else if (strpos($eField['Type'], 'decimal') !== false) {
                $varType = 'double';
                if(is_null($eField['Default'])){
                    if($eField['Key'] == 'PRI'){
                        $primaryKey = $eField['Field'];
                        $default = 'null';
                    } else {
                        $default = $eField['Null'] == 'NO' ? '0.00' : 'null';
                    }
                } else {
                    $default = (double)$eField['Default'];
                }
            } else if (strpos($eField['Type'], 'double') !== false) {
                $varType = 'double';
                if(is_null($eField['Default'])){
                    if($eField['Key'] == 'PRI'){
                        $primaryKey = $eField['Field'];
                        $default = 'null';
                    } else {
                        $default = $eField['Null'] == 'NO' ? '0.00' : 'null';
                    }
                } else {
                    $default = (double)$eField['Default'];
                }
            } else {
                $varType = 'string';
                if(is_null($eField['Default'])){
                    if($eField['Key'] == 'PRI'){
                        $primaryKey = $eField['Field'];
                        $default = 'null';
                    } else {
                        $default = $eField['Null'] == 'NO' ? "''" : 'null';
                    }
                } else {
                    $default = "'" . $eField['Default'] . "'";
                }
            }

            $filedStr .= PHP_EOL . '    /**' . PHP_EOL;
            $filedStr .= '     * ' . $eField['Comment'] . PHP_EOL;
            $filedStr .= '     * @var ' . $varType . PHP_EOL;
            $filedStr .= '     */' . PHP_EOL;
            $filedStr .= '    public $' . $eField['Field'] . ' = ' . $default . ';' . PHP_EOL;
        }
        $content = '<?php' . PHP_EOL . 'namespace Entities\\' . self::transferName($configs['db']) . ';' . PHP_EOL . PHP_EOL;
        $content .= 'use DB\\Entities\\MysqlEntity;' . PHP_EOL . PHP_EOL;
        $content .= 'class ' . $fileName . ' extends MysqlEntity {' . PHP_EOL;
        $content .= '    public function __construct() {' . PHP_EOL;
        $content .= '        parent::__construct(\'' . $configs['db'] . '\', \'' . $configs['table'] . '\', \'' . $primaryKey . '\');' . PHP_EOL;
        $content .= '    }' . PHP_EOL;
        $content .= $filedStr;
        $content .= '}' . PHP_EOL;

        file_put_contents($configs['path'] . $fileName . '.php', $content);
    }
}

MysqlTool::generator();