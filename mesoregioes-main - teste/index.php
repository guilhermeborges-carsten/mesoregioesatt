<?php
/**
 * Dashboard Logístico - Sistema de Análise de Embarques entre Mesorregiões
 * Conversão completa do sistema Python/Flask para PHP
 */

// Configurações básicas
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Sao_Paulo');

// Incluir autoloader
require_once 'vendor/autoload.php';

// Incluir classes principais
require_once 'src/Config/Config.php';
require_once 'src/Core/Router.php';
require_once 'src/Core/Controller.php';
require_once 'src/Core/Model.php';
require_once 'src/Core/View.php';
require_once 'src/Utils/ExcelProcessor.php';
require_once 'src/Utils/DataProcessor.php';
require_once 'src/Utils/ExportHelper.php';

// Inicializar configurações
$config = new Config();

// Inicializar roteador
$router = new Router();

// Definir rotas
$router->get('/', 'DashboardController@index');
$router->get('/heatmap', 'DashboardController@heatmap');
$router->get('/mapa_fluxos', 'DashboardController@mapaFluxos');
$router->get('/tabela', 'DashboardController@tabela');
$router->get('/balanco', 'DashboardController@balanco');
$router->get('/balanco_clientes', 'DashboardController@balancoClientes');
$router->get('/analise_clientes', 'DashboardController@analiseClientes');

// APIs
$router->post('/api/upload', 'ApiController@upload');
$router->get('/api/stats', 'ApiController@stats');
$router->get('/api/evolucao_mensal', 'ApiController@evolucaoMensal');
$router->get('/api/evolucao_mensal_clientes', 'ApiController@evolucaoMensalClientes');
$router->get('/api/insights_clientes', 'ApiController@insightsClientes');
$router->get('/api/top_origens', 'ApiController@topOrigens');
$router->get('/api/top_destinos', 'ApiController@topDestinos');
$router->get('/api/heatmap_data', 'ApiController@heatmapData');
$router->get('/api/fluxos_mapa', 'ApiController@fluxosMapa');
$router->get('/api/tabela_dados', 'ApiController@tabelaDados');
$router->get('/api/balanco_embarques', 'ApiController@balancoEmbarques');
$router->get('/api/balanco_clientes', 'ApiController@balancoClientes');
$router->get('/api/clientes', 'ApiController@clientes');
$router->get('/api/mesorregioes', 'ApiController@mesorregioes');
$router->get('/api/exportar_excel', 'ApiController@exportarExcel');
$router->get('/api/exportar_csv', 'ApiController@exportarCsv');
$router->get('/api/exportar_balanco_excel', 'ApiController@exportarBalancoExcel');
$router->get('/api/exportar_balanco_clientes_excel', 'ApiController@exportarBalancoClientesExcel');
$router->get('/api/download_template', 'ApiController@downloadTemplate');

// Processar requisição
$router->dispatch();
