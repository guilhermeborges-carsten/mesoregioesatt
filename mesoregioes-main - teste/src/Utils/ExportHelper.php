<?php

namespace App\Utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Helper para exportações específicas
 */
class ExportHelper
{
    /**
     * Exporta balanço de embarques para Excel
     */
    public function exportBalancoExcel($data, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Dados do balanço
        $balancoData = $data['data'] ?? [];
        $resumo = $data['resumo'] ?? [];

        // Cabeçalhos
        $headers = [
            'MESORREGIÃO',
            'EMBARQUES_ORIGEM',
            'EMBARQUES_DESTINO',
            'SALDO',
            'TOTAL_MOVIMENTADO',
            'PERCENTUAL_ORIGEM',
            'PERCENTUAL_DESTINO',
            'CLASSIFICACAO'
        ];

        // Escrever cabeçalhos
        $col = 1;
        foreach ($headers as $header) {
            $worksheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        // Aplicar estilo aos cabeçalhos
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $worksheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Escrever dados
        $row = 2;
        foreach ($balancoData as $item) {
            $col = 1;
            foreach ($headers as $header) {
                $value = $item[$header] ?? '';
                $worksheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Aplicar bordas a todos os dados
        $dataRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . ($row - 1);
        $worksheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Ajustar largura das colunas
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Adicionar aba de resumo
        $resumoSheet = $spreadsheet->createSheet();
        $resumoSheet->setTitle('Resumo');
        
        $resumoData = [
            ['Métrica', 'Valor'],
            ['Total de Mesorregiões', $resumo['total_mesorregioes'] ?? 0],
            ['Regiões Produtoras', $resumo['produtoras'] ?? 0],
            ['Regiões Consumidoras', $resumo['consumidoras'] ?? 0],
            ['Regiões Equilibradas', $resumo['equilibradas'] ?? 0]
        ];

        $row = 1;
        foreach ($resumoData as $resumoRow) {
            $col = 1;
            foreach ($resumoRow as $value) {
                $resumoSheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Aplicar estilo ao resumo
        $resumoRange = 'A1:B' . count($resumoData);
        $resumoSheet->getStyle($resumoRange)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Configurar headers para download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporta balanço de clientes para Excel
     */
    public function exportBalancoClientesExcel($data, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Dados do balanço
        $balancoData = $data['data'] ?? [];
        $resumoClientes = $data['resumo_clientes'] ?? [];
        $resumoGeral = $data['resumo_geral'] ?? [];

        // Cabeçalhos
        $headers = [
            'CLIENTE',
            'MESORREGIÃO',
            'EMBARQUES_ORIGEM',
            'EMBARQUES_DESTINO',
            'SALDO',
            'TOTAL_MOVIMENTADO',
            'PERCENTUAL_ORIGEM',
            'PERCENTUAL_DESTINO',
            'CLASSIFICACAO'
        ];

        // Escrever cabeçalhos
        $col = 1;
        foreach ($headers as $header) {
            $worksheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        // Aplicar estilo aos cabeçalhos
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $worksheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Escrever dados
        $row = 2;
        foreach ($balancoData as $item) {
            $col = 1;
            foreach ($headers as $header) {
                $value = $item[$header] ?? '';
                $worksheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Aplicar bordas a todos os dados
        $dataRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . ($row - 1);
        $worksheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Ajustar largura das colunas
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Adicionar aba de resumo por clientes
        $resumoClientesSheet = $spreadsheet->createSheet();
        $resumoClientesSheet->setTitle('Resumo_Clientes');
        
        $resumoClientesHeaders = [
            'CLIENTE',
            'TOTAL_MESORREGIÕES',
            'TOTAL_EMBARQUES_ORIGEM',
            'TOTAL_EMBARQUES_DESTINO',
            'TOTAL_MOVIMENTADO',
            'SALDO_TOTAL',
            'MESORREGIÕES_PRODUTORAS',
            'MESORREGIÕES_CONSUMIDORAS'
        ];

        $col = 1;
        foreach ($resumoClientesHeaders as $header) {
            $resumoClientesSheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        $row = 2;
        foreach ($resumoClientes as $item) {
            $col = 1;
            foreach ($resumoClientesHeaders as $header) {
                $value = $item[$header] ?? '';
                $resumoClientesSheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Adicionar aba de resumo geral
        $resumoGeralSheet = $spreadsheet->createSheet();
        $resumoGeralSheet->setTitle('Resumo_Geral');
        
        $resumoGeralData = [
            ['Métrica', 'Valor'],
            ['Total de Clientes', $resumoGeral['total_clientes'] ?? 0],
            ['Total de Mesorregiões', $resumoGeral['total_mesorregioes'] ?? 0],
            ['Regiões Produtoras', $resumoGeral['produtoras'] ?? 0],
            ['Regiões Consumidoras', $resumoGeral['consumidoras'] ?? 0],
            ['Regiões Equilibradas', $resumoGeral['equilibradas'] ?? 0]
        ];

        $row = 1;
        foreach ($resumoGeralData as $resumoRow) {
            $col = 1;
            foreach ($resumoRow as $value) {
                $resumoGeralSheet->setCellValueByColumnAndRow($col, $row, $value);
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
}
