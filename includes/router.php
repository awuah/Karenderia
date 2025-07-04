<?php
/**
 * Simple Router Class
 */

class Router {
    private $routes = array();

    /**
     * Add a route
     */
    public function add($route, $controller) {
        $this->routes[$route] = $controller;
    }

    /**
     * Dispatch the route
     */
    public function dispatch($url) {
        // Remove query string
        $url = strtok($url, '?');
        
        // Check for exact match first
        if (array_key_exists($url, $this->routes)) {
            $this->callController($this->routes[$url]);
            return;
        }

        // Check for parameterized routes
        foreach ($this->routes as $route => $controller) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove full match
                $this->callController($controller, $matches);
                return;
            }
        }

        // 404 Not Found
        $this->show404();
    }

    /**
     * Call the controller
     */
    private function callController($controller, $params = array()) {
        list($controllerName, $method) = explode('@', $controller);
        
        $controllerFile = 'controllers/' . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerName)) {
                $controllerInstance = new $controllerName();
                
                if (method_exists($controllerInstance, $method)) {
                    call_user_func_array(array($controllerInstance, $method), $params);
                } else {
                    $this->show404();
                }
            } else {
                $this->show404();
            }
        } else {
            $this->show404();
        }
    }

    /**
     * Show 404 error
     */
    private function show404() {
        http_response_code(404);
        include 'views/404.php';
    }
}
?>