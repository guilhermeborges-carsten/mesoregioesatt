<?php
/**
 * Dashboard Logístico - Sistema de Análise de Embarques entre Mesorregiões
 * Conversão completa do sistema Python/Flask para PHP
 */

// Configurações básicas
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Sao_Paulo');

// Incluir classes principais diretamente (sem Composer)
require_once 'src/Config/Config.php';
require_once 'src/Core/Router.php';
require_once 'src/Core/Controller.php';
require_once 'src/Core/Model.php';
require_once 'src/Core/View.php';
require_once 'src/Controllers/DashboardController.php';
require_once 'src/Controllers/ApiController.php';
require_once 'src/Models/EmbarquesModel.php';
require_once 'src/Utils/DataProcessor.php';
require_once 'src/Utils/ExcelProcessor.php';
require_once 'src/Utils/SimpleExcelProcessor.php';
require_once 'src/Utils/ExportHelper.php';

// Inicializar configurações
$config = new App\Config\Config();

// Inicializar roteador
$router = new App\Core\Router();

// Definir rotas
$router->get('/', 'App\\Controllers\\DashboardController@index');
$router->get('/heatmap', 'App\\Controllers\\DashboardController@heatmap');
$router->get('/mapa_fluxos', 'App\\Controllers\\DashboardController@mapaFluxos');
$router->get('/tabela', 'App\\Controllers\\DashboardController@tabela');
$router->get('/balanco', 'App\\Controllers\\DashboardController@balanco');
$router->get('/balanco_clientes', 'App\\Controllers\\DashboardController@balancoClientes');
$router->get('/analise_clientes', 'App\\Controllers\\DashboardController@analiseClientes');

// APIs
$router->post('/api/upload', 'App\\Controllers\\ApiController@upload');
$router->get('/api/stats', 'App\\Controllers\\ApiController@stats');
$router->get('/api/evolucao_mensal', 'App\\Controllers\\ApiController@evolucaoMensal');
$router->get('/api/evolucao_mensal_clientes', 'App\\Controllers\\ApiController@evolucaoMensalClientes');
$router->get('/api/insights_clientes', 'App\\Controllers\\ApiController@insightsClientes');
$router->get('/api/top_origens', 'App\\Controllers\\ApiController@topOrigens');
$router->get('/api/top_destinos', 'App\\Controllers\\ApiController@topDestinos');
$router->get('/api/heatmap_data', 'App\\Controllers\\ApiController@heatmapData');
$router->get('/api/fluxos_mapa', 'App\\Controllers\\ApiController@fluxosMapa');
$router->get('/api/tabela_dados', 'App\\Controllers\\ApiController@tabelaDados');
$router->get('/api/balanco_embarques', 'App\\Controllers\\ApiController@balancoEmbarques');
$router->get('/api/balanco_clientes', 'App\\Controllers\\ApiController@balancoClientes');
$router->get('/api/clientes', 'App\\Controllers\\ApiController@clientes');
$router->get('/api/mesorregioes', 'App\\Controllers\\ApiController@mesorregioes');
$router->get('/api/exportar_excel', 'App\\Controllers\\ApiController@exportarExcel');
$router->get('/api/exportar_csv', 'App\\Controllers\\ApiController@exportarCsv');
$router->get('/api/exportar_balanco_excel', 'App\\Controllers\\ApiController@exportarBalancoExcel');
$router->get('/api/exportar_balanco_clientes_excel', 'App\\Controllers\\ApiController@exportarBalancoClientesExcel');
$router->get('/api/download_template', 'App\\Controllers\\ApiController@downloadTemplate');

// Processar requisição
$router->dispatch();
