<?php

namespace App\Core;

/**
 * Sistema de roteamento simples
 */
class Router
{
    private $routes = [];
    private $middleware = [];

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover barra final se existir
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $this->executeHandler($route['handler'], $path);
                return;
            }
        }

        // Rota não encontrada
        $this->handleNotFound();
    }

    private function matchPath($routePath, $requestPath)
    {
        // Converter parâmetros de rota {param} para regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    private function executeHandler($handler, $path)
    {
        if (is_string($handler)) {
            // Formato: Controller@method
            list($controllerName, $method) = explode('@', $handler);
            
            $controllerClass = "DashboardLogistico\\Controllers\\{$controllerName}";
            
            if (!class_exists($controllerClass)) {
                $this->handleNotFound();
                return;
            }
            
            $controller = new $controllerClass();
            
            if (!method_exists($controller, $method)) {
                $this->handleNotFound();
                return;
            }
            
            // Extrair parâmetros da URL se houver
            $params = $this->extractParams($path);
            
            call_user_func_array([$controller, $method], $params);
        } elseif (is_callable($handler)) {
            call_user_func($handler);
        } else {
            $this->handleNotFound();
        }
    }

    private function extractParams($path)
    {
        // Implementar extração de parâmetros se necessário
        return [];
    }

    private function handleNotFound()
    {
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
    }
}
