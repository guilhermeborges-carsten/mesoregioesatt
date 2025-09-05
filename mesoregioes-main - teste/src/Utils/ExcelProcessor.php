<?php

namespace DashboardLogistico\Utils;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

/**
 * Processador de arquivos Excel
 */
class ExcelProcessor
{
    private $config;

    public function __construct()
    {
        $this->config = \DashboardLogistico\Config\Config::getInstance();
    }

    /**
     * Processa arquivo Excel e retorna dados estruturados
     */
    public function processExcelFile($filePath)
    {
        try {
            // Carregar arquivo Excel
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Converter para array
            $data = $worksheet->toArray();
            
            if (empty($data)) {
                return [null, "Arquivo vazio"];
            }

            // Verificar cabeçalhos
            $headers = array_shift($data);
            $expectedHeaders = [
                'TRECHO - FROTA PRÓPRIA',
                'MESORREGIÃO - ORIGEM', 
                'MESORREGIÃO - DESTINO',
                'MÊS',
                'EMBARQUES'
            ];

            // Verificar se as colunas estão corretas
            $missingHeaders = array_diff($expectedHeaders, $headers);
            if (!empty($missingHeaders)) {
                return [null, "Colunas obrigatórias não encontradas: " . implode(', ', $missingHeaders)];
            }

            // Mapear índices das colunas
            $columnIndexes = [];
            foreach ($expectedHeaders as $header) {
                $columnIndexes[$header] = array_search($header, $headers);
            }

            // Processar dados
            $processedData = [];
            foreach ($data as $row) {
                if (empty(array_filter($row))) {
                    continue; // Pular linhas vazias
                }

                $trecho = trim($row[$columnIndexes['TRECHO - FROTA PRÓPRIA']] ?? '');
                $origem = trim($row[$columnIndexes['MESORREGIÃO - ORIGEM']] ?? '');
                $destino = trim($row[$columnIndexes['MESORREGIÃO - DESTINO']] ?? '');
                $mes = trim($row[$columnIndexes['MÊS']] ?? '');
                $embarques = $row[$columnIndexes['EMBARQUES']] ?? 0;

                // Validar dados obrigatórios
                if (empty($trecho) || empty($origem) || empty($destino) || empty($mes)) {
                    continue;
                }

                // Converter embarques para número
                $embarques = is_numeric($embarques) ? (float)$embarques : 0;
                if ($embarques <= 0) {
                    continue;
                }

                // Processar coluna de mês
                $mesData = $this->processMonth($mes);
                if (!$mesData) {
                    continue;
                }

                $processedData[] = [
                    'TRECHO - FROTA PRÓPRIA' => $trecho,
                    'MESORREGIÃO - ORIGEM' => $origem,
                    'MESORREGIÃO - DESTINO' => $destino,
                    'MÊS' => $mes,
                    'EMBARQUES' => $embarques,
                    'ANO' => $mesData['ano'],
                    'MES_NUM' => $mesData['mes'],
                    'DATA' => $mesData['data']
                ];
            }

            if (empty($processedData)) {
                return [null, "Nenhum dado válido encontrado no arquivo"];
            }

            return [$processedData, null];

        } catch (\Exception $e) {
            return [null, "Erro ao processar arquivo: " . $e->getMessage()];
        }
    }

    /**
     * Processa coluna de mês
     */
    private function processMonth($mes)
    {
        // Formato esperado: "1 - 2023" ou "01/2023" ou "2023-01"
        $mes = trim($mes);
        
        // Tentar formato "1 - 2023"
        if (preg_match('/^(\d{1,2})\s*-\s*(\d{4})$/', $mes, $matches)) {
            $mesNum = (int)$matches[1];
            $ano = (int)$matches[2];
        }
        // Tentar formato "01/2023"
        elseif (preg_match('/^(\d{1,2})\/(\d{4})$/', $mes, $matches)) {
            $mesNum = (int)$matches[1];
            $ano = (int)$matches[2];
        }
        // Tentar formato "2023-01"
        elseif (preg_match('/^(\d{4})-(\d{1,2})$/', $mes, $matches)) {
            $ano = (int)$matches[1];
            $mesNum = (int)$matches[2];
        }
        else {
            return null;
        }

        // Validar mês e ano
        if ($mesNum < 1 || $mesNum > 12 || $ano < 2000 || $ano > 2100) {
            return null;
        }

        // Criar data
        $data = sprintf('%04d-%02d-01', $ano, $mesNum);

        return [
            'ano' => $ano,
            'mes' => $mesNum,
            'data' => $data
        ];
    }

    /**
     * Exporta dados para Excel
     */
    public function exportToExcel($data, $filename = 'export.xlsx')
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Definir cabeçalhos
        $headers = [
            'TRECHO - FROTA PRÓPRIA',
            'MESORREGIÃO - ORIGEM',
            'MESORREGIÃO - DESTINO',
            'MÊS',
            'EMBARQUES'
        ];

        $col = 1;
        foreach ($headers as $header) {
            $worksheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        // Adicionar dados
        $row = 2;
        foreach ($data as $item) {
            $col = 1;
            foreach ($headers as $header) {
                $worksheet->setCellValueByColumnAndRow($col, $row, $item[$header] ?? '');
                $col++;
            }
            $row++;
        }

        // Configurar headers para download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporta dados para CSV
     */
    public function exportToCsv($data, $filename = 'export.csv')
    {
        // Configurar headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        
        // Adicionar BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Definir cabeçalhos
        $headers = [
            'TRECHO - FROTA PRÓPRIA',
            'MESORREGIÃO - ORIGEM',
            'MESORREGIÃO - DESTINO',
            'MÊS',
            'EMBARQUES'
        ];

        fputcsv($output, $headers, ';');

        // Adicionar dados
        foreach ($data as $item) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $item[$header] ?? '';
            }
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;
    }
}
