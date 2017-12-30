# 环境要求
## 基础环境
- PHP7+
- redis3.2+
- etcd3
- inotify
## 必要扩展
- swoole1.9.21+
- msgpack
- yaf3.0.4+
- yaconf1.0+
- yac2.0+
- redis3.0+
- Seaslog1.6+
- PDO
- pcre
- pcntl
- opcache
## 可选扩展
- imgick3.4+
- mongodb1.2+
- xdebug2.5+
- xhprof1.0+
## 其他
- gcc4.8+ //php7编译用gcc4.8+会开启Global Register for opline and execute_data支持, 这个会带来5%左右的性能提升

# 框架介绍
## 使用介绍
- 操作系统只支持linux,不支持windows,因为pcntl扩展,nohup,inotify只有linux才可用
- nginx建议使用版本大于1.9,因为1.9的nginx增加了stream模块,支持tcp反向代理和负载均衡
- favicon.ico请求不在框架内部做处理,建议配置nginx静态文件访问来实现获取该文件
- 建议单独设置一个文件服务模块用于处理文件上传,图片裁剪等功能
- 多服务器部署,必须确保服务端口对外开放,以避免服务模块跨服务器请求调用因端口未开放出现错误
- 框架内部模块之间请求调用全都不用cookie和session
- task任务投递不要投递到taskId=0的进程,该进程用于定时更新模块配置信息
- 对外部只开放api模块,需要获取其他模块的数据,通过发送rpc请求到其他模块获取数据
- api模块返回数据根据业务需求,既可以用控制器的SyResult对象,也可以直接在响应请求中直接设置数据
- api模块负责接受外部请求,返回响应数据,包括设置响应头,cookie等
- 非api模块返回数据统一用控制器的SyResult对象
- 非api模块不能设置响应头,cookie等信息,如需设置这些信息,将这些信息作为响应数据放到SyResult中,返回给api组装来间接设置响应头,cookie等
- 非api模块发送请求只有POST方式,不支持其他方式
- 图片上传请参考api模块Image控制器的uploadImageAction方法
- 微信,支付宝支付与回调处理请参考sy_order模块下的OrderDao文件
- 所有数据库表必须有且只能有单主键,不允许联合主键
## 命令
```
    //前置命令,必须在开启服务之前运行
    nohup etcd --listen-client-urls http://10.27.166.170:2379 --advertise-client-urls http://10.27.166.170:2379 >/dev/null & --启动etcd服务
    nohup etcdctl watch sydev/modules/ sydev/modules0 >/usr/local/inotify/symodules/change_service.txt --endpoints=[10.27.166.170:2379] 2>&1 & --启动etcd监听服务
    chmod a+x /home/jw/phpspace/swooleyaf/symodules_inotify.sh
    nohup sh /home/jw/phpspace/swooleyaf/symodules_inotify.sh >/dev/null 2>&1 & --启动inotify实时更新
    //服务命令
    /usr/local/php7/bin/php helper_service_manager.php -s start-all --启动服务
    /usr/local/php7/bin/php helper_service_manager.php -s stop-all --关闭服务
    //微信更新access token和js ticket缓存
    //1:必须将helper_sytask.php文件加入到linux系统cron执行任务中
    //2:强制刷新缓存: /usr/local/php7/bin/php helper_sytask.php -refreshwx 1
    //3:如需要用到微信缓存,必须在每次启动服务后执行上述命令
    
    //清理脚本-解决cli模式php内存缓慢泄漏的问题(待验证),建议每隔一段时间执行一次,比如一个小时执行一次
    sync && echo 3 > /proc/sys/vm/drop_caches
```
## 预定义常量
- SY_ROOT //框架根目录
- SY_ENV //框架环境 dev:测试环境 product:生产环境
- SY_VERSION //框架版本号
- SY_MODULE //框架模块名称
- SY_API //框架API标识 true: 是对外的API接口 false:不是对外的API接口
- SY_SERVER_TOKEN_LENGTH //框架服务标识长度
## 服务管理
### 获取框架概览信息
    请求地址: http://api.xxx.com/syinfo
### 获取php信息
    请求地址: http://api.xxx.com/phpinfo
### 关闭或开启服务
    请求地址: http://api.xxx.com/serverctl
    请求参数:
        server_ip: string 服务IP
        server_port: int 服务端口
        server_status: int 服务状态 0:关闭 1:开启
## Mongodb文档
    https://docs.mongodb.com/php-library/
## API文档（使用apidoc生成）
### 参考链接
    https://github.com/apidoc/apidoc
### 安装nodejs和apidoc
```
    tar -xvf node-v6.10.2-linux-x64.tar
    mkdir /usr/local/nodejs
    mv node-v6.10.2-linux-x64/ /usr/local/nodejs
    vim /etc/profile
        export NODE_HOME=/usr/local/nodejs/node-v6.10.2-linux-x64
        export $PATH=$NODE_HOME/bin
    source /etc/profile
    npm config set registry "http://registry.npm.taobao.org"
    sudo npm install apidoc -g
```
### 添加配置
在项目根目录下添加名称为apidoc.json的文件，文件内容：
```
    {
        "name": "SyApi",
        "version": "1.0.0",
        "description": "API文档",
        "title": "API文档",
        "url" : "http://localhost:8080/Index"
    }
```
### 生成文档
```
    apidoc -i 项目根目录 -o 文档存放目录
    //如果出现错误 SyntaxError: Use of const in strict mode
    npm cache clean -f
    npm install -g n
    n stable
```
## XDebug代码分析
- 默认关闭了自动堆栈追踪和自动性能分析
- 开启堆栈追踪,如果是GET请求,必须在url上附带XDEBUG_TRACE参数,如果是POST请求,必须在请求体上附带XDEBUG_TRACE参数
- 开启性能分析,如果是GET请求,必须在url上附带XDEBUG_PROFILE参数,如果是POST请求,必须在请求体上附带XDEBUG_PROFILE参数
### 参考链接
    http://blog.csdn.net/why_2012_gogo/article/details/51170609
### 可视化工具
- KCacheGrind(Linux)
- QCacheGrind(Windows)

## XHPROF性能分析
### 使用样例
参考demo_xhprof.php文件

## 代码解耦
善用观察者模式来实现业务代码解耦,具体可参考邮件发送模块

## 接口签名
请求地址带上签名参数,统一只在api模块做签名校验,签名参数如下:
- _sign: 签名值,由数字,字母组成的48位字符串

## 定时任务-3.0
1. 定时任务处理都是通过发送HTTP GET请求的方式进行,在执行定时任务之前,必须确保请求接口可正常访问
2. 定时任务采用协程的方式运行,协程的相关了解请参考链接-http://www.laruence.com/2015/05/28/3038.html
3. 目前支持的定时任务有三种: 
- 一次性定时任务,必须指定任务的执行时间戳
- 间隔定时任务,必须指定任务的间隔时间,单位为秒
- cron定时任务,必须指定任务的cron计划时间
### 参考样例
```
    //一次性定时任务
    $data1 = new \Tool\Timer\TimerData();
    $data1->setExecTime(0, time() + 50);
    $data1->setUri('/Index/MIndex/index');
    $data1->setParams([
        'url' => 'http://www.baidu.com',
    ]);
    \Tool\Timer\TimerTool::addTimer($data1);

    //间隔定时任务
    $data2 = new \Tool\Timer\TimerData();
    $data2->setExecTime(1, 7);
    $data2->setUri('/Index/Index/test');
    \Tool\Timer\TimerTool::addTimer($data2);

    //cron定时任务
    $data3 = new \Tool\Timer\TimerData();
    $data3->setExecTime(2, '0 * * * * *');
    $data3->setUri('/Index/image/index');
    $data3->setParams([
        'callback' => 'jwtest123',
    ]);
    \Tool\Timer\TimerTool::addTimer($data3);
```
### 启动定时任务
```
    /usr/local/php7/bin/php helper_timer.php >/dev/null &
```
### 数据库连接池
- https://github.com/swoole/php-cp //连接池扩展
- https://github.com/swoole/swoole-src/blob/master/examples/mysql_proxy_server.php //swoole版
### 图片处理
- https://github.com/kosinix/grafika //参考地址