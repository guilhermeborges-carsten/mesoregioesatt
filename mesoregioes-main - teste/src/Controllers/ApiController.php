<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\EmbarquesModel;
use App\Utils\ExcelProcessor;
use App\Utils\ExportHelper;

/**
 * Controlador das APIs
 */
class ApiController extends Controller
{
    private $embarquesModel;
    private $excelProcessor;
    private $exportHelper;

    public function __construct()
    {
        parent::__construct();
        $this->embarquesModel = new EmbarquesModel();
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
            // Processar arquivo usando o Model
            $result = $this->embarquesModel->processExcelFile($file['tmp_name']);
            
            if (!$result['success']) {
                $this->jsonError($result['message']);
            }

            // Remover arquivo temporário
            unlink($file['tmp_name']);

            $this->jsonSuccess([
                'records' => $result['records']
            ], $result['message']);

        } catch (\Exception $e) {
            $this->jsonError('Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Estatísticas gerais
     */
    public function stats()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }

        $filters = $this->getParams();
        $stats = $this->embarquesModel->getStats($filters);
        
        $this->json($stats);
    }

    /**
     * Evolução mensal
     */
    public function evolucaoMensal()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getEvolucaoMensal($filters);
        
        $this->json($data);
    }

    /**
     * Evolução mensal por clientes
     */
    public function evolucaoMensalClientes()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getEvolucaoMensalClientes($filters);
        
        $this->json($data);
    }

    /**
     * Insights de clientes
     */
    public function insightsClientes()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getInsightsClientes($filters);
        
        $this->json($data);
    }

    /**
     * Top origens
     */
    public function topOrigens()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getTopOrigens($filters);
        
        $this->json($data);
    }

    /**
     * Top destinos
     */
    public function topDestinos()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getTopDestinos($filters);
        
        $this->json($data);
    }

    /**
     * Dados do heatmap
     */
    public function heatmapData()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getHeatmapData($filters);
        
        $this->json($data);
    }

    /**
     * Dados do mapa de fluxos
     */
    public function fluxosMapa()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getFluxosMapa($filters);
        
        $this->json($data);
    }

    /**
     * Dados da tabela
     */
    public function tabelaDados()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getTabelaDados($filters);
        
        $this->json($data);
    }

    /**
     * Balanço de embarques
     */
    public function balancoEmbarques()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getBalancoEmbarques($filters);
        
        $this->json($data);
    }

    /**
     * Balanço de clientes
     */
    public function balancoClientes()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $data = $this->embarquesModel->getBalancoClientes($filters);
        
        $this->json($data);
    }

    /**
     * Lista de clientes
     */
    public function clientes()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $data = $this->embarquesModel->getClientes();
        
        $this->json($data);
    }

    /**
     * Lista de mesorregiões
     */
    public function mesorregioes()
    {
        $data = $this->embarquesModel->getMesorregioes();
        
        $this->json($data);
    }

    /**
     * Exportar Excel
     */
    public function exportarExcel()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $result = $this->embarquesModel->exportarExcel($filters);
        
        if (!$result) {
            $this->jsonError('Erro ao exportar dados');
        }
    }

    /**
     * Exportar CSV
     */
    public function exportarCsv()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $result = $this->embarquesModel->exportarCsv($filters);
        
        if (!$result) {
            $this->jsonError('Erro ao exportar dados');
        }
    }

    /**
     * Exportar balanço Excel
     */
    public function exportarBalancoExcel()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $result = $this->embarquesModel->exportarBalancoExcel($filters);
        
        if (!$result) {
            $this->jsonError('Erro ao exportar balanço');
        }
    }

    /**
     * Exportar balanço clientes Excel
     */
    public function exportarBalancoClientesExcel()
    {
        if (!$this->embarquesModel->hasData()) {
            $this->jsonError('Nenhum dado carregado');
        }
        
        $filters = $this->getParams();
        $result = $this->embarquesModel->exportarBalancoClientesExcel($filters);
        
        if (!$result) {
            $this->jsonError('Erro ao exportar balanço de clientes');
        }
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
