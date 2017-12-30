#!/bin/bash
# sy框架服务更新监听脚本

#监听目录
MONITOR=/usr/local/inotify/symodules
#关注文件-服务变动
ATTENTION_CHANGE_SERVICE=/usr/local/inotify/symodules/change_service.txt
#处理执行文件-模块刷新
HANDLE_MODULE_REFRESH=/home/jw/phpspace/swooleyaf/helper_modules_refresh.php
/usr/local/inotify/bin/inotifywait -mrq --format '%w%f' -e modify $MONITOR | while read files
do
if [[ $files == $ATTENTION_CHANGE_SERVICE ]]; then
    /usr/local/php7/bin/php $HANDLE_MODULE_REFRESH
fi
echo "${files} was modify" >/dev/null 2>&1
done