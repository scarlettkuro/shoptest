<?php

namespace App;

class Router {

    /** @var Array $routes Array of routes (patterns with callables */
    private $routes = [];
    
    public function __construct() {
        $this->saveUrl();
    }

    /**
     * Adds route.
     * @param String $route Route pattern
     * @param Callable $action Array of two elements: class of the controller
     * and name of action.
     */
    public function addRoute($route, $action) {
        $this->routes[$route] = $action;
    }

    /**
     * Adds routes.
     * @param Callable[] $routes Array of routes. Route is array of two elements: 
     * class of the controller and name of action.
     */
    public function addRoutes($routes) {
        $this->routes = array_merge($this->routes, $routes);
    }

    /**
     * Make regex from route pattern.
     * @param String $pattern Route pattern
     * @return String Route regex.
     */
    protected function regularize($pattern) {
        $pattern = preg_quote($pattern, '/');
        $pattern = preg_replace('/\\\{(.*)\\\}/U', '(.*)', $pattern); //numeric
        $pattern = '/' . $pattern . '$/U';
        return $pattern;
    }

    /**
     * Matching route from url.
     * @param String $url URL
     * @return Array Callable route (controller and action), 
     * params, passed for route,
     * middleware.
     */
    public function dispatch($url) {
        $regularRoute = explode('?', $url)[0];
        
        foreach ($this->routes as $route => $action) {
            $matches = [];

            if (preg_match_all($this->regularize($route), $regularRoute, $matches, PREG_SET_ORDER)) {
                array_shift($matches[0]);
                $regular_params = $matches[0];
                
                return [$action, $regular_params];
            }
        }
    }
    
    /**
     * Create url for route 
     * @param Callable $action Array of two elements: class of the controller
     * and name of action.
     * @param Array Params to pass in route.
     * @return String URL
     */
    public function route($action, $params= []) {
        $patterns = [];
        foreach ($this->routes as $pattern => $actionCallback) {
            if ($actionCallback == $action) {
                $patterns[] = $pattern;
            }
        }
        
        foreach ($patterns as $pattern) {
            if (preg_match_all('/\{(.*)\}/U', $pattern) == count($params)) {
                foreach($params as $i => $param){
                    $pattern = preg_replace('/\{(.*)\}/U', $param, $pattern, 1);
                }
                return $pattern;
            }
        }
        
    }
    
     /**
     * Redirect
     * @param Callable $action Array of two elements: class of the controller
     * and name of action.
     */
    public function redirect($route) {
        if (is_array($route)) {
            $route = $this->route($route);
        }
        header("Location: $route");
        die();
    }
    
    /**
     * Saves last visited routes, so you can come back if needed
     */
    public function saveUrl() {
        if (!isset($_SESSION['router']['last_routes'])) {
            $_SESSION['router']['last_routes'] = [];
        }
        if (count($_SESSION['router']['last_routes']) == 2) {
            array_shift($_SESSION['router']['last_routes']);
        }
        $_SESSION['router']['last_routes'][] = $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Redirect to previous URL
     */
    public function returnBack() {
        if (isset($_SESSION['router']['last_routes'])) {
            $this->redirect(array_shift($_SESSION['router']['last_routes']));
        }
    }
}
