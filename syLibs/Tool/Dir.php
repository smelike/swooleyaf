<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/5 0005
 * Time: 11:30
 */
namespace Tool;

use Traits\SimpleTrait;

class Dir {
    use SimpleTrait;

    /**
     * 获取转换后的目录名称
     * @param string $dirName 目录名称
     * @return string
     */
    public static function getDirPath(string $dirName) : string {
        $formatName = str_ireplace("\\", "/", $dirName);

        return substr($formatName, -1) == "/" ? $formatName : $formatName . "/";
    }

    /**
     * 删除目录
     * @param string $dirName
     * @return bool
     */
    public static function del(string $dirName) : bool {
        if (is_file($dirName)) {
            unlink($dirName);
            return true;
        }

        $dirPath = self::getDirPath($dirName);
        if (!is_dir($dirPath)) {
            return true;
        }

        foreach (glob($dirPath . "*") as $eFile) {
            is_dir($eFile) ? self::del($eFile) : unlink($eFile);
        }

        return @rmdir($dirName);
    }

    /**
     * 批量创建目录
     * @param string $dirName 目录名
     * @param int    $auth    权限
     * @return bool
     */
    public static function create(string $dirName, $auth=0755) : bool {
        $dirPath = self::getDirPath($dirName);
        if (is_dir($dirPath)) {
            return true;
        }

        $dirs = explode('/', $dirPath);
        $dir  = '';
        foreach ( $dirs as $v ) {
            $dir .= $v . '/';
            if (is_dir($dir)) {
                continue;
            }
            mkdir($dir, $auth);
        }

        return is_dir($dirPath);
    }
}