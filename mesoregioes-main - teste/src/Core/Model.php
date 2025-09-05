<?php

namespace App\Core;

/**
 * Classe Model base para arquitetura MVC
 * Fornece funcionalidades básicas para manipulação de dados
 */
class Model
{
    protected $config;
    protected $data = [];
    protected $dataProcessor;
    protected $excelProcessor;

    public function __construct()
    {
        $this->config = new Config();
        $this->dataProcessor = new \App\Utils\DataProcessor();
        $this->excelProcessor = new \App\Utils\ExcelProcessor();
    }

    /**
     * Carregar dados do arquivo Excel
     */
    public function loadData($filePath)
    {
        try {
            $this->data = $this->excelProcessor->readExcel($filePath);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obter todos os dados
     */
    public function getAllData()
    {
        return $this->data;
    }

    /**
     * Verificar se há dados carregados
     */
    public function hasData()
    {
        return !empty($this->data);
    }

    /**
     * Obter configuração
     */
    public function getConfig($key = null)
    {
        if ($key) {
            return $this->config->get($key);
        }
        return $this->config;
    }

    /**
     * Obter processador de dados
     */
    public function getDataProcessor()
    {
        return $this->dataProcessor;
    }

    /**
     * Obter processador de Excel
     */
    public function getExcelProcessor()
    {
        return $this->excelProcessor;
    }

    /**
     * Limpar dados
     */
    public function clearData()
    {
        $this->data = [];
    }

    /**
     * Obter estatísticas básicas dos dados
     */
    public function getBasicStats()
    {
        if (empty($this->data)) {
            return [
                'total_records' => 0,
                'date_range' => null,
                'mesorregioes' => [],
                'clientes' => []
            ];
        }

        $dates = array_column($this->data, 'DATA');
        $mesorregioes = array_unique(array_merge(
            array_column($this->data, 'ORIGEM'),
            array_column($this->data, 'DESTINO')
        ));
        $clientes = array_unique(array_column($this->data, 'CLIENTE'));

        return [
            'total_records' => count($this->data),
            'date_range' => [
                'min' => min($dates),
                'max' => max($dates)
            ],
            'mesorregioes' => array_values($mesorregioes),
            'clientes' => array_values($clientes)
        ];
    }
}
