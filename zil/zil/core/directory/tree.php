<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\directory;

use zil\core\scrapper\Info;
use zil\core\tracer\ErrorTracer;


class Tree
{

    public function __construct()
    {
    }

    /**
     * Set up app tree during installation
     *
     * @param Info $Info
     *
     * @return void
     */
    public function createAppTree(Info $Info, bool $_isDefault)
    {
        try {
            $tree = $Info->getTree();
            if (is_null($tree)) {
                throw new \Exception("Error: couldn't get app tree\n", 1);
            } else {
                $app_name = $Info->getAppName();
                $m_struct = ["name" => $app_name, "trial" => 0, "prod" => false];
                $tree->apps->{$app_name} = $m_struct;

                if( $_isDefault)
                    $tree->defaultApp = $app_name;

                file_put_contents($Info->getSharedPath() . "data/.app.json", json_encode($tree, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Retrieve whole tree
     * @param string $app_name
     * @return object
     */
    public function getTree(): object
    {
        try {
            return (new Info())->getTree();
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }


    /**
     * Retrieve individual app tree
     * @param string $app_name
     * @return object
     */
    public function getAppTree(string $app_name): object
    {
        return (new Info())->getTree()->apps->{$app_name};
    }

    /**
     * Update tree
     *
     * @param Info $Info
     * @param string $app_name
     * @param object $tree
     * @return boolean
     */
    public function updateTree(Info $Info, object $tree): bool
    {
        if (!is_null($tree)) {
            file_put_contents($Info->getSharedPath() . "data/.app.json", json_encode($tree, JSON_PRETTY_PRINT));
            return true;
        }
        return false;
    }
}

?>
