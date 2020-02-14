<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Config;
use zil\core\interfaces\Writer;
use zil\core\scrapper\Info;
use zil\blueprint\Blueprint;
use zil\core\tracer\ErrorTracer;
use zil\factory\Filehandler;
use zil\factory\Database;

class Model implements Writer
{

    public function __construct(){}

    public function create(Info $Info) {
    }

    private function getDatabaseParams(){
        try {

            $AppDir     =   getcwd();

            $app_name = (new Info())->getTree()->currentApp;

            if(!file_exists("{$AppDir}/src/{$app_name}/config/config.php"))
                throw new \DomainException("Couldn't found config. file for {$app_name}");

            include_once("{$AppDir}/src/{$app_name}/config/config.php");

            $cfg = "src\\$app_name\\config\Config";

                $config =  new $cfg();

            return $config->getDatabaseParams();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function scaffold(Info $Info, string $table, string $model){

        try{

            if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                $app_base = $Info->getAppBase();
                $app_name = $Info->getAppName();

            }else{
                throw new \Exception("Error: Couldn't resolve app directories");
            }

            if(!empty($app_base) ){

                /**
                 * Get blueprint of a model
                 * @var BluePrint
                 */

                $Blueprint = new BluePrint($app_name);
                $Base = $Blueprint->model($model);

                $context = $Base['code'];

                /** Get the model name
                 *  Find the model file
                 */
                if( file_exists("{$app_base}/model/{$model}.php")){

                    $context = file_get_contents("{$app_base}/model/{$model}.php", 'rb');
                    preg_match_all("/(public|private|protected)[\s]+function[\s]+[\w]+[\s\S]+/", $context, $m);

                    /**
                     * Update Associate Model
                     */
                    if(sizeof($m[0]) > 0)
                        $context = preg_replace("/\#METHODS/", $m[0][0], $context);

                }

                /**
                 * Rewrite Model
                 */
                $context = preg_replace(["/\#METHODS/", "/(\?>)+([\s\S]+)/"], [null, '?>' ], $context);




                /**
                 * Get database handle with current app default db connection parameters
                 * @var [type]
                 */
                $db = $this->getDatabaseParams();
                $pdohandle = (new Database())->connect($db);

                /**
                 * Extract columns of table of interest
                 * @var [type]
                 */
                if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql' || $db['driver'] == 'mssql'){

                    $rs = $pdohandle->query("SELECT COLUMN_NAME FROM  INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='{$db['database']}' AND TABLE_NAME = '{$table}' ORDER BY ORDINAL_POSITION");

                }else if($db['driver'] == 'sqlite'){

                    $rs = $pdohandle->query("SELECT name FROM pragma_table_info('$table')");
                }else if($db['driver'] == 'oracle'){

                    $rs = $pdohandle->query("SELECT COULUMN_NAME FROM  ALL_TAB_COLUMNS WHERE TABLE_NAME = '{$table}'");
                }

                /**
                 * Push table columns to array
                 * @var array
                 */
                $modelAttribs = [];
                if($rs->rowCount() > 0){
                    while( list($col) = $rs->fetch() ){
                         array_push($modelAttribs, $col);
                    }
                }


                $Info->getProgressMessage("createModel");
                $context_arr = explode("\n", $context);

                $context = null;
                $class_found = false;
                $class_passed = false;
                $method_write_init = false;
                $class_attribute_written = false;

                $model_name = ucfirst($table);


                foreach ($context_arr as $key => $blueprint ) {

                    /** Skip empty lines */
                    if(strlen( trim($blueprint) ) === 0 && !$method_write_init)
                        continue;

                    /**
                     * Write class line
                     */
                    if(preg_match("/^class[\s]+.+$/", trim($blueprint), $m)){
                        $context .= "\n".$blueprint . "\n\n";
                        $class_found = true;
                        $class_passed = true;
                        continue;
                    }

                    /**
                     * Write all existing lines before or on class line
                     */
                    if(!$class_passed) {
                        $context .= $blueprint . "\n";
                        continue;
                    }

                    /**
                     * Wrap and ReWrite lines before methods
                     * In between the class and methods
                     */
                    if($class_passed && $class_found && $method_write_init == false){

                        /**
                         * Scan context after class keyword and before method declarations, however the positioning
                         */

                        if( preg_match("/^({|})$/", trim($blueprint), $m) ) {
                            $context .= $blueprint . "\n";
                        }

                        if( preg_match("/^use[\s]+[\s\S]+$/", trim($blueprint), $m) ) {
                            $context .= $blueprint . "\n\n";
                        }

                        if( preg_match("/^(public|private|protected)[\s]+static[\s]+[$]table[\s\S]+$/", trim($blueprint), $m) ) {
                            $context .= "\tpublic static \$table = '{$table}';\n";
                        }

                        if (preg_match("/^(public|private|protected)[\s]+static[\s]+[$]key[\s\S]+$/", trim($blueprint), $m)) {
                            $context .= $blueprint . "\n";
                        }

                        if (preg_match("/^(public)[\s]+[$][\w]+[\s\S]+$/", trim($blueprint), $m)) {
                            /**Erase previous attributes */
                            $context .= null;

                            if($class_attribute_written == false){
                                foreach($modelAttribs as $attrib){
                                    $attrib_of_interest = "\tpublic \${$attrib} = null;\n";

                                    if( ($attrib_of_interest != $blueprint) && (strlen($blueprint) != 0) )
                                        $context .= $attrib_of_interest;
                                    else
                                        $context .= null;
                                }

                                /**Add a trailing empty space*/
                                $context .= "\n";

                                $class_attribute_written = true;
                            }
                        }

                    }


                    /**
                     * Method found by implication closes the class and file
                     */
                    if( preg_match("/^(public|private|protected)([\s]+static)?[\s]+function[\s]+[\w]+[\s\S]+$/",trim( $blueprint), $m) ) {
                        /**
                         * Implies method hasn't been seen before , but the first been gotten
                         */
                        if($method_write_init == false) {
                            /**
                             * Add an empty line between instantiations and method
                             */
                            $context .= "\n\n";
                        }

                        $method_write_init = true;

                    }

                    /**
                     * Write all existing methods after the class line and instantiations
                     */
                    if($class_passed && $class_found && $method_write_init) {
                        $context .= "$blueprint\n";
                    }



                    unset($context_arr[$key]);
                }


                (new Filehandler())->createFile("{$app_base}model/".$model_name.".php", $context);
                unset( $context, $blueprint);

                /**
                 * Autoload classes
                 */

                print( shell_exec($Info->getCommand("autoload")) )."\n";

            }

        }catch(\Throwable $e){
            print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
        }finally{
            print "Model creation  closed\n";
        }

    }

    public function destroy(Info $Info, string $modelName){

        try{

            if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                $app_base = $Info->getAppBase();
                $modelName = \ucfirst($modelName);

            }else{
                throw new \Exception("Error: Couldn't resolve app directories");
            }

            print("Destroying {$modelName} model\n");
            if("{$app_base}/model/{$modelName}.php")
                @unlink("{$app_base}/model/{$modelName}.php");
            else
                print("{$modelName} not found\n");

            /**
             * Autoload classes
             */
            print( shell_exec($Info->getCommand("autoload")) )."\n";


        }catch(\Exception $e){

            print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
        }catch(\Throwable $e){

            print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
        }finally{
            print "destruction closed\n";
        }

    }



}
?>
