<?php
class module extends control
{
    public $moduleName;
    public $moduleRoot;

    public function init($params)
    {
        $this->moduleName = $params['moduleName'];
        $this->table      = "zt_" . $this->moduleName;
        $this->moduleRoot = $this->config->runDir . DS . $this->moduleName . DS;

        mkdir($this->moduleRoot);

        $this->initControl();
        $this->initModel();

        die();
        $this->initConfig();
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
        $this->view->module       = $this->moduleName;
        $this->view->author       = $this->config->author;
        $this->view->copyright    = $this->config->copyright;
        $this->view->idfield      = $this->config->idfield;
        $this->view->license      = $this->config->license;
        $this->view->link         = $this->config->link;
        $this->view->package      = $this->view->module;
        $this->view->version      = $this->config->version;
        $this->view->viewvars     = $this->config->viewvars;
        $this->view->table        = strtoupper($this->table);
        $this->view->createfix    = '';
        $this->view->createfixer  = '';
        $this->view->createreturn = '';
        $this->view->editfix      = '';
        $this->view->editfixer    = '';
        $this->view->editreturn   = '';
        $this->view->createcheck  = '';
        $this->view->createskip   = '';
        $this->view->editskip     = '';
        $this->view->editcheck    = '';
        $this->view->getbyid      = '';

        $modelFile = $this->moduleRoot . 'model.php';

        $modelCode = $this->parse('module', 'model.code');
        return $this->zcode->create($modelFile, $modelCode);
    }

    public function initLang()
    {
        mkdir($this->moduleRoot . 'lang');
        touch($this->moduleRoot . 'lang/zh-cn.php');
        touch($this->moduleRoot . 'lang/en.php');
    }

    public function initView($function)
    {
        $pageList = array("browse", 'view', 'create');
        foreach($pageList as $page)
        {
            if(method_exists($this->zcode, "init{$function}View")) call_user_func_array(array($this->zcode, "init{$function}View"), array());
        }
        return true;
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
