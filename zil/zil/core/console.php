<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core;

use zil\core\scrapper\Info;
use zil\core\writer\Component;
use zil\core\writer\Controller;
use zil\core\writer\Middleware;
use zil\core\writer\Migration;
use zil\core\writer\Model;
use zil\core\writer\Service;
use zil\core\writer\View;
use zil\factory\Schema;


class Console
{


    private function isFlag($command): bool
    {
        try {
            if (strpos($command, "-") !== false)
                return true;

            return false;
        } catch (\Throwable $t) {
            print($t->getMessage() . ' on line ' . $t->getLine() . ' (' . $t->getFile() . ")\n");
        }
    }

    private function help()
    {
        try {
            $mask = "%-20.27s \t %-30.60s\n";
            printf($mask, "use app <name>", "Switch app");
            printf($mask, "show --apps", "Show all apps");
            printf($mask, "show --current", "Show current working app\n");

            printf($mask, "app <name>", "Create app\n");

            printf($mask, "service <name>", "Create service");
            printf($mask, "controller <name>", "Create controller");
            printf($mask, "api <name>", "Create api\n");

            printf($mask, "view <name> -c <name>", "Create view");
            printf($mask, "component <name> -cp <name>", "Create component");
            printf($mask, "-c <name>", "Attach view to a controller\n");

            printf($mask, "middleware <name>", "Create a new middleware");
            printf($mask, "-md <name>", "Create a new middleware");

            printf($mask, "migration <name>", "Create a new migration");
            printf($mask, "-table <name>", "Use a table\n");
            printf($mask, "-t <name>", "Use a table\n");

            printf($mask, "migrate <name>", "Migrate <name>");
            printf($mask, "--all", "Migrate all from new migration");
            printf($mask, "--reset", "Delete all model and schema");
            printf($mask, "--rollback", "Rollback migration\n");
            printf($mask, "--no-model", "Don't point table to a model\n");

            printf($mask, "destroy", "Destroy app\n");
            printf($mask, "serve", "Start php development server");
            printf($mask, "prod", "Activate production mode");
            printf($mask, "dev", "Activate development mode");

        } catch (\Throwable $t) {
            print($t->getMessage() . ' on line ' . $t->getLine() . ' (' . $t->getFile() . ")\n");
        }
    }

    public function run(array $command, int $command_count, string $cwd)
    {
        try {
            $AppManager = new ApplicationManager();
            $command_string = preg_replace('/[\s]+/', ' ', implode(' ', $command));
            $command = explode(' ', $command_string);
            unset($command[0], $command_string);

            $command = array_values($command);
            if ($command_count == 0) {
                $this->help();
                exit();
            }

            if ($command_count == 1 || $command[0] == '-h' || $command[0] == 'h' || $command[0] == '?' || $command[0] == '-help' || $command[0] == 'help') {
                $this->help();
                exit();
            }

            if ($command_count > 1) {
                if ($command[0] != 'app') {
                    /**
                     * Highlight current working app.
                     **/
                    printf("\n\033[1;32m---CURRENT APP-NODE:%s Begins Task----\033[0m\n\n", (new Info())->getCurrentApp());
                }


                if ($command[0] == 'use') {


                    $name = strpos($command[1], '-') == false ? $command[1] : null;
                    if (!is_null($name))
                        $AppManager->useApp($name);
                    else
                        throw new \Exception("Error: Undefined app name");

                    exit();
                }

                if ($command[0] == 'exit') {

                    $name = strpos($command[1], '-') == false ? $command[1] : null;
                    if (!is_null($name))
                        $AppManager->useApp($name);
                    else
                        throw new \Exception("Error: Undefined app name");

                    exit();
                }

                if ($command[0] == 'show') {

                    $command = strpos($command[1], '-') == false ? strtolower($command[1]) : null;
                    if ($command == '--apps') {

                        foreach ($AppManager->showApps() as $app_name) {
                            print $app_name;
                        }

                    } else if ($command == '--current') {

                        print (new Info())->getCurrentApp() . "\n";

                    } else if ($command == '?') {

                        $mask = "%-20.27s \t %-30.60s\n";
                        printf($mask, "--apps", "List all apps");
                        printf($mask, "--current", "List current working app");

                    } else {
                        throw new \Exception("Invalid command: check show ?");

                    }

                    exit();
                }

                if ($command[0] == 'app') {

                    $command_found = true;

                    $name = strpos($command[1], '-') == false ? $command[1] : null;
                    if (!is_null($name)) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($name);

                        $AppManager->createApp($Info);

                    } else {
                        throw new \Exception("Error: Undefined app name");
                    }

                    exit();
                }

                if ($command[0] == 'api') {

                    $apiName = strpos($command[1], '-') == false ? $command[1] : null;
                    $appPointer = null;


                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        (new Controller())->create($Info, 'api/' . $apiName, 'Api');

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }


                if ($command[0] == 'controller' || $command[0] == '-c') {

                    $controllerName = strpos($command[1], '-') == false ? $command[1] : null;
                    $appPointer = null;
                    $viewName = null;

                    foreach ($command as $k => $args) {

                        if (($args == "-v" || $args == "-view") && isset($command[$k + 1])) {
                            $viewName = $command[$k + 1];
                        } else if ($args == "?") {

                            $mask = "%-20.27s \t %-30.60s\n";
                            printf($mask, "-v <name>", "Create controller and a view");
                            exit();
                        }
                    }


                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if (!is_null($viewName))
                            (new Controller())->createEx($Info, $controllerName, $viewName);
                        else
                            (new Controller())->createEx($Info, $controllerName);
                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'view' || $command[0] == '-v') {

                    $viewName = strpos($command[1], '-') == false ? $command[1] : null;

                    foreach ($command as $k => $args) {


                        if (($args == "-c" || $args == "-controller") && isset($command[$k + 1]))
                            $controllerName = $command[$k + 1];

                    }


                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {
                        $Info = new Info();

                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if (!isset($controllerName)) {
                            (new View())->create($Info, $viewName);
                        } else {
                            (new View())->createWithComponent($Info, $viewName, $controllerName);
                        }

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'component' || $command[0] == '-cp') {

                    $componentName = strpos($command[1], '-') == false ? $command[1] : null;
                    $isPointedToApiController = false;

                    foreach ($command as $k => $args) {


                        if (($args == "-c" || $args == "-controller") && isset($command[$k + 1])) {
                            $controllerName = $command[$k + 1];
                        } elseif ($args == "-api" && isset($command[$k + 1])) {
                            $controllerName = $command[$k + 1];
                            $isPointedToApiController = true;
                        }

                    }

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {
                        $Info = new Info();

                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if ($isPointedToApiController)
                            (new Component())->create($Info, $componentName, 'api/' . $controllerName);
                        else
                            (new Component())->create($Info, $componentName, $controllerName);

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'service' || $command[0] == '-s') {

                    $serviceName = strpos($command[1], '-') == false ? $command[1] : null;
                    $appPointer = null;


                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if ($serviceName !== null) {
                            $response = (new Service())->create($Info, $serviceName);
                            if ($response === 0)
                                throw new \Exception("Error: Unable to create service");
                        } else {
                            throw new \Exception("Error: Undefined service name");
                        }
                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }


                if ($command[0] == 'middleware' || $command[0] == '-md') {

                    $middlewareName = strpos($command[1], '-') == false ? $command[1] : null;
                    $appPointer = null;


                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if ($middlewareName !== null) {
                            $response = (new Middleware())->create($Info, $middlewareName);
                            if ($response === 0)
                                throw new \Exception("Error: Unable to create service");
                        } else {
                            throw new \Exception("Error: Undefined service name");
                        }
                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }


                if ($command[0] == 'migrate') {

                    $appPointer = null;

                    $Info = new Info();

                    $appPointer = $Info->getCurrentApp();

                    if (!empty($appPointer)) {

                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);


                        $reset = false;
                        $rollback = false;
                        $all = false;
                        $migration_pointer = null;

                        if (in_array('--rollback', $command))
                            $rollback = true;

                        if (in_array('--reset', $command))
                            $reset = true;

                        if (in_array('--all', $command))
                            $all = true;
                        else
                            $migration_pointer = isset($command[1]) && !$this->isFlag($command[1]) ? $command[1] : null;

                        Schema::migrate($Info, $migration_pointer, $reset, $rollback, $all);

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }
                    exit();
                }

                if ($command[0] == 'migration') {

                    if (!isset($command[1])) {
                        throw new \Exception("Error: Undefined migration name");
                    }

                    $migrationName = strpos($command[1], '-') == false ? $command[1] : null;

                    $appPointer = null;
                    $tableName = null;
                    unset($command[0], $command[1]);

                    foreach ($command as $k => $args) {

                        if ($args == '-table' || $args == '-t')
                            $tableName = isset($command[$k++]) ? $command[$k++] : null;

                    }

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if ($tableName == null)
                            throw new \Exception("Table not defined, use -table to define a table");
                        if ($migrationName !== null) {

                            $response = (new Migration())->create($Info, $migrationName, $tableName);

                            if ($response === 0)
                                throw new \Exception("Error: Unable to create migration");
                        } else {
                            throw new \Exception("Error: Undefined migration");
                        }
                    } else {
                        throw new \Exception("No app in use, use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'scaffold') {

                    if (!isset($command[1])) {
                        throw new \Exception("Error: Undefined model name");
                    }

                    $model = strpos($command[1], '-') == false ? $command[1] : null;

                    $appPointer = null;
                    $tableName = null;
                    unset($command[0], $command[1]);

                    foreach ($command as $k => $args) {

                        if ($args == '-table' || $args == '-t')
                            $tableName = isset($command[$k++]) ? $command[$k++] : null;

                    }

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        if ($tableName == null)
                            throw new \Exception("Table not defined, use -table to define a table");
                        if ($model !== null) {

                            $tableName = ucfirst($tableName);
                            $modelName = ucfirst($model);
                            $response = (new Model())->scaffold($Info, $tableName, $modelName);

                            if ($response === 0)
                                throw new \Exception("Error: Unable to create model");
                        } else {
                            throw new \Exception("Error: Undefined model");
                        }
                    } else {
                        throw new \Exception("No app in use, use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'prod') {

                    exit();
                }

                if ($command[0] == 'destroy') {

                    /**
                     * Destroy Block
                     */

                    $appPointer = null;
                    $cli_dir = $cwd;

                    $commandV = $command;
                    $commandValue = strtolower(@$commandV[2]) ?? null;

                    $command = strtolower($command[1]);

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cli_dir);
                        $Info->setAppName($appPointer);


                        if ($command == '--app') {

                            if (strlen($appPointer) > 0)
                                $AppManager->destroy($Info, null);
                            else
                                throw new \Exception("No app in use, try use <name>");

                        } else if ($command == '-controller' || $command == '-c') {


                            if (isset($commandValue)) {
                                $controllerName = $commandValue;

                                (new Controller())->destroy($Info, $controllerName);
                            } else {
                                throw new \Exception("Error: Undefined Controller");
                            }

                        } else if ($command == '-api') {

                            if (isset($commandValue)) {
                                $apiName = 'api/' . $commandValue;

                                (new Controller())->destroy($Info, $apiName);
                            } else {
                                throw new \Exception("Error: Undefined api name");
                            }

                        } else if ($command == '-service' || $command == '-s') {
                            if (isset($commandValue)) {
                                $serviceName = $commandValue;
                                (new Service())->destroy($Info, $serviceName);
                            } else {
                                throw new \Exception("Error: Undefined service");
                            }
                        } else if ($command == '-middleware' || $command == '-md') {
                            if (isset($commandValue)) {
                                $middlewareName = $commandValue;
                                (new Middleware())->destroy($Info, $middlewareName);
                            } else {
                                throw new \Exception("Error: Undefined middleware");
                            }
                        } else if ($command == '-view' || $command == '-v') {

                            if (isset($commandValue)) {
                                $viewName = $commandValue;

                                unset($commandV[1], $commandV[2]);

                                $commandV = array_values($commandV);
                                if (@$commandV[1] == '-controller' || @$commandV[1] == '-c') {
                                    $hostController = $commandV[2];
                                    (new View())->destroy($Info, $viewName, $hostController);
                                } else {
                                    (new View())->destroy($Info, $viewName, '');
                                }
                            } else {
                                throw new \Exception("Error: Undefined view name");
                            }
                        } else if ($command == '-component' || $command == '-cp') {

                            if (isset($commandValue)) {
                                $componentName = $commandValue;

                                unset($commandV[1], $commandV[2]);

                                $commandV = array_values($commandV);
                                if (@$commandV[1] == '-controller' || @$commandV[1] == '-c') {
                                    $hostController = $commandV[2];
                                    (new Component())->destroy($Info, "{$hostController}@{$componentName}");
                                } elseif (@$commandV[1] == '-api') {
                                    $hostApiController = "api/{$commandV[2]}";
                                    (new Component())->destroy($Info, "{$hostApiController}@{$componentName}");
                                } else {
                                    throw new \Exception("No controller found for the component {$componentName}");
                                }
                            } else {
                                throw new \Exception("Error: Undefined view name");
                            }
                        } else {

                            $mask = "%-20.27s \t %-30.60s\n";
                            printf($mask, "--app", "Destroy current app");
                            printf($mask, "-service <name>", "Destroy service");
                            printf($mask, "-middleware <name>", "Destroy middleware");
                            printf($mask, "-controller <name>", "Destroy controller and its views");
                            printf($mask, "-view <name>", "Destroy view");
                            printf($mask, "-component <name> -c <name>", "Destroy component");
                            printf($mask, "-component <name> -api <name>", "Destroy component");
                            printf($mask, "-view <name> -c <name>", "Destroy view and its component");
                            printf($mask, "-api <name>", "Destroy api");

                        }

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                }

                if ($command[0] == 'serve') {
                    $port = 5000;
                    if (isset($command[1])) {
                        if ($command[1] == '-port') {
                            if (isset($command[2]))
                                $port = intval($command[2]);
                        }
                    }

                    print "\n***Development server starts on <127.0.0.1:$port>\n\n";
                    shell_exec("php -S 127.0.0.1:$port");


                } else if ($command[0] == 'prod') {


                    $appPointer = null;

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        $AppManager->setProdMode($Info);

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                } else if ($command[0] == 'dev') {


                    $appPointer = null;

                    $appPointer = (new Info())->getCurrentApp();

                    if (strlen($appPointer) > 0) {

                        $Info = new Info();
                        $Info->setAppDir($cwd);
                        $Info->setAppName($appPointer);

                        $AppManager->setDevMode($Info);

                    } else {
                        throw new \Exception("No app in use, try use <name>");
                    }

                    exit();
                } else {
                    $this->help();
                    exit();
                }


            } else {
                $this->help();
            }
        } catch (\CompileError $t) {
            print($t->getMessage());
        } catch (\InvalidArgumentException $t) {
            print($t->getMessage());
        } catch (\LengthException $t) {
            print($t->getMessage());
        } catch (\Throwable $t) {
            print($t->getMessage() . ' on line ' . $t->getLine() . ' (' . $t->getFile() . ")\n");
        }

    }
}

?>
