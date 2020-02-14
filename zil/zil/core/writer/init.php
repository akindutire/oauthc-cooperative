<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;
use zil\blueprint\Blueprint;

use zil\factory\Filehandler;


class Init implements Writer
{

    public $_isDefault = false;

    public function __construct()
    {
    }

    public function create(Info $Info)
    {
        try {
            if (($Info->getAppBase() !== null) && ($Info->getAppName() !== null)) {

                $app_base = $Info->getAppBase();
                $app_name = $Info->getAppName();
                $AppDir = $Info->getAppDir();

            } else {
                throw new \Exception("Error: Couldn't resolve app directories");
            }

            /**
             * Handle of program context
             * @var Blueprint
             */
            $blueprint = new Blueprint($app_name);

            /**
             * Configuration key
             * @var [type]
             */
            $config[] = $app_name;

            /**
             * First app are default app and setup config array
             * @var [string]
             */
            if (!file_exists("{$AppDir}/index.php")) {
                $this->_isDefault = true;
                $Info->getTree()->defaultApp = $app_name;
                $config[] = $app_name;
            }

            /**
             * Load existing app into configuration space
             * @var [type]
             */
            foreach ($Info->getTree()->apps as $app) {
                $config[] = $app->name;
            }

            /**
             * Reset Configuration space to entail unique apps
             */
            $config = array_unique($config, SORT_REGULAR);

            /**
             * For Initialization file
             * @var [type]
             */
            $Base = $blueprint->init($config);
            (new Filehandler())->createFile("{$AppDir}/{$Base['filename']}", $Base['code']);

            /**
             * For Initialization server handler
             * @var [type]
             */
            $Base = $blueprint->hts();
            (new Filehandler())->createFile("{$AppDir}/{$Base['filename']}", $Base['code']);

            /**
             * For Project Composer
             * @var [type]
             */
            $Base = $blueprint->composer();
            if (!\file_exists("{$AppDir}/{$Base['filename']}")) {
                $Info->getprogressMessage("createComposer");
                (new Filehandler())->createFile("{$AppDir}/{$Base['filename']}", $Base['code']);
            }

            /**
             * For App configuration
             * @var [type]
             */
            $Base = $blueprint->config();
            $Info->getprogressMessage("createConfiguration");
            (new Filehandler())->createFile("{$app_base}/config/{$Base['filename']}", $Base['code']);

            /**
             * For App routes
             * @var [type]
             */
            $Base = $blueprint->route('Api');
            echo "Preparing Api Route...\n";
            (new Filehandler())->createFile("{$app_base}/route/{$Base['filename']}", $Base['code']);

            /**
             * For App route
             */
            $Base = $blueprint->route('Web');
            echo "Preparing Web Route...\n";
            (new Filehandler())->createFile("{$app_base}/route/{$Base['filename']}", $Base['code']);

            /**
             * For App controller
             * @var [type]
             */
            $Base = $blueprint->controller('Home');
            $Info->getprogressMessage("createController");
            (new Filehandler())->createFile("{$app_base}/controller/{$Base['filename']}", $Base['code']);

            /**
             * Autoload classes
             */
            print(shell_exec($Info->getCommand("autoload"))) . "\n";
        } catch (\Exception $e) {
            print($e->getMessage() . ' on line ' . $e->getLine() . ' (' . $e->getFile() . ")\n");
        } catch (\Throwable $e) {
            print($e->getMessage() . ' on line ' . $e->getLine() . ' (' . $e->getFile() . ")\n");
        }
    }

    public function destroy(Info $Info, ?string $name = null)
    {
        try {
            if (($Info->getAppBase() !== null) && ($Info->getAppName() !== null)) {
                $app_base = $Info->getAppBase();
                $app_name = $Info->getAppName();
                $AppDir = $Info->getAppDir();
            } else {
                throw new \Exception("Error: Couldn't resolve app directories");
            }

            /**
             * Handle of program context
             * @var Blueprint
             */
            $blueprint = new Blueprint($app_name);

            /**
             * Load existing app into configuration space
             * @var [type]
             */
            $config = [];
            foreach ($Info->getTree()->apps as $app) {
                $config[] = $app->name;
            }
            /**
             * Set next app in configuration space as default
             */
            $Info->getTree()->defaultApp = isset($config[0]) ? $config[0] : '';
            $Info->getTree()->currentApp = isset($config[0]) ? $config[0] : '';

            /**
             * Reset Configuration space to entail unique apps
             */
            $config = array_unique($config, SORT_REGULAR);

            /**
             * For Initialization file
             * @var [type]
             */
            $Base = $blueprint->init($config);
            (new Filehandler())->createFile("{$AppDir}/{$Base['filename']}", $Base['code']);

            if (count((array)$Info->getTree()->apps) == 0) {
                if (file_exists("{$Info->getAppDir()}index.php"))
                    unlink("{$Info->getAppDir()}index.php");

                if (file_exists("{$Info->getAppDir()}.htaccess"))
                    unlink("{$Info->getAppDir()}.htaccess");
            }
        } catch (\Exception $e) {
            print($e->getMessage() . ' on line ' . $e->getLine() . ' (' . $e->getFile() . ")\n");
        } catch (\Throwable $e) {
            print($e->getMessage() . ' on line ' . $e->getLine() . ' (' . $e->getFile() . ")\n");
        } finally {
            print "destruction closed\n";
        }
    }
}

?>
