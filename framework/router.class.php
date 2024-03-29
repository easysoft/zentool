<?php
/**
 * router类从baseRouter类集成而来，您可以通过修改这个文件实现对baseRouter类的扩展。
 * The router class extend from baseRouter class, you can extend baseRouter class by change this file.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code. In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class router
{
    /**
     * ZenTaoPHP的基础目录，一般是程序的根目录。
     * The base path of the ZenTaoPHP framework.
     *
     * @var string
     * @access public
     */
    public $basePath;

    /**
     * 框架的根目录。
     * The root directory of the framwork($this->basePath/framework)
     *
     * @var string
     * @access public
     */
    public $frameRoot;

    /**
     * 类库的根目录。{$this->basePath/lib}
     * The root directory of the library($this->basePath/lib).
     *
     * @var string
     * @access public
     */
    public $coreLibRoot;

    /**
     * 应用名称
     * The appName.
     *
     * @var string
     * @access public
     */
    public $appName = '';

    /**
     * 应用程序的根目录。
     * The root directory of the app.
     *
     * @var string
     * @access public
     */
    public $appRoot;

    /**
     * 临时文件的根目录。
     * The root directory of temp.
     *
     * @var string
     * @access public
     */
    public $tmpRoot;

    /**
     * 缓存的根目录。
     * The root directory of cache.
     *
     * @var string
     * @access public
     */
    public $cacheRoot;

    /**
     * WWW目录。
     * The root directory of www.
     *
     * @var string
     * @access public
     */
    public $wwwRoot;

    /**
     * 附件存放目录。
     * The root directory of data.
     *
     * @var string
     * @access public
     */
    public $dataRoot;

    /**
     * 日志文件的根目录。
     * The root directory of log.
     *
     * @var string
     * @access public
     */
    public $logRoot;

    /**
     * 配置文件的根目录。
     * The root directory of config.
     *
     * @var string
     * @access public
     */
    public $configRoot;

    /**
     * 模块的根目录。
     * The root directory of module.
     *
     * @var string
     * @access public
     */
    public $moduleRoot;

    /**
     * 主题的根目录。
     * The root directory of theme.
     *
     * @var string
     * @access public
     */
    public $themeRoot;

    /**
     * 用户使用的语言。
     * The lang of the client user.
     *
     * @var string
     * @access public
     */
    public $clientLang;

    /**
     * 用户使用的主题。
     * The theme of the client user.
     *
     * @var string
     * @access public
     */
    public $clientTheme;

    /**
     * 客户端设备类型。
     * The device type of client.
     *
     * @var string
     * @access public
     */
    public $clientDevice;

    /**
     * 当前模块的control对象。
     * The control object of current module.
     *
     * @var object
     * @access public
     */
    public $control;

    /**
     * 模块名。
     * The module name
     *
     * @var string
     * @access public
     */
    public $moduleName;

    /**
     * 当前访问模块的control文件。
     * The control file of the module current visiting.
     *
     * @var string
     * @access public
     */
    public $controlFile;

    /**
     * 当前访问的方法名。
     * The name of the method current visiting.
     *
     * @var string
     * @access public
     */
    public $methodName;

    /**
     * 当前方法的扩展文件。
     * The action extension file of current method.
     *
     * @var string
     * @access public
     */
    public $extActionFile;

    /**
     * 访问的URI。
     * The URI.
     *
     * @var string
     * @access public
     */
    public $URI;

    /**
     * url地址传递的参数。
     * The params passed in through url.
     *
     * @var array
     * @access public
     */
    public $params;

    /**
     * 视图类型。
     * The view type.
     *
     * @var string
     * @access public
     */
    public $viewType;

    /**
     * 全局$config对象。
     * The global $config object.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * 全局$lang对象。
     * The global $lang object.
     *
     * @var object
     * @access public
     */
    public $lang;

    /**
     * $post对象，用于访问$_POST变量。
     * The $post object, used to access the $_POST var.
     *
     * @var ojbect
     * @access public
     */
    public $post;

    /**
     * $get对象，用于访问$_GET变量。
     * The $get object, used to access the $_GET var.
     *
     * @var ojbect
     * @access public
     */
    public $get;

    /**
     * $server对象，用于访问$_SERVER变量。
     * The $server object, used to access the $_SERVER var.
     *
     * @var ojbect
     * @access public
     */
    public $server;

    /**
     * 网站代号。
     * The code of current site.
     *
     * @var string
     * @access public
     */
    public $siteCode;

    /**
     * 命令行参数
     * Arguments for command.
     *
     * @var array
     * @access public
     */
    public $args;

    /**
     * 构造方法, 设置路径，类，超级变量等。注意：
     * 1.应该使用createApp()方法实例化router类；
     * 2.如果$appRoot为空，框架会根据$appName计算应用路径。
     *
     * The construct function.
     * Prepare all the paths, classes, super objects and so on.
     * Notice:
     * 1. You should use the createApp() method to get an instance of the router.
     * 2. If the $appRoot is empty, the framework will compute the appRoot according the $appName
     *
     * @param string $appName   the name of the app
     * @param string $appRoot   the root path of the app
     * @access public
     * @return void
     */
    public function __construct($appRoot = '')
    {
        $this->setPathFix();
        $this->setBasePath();
        $this->setFrameRoot();
        $this->setCoreLibRoot();

        $this->parseConfig('main');
        $appName    = isset($this->config->z_app) ? $this->config->z_app : 'zentao';
        $clientLang = isset($this->config->z_clientLang) ? $this->config->z_clientLang : '';

        $this->setConfigRoot();
        $this->loadMainConfig();
        $this->setAppName($appName);
        $this->setAppRoot($appRoot);
        $this->setTmpRoot();
        $this->setCacheRoot();
        $this->setLogRoot();
        $this->setModuleRoot();
        $this->setWwwRoot();
        $this->setDataRoot();
        $this->loadAppConfig();
        $this->setOS();
        $this->parseConfig();
        $this->setRunDir();
        $this->setClientLang($clientLang);

        $this->loadClass('filter', $static = true);

        $this->setSuperVars();
        $this->setDebug();
        $this->setErrorHandler();
        $this->setTimezone();

        if($this->config->framework->multiLanguage) $this->loadLang('common');
    }

    /**
     * 创建一个应用。
     * Create an application.
     *
     * @param string $appRoot   应用根路径。The root path of the app.
     * @param string $className 应用类名，如果对router类做了扩展，需要指定类名。When extends router class, you should pass in the child router class name.
     * @static
     * @access public
     * @return object   the app object
     */
    public static function createApp($appRoot = '', $className = '')
    {
        if(empty($className)) $className = __CLASS__;
        return new $className($appRoot);
    }

    /**
     * 设置操作系统。
     * Set OS.
     *
     * @access public
     * @return void
     */
    public function setOS()
    {
        $os = strtolower(PHP_OS);
        if(strpos($os, 'win') !== false) $os = 'windows';

        $this->config = empty($this->config) ? new stdClass() : $this->config;
        $this->config->os = $os;
    }

    /**
     * 设置运行目录。
     * Set runtime dir.
     *
     * @access public
     * @return void
     */
    public function setRunDir()
    {
        $this->config->runDir = $this->config->os == 'windows' ? iconv("UTF-8", "GB2312", getcwd()) : getcwd();
    }

    /**
     * 解析配置文件。
     * Prase config file content.
     *
     * @access public
     * @return void
     */
    public function parseConfig($name = '')
    {
        if($name == 'main')
        {
            $this->setOS();
        }
        $userHome = '';
        if($this->config->os == 'windows')
        {
            if(isset($_SERVER['HOMEDRIVE']) and isset($_SERVER['HOMEPATH']))
            {
                $userHome = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'] . DS;
            }
            else
            {
                if(isset($_SERVER['TMP'])  and !empty($_SERVER['TMP']))  $userHome = realpath($_SERVER['TMP']);
                if(isset($_SERVER['TEMP']) and !empty($_SERVER['TEMP'])) $userHome = realpath($_SERVER['TEMP']);
                if(empty($userHome)) $userHome = dirname(__FILE__);

                if(substr($userHome, -1, 1) != DS) $userHome .= DS;
                if(!is_writable($userHome)) $this->triggerError("Unable write tmp!");
            }
        }
        else
        {
            $userHome = getenv('HOME') . DS;
        }

        if(empty($name)) $name = $this->appName;
        $configFile = $userHome . '.zconfig/' . $name;
        $this->config->userConfigFile = $configFile;

        if(file_exists($configFile))
        {
            $userConfig = file($configFile);
            foreach($userConfig as $key => $val) $userConfig[$key] = trim($val);

            //解析多余空格
            $userConfig = preg_replace('/[[:space:]]+/', '', $userConfig);

            //解析#号注释
            $userConfig = preg_replace('/#.*/', '', $userConfig);
            foreach($userConfig as $val)
            {
                if(strpos($val, '#') === false && strpos($val, '=') !== false)
                {
                    $config = explode('=', $val);
                    if($config[0] && $config[1])
                    {
                        $configName = $config[0];
                        $this->config->$configName = $config[1];
                    }
                }
            }
        }
        else
        {
            if(!file_exists($userHome . '.zconfig/')) @mkdir($userHome . '.zconfig');

            touch($configFile);
        }
    }

    //-------------------- 路径相关方法(Path related methods)--------------------//

    /**
     * 设置应用名称。
     * Set app name.
     *
     * @param  string    $appName
     * @access public
     * @return void
     */
    public function setAppName($appName)
    {
        global $argv, $argc;

        $this->appName = $appName;

        if($argc >= 3)
        {
            $feature = $argv[1];
            $count   = 0;
            foreach($this->config->command as $app => $features)
            {
                $command    = $argv[2];
                $configFile = dirname(__FILE__, 2) . DS . 'config' . DS . $app . '.php';
                $appConfig  = file_exists($configFile) ? file_get_contents($configFile) : '';

                /* Check abbr command used. */
                if(strpos($command, '-') === 0)
                {
                    /* Match by module config. */
                    $moduleFile   = dirname(__FILE__, 2) . DS . 'module' . DS . $app . DS . $feature . DS . 'config.php';
                    $moduleConfig = file_exists($moduleFile) ? file_get_contents($moduleFile) : '';
                    preg_match('/\$config->arguments\[\'' . $command . '\'\]\s+=\s+\'(\w+)\';/', $moduleConfig, $commands);
                    if(count($commands) > 1) $command = $commands[1];

                    if($command == $argv[2])
                    {
                        /* Match by app config. */
                        preg_match('/\$config->arguments\[\'' . $command . '\'\]\s+=\s+\'(\w+)\';/', $appConfig, $commands);
                        if(count($commands) > 1) $command = $commands[1];
                    }
                }

                if((isset($features->$feature) and in_array($command, $features->$feature)) or strpos($appConfig, "\$config->abbreviations->$feature"))
                {
                    $this->appName = $app;
                    $count++;
                }
            }

            if($count > 1) $this->appName = $appName;
        }
    }

    /**
     * 设置目录分隔符。
     * Set the path directory separator.
     *
     * @access public
     * @return void
     */
    public function setPathFix()
    {
        define('DS', DIRECTORY_SEPARATOR);
    }

    /**
     * 设置基础目录。
     * Set the base path.
     *
     * @access public
     * @return void
     */
    public function setBasePath()
    {
        $tmp = realpath(dirname(dirname(__FILE__)));
        if ($tmp) {
            $this->basePath = $tmp . DS;
        } else {
            $this->basePath = dirname(dirname(__FILE__)) . DS;
        }
        //$this->basePath = realpath(dirname(dirname(__FILE__))) . DS;
    }

    /**
     * 设置框架根目录。
     * Set the frame root.
     *
     * @access public
     * @return void
     */
    public function setFrameRoot()
    {
        $this->frameRoot = $this->basePath . 'framework' . DS;
    }

    /**
     * 设置类库的根目录。
     * Set the app lib root.
     *
     * @access public
     * @return void
     */
    public function setCoreLibRoot()
    {
        $this->coreLibRoot = $this->basePath . 'lib' . DS;
    }

    /**
     * 设置应用的根目录。
     * Set the app root.
     *
     * @param string $appRoot
     * @access public
     * @return void
     */
    public function setAppRoot($appRoot = '')
    {
        if(empty($appRoot))  $this->appRoot = $this->basePath . 'app' . DS . $this->appName . DS;
        if(!empty($appRoot)) $this->appRoot = realpath($appRoot) . DS;
    }

    /**
     * 设置临时文件的根目录。
     * Set the tmp root.
     *
     * @access public
     * @return void
     */
    public function setTmpRoot()
    {
        $this->tmpRoot = $this->basePath . 'tmp' . DS;
    }

    /**
     * 设置缓存的根目录。
     * Set the cache root.
     *
     * @access public
     * @return void
     */
    public function setCacheRoot()
    {
        $this->cacheRoot = $this->tmpRoot . 'cache' . DS;
    }

    /**
     * 设置log的根目录。
     * Set the log root.
     *
     * @access public
     * @return void
     */
    public function setLogRoot()
    {
        $this->logRoot = $this->tmpRoot . 'log' . DS;
    }

    /**
     * 设置config配置文件的根目录。
     * Set the config root.
     *
     * @access public
     * @return void
     */
    public function setConfigRoot()
    {
        $this->configRoot = $this->basePath . 'config' . DS;
    }

    /**
     * 设置模块的根目录。
     * Set the module root.
     *
     * @access public
     * @return void
     */
    public function setModuleRoot()
    {
        $this->moduleRoot = $this->basePath . 'module' . DS . $this->appName . DS;
    }

    /**
     * 设置www的根目录。
     * Set the www root.
     *
     * @access public
     * @return void
     */
    public function setWwwRoot()
    {
        $this->wwwRoot = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), DS) . DS;
    }

   /**
     * 设置data根目录。
     * Set the data root.
     *
     * @access public
     * @return void
     */
    public function setDataRoot()
    {
        $this->dataRoot = $this->wwwRoot . 'data' . DS;
    }

    /**
     * 设置超级变量。
     * Set the super vars.
     *
     * @access public
     * @return void
     */
    public function setSuperVars()
    {
        $this->post   = new super('post');
        $this->get    = new super('get');
        $this->server = new super('server');
    }

    /**
     * 设置Debug模式。
     * set Debug.
     *
     * @access public
     * @return void
     */
    public function setDebug()
    {
        if(!empty($this->config->debug)) error_reporting(E_ALL & ~ E_STRICT);
    }

    /**
     * 设置错误处理句柄。
     * Set the error handler.
     *
     * @access public
     * @return void
     */
    public function setErrorHandler()
    {
        set_error_handler(array($this, 'saveError'));
        register_shutdown_function(array($this, 'shutdown'));
    }

    /**
     * 根据用户浏览器的语言设置和服务器配置，选择显示的语言。
     * 优先级：$lang参数 > session > cookie > 浏览器 > 配置文件。
     *
     * Set the language.
     * Using the order of method $lang param, session, cookie, browser and the default lang.
     *
     * @param   string $lang  zh-cn|zh-tw|zh-hk|en
     * @access  public
     * @return  void
     */
    public function setClientLang($lang = '')
    {
        if(!empty($lang) and isset($this->config->langs[$lang])) $this->clientLang = $lang;

        if(!empty($this->clientLang))
        {
            $this->clientLang = strtolower($this->clientLang);
            if(!isset($this->config->langs[$this->clientLang])) $this->clientLang = $this->config->default->lang;
        }
        else
        {
            $this->clientLang = $this->config->default->lang;
        }

        return true;
    }

    /**
     * 获取应用名称
     * Get app name
     *
     * @access public
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * 获取$basePath，即基础路径。
     * Get the $basePath var.
     *
     * @access public
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * 获取$frameRoot，即框架根目录。
     * Get the $frameRoot var.
     *
     * @access public
     * @return string
     */
    public function getFrameRoot()
    {
        return $this->frameRoot;
    }

    /**
     * 获取$appRoot变量，即应用的根目录。
     * Get the $appRoot var.
     *
     * @access public
     * @return string
     */
    public function getAppRoot()
    {
        return $this->appRoot;
    }

    /**
     * 获取$wwwRoot变量。
     * Get the $wwwRoot var
     *
     * @access public
     * @return string
     */
    public function getWwwRoot()
    {
        return $this->wwwRoot;
    }

    /**
     * 获取$coreLibRoot变量，即应用类库的根目录。
     * Get the $coreLibRoot var.
     *
     * @access public
     * @return string
     */
    public function getCoreLibRoot()
    {
        return $this->coreLibRoot;
    }

    /**
     * 获取$tmpRoot变量，即临时文件的根目录。
     * Get the $tmpRoot var.
     *
     * @access public
     * @return string
     */
    public function getTmpRoot()
    {
        return $this->tmpRoot;
    }

    /**
     * 获取$cacheRoot变量，即缓存文件的根目录。
     * Get the $cacheRoot var.
     *
     * @access public
     * @return string
     */
    public function getCacheRoot()
    {
        return $this->cacheRoot;
    }

    /**
     * 获取$logRoot变量，即日志文件的根目录。
     * Get the $logRoot var.
     *
     * @access public
     * @return string
     */
    public function getLogRoot()
    {
        return $this->logRoot;
    }

    /**
     * 获取$configRoot变量，即配置文件的根目录。
     * Get the $configRoot var.
     *
     * @access public
     * @return string
     */
    public function getConfigRoot()
    {
        return $this->configRoot;
    }

    /**
     * 获取$moduleRoot变量，即应用模块的根目录。
     * Get the $moduleRoot var.
     *
     * @param  string $appName
     * @access public
     * @return string
     */
    public function getModuleRoot($appName = '')
    {
        if($appName == '') return $this->moduleRoot;
        return dirname($this->moduleRoot) . DS . $appName . DS;
    }

    /**
     * 获取$webRoot，即应用的路径。
     * Get the $webRoot var.
     *
     * @access public
     * @return string
     */
    public function getWebRoot()
    {
        return $this->config->webRoot;
    }

    /**
     * 获取$dataRoot目录
     * Get the $dataRoot var
     *
     * @access public
     * @return string
     */
    public function getDataRoot()
    {
        return $this->dataRoot;
    }

   //------ 客户端环境有关的函数(Client environment related functions) ------//

    /**
     * 根据配置设置当前时区。
     * Set the time zone according to the config.
     *
     * @access public
     * @return void
     */
    public function setTimezone()
    {
        if(isset($this->config->timezone)) date_default_timezone_set($this->config->timezone);
    }

    /**
     * 获取$clientLang变量，即客户端的语言。
     * Get the $clientLang var.
     *
     * @access public
     * @return string
     */
    public function getClientLang()
    {
        return $this->clientLang;
    }

    //-------------------- 请求相关的方法(Request related methods) --------------------//

    /**
     * 解析本次请求的入口方法，根据请求的类型(PATH_INFO GET)，调用相应的方法。
     * The entrance of parseing request. According to the requestType, call related methods.
     *
     * @access public
     * @param  array $argv
     * @return void
     */
    public function parseRequest()
    {
        if($this->config->os == 'linux') system('stty erase ^H');

        global $argv, $argc;

        $this->runMainParse();

        /* Abbreviation param. */
        if(!empty($argv[1]) and isset($this->config->abbreviations->{$argv[1]}))
        {
            $newArgv = array();
            foreach($argv as $key => $val)
            {
                if($key == 1) $newArgv[] = $this->config->abbreviations->{$argv[1]};
                $newArgv[] = $val;
            }
            $argv = $newArgv;
            $argc++;
        }

        $module = (empty($argv[1]) or substr($argv[1], 0, 1) == '-' or $argv[1] == 'help') ? $this->config->default->module : $argv[1];
        $method = (empty($argv[2]) or substr($argv[2], 0, 1) == '-') ? $this->config->default->method : $argv[2];
        $this->setModuleName($module);
        $this->setMethodName($method);
        $this->setControlFile();
        if($module == 'set' and $this->moduleName == 'entry') die;

        $this->args = $argv;
    }

    /**
     * Run main parse.
     *
     * @access public
     * @return void
     */
    public function runMainParse()
    {
        global $argv, $argc, $lang;
        if(in_array($argv[1], array('-v', '--version'))) die(fwrite(STDOUT, $this->config->version . PHP_EOL));

        /* Set app. */
        if($argv[1] == 'app')
        {
            if($argv[2] == 'switch' && isset($argv[3]))
            {
                if(!isset($this->config->apps[$argv[3]])) die(helper::output($lang->appNotReal, 'err'));
                if($this->setMainConfig(array('z_app' => $argv[3]))) die(helper::output($lang->appChanged));
            }
            die(helper::output(implode(PHP_EOL, $this->config->apps)));
        }
        if($argc == 2 and isset($this->config->apps[$argv[1]]) and $this->setMainConfig(array('z_app' => $argv[1]))) die(helper::output($lang->appChanged));

        /* Set client lang. */
        if($argv[1] == 'set')
        {
            helper::output($lang->setLangTip);
            foreach($this->config->langs as $key => $language) helper::output(str_pad($key . ': ', 7) . $language);

            while(true)
            {
                $input = trim(fgets(STDIN));
                if(isset($this->config->langs[$input]) and $this->setMainConfig(array('z_clientLang' => $input)))
                {
                    $this->clientLang = $input;
                    break;
                }
                helper::output($lang->langNotReal, 'err');
            }
            $this->parseConfig();
        }

    }

    /**
     * Set main config.
     *
     * @param  array  $configs
     * @access public
     * @return bool
     */
    public function setMainConfig($configs = array())
    {
        $this->parseConfig('main');
        $mainConfigFile = dirname($this->config->userConfigFile) . DS . 'main';
        if(!is_writable($mainConfigFile)) return false;

        $configContent = file_get_contents($mainConfigFile);
        foreach($configs as $name => $value)
        {
            if(isset($this->config->$name))
            {
                $configContent = str_replace("$name = {$this->config->$name}", "$name = $value", $configContent);
            }
            else
            {
                $configContent .= "$name = $value" . PHP_EOL;
            }
        }

        $configFile = @fopen($mainConfigFile, "w");
        fwrite($configFile, $configContent);
        fclose($configFile);
        return true;
    }

    //-------------------- 模块及扩展设置(Module and extension) --------------------//

    /**
     * 加载common模块。
     *
     *  common模块比较特别，它会执行几乎每次请求都需要执行的操作，例如：
     *  打开session，检查权限等等。
     *  加载完$lang, $config, $dbh后，需要在入口文件(www/index.php)中手动调用该方法。
     *
     * Load the common module
     *
     *  The common module is a special module, which can be used to do some common things. For examle:
     *  start session, check priviledge and so on.
     *  This method should called manually in the router file(www/index.php) after the $lang, $config, $dbh loaded.
     *
     * @access public
     * @return object|bool  the common model object or false if not exits.
     */
    public function loadCommon()
    {
        $this->setModuleName('common');
        $commonModelFile = $this->setModelFile('common');
        if(!file_exists($commonModelFile)) return false;

        helper::import($commonModelFile);

        if($this->config->framework->extensionLevel == 0 and class_exists('commonModel'))    return new commonModel();
        if($this->config->framework->extensionLevel > 0  and class_exists('extCommonModel')) return new extCommonModel();

        if(class_exists('commonModel')) return new commonModel();
        return false;
    }

    /**
     * 设置要被调用的模块名。
     * Set the name of the module to be called.
     *
     * @param   string $moduleName  the module name
     * @access  public
     * @return  void
     */
    public function setModuleName($moduleName = '')
    {
        if($this->checkModuleName($moduleName)) $this->moduleName = strtolower($moduleName);
    }

    /**
     * 设置要被调用的控制器文件。
     * Set the control file of the module to be called.
     *
     * @param   bool    $exitIfNone     没有找到该控制器文件的情况：如果该参数为true，则终止程序；如果为false，则打印错误日志
     *                                  If control file not foundde, how to do. True, die the whole app. false, log error.
     * @access  public
     * @return  bool
     */
    public function setControlFile($exitIfNone = true)
    {
        $this->controlFile = $this->moduleRoot . $this->moduleName . DS . 'control.php';
        if(file_exists($this->controlFile)) return true;

        $this->setModuleName($this->config->default->module);
        $this->setControlFile();
    }

    /**
     * 设置要被调用的方法名。
     * Set the name of the method calling.
     *
     * @param string $methodName
     * @access public
     * @return void
     */
    public function setMethodName($methodName = '')
    {
        if($this->checkMethodName($methodName)) $this->methodName = strtolower($methodName);
    }

    /**
     * 获取一个模块的路径。
     * Get the path of one module.
     *
     * @param  string $appName    the app name
     * @param  string $moduleName    the module name
     * @access public
     * @return string the module path
     */
    public function getModulePath($appName = '', $moduleName = '')
    {
        if($moduleName == '') $moduleName = $this->moduleName;

        if($this->checkModuleName($moduleName))
        {
            $modulePath = $this->getModuleRoot($appName) . strtolower($moduleName) . DS;
            if($moduleName == 'common') $modulePath = dirname($modulePath, 2) . DS . 'common' . DS;
            return $modulePath;
        }
    }

    /**
     * 获取一个模块的扩展路径。 Get extension path of one module.
     *
     * If the extensionLevel == 0, return empty array.
     * If the extensionLevel == 1, return the common extension directory.
     * If the extensionLevel == 2, return the common and site extension directories.
     *
     * @param   string $appName        the app name
     * @param   string $moduleName     the module name
     * @param   string $ext            the extension type, can be control|model|view|lang|config
     * @access  public
     * @return  string the extension path.
     */
    public function getModuleExtPath($appName, $moduleName, $ext)
    {
        /* 检查失败或者extensionLevel为0，直接返回空。If check failed or extensionLevel == 0, return empty array. */
        if(!$this->checkModuleName($moduleName) or $this->config->framework->extensionLevel == 0) return array();

        /* When extensionLevel == 1. */
        $modulePath = $this->getModulePath($appName, $moduleName);
        $paths = array();
        $paths['common'] = $modulePath . 'ext' . DS . $ext . DS;
        if($this->config->framework->extensionLevel == 1) return $paths;

        /* When extensionLevel == 2. */
        $paths['site'] = empty($this->siteCode) ? '' : $modulePath . 'ext' . DS . '_' . $this->siteCode . DS . $ext . DS;
        return $paths;
    }

    /**
     * 检查模块中某一个变量必须为英文字母和数字组合。Check module a variable must be ascii.
     *
     * @param  string    $var
     * @access public
     * @return bool
     */
    public function checkModuleName($var)
    {
        global $filter;
        $rule = $filter->default->moduleName;
        if(validater::checkByRule($var, $rule)) return true;
        $this->triggerError("'$var' illegal. ", __FILE__, __LINE__, $exit = true);
    }

    /**
     * 检查方法中某一个变量必须为英文字母和数字组合。Check method a variable must be ascii.
     *
     * @param  string    $var
     * @access public
     * @return bool
     */
    public function checkMethodName($var)
    {
        global $filter;
        $rule = $filter->default->methodName;
        if($this->config->framework->filterParam == 2 and isset($filter->{$this->moduleName}->methodName)) $rule = $filter->{$this->moduleName}->methodName;

        if(validater::checkByRule($var, $rule)) return true;
        $this->triggerError("'$var' illegal. ", __FILE__, __LINE__, $exit = true);
    }

    /**
     * 设置Action的扩展文件。 Set the action extension file.
     *
     * @access  public
     * @return  bool
     */
    public function setActionExtFile()
    {
        $moduleExtPaths = $this->getModuleExtPath('', $this->moduleName, 'control');

        /* 如果扩展目录为空，不包含任何扩展文件。If there's no ext pathes return false.*/
        if(empty($moduleExtPaths)) return false;

        /* 如果extensionLevel == 2，且扩展文件存在，返回该站点扩展文件。If extensionLevel == 2 and site extensionFile exists, return it. */
        if($this->config->framework->extensionLevel == 2 and !empty( $moduleExtPaths['site']))
        {
            $this->extActionFile = $moduleExtPaths['site'] . $this->methodName . '.php';
            if(file_exists($this->extActionFile)) return true;
        }

        /* 然后再尝试寻找公共扩展文件。Then try to find the common extension file. */
        $this->extActionFile = $moduleExtPaths['common'] . $this->methodName . '.php';
        return file_exists($this->extActionFile);
    }

    /**
     * 设置一个模块的model文件，如果存在model扩展，一起合并。
     * Set the model file of one module. If there's an extension file, merge it with the main model file.
     *
     * @param   string $moduleName the module name
     * @param   string $appName the app name
     * @static
     * @access  public
     * @return  string the model file
     */
    public function setModelFile($moduleName, $appName = '')
    {
        if($appName == '') $appName = $this->getAppName();

        /* 设置主model文件。 Set the main model file. */
        $mainModelFile = $this->getModulePath($appName, $moduleName) . 'model.php';
        if($this->config->framework->extensionLevel == 0) return $mainModelFile;

        /* 计算扩展的文件和hook文件。Compute the extension files and hook files. */
        $hookFiles     = array();
        $extFiles      = array();
        $siteExtended  = false;

        $modelExtPaths = $this->getModuleExtPath($appName, $moduleName, 'model');
        foreach($modelExtPaths as $extType => $modelExtPath)
        {
            if(empty($modelExtPath)) continue;

            $tmpHookFiles = helper::ls($modelExtPath . 'hook/', '.php');
            $tmpExtFiles  = helper::ls($modelExtPath, '.php');
            $hookFiles    = array_merge($hookFiles, $tmpHookFiles);
            $extFiles     = array_merge($extFiles,  $tmpExtFiles);

            if($extType == 'site' and (!empty($tmpHookFiles) or !empty($tmpExtFiles))) $siteExtended = true;
        }

        /* 如果没有扩展文件，返回主文件。 If no extension or hook files, return the main file directly. */
        if(empty($extFiles) and empty($hookFiles)) return $mainModelFile;

        /* 计算合并之后的modelFile路径。Compute the merged model file path. */
        $extModelPrefix  = '';
        $mergedModelDir  = $this->getTmpRoot() . 'model' . DS . ($extModelPrefix ? $extModelPrefix . DS : '');
        $mergedModelFile = $mergedModelDir . $moduleName . '.php';
        if(!is_dir($mergedModelDir)) mkdir($mergedModelDir, 0755, true);

        /* 判断生成的缓存文件是否需要更新。 Judge whether the merged model file needed update or not. */
        if(!$this->needModelFileUpdate($mergedModelFile, $extFiles, $hookFiles, $modelExtPaths, $mainModelFile)) return $mergedModelFile;

        /* 合并扩展和hook文件。Merge the extension and hook files. */
        $modelLines = $this->mergeModelExtFiles($moduleName, $mainModelFile, $extFiles, $mergedModelDir);
        $this->mergeModelHookFiles($moduleName, $mainModelFile, $modelLines, $hookFiles, $mergedModelDir, $mergedModelFile);

        return $mergedModelFile;
    }

    /**
     * 检查合并之后的model文件是否需要更新。Check whether the merged model file need update or not.
     *
     * @param  string    $mainModelFile
     * @param  string    $mergedModelFile
     * @param  string    $modelExtPaths
     * @param  array     $extFiles
     * @param  array     $hookFiles
     * @access public
     * @return bool
     */
    public function needModelFileUpdate($mergedModelFile, $extFiles, $hookFiles, $modelExtPaths, $mainModelFile)
    {
        $lastTime = file_exists($mergedModelFile) ? filemtime($mergedModelFile) : 0;

        foreach($extFiles  as $extFile)  if(filemtime($extFile)  > $lastTime) return true;
        foreach($hookFiles as $hookFile) if(filemtime($hookFile) > $lastTime) return true;

        $modelExtPath  = $modelExtPaths['common'];
        $modelHookPath = $modelExtPaths['common'] . 'hook/';
        if(is_dir($modelExtPath ) and filemtime($modelExtPath)  > $lastTime) return true;
        if(is_dir($modelHookPath) and filemtime($modelHookPath) > $lastTime) return true;

        if(!empty($modelExtPaths['site']))
        {
            $modelExtPath  = $modelExtPaths['site'];
            $modelHookPath = $modelExtPaths['site'] . 'hook/';
            if(is_dir($modelExtPath ) and filemtime($modelExtPath)  > $lastTime) return true;
            if(is_dir($modelHookPath) and filemtime($modelHookPath) > $lastTime) return true;
        }

        if(filemtime($mainModelFile) > $lastTime) return true;

        return false;
    }

    /**
     * 将model的扩展文件合并在一起。Merge model ext files.
     *
     * @param  string    $moduleName
     * @param  string    $mainModelFile
     * @param  array     $extFiles
     * @param  string    $mergedModelDir
     * @access public
     * @return void
     */
    public function mergeModelExtFiles($moduleName, $mainModelFile, $extFiles, $mergedModelDir)
    {
        /* 设置类名。Set the class names. */
        $modelClass    = "{$moduleName}Model";
        $tmpModelClass = "tmpExt$modelClass";

        /* 开始拼装代码。Prepare the codes. */
        $modelLines  = "<?php\n";
        $modelLines .= "global \$app;\n";
        $modelLines .= "helper::cd(\$app->getBasePath());\n";
        $modelLines .= "helper::import('$mainModelFile');\n";
        $modelLines .= "helper::cd();\n";
        $modelLines .= "class $tmpModelClass extends $modelClass \n{\n";

        /* 将扩展文件的代码合并到代码中。Cycle all the extension files and merge them into model lines. */
        foreach($extFiles as $extFile) $modelLines .= self::removePHPTAG($extFile);

        /* 做个标记，方便后面替换代码使用。Make a mark for replacing codes. */
        $replaceMark = '//**//';
        $modelLines .= "\n$replaceMark\n}";

        /* 生成一个临时的model扩展文件，并加载，用于后续的hook文件加载使用。Create a tmp merged model file and import it for merge hook codes using. */
        $tmpModelFile = $mergedModelDir . "tmp$moduleName.php";
        if(@file_put_contents($tmpModelFile, $modelLines))
        {
            if(!class_exists($tmpModelClass)) include $tmpModelFile;
            return $modelLines;
        }

        $this->triggerError("ERROR: $tmpModelFile not writable.", __FILE__, __LINE__, true);
    }

    /**
     * 合并model的hook脚本。Merge hook files for a model.
     *
     * @access public
     * @return void
     */
    public function mergeModelHookFiles($moduleName, $mainModelFile, $modelLines, $hookFiles, $mergedModelDir, $mergedModelFile)
    {
        /* 定义相关变量。Init vars. */
        $modelClass    = $moduleName . 'Model';
        $extModelClass = 'ext' . $modelClass;
        $tmpModelClass = 'tmpExt' . $modelClass;
        $tmpModelFile  = $mergedModelDir . "tmp$moduleName.php";
        $replaceMark   = '//**//';

        /* 读取hook文件。Get hook codes need to merge. */
        $hookCodes = array();
        foreach($hookFiles as $hookFile)
        {
            /* 通过文件名获得其对应的方法名。Get methods according it's filename. */
            $fileName = baseName($hookFile);
            list($method) = explode('.', $fileName);
            $hookCodes[$method][] = self::removePHPTAG($hookFile);
        }

        /* 合并Hook文件。Cycle the hook methods and merge hook codes. */
        $hookedMethods    = array_keys($hookCodes);
        $mainModelCodes   = file($mainModelFile);
        $mergedModelCodes = file($tmpModelFile);
        foreach($hookedMethods as $method)
        {
            /* 通过反射获得hook脚本对应的方法所在的文件和起止行数。Reflection the hooked method to get it's defined position. */
            $methodRelfection = new reflectionMethod($tmpModelClass, $method);
            $definedFile = $methodRelfection->getFileName();
            $startLine   = $methodRelfection->getStartLine() . ' ';
            $endLine     = $methodRelfection->getEndLine() . ' ';

            /* 将Hook脚本和老的代码合并在一起，并替换原来的定义。Merge hook codes with old codes and replace back. */
            $oldCodes  = $definedFile == $tmpModelFile ? $mergedModelCodes : $mainModelCodes;
            $oldCodes  = join("", array_slice($oldCodes, $startLine - 1, $endLine - $startLine + 1));
            $openBrace = strpos($oldCodes, '{');
            $newCodes  = substr($oldCodes, 0, $openBrace + 1) . "\n" . join("\n", $hookCodes[$method]) . substr($oldCodes, $openBrace + 1);

            if($definedFile == $tmpModelFile) $modelLines = str_replace($oldCodes, $newCodes, $modelLines);
            if($definedFile != $tmpModelFile) $modelLines = str_replace($replaceMark, $newCodes . "\n$replaceMark", $modelLines);
        }

        /* 保存最终的Model文件。Save the last merged model file. */
        $modelLines = str_replace($tmpModelClass, $extModelClass, $modelLines);
        file_put_contents($mergedModelFile, $modelLines);
        unlink($tmpModelFile);
    }

    /**
     * Remove tags of PHP
     *
     * @param  string    $fileName
     * @static
     * @access public
     * @return string
     */
    static public function removePHPTAG($fileName)
    {
        $code = trim(file_get_contents($fileName));
        if(strpos($code, '<?php') === 0)     $code = ltrim($code, '<?php');
        if(strrpos($code, '?>')   !== false) $code = rtrim($code, '?>');
        return trim($code);
    }

    //-------------------- 路由相关方法(Routing related methods) --------------------//

    /**
     * 加载一个模块：
     * 1. 引入控制器文件或扩展的方法文件；
     * 2. 创建control对象；
     * 3. 解析url，得到请求的参数；
     * 4. 使用call_user_function_array调用相应的方法。
     *
     * Load a module.
     * 1. include the control file or the extension action file.
     * 2. create the control object.
     * 3. set the params passed in through url.
     * 4. call the method by call_user_function_array
     *
     * @access public
     * @return bool|object  if the module object of die.
     */
    public function loadModule()
    {
        $appName    = $this->appName;
        $moduleName = $this->moduleName;
        $methodName = $this->methodName;

        /*
         * 引入该模块的control文件。
         * Include the control file of the module.
         **/
        $file2Included = $this->setActionExtFile() ? $this->extActionFile : $this->controlFile;
        chdir(dirname($file2Included));
        include $file2Included;

        /*
         * 设置control的类名。
         * Set the class name of the control.
         **/
        $className = class_exists("my$moduleName") ? "my$moduleName" : $moduleName;
        if(!class_exists($className)) $this->triggerError("the control $className not found", __FILE__, __LINE__, $exit = true);

        /*
         * 创建control类的实例。
         * Create a instance of the control.
         **/
        $module = new $className();
        if(!method_exists($module, $methodName)) $methodName = $this->config->default->method;
        $this->control = $module;

        /* include default value for module*/
        $defaultValueFiles = glob($this->getTmpRoot() . "defaultvalue/*.php");
        if($defaultValueFiles) foreach($defaultValueFiles as $file) include $file;

        /*
         * 使用反射机制获取函数参数的默认值。
         * Get the default settings of the method to be called using the reflecting.
         *
         * */
        $defaultParams = array();
        $methodReflect = new reflectionMethod($className, $methodName);
        foreach($methodReflect->getParameters() as $param)
        {
            $name = $param->getName();

            $default = '_NOT_SET';
            if(isset($paramDefaultValue[$appName][$className][$methodName][$name]))
            {
                $default = $paramDefaultValue[$appName][$className][$methodName][$name];
            }
            elseif(isset($paramDefaultValue[$className][$methodName][$name]))
            {
                $default = $paramDefaultValue[$className][$methodName][$name];
            }
            elseif($param->isDefaultValueAvailable())
            {
                $default = $param->getDefaultValue();
            }

            $defaultParams[$name] = $default;
        }

        $this->params = $this->formatParams($moduleName, $methodName);

        /* 调用该方法   Call the method. */
        call_user_func_array(array($module, $methodName), $this->params);
        return $module;
    }

    /**
     * 整理参数信息。
     * Format params.
     *
     * @access public
     * @return array
     */
    public function formatParams($moduleName = '', $methodName = '')
    {
        $params   = array();
        $paramKey = '';
        foreach($this->args as $key => $val)
        {
            if($key == 0) continue;
            if($key < 2 and substr($val, 0, 1) != '-') continue;

            if(isset($this->config->arguments[$moduleName . $val]) or isset($this->config->arguments[$val]))
            {
                $paramKey = isset($this->config->arguments[$moduleName . $val]) ? $this->config->arguments[$moduleName . $val] : $this->config->arguments[$val];
                $params[$paramKey] = '';
            }
            elseif($key == 3 and substr($val, 0, 1) != '-' and isset($this->config->$moduleName->paramKey[$methodName]))
            {
                $params[$this->config->$moduleName->paramKey[$methodName]] = $val;
            }
            elseif($paramKey)
            {
                $params[$paramKey] = $val;
            }
        }

        return array('params' => $params);
    }

    /**
     * 获取$moduleName变量。
     * Get the $moduleName var.
     *
     * @access public
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * 获取$controlFile变量。
     * Get the $controlFile var.
     *
     * @access public
     * @return string
     */
    public function getControlFile()
    {
        return $this->controlFile;
    }

    /**
     * 获取$methodName变量。
     * Get the $methodName var.
     *
     * @access public
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * 获取$param变量。
     * Get the $param var.
     *
     * @access public
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    //-------------------- 常用的工具方法(Tool methods) ------------------//

    /**
     * 从类库中加载一个类文件。
     *
     * Load a class file.
     *
     * @param   string $className  the class name
     * @param   bool   $static     statis class or not
     * @access  public
     * @return  object|bool the instance of the class or just true.
     */
    public function loadClass($className, $static = false)
    {
        $className = strtolower($className);

        /* 搜索$coreLibRoot(Search in $coreLibRoot) */
        $classFile = $this->coreLibRoot . $className;
        if(is_dir($classFile)) $classFile .= DS . $className;
        $classFile .= '.class.php';
        if(!helper::import($classFile)) $this->triggerError("class file $classFile not found", __FILE__, __LINE__, $exit = true);

        /* 如果是静态调用，则返回(If staitc, return) */
        if($static) return true;

        /* 实例化该类(Instance it) */
        global $$className;
        if(!class_exists($className)) $this->triggerError("the class $className not found in $classFile", __FILE__, __LINE__, $exit = true);
        if(!is_object($$className)) $$className = new $className();
        return $$className;
    }

    /**
     * 加载整个应用公共的配置文件。
     * Load the common config files for the app.
     *
     * @access public
     * @return void
     */
    public function loadMainConfig()
    {
        /* 初始化$config对象。Init the $config object. */
        global $config, $filter;
        if(!is_object($config)) $config = new config();
        $this->config = $config;

        /* 加载主配置文件。 Load the main config file. */
        $mainConfigFile = $this->configRoot . 'config.php';
        if(!file_exists($mainConfigFile)) $this->triggerError("The main config file $mainConfigFile not found", __FILE__, __LINE__, $exit = true);
        include $mainConfigFile;
    }

    /**
     * 加载当前应用的配置文件。
     * Load the common config files for the app.
     *
     * @access public
     * @return void
     */
    public function loadAppConfig()
    {
        global $config;

        /* 加载配置文件。 Load the app config file. */
        $appConfigFile = $this->configRoot . $this->appName . '.php';
        if(file_exists($appConfigFile)) include $appConfigFile;
    }

    /**
     * 当multiSite功能打开的时候，加载额外的配置文件。
     * When multiSite feature enabled, load extra config file.
     *
     * @access public
     * @return void
     */
    public function loadExtraConfig()
    {
        global $config;
        $multiConfigFile = $this->configRoot . 'multi.php';
        if(file_exists($multiConfigFile)) include $multiConfigFile;

        $siteConfigFile = $this->configRoot . "sites/{$this->siteCode}.php";
        if(file_exists($siteConfigFile))  include $siteConfigFile;
    }

    /**
     * 加载模块的config文件，返回全局$config对象。
     * 如果该模块是common，加载$configRoot的配置文件，其他模块则加载其模块的配置文件。
     *
     * Load config and return it as the global config object.
     * If the module is common, search in $configRoot, else in $modulePath.
     *
     * @param   string $moduleName     module name
     * @param   string $appName        app name
     * @param   bool   $exitIfNone     exit or not
     * @access  public
     * @return  object|bool the config object or false.
     */
    public function loadModuleConfig($moduleName, $appName = '')
    {
        global $config;

        if($config and (!isset($config->$moduleName) or !is_object($config->$moduleName))) $config->$moduleName = new stdclass();

        /* 初始化数组。Init the variables. */
        $extConfigFiles       = array();
        $commonExtConfigFiles = array();
        $siteExtConfigFiles   = array();

        /* 先获得模块的主配置文件。Get the main config file for current module first. */
        $mainConfigFile = $this->getModulePath($appName, $moduleName) . 'config.php';

        /* 查找扩展配置文件。Get extension config files. */
        if($config->framework->extensionLevel > 0) $extConfigPath = $this->getModuleExtPath($appName, $moduleName, 'config');
        if($config->framework->extensionLevel >= 1 and !empty($extConfigPath['common'])) $commonExtConfigFiles = helper::ls($extConfigPath['common'], '.php');
        if($config->framework->extensionLevel == 2 and !empty($extConfigPath['site']))   $siteExtConfigFiles   = helper::ls($extConfigPath['site'], '.php');
        $extConfigFiles = array_merge($commonExtConfigFiles, $siteExtConfigFiles);

        /* 将主配置文件和扩展配置文件合并在一起。Put the main config file and extension config files together. */
        $configFiles = array_merge(array($mainConfigFile), $extConfigFiles);

        /* 加载每一个配置文件。Load every config file. */
        static $loadedConfigs = array();
        foreach($configFiles as $configFile)
        {
            if(in_array($configFile, $loadedConfigs)) continue;
            if(file_exists($configFile)) include $configFile;
            $loadedConfigs[] = $configFile;
        }

        /* 加载数据库中与本模块相关的配置项。Merge from the db configs. */
        if($moduleName != 'common')
        {
            if(isset($config->system->$moduleName))   $this->mergeConfig($config->system->$moduleName, $moduleName);
            if(isset($config->personal->$moduleName)) $this->mergeConfig($config->personal->$moduleName, $moduleName);
        }
    }

    /**
     * Merge db config.
     *
     * @param  array  $dbConfig
     * @param  string $moduleName
     * @access public
     * @return void
     */
    public  function mergeConfig($dbConfig, $moduleName = 'common')
    {
        global $config;

        /* 如果没有设置本模块配置，则首先进行初始化。Init the $config->$moduleName if not set.*/
        if($moduleName != 'common' and !isset($config->$moduleName)) $config->$moduleName = new stdclass();

        $config2Merge = $config;
        if($moduleName != 'common') $config2Merge = $config->$moduleName;

        foreach($dbConfig as $item)
        {
            if($item->section)
            {
                if(!isset($config2Merge->{$item->section})) $config2Merge->{$item->section} = new stdclass();
                if(is_object($config2Merge->{$item->section}))
                {
                    $config2Merge->{$item->section}->{$item->key} = $item->value;
                }
            }
            else
            {
                $config2Merge->{$item->key} = $item->value;
            }
        }
    }

    /**
     * 向客户端输出配置参数，客户端可以根据这些参数实现和调整请求的逻辑。
     * Export the config params to the client, thus the client can adjust it's logic according the config.
     *
     * @access public
     * @return void
     */
    public function exportConfig()
    {
        $view = new stdclass();
        $view->version     = $this->config->version;
        $view->requestType = $this->config->requestType;
        $view->requestFix  = $this->config->requestFix;
        $view->moduleVar   = $this->config->moduleVar;
        $view->methodVar   = $this->config->methodVar;
        $view->viewVar     = $this->config->viewVar;
        $view->sessionVar  = $this->config->sessionVar;

        $this->session->set('random', mt_rand(0, 10000));
        $view->sessionName = session_name();
        $view->sessionID   = session_id();
        $view->random      = $this->session->random;
        $view->expiredTime = ini_get('session.gc_maxlifetime');
        $view->serverTime  = time();

        echo json_encode($view);
    }

    /**
     * 加载语言文件，返回全局$lang对象。
     * Load lang and return it as the global lang object.
     *
     * @param   string $moduleName     the module name
     * @param   string $appName     the app name
     * @access  public
     * @return  bool|ojbect the lang object or false.
     */
    public function loadLang($moduleName, $appName = '')
    {
        /* 初始化变量。Init vars. */
        $modulePath      = $this->getModulePath($appName, $moduleName);
        $extLangFiles    = array();
        $langFilesToLoad = array();

        /* 判断主语言文件是否存在。Whether the main lang file exists or not. */
        $mainLangFile = $modulePath . 'lang' . DS . $this->clientLang . '.php';
        if(file_exists($mainLangFile)) $langFilesToLoad[] = $mainLangFile;

        /* 获取扩展语言文件。If extensionLevel > 0, get extension lang files. */
        if($this->config->framework->extensionLevel > 0)
        {
            $commonExtLangFiles = array();
            $siteExtLangFiles   = array();

            $extLangPath = $this->getModuleExtPath($appName, $moduleName, 'lang');
            if($this->config->framework->extensionLevel >= 1 and !empty($extLangPath['common'])) $commonExtLangFiles = helper::ls($extLangPath['common'] . $this->clientLang, '.php');
            if($this->config->framework->extensionLevel == 2 and !empty($extLangPath['site']))   $siteExtLangFiles   = helper::ls($extLangPath['site'] . $this->clientLang, '.php');
            $extLangFiles  = array_merge($commonExtLangFiles, $siteExtLangFiles);
        }

        /* 计算最终要加载的语言文件。 Get the lang files to be loaded. */
        $langFilesToLoad = array_merge($langFilesToLoad, $extLangFiles);
        if(empty($langFilesToLoad)) return false;

        /* 加载语言文件。Load lang files. */
        global $lang;
        if(!is_object($lang)) $lang = new language();
        if(!isset($lang->$moduleName)) $lang->$moduleName = new stdclass();

        static $loadedLangs = array();
        foreach($langFilesToLoad as $langFile)
        {
            if(in_array($langFile, $loadedLangs)) continue;
            include $langFile;
            $loadedLangs[] = $langFile;
        }

        $this->lang = $lang;
        return $lang;
    }

    //-------------------- 错误处理方法(Error methods) ------------------//

    /**
     * 程序停止时执行的函数。
     * The shutdown handler.
     *
     * @access public
     * @return void
     */
    public function shutdown()
    {
        /*
         * 发现错误，保存到日志中。
         * If any error occers, save it.
         */
        if(!function_exists('error_get_last')) return;
        $error = error_get_last();
        if($error) $this->saveError($error['type'], $error['message'], $error['file'], $error['line']);
    }

    /**
     * 触发一个错误。
     * Trigger an error.
     *
     * @param string    $message    错误信息      error message
     * @param string    $file       所在文件      the file error occers
     * @param int       $line       错误行        the line error occers
     * @param bool      $exit       是否停止程序  exit the program or not
     * @access public
     * @return void
     */
    public function triggerError($message, $file, $line, $exit = false)
    {
        /* 设置错误信息(Set the error info) */
        $message = htmlspecialchars($message);
        $log     = "ERROR: $message in $file on line $line";
        if(isset($_SERVER['SCRIPT_URI'])) $log .= ", request: $_SERVER[SCRIPT_URI]";;
        $trace = debug_backtrace();
        extract($trace[0]);
        extract($trace[1]);
        $log .= ", last called by $file on line $line through function $function.\n";

        /* 触发错误(Trigger the error) */
        trigger_error($log, $exit ? E_USER_ERROR : E_USER_WARNING);
    }

    /**
     * 保存错误信息。
     * Save error info.
     *
     * @param  int    $level
     * @param  string $message
     * @param  string $file
     * @param  int    $line
     * @access public
     * @return void
     */
    public function saveError($level, $message, $file, $line)
    {
        if(empty($this->config->debug))  return true;
        if(!is_dir($this->logRoot))      return true;
        if(!is_writable($this->logRoot)) return true;

        /*
         * 删除设定时间之前的日志。
         * Delete the log before the set time.
         **/
        if(mt_rand(0, 10) == 1)
        {
            $logDays = isset($this->config->framework->logDays) ? $this->config->framework->logDays : 14;
            $dayTime = time() - $logDays * 24 * 3600;
            foreach(glob($this->getLogRoot() . '*') as $logFile)
            {
                if(filemtime($logFile) <= $dayTime) unlink($logFile);
            }
        }

        /*
         * 忽略该错误：Redefining already defined constructor。
         * Skip the error: Redefining already defined constructor.
         **/
        if(strpos($message, 'Redefining') !== false) return true;

        /*
         * 设置错误信息。
         * Set the error info.
         **/
        $errorLog  = "\n" . date('H:i:s') . " $message in <strong>$file</strong> on line <strong>$line</strong> ";
        $errorLog .= "when visiting <strong>" . $this->getURI() . "</strong>\n";

        /*
         * 为了安全起见，对公网环境隐藏脚本路径。
         * If the ip is pulic, hidden the full path of scripts.
         */
        if(!defined('IN_SHELL') and !($this->server->remote_addr == '127.0.0.1' or filter_var($this->server->remote_addr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false))
        {
            $errorLog  = str_replace($this->getBasePath(), '', $errorLog);
        }

        /* 保存到日志文件(Save to log file) */
        $errorFile = $this->logRoot . 'php.' . date('Ymd') . '.log.php';
        if(!is_file($errorFile)) file_put_contents($errorFile, "<?php\n die();\n?>\n");

        $fh = fopen($errorFile, 'a');
        if($fh) fwrite($fh, strip_tags($errorLog)) and fclose($fh);

        /*
         * 如果debug > 1，显示warning, notice级别的错误。
         * If the debug > 1, show warning, notice error.
         **/
        if($level == E_NOTICE or $level == E_WARNING or $level == E_STRICT or $level == 8192) // 8192: E_DEPRECATED
        {
            if(!empty($this->config->debug) and $this->config->debug > 1)
            {
                $cmd  = "vim +$line $file";
                $size = strlen($cmd);
                echo "<pre class='alert alert-danger'>$message: ";
                echo "<input type='text' value='$cmd' size='$size' style='border:none; background:none;' onclick='this.select();' /></pre>";
            }
        }

        /*
         * 如果是严重错误，停止程序。
         * If error level is serious, die.
         * */
        if($level == E_ERROR or $level == E_PARSE or $level == E_CORE_ERROR or $level == E_COMPILE_ERROR or $level == E_USER_ERROR)
        {
            if(empty($this->config->debug)) die();
            if(PHP_SAPI == 'cli') die($errorLog);

            $htmlError  = "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
            $htmlError .= "<body>" . nl2br($errorLog) . "</body></html>";
            die($htmlError);
        }
    }
}

/**
 * config类。
 * The config class.
 *
 * @package framework
 */
class config
{
    /**
     * 设置成员变量，成员可以是'db.user'类似的格式。
     * Set the value of a member. the member can be the format like db.user.
     *
     * <code>
     * <?php
     * $config->set('db.user', 'wwccss');
     * ?>
     * </code>
     * @param   string  $key    the key of the member
     * @param   mixed   $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        helper::setMember('config', $key, $value);
    }
}

/**
 * lang类。
 * The lang class.
 *
 * @package framework
 */
class language
{
    /**
     * 设置成员变量，成员可以是'db.user'类似的格式。
     * Set the value of a member. the member can be the foramt like db.user.
     *
     * <code>
     * <?php
     * $lang->set('version', '1.0);
     * ?>
     * </code>
     * @param   string  $key    成员的键名，可以是father.child的形式。
     *                          the key of the member, can be father.child
     * @param   mixed   $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        helper::setMember('lang', $key, $value);
    }

    /**
     * 显示一个成员的值。
     * Show a member.
     *
     * @param   object $obj    the object
     * @param   string $key    the key
     * @access  public
     * @return  void
     */
    public function show($obj, $key)
    {
        $obj = (array)$obj;
        echo isset($obj[$key]) ? $obj[$key] : '';
    }
}

/**
 * 超级对象类，转化超级全局变量。
 * The super object class.
 *
 * @package framework
 */
class super
{
    /**
     * 构造函数，设置超级变量名。
     * Construct, set the var scope.
     *
     * @param   string $scope  the scope, can be server, post, get, cookie, session, global
     * @access  public
     * @return  void
     */
    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    /**
     * 设置超级变量的成员值。
     * Set one member value.
     *
     * @param   string    the key
     * @param   mixed $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        if($this->scope == 'post')
        {
            $_POST[$key] = $value;
        }
        elseif($this->scope == 'get')
        {
            $_GET[$key] = $value;
        }
        elseif($this->scope == 'server')
        {
            $_SERVER[$key] = $value;
        }
        elseif($this->scope == 'cookie')
        {
            $_COOKIE[$key] = $value;
        }
        elseif($this->scope == 'session')
        {
            $_SESSION[$key] = $value;
        }
        elseif($this->scope == 'env')
        {
            $_ENV[$key] = $value;
        }
        elseif($this->scope == 'global')
        {
            $GLOBALS[$key] = $value;
        }
    }

    /**
     * 超级变量的魔术方法，比如用$post->key访问$_POST['key']。
     * The magic get method.
     *
     * @param  string $key    the key
     * @access public
     * @return mixed|bool     return the value of the key or false.
     */
    public function __get($key)
    {
        if($this->scope == 'post')
        {
            if(isset($_POST[$key])) return $_POST[$key];
            return false;
        }
        elseif($this->scope == 'get')
        {
            if(isset($_GET[$key])) return $_GET[$key];
            return false;
        }
        elseif($this->scope == 'server')
        {
            if($key == 'ajax') return isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false;
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            $key = strtoupper($key);
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            return false;
        }
        elseif($this->scope == 'cookie')
        {
            if(isset($_COOKIE[$key])) return $_COOKIE[$key];
            return false;
        }
        elseif($this->scope == 'session')
        {
            if(isset($_SESSION[$key])) return $_SESSION[$key];
            return false;
        }
        elseif($this->scope == 'env')
        {
            if(isset($_ENV[$key])) return $_ENV[$key];
            return false;
        }
        elseif($this->scope == 'global')
        {
            if(isset($GLOBALS[$key])) return $GLOBALS[$key];
            return false;
        }
        else
        {
            return false;
        }
    }

    /**
     * 打印变量的详细结构。
     * Print the structure.
     *
     * @access public
     * @return void
     */
    public function a()
    {
        if($this->scope == 'post')    a($_POST);
        if($this->scope == 'get')     a($_GET);
        if($this->scope == 'env')     a($_ENV);
        if($this->scope == 'global')  a($GLOBALS);
    }
}
