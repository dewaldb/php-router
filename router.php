<?php
class Router {
    private $routes;
    private $params;
    private $default_route;
    private $not_found_function;
    private $access_denied_function;
    private $check_access_function;
    
    function __construct() {
        $this->routes = array();
        $this->params = array();
        $this->default_route = "";
        $this->not_found_function = "";
        $this->access_denied_function = "";
        $this->check_access_function = "";
    }
    
    function run() {
        if(isset($_GET["q"])) {
            $this->default_route = $_GET["q"];
        }
        
        $params = explode("/", $this->default_route);
        
        if(isset($this->routes[$params[0]])) {
            // the route exists
            if($this->check_access_function) {
                // a function for checking permissions has been set so call it
                if(call_user_func($this->check_access_function, $this->routes[$params[0]]["roles"])) {
                    // access granted, run the route's function
                    call_user_func($this->routes[$params[0]]["function"], $params);
                } else {
                    // access denied
                    header('HTTP/1.1 403 Forbidden');
                    if($this->access_denied_function) {
                        call_user_func($this->access_denied_function, $params);
                    }
                }
            } else {
                // access granted by default
                call_user_func($this->routes[$params[0]]["function"], $params);
            }
        } else {
            // the route is not found
            header('HTTP/1.0 404 Not Found');
            if($this->not_found_function) {
                call_user_func($this->not_found_function, $params);
            }
        }
    }
    
    function add($route, $function, $roles) {
        $this->routes[$route] = array(
            "function" => $function,
            "roles" => $roles
        );
    }
    
    function setDefaultRoute($route) {
        $this->default_route = $route;
    }
    
    function setNotFoundFunction($function) {
        $this->not_found_function = $function;
    }
    
    function setAccessDeniedFunction($function) {
        $this->access_denied_function = $function;
    }
    
    function setCheckAccessFunction($function) {
        $this->check_access_function = $function;
    }
}
// create the global $router object - we only need one instance and using something like Router::getInstance() is just ugly...
$router = new Router();
?>