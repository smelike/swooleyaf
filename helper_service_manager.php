<?php
function controllerLog(string $sudo,string $serverPath,string $command) {
    $phpCommand = $sudo . '/usr/local/php7/bin/php helper_service.php -n ' . $serverPath . ' ' . $command;
    system($phpCommand);
}

function getClientOption(string $key, $default=null) {
    global $argv;
    $option = null;
    foreach ($argv as $eKey => $eVal) {
        if(($key == $eVal) && isset($argv[$eKey+1])){
            $option = $argv[$eKey+1];
            break;
        }
    }

    return $option ?? $default;
}

//操作系统
$unameInfo = php_uname();
$sudo = '';
if (strpos($unameInfo, 'Darwin') !== false) {
    $sudo = 'sudo ';
}

$projects = [
    'syLibs' => [
        'git_branch' => 'master',
        'type' => 'lib',
    ],
    'sy_api' => [
        'git_branch' => 'master',
        'type' => 'module',
        'listens' => [
            0 => [
                'port' => 7100,
                'weight' => 1,
            ],
        ],
    ],
    'sy_order' => [
        'git_branch' => 'master',
        'type' => 'module',
        'listens' => [
            0 => [
                'port' => 7120,
                'weight' => 1,
            ],
        ],
    ],
    'sy_user' => [
        'git_branch' => 'master',
        'type' => 'module',
        'listens' => [
            0 => [
                'port' => 7140,
                'weight' => 1,
            ],
        ],
    ],
    'sy_services' => [
        'git_branch' => 'master',
        'type' => 'module',
        'listens' => [
            0 => [
                'port' => 7160,
                'weight' => 1,
            ],
        ],
    ],
];

$command = getClientOption('-s');
switch ($command) {
    case 'start-all' :
        foreach ($projects as $name => $eProject) {
            if ($eProject['type'] == 'module') {
                foreach ($eProject['listens'] as $eListen) {
                    controllerLog($sudo, $name, '-s start -module ' . $name . ' -port ' . $eListen['port'] . ' -weight ' . $eListen['weight']);
                }
            }
        }
        break;
    case 'stop-all' :
        foreach ($projects as $name => $eProject) {
            if ($eProject['type'] == 'module') {
                foreach ($eProject['listens'] as $eListen) {
                    controllerLog($sudo, $name, '-s stop -module ' . $name . ' -port ' . $eListen['port'] . ' -weight ' . $eListen['weight']);
                }
            }
        }
        break;
    case 'restart-all' :
        foreach ($projects as $name => $eProject) {
            if ($eProject['type'] == 'module') {
                foreach ($eProject['listens'] as $eListen) {
                    controllerLog($sudo, $name, '-s restart -module ' . $name . ' -port ' . $eListen['port'] . ' -weight ' . $eListen['weight']);
                }
            }
        }
        break;
    case 'commit-all' :
        $message = 'auto commit at ' . date('Y-m-d H:i:s');
        foreach ($projects as $name => $eProject) {
            system('cd ' . __DIR__ . '/' . $name . ' && git commit -am "' . $message . '"');
        }
        echo PHP_EOL;
        break;
    case 'push-all' :
        foreach ($projects as $name => $eProject) {
            system('cd ' . __DIR__ . '/' . $name . ' && git checkout ' . $eProject['git_version'] . ' && git push origin '
                . $eProject['git_branch']);
        }
        echo PHP_EOL;
        break;
    case 'pull-all' :
        foreach ($projects as $name => $eProject) {
            system('cd ' . __DIR__ . '/' . $name . ' && git checkout ' . $eProject['git_version'] . ' && git pull origin '
                . $eProject['git_branch']);
        }
        echo PHP_EOL;
        break;
    default :
        system('echo -e "\e[1;31m command not exist \e[0m"');
}