<?php

namespace DashboardLogistico\Utils;

use DashboardLogistico\Config\Config;

/**
 * Processador de dados - substitui funcionalidades do pandas
 */
class DataProcessor
{
    private $data = null;
    private $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     * Define os dados globais
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Verifica se há dados carregados
     */
    public function hasData()
    {
        return $this->data !== null && !empty($this->data);
    }

    /**
     * Obtém os dados
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Aplica filtros aos dados
     */
    public function applyFilters($filters)
    {
        if (!$this->hasData()) {
            return [];
        }

        $filteredData = $this->data;

        // Filtro de data início
        if (!empty($filters['data_inicio'])) {
            $dataInicio = $filters['data_inicio'] . '-01';
            $filteredData = array_filter($filteredData, function($row) use ($dataInicio) {
                return $row['DATA'] >= $dataInicio;
            });
        }

        // Filtro de data fim
        if (!empty($filters['data_fim'])) {
            $dataFim = $filters['data_fim'] . '-31';
            $filteredData = array_filter($filteredData, function($row) use ($dataFim) {
                return $row['DATA'] <= $dataFim;
            });
        }

        // Filtro de origens
        if (!empty($filters['origens'])) {
            $origens = is_array($filters['origens']) ? $filters['origens'] : [$filters['origens']];
            $filteredData = array_filter($filteredData, function($row) use ($origens) {
                return in_array($row['MESORREGIÃO - ORIGEM'], $origens);
            });
        }

        // Filtro de destinos
        if (!empty($filters['destinos'])) {
            $destinos = is_array($filters['destinos']) ? $filters['destinos'] : [$filters['destinos']];
            $filteredData = array_filter($filteredData, function($row) use ($destinos) {
                return in_array($row['MESORREGIÃO - DESTINO'], $destinos);
            });
        }

        return array_values($filteredData);
    }

    /**
     * Calcula estatísticas gerais
     */
    public function getStats($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        $totalEmbarques = array_sum(array_column($data, 'EMBARQUES'));
        
        // Contar origens e destinos únicos
        $origens = array_unique(array_column($data, 'MESORREGIÃO - ORIGEM'));
        $destinos = array_unique(array_column($data, 'MESORREGIÃO - DESTINO'));
        
        // Período
        $datas = array_column($data, 'DATA');
        sort($datas);
        $periodoInicio = date('m/Y', strtotime($datas[0]));
        $periodoFim = date('m/Y', strtotime(end($datas)));

        // Top 5 origens
        $origensStats = [];
        foreach ($data as $row) {
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $origensStats[$origem] = ($origensStats[$origem] ?? 0) + $row['EMBARQUES'];
        }
        arsort($origensStats);
        $topOrigens = array_slice($origensStats, 0, 5, true);

        // Top 5 destinos
        $destinosStats = [];
        foreach ($data as $row) {
            $destino = $row['MESORREGIÃO - DESTINO'];
            $destinosStats[$destino] = ($destinosStats[$destino] ?? 0) + $row['EMBARQUES'];
        }
        arsort($destinosStats);
        $topDestinos = array_slice($destinosStats, 0, 5, true);

        return [
            'total_embarques' => (int)$totalEmbarques,
            'total_origens' => count($origens),
            'total_destinos' => count($destinos),
            'periodo_inicio' => $periodoInicio,
            'periodo_fim' => $periodoFim,
            'top_origens' => array_map(function($regiao, $embarques) {
                return ['regiao' => $regiao, 'embarques' => (int)$embarques];
            }, array_keys($topOrigens), array_values($topOrigens)),
            'top_destinos' => array_map(function($regiao, $embarques) {
                return ['regiao' => $regiao, 'embarques' => (int)$embarques];
            }, array_keys($topDestinos), array_values($topDestinos))
        ];
    }

    /**
     * Calcula evolução mensal
     */
    public function getEvolucaoMensal($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Agrupar por mês
        $evolucao = [];
        foreach ($data as $row) {
            $key = $row['ANO'] . '-' . sprintf('%02d', $row['MES_NUM']);
            if (!isset($evolucao[$key])) {
                $evolucao[$key] = [
                    'ano' => $row['ANO'],
                    'mes' => $row['MES_NUM'],
                    'data' => $row['DATA'],
                    'embarques' => 0
                ];
            }
            $evolucao[$key]['embarques'] += $row['EMBARQUES'];
        }

        // Ordenar por data
        ksort($evolucao);
        $evolucao = array_values($evolucao);

        // Calcular tendência (média móvel de 3 meses)
        $tendencia = [];
        for ($i = 0; $i < count($evolucao); $i++) {
            $start = max(0, $i - 1);
            $end = min(count($evolucao) - 1, $i + 1);
            $count = $end - $start + 1;
            $sum = 0;
            for ($j = $start; $j <= $end; $j++) {
                $sum += $evolucao[$j]['embarques'];
            }
            $tendencia[] = round($sum / $count);
        }

        return [
            'labels' => array_map(function($item) {
                return $item['mes'] . '/' . $item['ano'];
            }, $evolucao),
            'embarques' => array_column($evolucao, 'embarques'),
            'tendencia' => $tendencia
        ];
    }

    /**
     * Calcula evolução mensal por clientes
     */
    public function getEvolucaoMensalClientes($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Extrair nome do cliente
        foreach ($data as &$row) {
            $row['CLIENTE'] = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
        }

        // Agrupar por cliente e mês
        $evolucaoClientes = [];
        foreach ($data as $row) {
            $key = $row['CLIENTE'] . '|' . $row['ANO'] . '-' . sprintf('%02d', $row['MES_NUM']);
            if (!isset($evolucaoClientes[$key])) {
                $evolucaoClientes[$key] = [
                    'cliente' => $row['CLIENTE'],
                    'ano' => $row['ANO'],
                    'mes' => $row['MES_NUM'],
                    'data' => $row['DATA'],
                    'embarques' => 0
                ];
            }
            $evolucaoClientes[$key]['embarques'] += $row['EMBARQUES'];
        }

        // Agrupar por cliente
        $clientes = [];
        foreach ($evolucaoClientes as $item) {
            $cliente = $item['cliente'];
            if (!isset($clientes[$cliente])) {
                $clientes[$cliente] = [];
            }
            $clientes[$cliente][] = $item;
        }

        // Ordenar dados de cada cliente por data
        foreach ($clientes as $cliente => &$dados) {
            usort($dados, function($a, $b) {
                return strcmp($a['data'], $b['data']);
            });

            // Calcular tendência para cada cliente
            $tendencia = [];
            for ($i = 0; $i < count($dados); $i++) {
                $start = max(0, $i - 1);
                $end = min(count($dados) - 1, $i + 1);
                $count = $end - $start + 1;
                $sum = 0;
                for ($j = $start; $j <= $end; $j++) {
                    $sum += $dados[$j]['embarques'];
                }
                $tendencia[] = round($sum / $count);
            }

            $clientes[$cliente] = [
                'labels' => array_map(function($item) {
                    return $item['mes'] . '/' . $item['ano'];
                }, $dados),
                'embarques' => array_column($dados, 'embarques'),
                'tendencia' => $tendencia
            ];
        }

        return [
            'clientes' => array_keys($clientes),
            'dados' => $clientes
        ];
    }

    /**
     * Extrai nome do cliente do trecho
     */
    private function extractClientName($trecho)
    {
        $parts = explode(' - ', $trecho);
        return trim($parts[0]);
    }

    /**
     * Calcula top origens
     */
    public function getTopOrigens($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        $limit = (int)($filters['limit'] ?? 20);

        // Agrupar por origem
        $origens = [];
        foreach ($data as $row) {
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $origens[$origem] = ($origens[$origem] ?? 0) + $row['EMBARQUES'];
        }

        // Ordenar e limitar
        arsort($origens);
        $topOrigens = array_slice($origens, 0, $limit, true);

        $total = array_sum($topOrigens);

        return [
            'origens' => array_keys($topOrigens),
            'embarques' => array_values($topOrigens),
            'percentuais' => array_map(function($embarques) use ($total) {
                return round(($embarques / $total) * 100, 1);
            }, array_values($topOrigens))
        ];
    }

    /**
     * Calcula top destinos
     */
    public function getTopDestinos($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        $limit = (int)($filters['limit'] ?? 20);

        // Agrupar por destino
        $destinos = [];
        foreach ($data as $row) {
            $destino = $row['MESORREGIÃO - DESTINO'];
            $destinos[$destino] = ($destinos[$destino] ?? 0) + $row['EMBARQUES'];
        }

        // Ordenar e limitar
        arsort($destinos);
        $topDestinos = array_slice($destinos, 0, $limit, true);

        $total = array_sum($topDestinos);

        return [
            'destinos' => array_keys($topDestinos),
            'embarques' => array_values($topDestinos),
            'percentuais' => array_map(function($embarques) use ($total) {
                return round(($embarques / $total) * 100, 1);
            }, array_values($topDestinos))
        ];
    }

    /**
     * Calcula dados do heatmap
     */
    public function getHeatmapData($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Aplicar filtro de volume mínimo
        $volumeMinimo = (int)($filters['volume_minimo'] ?? 0);
        if ($volumeMinimo > 0) {
            $data = array_filter($data, function($row) use ($volumeMinimo) {
                return $row['EMBARQUES'] >= $volumeMinimo;
            });
        }

        // Agrupar por origem-destino
        $heatmapData = [];
        foreach ($data as $row) {
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $destino = $row['MESORREGIÃO - DESTINO'];
            $key = $origem . '|' . $destino;
            
            if (!isset($heatmapData[$key])) {
                $heatmapData[$key] = [
                    'origem' => $origem,
                    'destino' => $destino,
                    'embarques' => 0
                ];
            }
            $heatmapData[$key]['embarques'] += $row['EMBARQUES'];
        }

        // Obter origens e destinos únicos
        $origens = array_unique(array_column($heatmapData, 'origem'));
        $destinos = array_unique(array_column($heatmapData, 'destino'));
        
        sort($origens);
        sort($destinos);

        // Criar matriz
        $matriz = [];
        foreach ($origens as $i => $origem) {
            $matriz[$i] = [];
            foreach ($destinos as $j => $destino) {
                $key = $origem . '|' . $destino;
                $matriz[$i][$j] = $heatmapData[$key]['embarques'] ?? 0;
            }
        }

        // Top fluxos
        $topFluxos = array_values($heatmapData);
        usort($topFluxos, function($a, $b) {
            return $b['embarques'] - $a['embarques'];
        });
        $topFluxos = array_slice($topFluxos, 0, 10);

        // Calcular percentuais para top fluxos
        $totalEmbarques = array_sum(array_column($heatmapData, 'embarques'));
        foreach ($topFluxos as &$fluxo) {
            $fluxo['percentual'] = $totalEmbarques > 0 ? round(($fluxo['embarques'] / $totalEmbarques) * 100, 1) : 0;
        }

        return [
            'origens' => $origens,
            'destinos' => $destinos,
            'matriz' => $matriz,
            'top_fluxos' => $topFluxos
        ];
    }

    /**
     * Calcula dados para o mapa
     */
    public function getFluxosMapa($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Aplicar filtros adicionais
        $volumeMinimo = (int)($filters['volume_minimo'] ?? 0);
        $topN = (int)($filters['top_n'] ?? 20);

        // Agrupar por origem-destino
        $fluxos = [];
        foreach ($data as $row) {
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $destino = $row['MESORREGIÃO - DESTINO'];
            $key = $origem . '|' . $destino;
            
            if (!isset($fluxos[$key])) {
                $fluxos[$key] = [
                    'origem' => $origem,
                    'destino' => $destino,
                    'embarques' => 0,
                    'origem_coords' => $this->getCoordinates($origem),
                    'destino_coords' => $this->getCoordinates($destino)
                ];
            }
            $fluxos[$key]['embarques'] += $row['EMBARQUES'];
        }

        // Filtrar por volume mínimo
        if ($volumeMinimo > 0) {
            $fluxos = array_filter($fluxos, function($fluxo) use ($volumeMinimo) {
                return $fluxo['embarques'] >= $volumeMinimo;
            });
        }

        // Ordenar por volume e limitar
        uasort($fluxos, function($a, $b) {
            return $b['embarques'] - $a['embarques'];
        });
        
        $fluxos = array_slice($fluxos, 0, $topN, true);

        // Calcular percentuais
        $totalEmbarques = array_sum(array_column($fluxos, 'embarques'));
        foreach ($fluxos as &$fluxo) {
            $fluxo['percentual'] = $totalEmbarques > 0 ? round(($fluxo['embarques'] / $totalEmbarques) * 100, 1) : 0;
        }

        // Obter coordenadas únicas
        $coordenadas = [];
        foreach ($fluxos as $fluxo) {
            $coordenadas[$fluxo['origem']] = $fluxo['origem_coords'];
            $coordenadas[$fluxo['destino']] = $fluxo['destino_coords'];
        }

        return [
            'fluxos' => array_values($fluxos),
            'coordenadas' => $coordenadas
        ];
    }

    /**
     * Obtém coordenadas de uma mesorregião
     */
    private function getCoordinates($mesorregiao)
    {
        $coordinates = $this->config->get('coordinates', []);
        
        // Buscar correspondência exata
        if (isset($coordinates[$mesorregiao])) {
            return $coordinates[$mesorregiao];
        }

        // Buscar correspondência parcial
        $mesorregiaoUpper = strtoupper($mesorregiao);
        foreach ($coordinates as $key => $coords) {
            if (strpos($key, $mesorregiaoUpper) !== false || strpos($mesorregiaoUpper, $key) !== false) {
                return $coords;
            }
        }

        // Coordenadas padrão (centro do Brasil)
        return [-15.78, -47.92];
    }

    /**
     * Obtém mesorregiões disponíveis
     */
    public function getMesorregioes()
    {
        if (!$this->hasData()) {
            return ['error' => 'Nenhum dado carregado'];
        }

        $origens = array_unique(array_column($this->data, 'MESORREGIÃO - ORIGEM'));
        $destinos = array_unique(array_column($this->data, 'MESORREGIÃO - DESTINO'));
        
        sort($origens);
        sort($destinos);

        return [
            'origens' => $origens,
            'destinos' => $destinos
        ];
    }

    /**
     * Obtém clientes únicos
     */
    public function getClientes()
    {
        if (!$this->hasData()) {
            return ['error' => 'Nenhum dado carregado'];
        }

        $clientes = [];
        foreach ($this->data as $row) {
            $cliente = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
            $clientes[] = $cliente;
        }

        $clientes = array_unique($clientes);
        sort($clientes);

        return [
            'clientes' => $clientes,
            'total' => count($clientes)
        ];
    }

    /**
     * Calcula insights de clientes
     */
    public function getInsightsClientes($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Extrair nome do cliente
        foreach ($data as &$row) {
            $row['CLIENTE'] = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
        }

        // Agrupar por cliente
        $clientes = [];
        foreach ($data as $row) {
            $cliente = $row['CLIENTE'];
            if (!isset($clientes[$cliente])) {
                $clientes[$cliente] = [];
            }
            $clientes[$cliente][] = $row;
        }

        $insightsClientes = [];
        foreach ($clientes as $cliente => $dadosCliente) {
            $totalEmbarques = array_sum(array_column($dadosCliente, 'EMBARQUES'));
            $totalRegistros = count($dadosCliente);
            
            // Período
            $datas = array_column($dadosCliente, 'DATA');
            sort($datas);
            $periodoInicio = date('m/Y', strtotime($datas[0]));
            $periodoFim = date('m/Y', strtotime(end($datas)));

            // Top origens e destinos
            $origensStats = [];
            $destinosStats = [];
            foreach ($dadosCliente as $row) {
                $origem = $row['MESORREGIÃO - ORIGEM'];
                $destino = $row['MESORREGIÃO - DESTINO'];
                $origensStats[$origem] = ($origensStats[$origem] ?? 0) + $row['EMBARQUES'];
                $destinosStats[$destino] = ($destinosStats[$destino] ?? 0) + $row['EMBARQUES'];
            }

            arsort($origensStats);
            arsort($destinosStats);
            $topOrigens = array_slice($origensStats, 0, 5, true);
            $topDestinos = array_slice($destinosStats, 0, 5, true);

            // Análise de sazonalidade
            $sazonalidade = [];
            foreach ($dadosCliente as $row) {
                $mes = $row['MES_NUM'];
                $sazonalidade[$mes] = ($sazonalidade[$mes] ?? 0) + $row['EMBARQUES'];
            }
            ksort($sazonalidade);

            // Calcular crescimento
            $crescimento = 0;
            if (count($sazonalidade) > 1) {
                $valores = array_values($sazonalidade);
                $primeiroMes = $valores[0];
                $ultimoMes = end($valores);
                if ($primeiroMes > 0) {
                    $crescimento = (($ultimoMes - $primeiroMes) / $primeiroMes) * 100;
                }
            }

            // Identificar oportunidades e melhorias
            $oportunidades = [];
            $melhorias = [];

            // Análise de concentração geográfica
            if (!empty($topOrigens)) {
                $maiorOrigem = reset($topOrigens);
                if ($maiorOrigem / $totalEmbarques > 0.7) {
                    $oportunidades[] = "Diversificar origens para reduzir dependência de uma única região";
                }
            }

            if (!empty($topDestinos)) {
                $maiorDestino = reset($topDestinos);
                if ($maiorDestino / $totalEmbarques > 0.7) {
                    $oportunidades[] = "Expandir para novos destinos para aumentar mercado";
                }
            }

            // Análise de crescimento
            if ($crescimento > 20) {
                $melhorias[] = "Crescimento forte - considerar expansão de capacidade";
            } elseif ($crescimento < -10) {
                $melhorias[] = "Declínio detectado - investigar causas e implementar ações corretivas";
            }

            // Análise de sazonalidade
            if (!empty($sazonalidade)) {
                $mesMaior = array_keys($sazonalidade, max($sazonalidade))[0];
                $mesMenor = array_keys($sazonalidade, min($sazonalidade))[0];
                
                if (end($sazonalidade) > reset($sazonalidade)) {
                    $melhorias[] = "Tendência positiva - manter estratégias de crescimento";
                }
            }

            $insightsClientes[$cliente] = [
                'estatisticas' => [
                    'total_embarques' => (int)$totalEmbarques,
                    'total_registros' => $totalRegistros,
                    'periodo_inicio' => $periodoInicio,
                    'periodo_fim' => $periodoFim,
                    'crescimento_percentual' => round($crescimento, 1)
                ],
                'top_origens' => array_map(function($regiao, $embarques) use ($totalEmbarques) {
                    return [
                        'regiao' => $regiao,
                        'embarques' => (int)$embarques,
                        'percentual' => round(($embarques / $totalEmbarques) * 100, 1)
                    ];
                }, array_keys($topOrigens), array_values($topOrigens)),
                'top_destinos' => array_map(function($regiao, $embarques) use ($totalEmbarques) {
                    return [
                        'regiao' => $regiao,
                        'embarques' => (int)$embarques,
                        'percentual' => round(($embarques / $totalEmbarques) * 100, 1)
                    ];
                }, array_keys($topDestinos), array_values($topDestinos)),
                'sazonalidade' => [
                    'meses' => array_keys($sazonalidade),
                    'embarques' => array_values($sazonalidade),
                    'mes_maior' => $mesMaior ?? 0,
                    'mes_menor' => $mesMenor ?? 0
                ],
                'oportunidades' => $oportunidades,
                'melhorias' => $melhorias,
                'score_performance' => min(100, max(0, 50 + $crescimento))
            ];
        }

        // Análise geral
        $totalGeral = array_sum(array_column($data, 'EMBARQUES'));
        $crescimentoGeral = 0;

        if (count($data) > 0) {
            // Calcular crescimento geral
            $evolucao = $this->getEvolucaoMensal($filters);
            if (isset($evolucao['embarques']) && count($evolucao['embarques']) > 1) {
                $embarques = $evolucao['embarques'];
                $primeiroMes = $embarques[0];
                $ultimoMes = end($embarques);
                if ($primeiroMes > 0) {
                    $crescimentoGeral = (($ultimoMes - $primeiroMes) / $primeiroMes) * 100;
                }
            }
        }

        $insightsGeral = [
            'total_embarques' => (int)$totalGeral,
            'total_clientes' => count($clientes),
            'crescimento_geral' => round($crescimentoGeral, 1),
            'clientes_ativos' => count(array_filter($insightsClientes, function($insight) {
                return $insight['estatisticas']['total_embarques'] > 0;
            })),
            'distribuicao_clientes' => array_map(function($cliente, $insight) use ($totalGeral) {
                return [
                    'cliente' => $cliente,
                    'percentual' => round(($insight['estatisticas']['total_embarques'] / $totalGeral) * 100, 1),
                    'score' => $insight['score_performance']
                ];
            }, array_keys($insightsClientes), array_values($insightsClientes))
        ];

        return [
            'insights_geral' => $insightsGeral,
            'insights_clientes' => $insightsClientes
        ];
    }

    /**
     * Calcula dados da tabela
     */
    public function getTabelaDados($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado com os filtros aplicados'];
        }

        // Aplicar filtro de volume mínimo
        $volumeMinimo = (int)($filters['volume_minimo'] ?? 0);
        if ($volumeMinimo > 0) {
            $data = array_filter($data, function($row) use ($volumeMinimo) {
                return $row['EMBARQUES'] >= $volumeMinimo;
            });
        }

        // Aplicar filtro de clientes
        if (!empty($filters['clientes'])) {
            $clientes = is_array($filters['clientes']) ? $filters['clientes'] : [$filters['clientes']];
            $data = array_filter($data, function($row) use ($clientes) {
                $cliente = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
                return in_array($cliente, $clientes);
            });
        }

        $totalRegistros = count($data);
        $page = (int)($filters['page'] ?? 1);
        $limit = (int)($filters['limit'] ?? 50);
        $offset = ($page - 1) * $limit;

        // Paginação
        $dadosPaginados = array_slice($data, $offset, $limit);

        $dadosTabela = [];
        foreach ($dadosPaginados as $row) {
            $cliente = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
            
            $dadosTabela[] = [
                'data' => $row['DATA'],
                'origem' => $row['MESORREGIÃO - ORIGEM'],
                'destino' => $row['MESORREGIÃO - DESTINO'],
                'cliente' => $cliente,
                'embarques' => (int)$row['EMBARQUES'],
                'valor' => (float)($row['VALOR'] ?? 0),
                'peso' => (float)($row['PESO'] ?? 0),
                'volume' => (float)($row['VOLUME'] ?? 0)
            ];
        }

        // Calcular estatísticas
        $totalEmbarques = array_sum(array_column($data, 'EMBARQUES'));
        $clientesUnicos = count(array_unique(array_map(function($row) {
            return $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
        }, $data)));
        $embarquesMedios = $totalRegistros > 0 ? $totalEmbarques / $totalRegistros : 0;

        return [
            'dados' => $dadosTabela,
            'total_registros' => $totalRegistros,
            'total_embarques' => (int)$totalEmbarques,
            'total_clientes' => $clientesUnicos,
            'embarques_medios' => round($embarquesMedios, 2),
            'paginacao' => [
                'pagina_atual' => $page,
                'total_paginas' => ceil($totalRegistros / $limit),
                'registros_por_pagina' => $limit,
                'total_registros' => $totalRegistros
            ]
        ];
    }

    /**
     * Calcula balanço de embarques
     */
    public function getBalancoEmbarques($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado'];
        }

        // Agrupar por origem e destino
        $origens = [];
        $destinos = [];
        
        foreach ($data as $row) {
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $destino = $row['MESORREGIÃO - DESTINO'];
            $embarques = $row['EMBARQUES'];
            
            $origens[$origem] = ($origens[$origem] ?? 0) + $embarques;
            $destinos[$destino] = ($destinos[$destino] ?? 0) + $embarques;
        }

        // Combinar origens e destinos
        $mesorregioes = array_unique(array_merge(array_keys($origens), array_keys($destinos)));
        
        $resultado = [];
        foreach ($mesorregioes as $mesorregiao) {
            $embarquesOrigem = $origens[$mesorregiao] ?? 0;
            $embarquesDestino = $destinos[$mesorregiao] ?? 0;
            $saldo = $embarquesOrigem - $embarquesDestino;
            $totalMovimentado = $embarquesOrigem + $embarquesDestino;
            
            $percentualOrigem = $totalMovimentado > 0 ? ($embarquesOrigem / $totalMovimentado) * 100 : 0;
            $percentualDestino = $totalMovimentado > 0 ? ($embarquesDestino / $totalMovimentado) * 100 : 0;
            
            $classificacao = 'Equilibrada (Origem = Destino)';
            if ($saldo > 0) {
                $classificacao = 'Produtora (Origem > Destino)';
            } elseif ($saldo < 0) {
                $classificacao = 'Consumidora (Destino > Origem)';
            }
            
            $resultado[] = [
                'MESORREGIÃO' => $mesorregiao,
                'EMBARQUES_ORIGEM' => (int)$embarquesOrigem,
                'EMBARQUES_DESTINO' => (int)$embarquesDestino,
                'SALDO' => (int)$saldo,
                'TOTAL_MOVIMENTADO' => (int)$totalMovimentado,
                'PERCENTUAL_ORIGEM' => round($percentualOrigem, 1),
                'PERCENTUAL_DESTINO' => round($percentualDestino, 1),
                'CLASSIFICACAO' => $classificacao
            ];
        }

        // Ordenar por total movimentado
        usort($resultado, function($a, $b) {
            return $b['TOTAL_MOVIMENTADO'] - $a['TOTAL_MOVIMENTADO'];
        });

        // Aplicar filtro de classificação se especificado
        $classificacao = $filters['classificacao'] ?? '';
        if ($classificacao) {
            $resultado = array_filter($resultado, function($item) use ($classificacao) {
                switch ($classificacao) {
                    case 'produtora':
                        return $item['SALDO'] > 0;
                    case 'consumidora':
                        return $item['SALDO'] < 0;
                    case 'equilibrada':
                        return $item['SALDO'] == 0;
                    default:
                        return true;
                }
            });
        }

        // Aplicar limite se especificado
        $limit = (int)($filters['limit'] ?? 0);
        if ($limit > 0) {
            $resultado = array_slice($resultado, 0, $limit);
        }

        // Calcular resumo
        $produtoras = count(array_filter($resultado, function($item) {
            return $item['SALDO'] > 0;
        }));
        $consumidoras = count(array_filter($resultado, function($item) {
            return $item['SALDO'] < 0;
        }));
        $equilibradas = count(array_filter($resultado, function($item) {
            return $item['SALDO'] == 0;
        }));

        // Calcular percentuais
        $totalGeral = array_sum(array_column($resultado, 'TOTAL_MOVIMENTADO'));
        foreach ($resultado as &$item) {
            $item['PERCENTUAL'] = $totalGeral > 0 ? round(($item['TOTAL_MOVIMENTADO'] / $totalGeral) * 100, 1) : 0;
        }

        return [
            'balanco' => array_values($resultado),
            'resumo' => [
                'total_mesorregioes' => count($resultado),
                'produtoras' => $produtoras,
                'consumidoras' => $consumidoras,
                'equilibradas' => $equilibradas
            ]
        ];
    }

    /**
     * Calcula balanço de clientes
     */
    public function getBalancoClientes($filters = [])
    {
        $data = $this->applyFilters($filters);
        
        if (empty($data)) {
            return ['error' => 'Nenhum dado encontrado'];
        }

        // Extrair nome do cliente
        foreach ($data as &$row) {
            $row['CLIENTE'] = $this->extractClientName($row['TRECHO - FROTA PRÓPRIA']);
        }

        // Agrupar por cliente e mesorregião
        $clientes = [];
        foreach ($data as $row) {
            $cliente = $row['CLIENTE'];
            $origem = $row['MESORREGIÃO - ORIGEM'];
            $destino = $row['MESORREGIÃO - DESTINO'];
            $embarques = $row['EMBARQUES'];
            
            if (!isset($clientes[$cliente])) {
                $clientes[$cliente] = [];
            }
            
            if (!isset($clientes[$cliente][$origem])) {
                $clientes[$cliente][$origem] = ['origem' => 0, 'destino' => 0];
            }
            if (!isset($clientes[$cliente][$destino])) {
                $clientes[$cliente][$destino] = ['origem' => 0, 'destino' => 0];
            }
            
            $clientes[$cliente][$origem]['origem'] += $embarques;
            $clientes[$cliente][$destino]['destino'] += $embarques;
        }

        $resultado = [];
        $resumoClientes = [];
        
        foreach ($clientes as $cliente => $mesorregioes) {
            $totalEmbarquesOrigem = 0;
            $totalEmbarquesDestino = 0;
            $mesorregioesProdutoras = 0;
            $mesorregioesConsumidoras = 0;
            
            foreach ($mesorregioes as $mesorregiao => $dados) {
                $embarquesOrigem = $dados['origem'];
                $embarquesDestino = $dados['destino'];
                $saldo = $embarquesOrigem - $embarquesDestino;
                $totalMovimentado = $embarquesOrigem + $embarquesDestino;
                
                if ($totalMovimentado > 0) {
                    $percentualOrigem = ($embarquesOrigem / $totalMovimentado) * 100;
                    $percentualDestino = ($embarquesDestino / $totalMovimentado) * 100;
                    
                    $classificacao = 'Equilibrada (Origem = Destino)';
                    if ($saldo > 0) {
                        $classificacao = 'Produtora (Origem > Destino)';
                        $mesorregioesProdutoras++;
                    } elseif ($saldo < 0) {
                        $classificacao = 'Consumidora (Destino > Origem)';
                        $mesorregioesConsumidoras++;
                    }
                    
                    $resultado[] = [
                        'CLIENTE' => $cliente,
                        'MESORREGIÃO' => $mesorregiao,
                        'EMBARQUES_ORIGEM' => (int)$embarquesOrigem,
                        'EMBARQUES_DESTINO' => (int)$embarquesDestino,
                        'SALDO' => (int)$saldo,
                        'TOTAL_MOVIMENTADO' => (int)$totalMovimentado,
                        'PERCENTUAL_ORIGEM' => round($percentualOrigem, 1),
                        'PERCENTUAL_DESTINO' => round($percentualDestino, 1),
                        'CLASSIFICACAO' => $classificacao
                    ];
                    
                    $totalEmbarquesOrigem += $embarquesOrigem;
                    $totalEmbarquesDestino += $embarquesDestino;
                }
            }
            
            $resumoClientes[] = [
                'CLIENTE' => $cliente,
                'TOTAL_MESORREGIÕES' => count($mesorregioes),
                'TOTAL_EMBARQUES_ORIGEM' => (int)$totalEmbarquesOrigem,
                'TOTAL_EMBARQUES_DESTINO' => (int)$totalEmbarquesDestino,
                'TOTAL_MOVIMENTADO' => (int)($totalEmbarquesOrigem + $totalEmbarquesDestino),
                'SALDO_TOTAL' => (int)($totalEmbarquesOrigem - $totalEmbarquesDestino),
                'MESORREGIÕES_PRODUTORAS' => $mesorregioesProdutoras,
                'MESORREGIÕES_CONSUMIDORAS' => $mesorregioesConsumidoras
            ];
        }

        // Ordenar resultado por cliente e total movimentado
        usort($resultado, function($a, $b) {
            if ($a['CLIENTE'] === $b['CLIENTE']) {
                return $b['TOTAL_MOVIMENTADO'] - $a['TOTAL_MOVIMENTADO'];
            }
            return strcmp($a['CLIENTE'], $b['CLIENTE']);
        });

        // Ordenar resumo por total movimentado
        usort($resumoClientes, function($a, $b) {
            return $b['TOTAL_MOVIMENTADO'] - $a['TOTAL_MOVIMENTADO'];
        });

        // Aplicar filtros
        $classificacao = $filters['classificacao'] ?? '';
        if ($classificacao) {
            $resultado = array_filter($resultado, function($item) use ($classificacao) {
                switch ($classificacao) {
                    case 'produtora':
                        return $item['SALDO'] > 0;
                    case 'consumidora':
                        return $item['SALDO'] < 0;
                    case 'equilibrada':
                        return $item['SALDO'] == 0;
                    default:
                        return true;
                }
            });
        }

        $cliente = $filters['cliente'] ?? '';
        if ($cliente) {
            $resultado = array_filter($resultado, function($item) use ($cliente) {
                return $item['CLIENTE'] === $cliente;
            });
        }

        $limit = (int)($filters['limit'] ?? 0);
        if ($limit > 0) {
            $resultado = array_slice($resultado, 0, $limit);
        }

        // Calcular resumo geral
        $produtoras = count(array_filter($resultado, function($item) {
            return $item['SALDO'] > 0;
        }));
        $consumidoras = count(array_filter($resultado, function($item) {
            return $item['SALDO'] < 0;
        }));
        $equilibradas = count(array_filter($resultado, function($item) {
            return $item['SALDO'] == 0;
        }));

        // Calcular percentuais para o resultado
        $totalGeral = array_sum(array_column($resultado, 'TOTAL_MOVIMENTADO'));
        foreach ($resultado as &$item) {
            $item['PERCENTUAL'] = $totalGeral > 0 ? round(($item['TOTAL_MOVIMENTADO'] / $totalGeral) * 100, 1) : 0;
        }

        return [
            'balanco' => array_values($resultado),
            'resumo_clientes' => $resumoClientes,
            'resumo_geral' => [
                'total_clientes' => count($resumoClientes),
                'total_mesorregioes' => count($resultado),
                'produtoras' => $produtoras,
                'consumidoras' => $consumidoras,
                'equilibradas' => $equilibradas
            ]
        ];
    }
}
