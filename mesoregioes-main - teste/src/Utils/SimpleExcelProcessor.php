<?php

namespace App\Utils;

use App\Config\Config;

/**
 * Processador de Excel simplificado (sem dependências externas)
 * Para uso quando o Composer não estiver disponível
 */
class SimpleExcelProcessor
{
    private $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     * Lê arquivo Excel usando funções básicas do PHP
     * Nota: Esta é uma implementação básica para arquivos CSV
     */
    public function readExcel($filePath)
    {
        try {
            // Verificar se é um arquivo CSV
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if ($extension === 'csv') {
                return $this->readCsv($filePath);
            }
            
            // Para arquivos Excel, retornar erro informativo
            throw new \Exception('Arquivos Excel (.xlsx/.xls) requerem PhpSpreadsheet. Use arquivos CSV ou instale o Composer.');
            
        } catch (\Exception $e) {
            throw new \Exception("Erro ao processar arquivo: " . $e->getMessage());
        }
    }

    /**
     * Lê arquivo CSV
     */
    private function readCsv($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception('Não foi possível abrir o arquivo');
        }

        // Ler cabeçalho
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Arquivo vazio ou inválido');
        }

        // Verificar colunas obrigatórias
        $requiredColumns = ['DATA', 'ORIGEM', 'DESTINO', 'CLIENTE', 'VOLUME'];
        $missingHeaders = [];
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $headers)) {
                $missingHeaders[] = $column;
            }
        }
        
        if (!empty($missingHeaders)) {
            fclose($handle);
            throw new \Exception("Colunas obrigatórias não encontradas: " . implode(', ', $missingHeaders));
        }

        // Ler dados
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        fclose($handle);
        
        if (empty($data)) {
            throw new \Exception('Nenhum dado encontrado no arquivo');
        }

        // Processar dados
        return $this->processData($data);
    }

    /**
     * Processa os dados lidos
     */
    private function processData($data)
    {
        $processedData = [];
        
        foreach ($data as $row) {
            // Validar e limpar dados
            $processedRow = [
                'DATA' => $this->formatDate($row['DATA']),
                'ORIGEM' => trim($row['ORIGEM']),
                'DESTINO' => trim($row['DESTINO']),
                'CLIENTE' => trim($row['CLIENTE']),
                'VOLUME' => $this->formatVolume($row['VOLUME'])
            ];
            
            // Adicionar apenas se todos os campos estiverem preenchidos
            if (!empty($processedRow['DATA']) && 
                !empty($processedRow['ORIGEM']) && 
                !empty($processedRow['DESTINO']) && 
                !empty($processedRow['CLIENTE']) && 
                $processedRow['VOLUME'] > 0) {
                $processedData[] = $processedRow;
            }
        }
        
        return $processedData;
    }

    /**
     * Formata data
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        // Tentar diferentes formatos de data
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            $dateObj = \DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj->format('Y-m-d');
            }
        }
        
        // Se não conseguir formatar, retornar a data original
        return $date;
    }

    /**
     * Formata volume
     */
    private function formatVolume($volume)
    {
        if (empty($volume)) {
            return 0;
        }
        
        // Remover caracteres não numéricos exceto ponto e vírgula
        $volume = preg_replace('/[^0-9.,]/', '', $volume);
        
        // Converter vírgula para ponto
        $volume = str_replace(',', '.', $volume);
        
        return (float) $volume;
    }

    /**
     * Exporta dados para CSV
     */
    public function exportToCsv($data, $filename = 'export.csv')
    {
        if (empty($data)) {
            throw new \Exception('Nenhum dado para exportar');
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        
        // Escrever cabeçalho
        fputcsv($output, array_keys($data[0]));
        
        // Escrever dados
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Exporta dados para Excel (versão simplificada - CSV com extensão .xlsx)
     */
    public function exportToExcel($data, $filename = 'export.xlsx')
    {
        // Para simplificar, exportar como CSV mas com extensão .xlsx
        $csvFilename = str_replace('.xlsx', '.csv', $filename);
        $this->exportToCsv($data, $csvFilename);
    }
}
