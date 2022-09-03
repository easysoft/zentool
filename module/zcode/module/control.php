<?php
class module extends control
{
    public $moduleName;
    public $moduleRoot;

    public function init($params)
    {
        $this->save('module', 'create', '');
        $this->moduleName = $params['moduleName'];
        $this->moduleRoot = $this->config->runDir . DS . $moduleName . DS;

        mkdir($moduleRoot);

        $this->initConfig();
        $this->initControl();
        $this->initModel();
        $this->initLang();
        $this->initCss();
        $this->initJs();
        $this->initView();

        echo 'inited';
    }

    public function initConfig()
    {
        touch($this->moduleRoot . 'config.php');
    }

    public function initControl()
    {
        touch($this->moduleRoot . 'control.php');
    }

    public function initModel()
    {
        touch($this->moduleRoot . 'model.php');
    }

    public function initLang()
    {
        mkdir($this->moduleRoot . 'lang');
        touch($this->moduleRoot . 'lang/zh-cn.php');
        touch($this->moduleRoot . 'lang/en.php');
    }

    public function initView()
    {
        mkdir($this->moduleRoot . 'view');
    }

    public function initJs()
    {
        mkdir($this->moduleRoot . 'js');
    }

    public function initCss()
    {
        mkdir($this->moduleRoot . 'css');
    }
}
