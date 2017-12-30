<?php
class IndexController extends CommonController {
    public function init() {
        parent::init();
    }

    public function testAction() {
        \Response\SyResponseHttp::header('Content-Type', 'text/html; charset=utf-8');
        $renderRes = $this->getView()->render('index/index.html', [
            'aaa' => 'xxdd'
        ]);

        $this->sendRsp($renderRes);
    }

    public function test2Action() {
        \Response\SyResponseHttp::header('Content-Type', 'text/html; charset=utf-8');
        $renderRes = $this->getView()->display('index.tpl', [
            'aaa' => 'xxdd',
        ]);

        $this->sendRsp($renderRes);
    }

    /**
     * @SyFilter-{"field": "url","explain": "链接","type": "string","rules": {"required": 1,"url": 1}}
     */
    public function indexAction(){
        $this->SyResult->setData([
            'short_url' => \SyServer\HttpServer::getServerConfig('cookiedomain_base'),
        ]);

        $this->sendRsp();
    }

    public function debugAction(){
        xdebug_start_trace();
        $timerData = new \Tool\Timer\TimerData();
        $timerData->setExecTime(2, '0 * * * * *');
        $timerData->setUri('/Index/image/index');
        $timerData->setParams([
            'callback' => 'jwtest123',
        ]);

        xdebug_stop_trace();
        $this->SyResult->setData([
            'id' => $timerData->getId(),
            'persist' => $timerData->getPersist(),
            'uri' => $timerData->getUri(),
            'params' => $timerData->getParams(),
            'time' => $timerData->getTime(),
            'profile_name' => xdebug_get_profiler_filename(),
            'getdata' => $_GET,
            'profiler' => ini_get('xdebug.profiler_enable_trigger_value'),
        ]);

        $this->sendRsp();
    }
}