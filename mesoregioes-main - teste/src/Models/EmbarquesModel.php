<?php

namespace App\Models;

use App\Core\Model;
use App\Utils\DataProcessor;
use App\Utils\ExcelProcessor;
use App\Utils\SimpleExcelProcessor;
use App\Utils\ExportHelper;

/**
 * Model específico para dados de embarques
 * Gerencia todas as operações relacionadas aos dados de embarques
 */
class EmbarquesModel extends Model
{
    private $exportHelper;

    public function __construct()
    {
        parent::__construct();
        
        // Sobrescrever o dataProcessor da classe pai
        $this->dataProcessor = new DataProcessor();
        
        // Verificar se PhpSpreadsheet está disponível e sobrescrever o excelProcessor
        if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            $this->excelProcessor = new ExcelProcessor();
        } else {
            $this->excelProcessor = new SimpleExcelProcessor();
        }
        
        $this->exportHelper = new ExportHelper();
    }

    /**
     * Processar arquivo Excel e carregar dados
     */
    public function processExcelFile($filePath)
    {
        try {
            $this->data = $this->excelProcessor->readExcel($filePath);
            return [
                'success' => true,
                'message' => 'Arquivo processado com sucesso',
                'records' => count($this->data)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao processar arquivo: ' . $e->getMessage(),
                'records' => 0
            ];
        }
    }

    /**
     * Obter estatísticas dos dados
     */
    public function getStats($filters = [])
    {
        if (empty($this->data)) {
            return $this->dataProcessor->getEmptyStats();
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getStats($filteredData);
    }

    /**
     * Obter evolução mensal
     */
    public function getEvolucaoMensal($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getEvolucaoMensal($filteredData);
    }

    /**
     * Obter evolução mensal por clientes
     */
    public function getEvolucaoMensalClientes($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getEvolucaoMensalClientes($filteredData);
    }

    /**
     * Obter insights de clientes
     */
    public function getInsightsClientes($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getInsightsClientes($filteredData);
    }

    /**
     * Obter top origens
     */
    public function getTopOrigens($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getTopOrigens($filteredData);
    }

    /**
     * Obter top destinos
     */
    public function getTopDestinos($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getTopDestinos($filteredData);
    }

    /**
     * Obter dados para heatmap
     */
    public function getHeatmapData($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getHeatmapData($filteredData);
    }

    /**
     * Obter fluxos para mapa
     */
    public function getFluxosMapa($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getFluxosMapa($filteredData);
    }

    /**
     * Obter dados para tabela
     */
    public function getTabelaDados($filters = [])
    {
        if (empty($this->data)) {
            return [
                'data' => [],
                'total' => 0,
                'page' => 1,
                'per_page' => 50,
                'total_pages' => 0
            ];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getTabelaDados($filteredData, $filters);
    }

    /**
     * Obter balanço de embarques
     */
    public function getBalancoEmbarques($filters = [])
    {
        if (empty($this->data)) {
            return [
                'balanco' => [],
                'resumo_geral' => [
                    'total_mesorregioes' => 0,
                    'produtoras' => 0,
                    'consumidoras' => 0,
                    'equilibradas' => 0
                ]
            ];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getBalancoEmbarques($filteredData);
    }

    /**
     * Obter balanço de clientes
     */
    public function getBalancoClientes($filters = [])
    {
        if (empty($this->data)) {
            return [
                'balanco' => [],
                'resumo_clientes' => [],
                'resumo_geral' => [
                    'total_clientes' => 0,
                    'total_mesorregioes' => 0,
                    'produtoras' => 0,
                    'consumidoras' => 0,
                    'equilibradas' => 0
                ]
            ];
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->dataProcessor->getBalancoClientes($filteredData);
    }

    /**
     * Obter lista de clientes
     */
    public function getClientes()
    {
        if (empty($this->data)) {
            return [];
        }

        return $this->dataProcessor->getClientes($this->data);
    }

    /**
     * Obter lista de mesorregiões
     */
    public function getMesorregioes()
    {
        return $this->config->get('mesorregioes', []);
    }

    /**
     * Exportar dados para Excel
     */
    public function exportarExcel($filters = [])
    {
        if (empty($this->data)) {
            return false;
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->excelProcessor->exportToExcel($filteredData, 'embarques_export.xlsx');
    }

    /**
     * Exportar dados para CSV
     */
    public function exportarCsv($filters = [])
    {
        if (empty($this->data)) {
            return false;
        }

        $filteredData = $this->dataProcessor->applyFilters($this->data, $filters);
        return $this->excelProcessor->exportToCsv($filteredData, 'embarques_export.csv');
    }

    /**
     * Exportar balanço para Excel
     */
    public function exportarBalancoExcel($filters = [])
    {
        if (empty($this->data)) {
            return false;
        }

        $balancoData = $this->getBalancoEmbarques($filters);
        return $this->exportHelper->exportBalancoExcel($balancoData, 'balanco_embarques.xlsx');
    }

    /**
     * Exportar balanço de clientes para Excel
     */
    public function exportarBalancoClientesExcel($filters = [])
    {
        if (empty($this->data)) {
            return false;
        }

        $balancoData = $this->getBalancoClientes($filters);
        return $this->exportHelper->exportBalancoClientesExcel($balancoData, 'balanco_clientes.xlsx');
    }

    /**
     * Obter dados filtrados
     */
    public function getFilteredData($filters = [])
    {
        if (empty($this->data)) {
            return [];
        }

        return $this->dataProcessor->applyFilters($this->data, $filters);
    }

    /**
     * Validar dados carregados
     */
    public function validateData()
    {
        if (empty($this->data)) {
            return [
                'valid' => false,
                'message' => 'Nenhum dado carregado'
            ];
        }

        $requiredColumns = ['DATA', 'ORIGEM', 'DESTINO', 'CLIENTE', 'VOLUME'];
        $columns = array_keys($this->data[0] ?? []);

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $columns)) {
                return [
                    'valid' => false,
                    'message' => "Coluna obrigatória não encontrada: {$column}"
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'Dados válidos',
            'records' => count($this->data)
        ];
    }
}
