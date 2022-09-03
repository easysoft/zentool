<?php
class module extends control
{
    public $moduleName;
    public $moduleRoot;

    public function init($params)
    {
        $this->moduleName = $params['moduleName'];
        $this->moduleRoot = $this->config->runDir . DS . $this->moduleName . DS;

        mkdir($this->moduleRoot);

        $this->initControl();

        die();
        $this->initConfig();
        $this->initModel();
        $this->initLang();
        $this->initCss();
        $this->initJs();
        $this->initView();

        echo 'inited';
    }

    public function initControl()
    {
        $this->view->module    = $this->moduleName;
        $this->view->author    = $this->config->author;
        $this->view->copyright = $this->config->copyright;
        $this->view->idfield   = $this->config->idfield;
        $this->view->license   = $this->config->license;
        $this->view->link      = $this->config->link;
        $this->view->package   = $this->view->module;
        $this->view->version   = $this->config->version;
        $this->view->viewvars  = $this->config->viewvars;

        $controlFile = $this->moduleRoot . 'control.php';

        $controlCode = $this->parse('module', 'control.code');
        return $this->zcode->create($controlFile, $controlCode);
    }

    public function initConfig()
    {
        touch($this->moduleRoot . 'config.php');
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
