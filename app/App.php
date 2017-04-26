<?php

namespace App;

/**
 * Description of App
 *
 * @author kuro
 */
class App {
    
    /** @var App $app Instance of app */
    private static $app;
    
    /** @var Array $params App params */
    private $params;
    /** @var Array $components Instance of app */
    private $components;
    
    
    /**
     * @param Array $params Params of apps
     */
    public function __construct($params = []) {
        session_start();
        $this->addComponent('router', new Router());
        $this->params = $params;
        self::$app = $this;
    }
    
    /**
     * Returns instance of the app
     * @return App App instance
     */
    public static function app() {
        return self::$app;
    }

    /**
     * Adds route.
     * @param String $route Route pattern
     * @param Callable $action Array of two elements: class of the controller
     * and name of action.
     */
    public function addRoute($route, $action) {
        $this->component('router')->addRoute($route, $action);
    }

    /**
     * Shrotcut to the Router's addRoutes.
     * Adds routes.
     * @param Callable[] $routes Array of routes. Route is array of two elements: 
     * class of the controller and name of action.
     */
    public function addRoutes($routes) {
        $this->component('router')->addRoutes($routes);
    }

    /**
     * Shrotcut to the Router's route.
     * @param Callable $action Array of two elements: class of the controller
     * and name of action.
     * @param Array Params to pass in route.
     * @return String URL
     */
    public function route($action, $params = []) {
        return $this->component('router')->route($action, $params);
    }

    /**
     * Running app. 
     * Collect middlewares. Pass request and responce through them.
     * Dispatch url, call correct action for it.
     */
    public function run() {
        $route_action = $this->component('router')->dispatch($_SERVER['REQUEST_URI']);
        echo call_user_func_array($route_action[0], $route_action[1]);
        die();
    }
    
    /**
     * Returns params
     * @return Array Array of params
     */
    public function params() {
        return $this->params;
    }
    
    /**
     * DI. Adds any object or mixed value, accessisble 
     * from any point of the application.
     * @param String $id Component identifier
     * @param mixed Component
     */
    public function addComponent($id, $component) {
        $this->components[$id] = $component;
    }
    
    /**
     * DI. Retrieve added component from any point of the application.
     * @param String $id Component identifier
     * @return mixed Component
     */
    public function component($id) {
        return $this->components[$id];
    }
}
