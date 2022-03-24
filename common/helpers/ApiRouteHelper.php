<?php

namespace common\helpers;

use Exception;
use yii\helpers\Inflector;

class ApiRouteHelper {

    /**
     * @var defaultMethod if action prefix doesnt exist in $mapping_array then will assume it as GET request
     */
    public const defaultMethod = 'GET';

    /**
     * @var array $mapping_array mapping method request from action signutare
     * e.g: actionGetProducts will assume it as get request, actionCreateProduct will assume it as post request
     */
    public static $mapping_array = [
        'get' => 'GET',
        'show' => 'GET',
        'view' => 'GET',
        'create' => 'POST',
        'store' => 'POST',
        'add' => 'POST',
        'update' => 'PUT',
        'edit' => 'PUT',
        'delete' => 'DELETE',
        'remove' => 'DELETE',
        'clear' => 'DELETE',
    ];

    /**
     * Generate a list of all actions in target controller
     * @param string $controllerFullPath full path to controller want to get its actions
     * @throws Exception If target controller doesnt found
     * @return array target controller actions
     */
    public static function getActionsList($controllerFullPath) : array {
        if(!file_exists($controllerFullPath))
            throw new Exception($controllerFullPath . ' File not found');

        $actions = [];
        
        preg_match_all('/public function action(\w+?)\(/', file_get_contents($controllerFullPath), $result);
        foreach ($result[1] as $action) {
            $actions[Inflector::camel2id($action)] = Inflector::camel2id($action);
        }
        asort($actions);
        return $actions;
    }

    /**
     * Generate controller verbs dynamiclly
     * @param String $controllerFullPath full path to controller want to generate verbs from it
     * @param array $additionalVerbs additional routes were names not compatable with naming standards
     * @throws Exception If target controller doesnt found
     * @return array array of generated verbs for all actions in controller
     */
    public static function generateVerbsFromController($controllerFullPath, $additionalVerbs = []) : array {
        
        if(!file_exists($controllerFullPath))
            throw new Exception($controllerFullPath . ' File not found');

        $verbs = [];
        foreach(self::getActionsList($controllerFullPath) as $action) {
            if(isset(self::$mapping_array[strtok($action, '-')]))
                $verbs[$action] = [self::$mapping_array[strtok($action, '-')]];
            elseif(isset($additionalVerbs[strtok($action, '-')]))
                $verbs[$action] = $additionalVerbs[$action];
            else
                $verbs[$action] = [self::defaultMethod];
        }

        return $verbs;
    }
}