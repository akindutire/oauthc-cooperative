<?php
declare(strict_types=1);

namespace zil;

error_reporting(E_ALL);

use zil\core\server\Router;
use zil\core\tracer\ErrorTracer;
use zil\core\directory\Tree;

use zil\core\exception\BadConfigurationException;

use zil\core\interfaces\Config;

use zil\core\middleware\Session as SessionMiddleware;

use zil\factory\Filehandler;


class App
{

    use \zil\core\facades\helpers\Reporter;
    use \zil\core\facades\decorators\Route_D1;

    protected static $_curSysPath = null;
    protected static $_curAppName = null;
    protected static $_curAppPath = null;
    protected static $_databaseParams = [];
    protected static $_eventLog = true;
    protected static $_requestBase = null;
    protected static $_configOptions = [];
    protected static $_corsPolicy = [];

    /**
     * @var Config
     */
    private $config;


    /**
     * Set up app
     *
     * @param array $config
     * @param boolean $eventLog
     */
    public function __construct(array $configs)
    {

        try {
            list($srcBaseIndicator) = explode("/", trim($_SERVER['REQUEST_URI'], '/'));
            /**
             * Boolean that describes when to break out of app search loop
             * @var boolean
             */
            $appFound = false;
            /**
             * App Search loop
             * @var [Config]
             */
            $Tree = new Tree;
            $defaultApp = $Tree->getTree()->defaultApp;
            foreach ($configs as $k => $x_config) {
                if (in_array('zil\core\interfaces\Config', class_implements($x_config))) {
                    /**
                     * [Condition Run when first segment of request URI matches doesn't highlight any app. name]
                     * @var [string]
                     */
                    if (empty($srcBaseIndicator) || !is_dir("src/{$srcBaseIndicator}")) {
                        if (!empty($defaultApp)) {
                            if ($defaultApp == $x_config->getAppName()) {
                                $config = $x_config;
                                $appFound = true;
                                break;
                            }else{
                                continue;
                            }
                        } else {
                            throw new \Exception("Unknown application, at least provide one default app to load anonymous app");
                        }
                    } else {
                        if (is_dir("src/{$srcBaseIndicator}") && $srcBaseIndicator == $x_config->getAppName()) {
                            $config = $x_config;
                            $appFound = true;
                            break;
                        }
                    }
                } else {
                    throw new BadConfigurationException("Configuration class must implement zil\core\Config");
                }
            }

            if ($appFound == false) {
                throw new \Exception("Unknown application, at least provide one default app to load anonymous app");
            }

            self::$_curAppPath = '/src/' . $config->getAppName() . '/';
            self::$_databaseParams = $config->getDatabaseParams();
            self::$_curAppName = $config->getAppName();
            self::$_eventLog = false;
            self::$_curSysPath = __DIR__;
            self::$_corsPolicy = $config->getCorsPolicy();

            /**
             * Get Request base
             */
            self::$_requestBase = '/';
            if ( $Tree->getAppTree($config->getAppName())->name !== $Tree->getTree()->defaultApp)
                self::$_requestBase = "/" . $config->getAppName() . '/';

            /**
             * Get Config Options
             */
            if (sizeof($config->options()) > 0)
                self::$_configOptions = $config->options();

            $this->SessionInit($config->getAppName());

        } catch (\InvalidArgumentException $t) {
            new ErrorTracer($t);
        } catch (\BadMethodCallException $t) {
            new ErrorTracer($t);
        } catch (\Throwable | BadConfigurationException $t) {
            new ErrorTracer($t);
        }
        $this->config = $config;
    }

    /**
     * Initialize Session
     * @method SessionInit
     * @param string $prefix [description]
     */
    private function SessionInit(string $prefix): void
    {
        try {
            /**
             * Base folder of project
             * @var [type]
             */
            $projectBasePath = isset(self::$_configOptions['projectBasePath']) ? self::$_configOptions['projectBasePath'] : '/';
            $session_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $projectBasePath . str_replace("\\", "/", self::$_curAppPath) . "/session/";

            if (!is_dir($session_path))
                (new Filehandler())->createDir($session_path, 0775);

            SessionMiddleware::secureSession($session_path, $prefix);
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Bootstrap  the application
     * @return void
     */
    public function start()
    {
        try {
            Router::Route();
            return;
        } catch (\BadMethodCallException $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Bootstrap app while on development mode
     * @param string $only [Acceptable Ips]
     * @return void
     */
    public function dev(string ...$onlys)
    {
        try {
            if (in_array($this->ipDetect(), $onlys))
                $this->start();
            else
                self::report(503);

        } catch (\TypeError $t) {
            new ErrorTracer($t);
        } catch (\DomainException $t) {
            new ErrorTracer($t);
        } catch (\Exception $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Exit app
     * @method stop
     */
    public function stop(): void
    {
        try {
            list(self::$_curAppPath, self::$_databaseParams, self::$_curSysPath, self::$_eventLog) = [[], null, [], null, null];
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }
}

?>
