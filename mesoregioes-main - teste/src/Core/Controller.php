<?php

namespace DashboardLogistico\Core;

use DashboardLogistico\Config\Config;
use DashboardLogistico\Core\View;
use DashboardLogistico\Utils\DataProcessor;

/**
 * Classe base para controladores
 */
abstract class Controller
{
    protected $config;
    protected $view;
    protected $dataProcessor;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->view = new View();
        $this->dataProcessor = new DataProcessor();
    }

    /**
     * Retorna dados JSON
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retorna erro JSON
     */
    protected function jsonError($message, $statusCode = 400)
    {
        $this->json(['error' => $message], $statusCode);
    }

    /**
     * Retorna sucesso JSON
     */
    protected function jsonSuccess($data = null, $message = 'Sucesso')
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->json($response);
    }

    /**
     * Renderiza uma view
     */
    protected function render($template, $data = [])
    {
        $this->view->render($template, $data);
    }

    /**
     * Obtém parâmetros da requisição
     */
    protected function getParams()
    {
        return $_GET;
    }

    /**
     * Obtém dados POST
     */
    protected function getPostData()
    {
        return $_POST;
    }

    /**
     * Obtém arquivos enviados
     */
    protected function getFiles()
    {
        return $_FILES;
    }

    /**
     * Valida se há dados carregados
     */
    protected function validateDataLoaded()
    {
        if (!$this->dataProcessor->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
    }

    /**
     * Aplica filtros aos dados
     */
    protected function applyFilters()
    {
        $filters = $this->getParams();
        return $this->dataProcessor->applyFilters($filters);
    }
}
