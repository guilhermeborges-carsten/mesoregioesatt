<?php

namespace DashboardLogistico\Controllers;

use DashboardLogistico\Core\Controller;
use DashboardLogistico\Utils\ExcelProcessor;
use DashboardLogistico\Utils\ExportHelper;

/**
 * Controlador das APIs
 */
class ApiController extends Controller
{
    private $excelProcessor;
    private $exportHelper;

    public function __construct()
    {
        parent::__construct();
        $this->excelProcessor = new ExcelProcessor();
        $this->exportHelper = new ExportHelper();
    }

    /**
     * Upload de arquivo Excel
     */
    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Método não permitido', 405);
        }

        if (!isset($_FILES['file'])) {
            $this->jsonError('Nenhum arquivo enviado');
        }

        $file = $_FILES['file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->jsonError('Erro no upload do arquivo');
        }

        // Validar tipo de arquivo
        $allowedExtensions = ['xlsx', 'xls'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $this->jsonError('Formato de arquivo não suportado. Use .xlsx ou .xls');
        }

        // Validar tamanho do arquivo
        $maxSize = $this->config->get('upload.max_file_size', 16 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            $this->jsonError('Arquivo muito grande. Tamanho máximo: ' . round($maxSize / 1024 / 1024) . 'MB');
        }

        try {
            // Processar arquivo
            list($data, $error) = $this->excelProcessor->processExcelFile($file['tmp_name']);
            
            if ($error) {
                $this->jsonError($error);
            }

            // Salvar dados no processador
            $this->dataProcessor->setData($data);

            // Remover arquivo temporário
            unlink($file['tmp_name']);

            $this->jsonSuccess(null, 'Arquivo processado com sucesso');

        } catch (\Exception $e) {
            $this->jsonError('Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Estatísticas gerais
     */
    public function stats()
    {
        if (!$this->dataProcessor->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }

        $filters = $this->getParams();
        $stats = $this->dataProcessor->getStats($filters);
        
        $this->json($stats);
    }

    /**
     * Evolução mensal
     */
    public function evolucaoMensal()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getEvolucaoMensal($filters);
        
        $this->json($data);
    }

    /**
     * Evolução mensal por clientes
     */
    public function evolucaoMensalClientes()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getEvolucaoMensalClientes($filters);
        
        $this->json($data);
    }

    /**
     * Insights de clientes
     */
    public function insightsClientes()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getInsightsClientes($filters);
        
        $this->json($data);
    }

    /**
     * Top origens
     */
    public function topOrigens()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getTopOrigens($filters);
        
        $this->json($data);
    }

    /**
     * Top destinos
     */
    public function topDestinos()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getTopDestinos($filters);
        
        $this->json($data);
    }

    /**
     * Dados do heatmap
     */
    public function heatmapData()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getHeatmapData($filters);
        
        $this->json($data);
    }

    /**
     * Dados do mapa de fluxos
     */
    public function fluxosMapa()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getFluxosMapa($filters);
        
        $this->json($data);
    }

    /**
     * Dados da tabela
     */
    public function tabelaDados()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getTabelaDados($filters);
        
        $this->json($data);
    }

    /**
     * Balanço de embarques
     */
    public function balancoEmbarques()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getBalancoEmbarques($filters);
        
        $this->json($data);
    }

    /**
     * Balanço de clientes
     */
    public function balancoClientes()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getBalancoClientes($filters);
        
        $this->json($data);
    }

    /**
     * Lista de clientes
     */
    public function clientes()
    {
        $this->validateDataLoaded();
        
        $data = $this->dataProcessor->getClientes();
        
        $this->json($data);
    }

    /**
     * Lista de mesorregiões
     */
    public function mesorregioes()
    {
        $this->validateDataLoaded();
        
        $data = $this->dataProcessor->getMesorregioes();
        
        $this->json($data);
    }

    /**
     * Exportar Excel
     */
    public function exportarExcel()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->applyFilters($filters);
        
        if (empty($data)) {
            $this->jsonError('Nenhum dado encontrado com os filtros aplicados');
        }

        $filename = 'embarques_' . date('Ymd_His') . '.xlsx';
        $this->excelProcessor->exportToExcel($data, $filename);
    }

    /**
     * Exportar CSV
     */
    public function exportarCsv()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->applyFilters($filters);
        
        if (empty($data)) {
            $this->jsonError('Nenhum dado encontrado com os filtros aplicados');
        }

        $filename = 'embarques_' . date('Ymd_His') . '.csv';
        $this->excelProcessor->exportToCsv($data, $filename);
    }

    /**
     * Exportar balanço Excel
     */
    public function exportarBalancoExcel()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getBalancoEmbarques($filters);
        
        if (isset($data['error'])) {
            $this->jsonError($data['error']);
        }

        $filename = 'balanco_embarques_' . date('Ymd_His') . '.xlsx';
        $this->exportHelper->exportBalancoExcel($data, $filename);
    }

    /**
     * Exportar balanço clientes Excel
     */
    public function exportarBalancoClientesExcel()
    {
        $this->validateDataLoaded();
        
        $filters = $this->getParams();
        $data = $this->dataProcessor->getBalancoClientes($filters);
        
        if (isset($data['error'])) {
            $this->jsonError($data['error']);
        }

        $filename = 'balanco_clientes_' . date('Ymd_His') . '.xlsx';
        $this->exportHelper->exportBalancoClientesExcel($data, $filename);
    }

    /**
     * Download do template Excel
     */
    public function downloadTemplate()
    {
        $templatePath = $this->config->get('upload.template_path', 'templates/template_embarques.xlsx');
        
        if (!file_exists($templatePath)) {
            $this->jsonError('Template não encontrado');
        }

        $filename = 'template_embarques.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($templatePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        readfile($templatePath);
        exit;
    }
}
