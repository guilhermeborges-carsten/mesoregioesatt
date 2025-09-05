<?php
$title = 'Dashboard Inicial - Análise Logística';
$content = '
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 text-primary">
                <i class="bi bi-speedometer2"></i> Dashboard Logístico
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshStats()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
                <button class="btn btn-success" onclick="exportAllData()">
                    <i class="bi bi-download"></i> Exportar Tudo
                </button>
            </div>
        </div>
        
        <!-- Alert para dados não carregados -->
        <div id="noDataAlert" class="alert alert-info d-none">
            <i class="bi bi-info-circle"></i>
            <strong>Nenhum dado carregado.</strong> 
            Use o menu "Upload" para carregar um arquivo Excel com dados de embarques.
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="row mb-4" id="filtrosSection" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm filtros-section">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel"></i> Filtros Avançados
                </h5>
            </div>
            <div class="card-body">
                <!-- Indicador de Filtros Ativos -->
                <div id="filtrosAtivos" class="filtros-ativos d-none">
                    <h6 class="mb-2"><i class="bi bi-check-circle"></i> Filtros Ativos:</h6>
                    <div id="filtrosAtivosList"></div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="dataInicio" class="form-label">Data Início</label>
                        <input type="month" class="form-control" id="dataInicio">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dataFim" class="form-label">Data Fim</label>
                        <input type="month" class="form-control" id="dataFim">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="origemFilter" class="form-label">Origem</label>
                        <select class="form-select" id="origemFilter" multiple>
                            <!-- Preenchido via JavaScript -->
                        </select>
                        <small class="form-text text-muted">Selecione uma ou mais origens</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="destinoFilter" class="form-label">Destino</label>
                        <select class="form-select" id="destinoFilter" multiple>
                            <!-- Preenchido via JavaScript -->
                        </select>
                        <small class="form-text text-muted">Selecione um ou mais destinos</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="limitFilter" class="form-label">Limite de Rankings</label>
                        <select class="form-select" id="limitFilter">
                            <option value="5" selected>Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="15">Top 15</option>
                            <option value="20">Top 20</option>
                            <option value="50">Top 50</option>
                        </select>
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" onclick="aplicarFiltros()">
                                <i class="bi bi-check-circle"></i> Aplicar Filtros
                            </button>
                            <button class="btn btn-outline-secondary" onclick="resetarFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Resetar
                            </button>
                            <button class="btn btn-outline-info" onclick="mostrarFiltrosAtivos()">
                                <i class="bi bi-eye"></i> Ver Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4" id="statsCards">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-truck text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Total de Embarques</h5>
                <h2 class="text-primary mb-0" id="totalEmbarques">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-geo-alt text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Mesorregiões Origem</h5>
                <h2 class="text-success mb-0" id="totalOrigens">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-geo-alt-fill text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Mesorregiões Destino</h5>
                <h2 class="text-info mb-0" id="totalDestinos">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-range text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Período</h5>
                <h6 class="text-warning mb-0" id="periodo">-</h6>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Principais -->
<div class="row mb-4">
    <!-- Evolução Mensal -->
    <div class="col-xl-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Evolução de Embarques por Mês
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="setChartPeriod(\'all\')">Todos</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setChartPeriod(\'last6\')">Últimos 6M</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setChartPeriod(\'last12\')">Últimos 12M</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="evolucaoChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top 5 Origem e Destino -->
    <div class="col-xl-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ol"></i> Top Rankings <span id="rankingsTitle">(Top 5)</span>
                </h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#topOrigens" type="button">
                            <i class="bi bi-arrow-up-circle"></i> Origem
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#topDestinos" type="button">
                            <i class="bi bi-arrow-down-circle"></i> Destino
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="topOrigens">
                        <div id="topOrigensList">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-arrow-clockwise"></i> Carregando...
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="topDestinos">
                        <div id="topDestinosList">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-arrow-clockwise"></i> Carregando...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Navegação -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="bi bi-compass"></i> Análises Disponíveis
        </h4>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-grid-3x3-gap text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Heatmap O-D</h5>
                <p class="card-text text-muted">Visualize a intensidade dos fluxos entre origens e destinos</p>
                <a href="' . $this->url('/heatmap') . '" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-geo-alt text-success" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Mapa de Fluxos</h5>
                <p class="card-text text-muted">Explore os fluxos geográficos no mapa do Brasil</p>
                <a href="' . $this->url('/mapa_fluxos') . '" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-table text-info" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Tabela Detalhada</h5>
                <p class="card-text text-muted">Analise os dados em formato tabular com filtros</p>
                <a href="' . $this->url('/tabela') . '" class="btn btn-info">
                    <i class="bi bi-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-calculator text-success" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Balanço de Embarques</h5>
                <p class="card-text text-muted">Analise o saldo (origem - destino) por mesorregião</p>
                <a href="' . $this->url('/balanco') . '" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-download text-warning" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Exportar Dados</h5>
                <p class="card-text text-muted">Baixe os dados em Excel ou CSV para análise externa</p>
                <button class="btn btn-warning" onclick="exportAllData()">
                    <i class="bi bi-download"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>
';

$extra_js = '
<script>
let evolucaoChart;
let currentFilters = {};

// Inicializar página
document.addEventListener("DOMContentLoaded", function() {
    checkDataStatus();
    initializeSelect2();
});

// Verificar status dos dados
function checkDataStatus() {
    console.log("Verificando status dos dados...");
    
    fetch("/api/stats")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.log("Dados não carregados:", data.error);
                showNoDataAlert();
            } else {
                console.log("Dados carregados com sucesso:", data);
                hideNoDataAlert();
                updateStats(data);
                
                // Criar gráfico apenas se não existir
                if (!evolucaoChart) {
                    console.log("Criando gráfico de evolução...");
                    createEvolucaoChart();
                } else {
                    console.log("Gráfico já existe, atualizando dados...");
                    loadEvolucaoData();
                }
                
                loadTopRankings();
                showFiltrosSection();
            }
        })
        .catch(error => {
            console.error("Erro ao verificar dados:", error);
            showNoDataAlert();
        });
}

// Mostrar alerta de dados não carregados
function showNoDataAlert() {
    document.getElementById("noDataAlert").classList.remove("d-none");
    document.getElementById("statsCards").style.display = "none";
    document.getElementById("filtrosSection").style.display = "none";
}

// Ocultar alerta de dados não carregados
function hideNoDataAlert() {
    document.getElementById("noDataAlert").classList.add("d-none");
    document.getElementById("statsCards").style.display = "flex";
    document.getElementById("filtrosSection").style.display = "block";
}

// Atualizar estatísticas
function updateStats(data) {
    document.getElementById("totalEmbarques").textContent = data.total_embarques.toLocaleString("pt-BR");
    document.getElementById("totalOrigens").textContent = data.total_origens;
    document.getElementById("totalDestinos").textContent = data.total_destinos;
    document.getElementById("periodo").textContent = `${data.periodo_inicio} - ${data.periodo_fim}`;
}

// Criar gráfico de evolução
function createEvolucaoChart() {
    console.log("Iniciando criação do gráfico de evolução...");
    
    const canvas = document.getElementById("evolucaoChart");
    if (!canvas) {
        console.error("Canvas evolucaoChart não encontrado");
        return;
    }
    
    const ctx = canvas.getContext("2d");
    if (!ctx) {
        console.error("Não foi possível obter o contexto 2D do canvas");
        return;
    }
    
    // Destruir gráfico anterior se existir
    if (evolucaoChart) {
        console.log("Destruindo gráfico anterior...");
        evolucaoChart.destroy();
    }
    
    try {
        evolucaoChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: ["Carregando..."],
                datasets: [{
                    label: "Embarques",
                    data: [0],
                    borderColor: "rgb(75, 192, 192)",
                    backgroundColor: "rgba(75, 192, 192, 0.1)",
                    tension: 0.1,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: "Tendência (3 meses)",
                    data: [0],
                    borderColor: "rgb(255, 99, 132)",
                    backgroundColor: "rgba(255, 99, 132, 0.1)",
                    tension: 0.1,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "top",
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ": " + context.parsed.y.toLocaleString("pt-BR");
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: "rgba(0,0,0,0.1)"
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0,0,0,0.1)"
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString("pt-BR");
                            }
                        }
                    }
                },
                interaction: {
                    mode: "nearest",
                    axis: "x",
                    intersect: false
                }
            }
        });
        
        console.log("Gráfico criado com sucesso:", evolucaoChart);
        
        // Carregar dados após criar o gráfico
        setTimeout(() => {
            console.log("Chamando loadEvolucaoData após timeout...");
            loadEvolucaoData();
        }, 100);
        
    } catch (error) {
        console.error("Erro ao criar gráfico:", error);
    }
}

// Carregar dados de evolução
function loadEvolucaoData() {
    // Construir parâmetros de filtro
    const params = new URLSearchParams();
    if (currentFilters.data_inicio) params.append("data_inicio", currentFilters.data_inicio);
    if (currentFilters.data_fim) params.append("data_fim", currentFilters.data_fim);
    if (currentFilters.origens && currentFilters.origens.length > 0) {
        currentFilters.origens.forEach(origem => params.append("origens", origem));
    }
    if (currentFilters.destinos && currentFilters.destinos.length > 0) {
        currentFilters.destinos.forEach(destino => params.append("destinos", destino));
    }
    
    console.log("Carregando dados de evolução com filtros:", currentFilters);
    console.log("URL da API:", `/api/evolucao_mensal?${params.toString()}`);
    
    fetch(`/api/evolucao_mensal?${params.toString()}`)
        .then(response => {
            console.log("Resposta da API:", response.status, response.statusText);
            return response.json();
        })
        .then(data => {
            console.log("Dados recebidos da API:", data);
            
            if (data.error) {
                console.error("Erro ao carregar dados de evolução:", data.error);
                if (evolucaoChart) {
                    evolucaoChart.data.labels = ["Sem dados"];
                    evolucaoChart.data.datasets[0].data = [0];
                    evolucaoChart.data.datasets[1].data = [0];
                    evolucaoChart.update();
                }
                return;
            }
            
            if (data.labels && data.embarques) {
                if (evolucaoChart && data.labels.length > 0) {
                    evolucaoChart.data.labels = data.labels;
                    evolucaoChart.data.datasets[0].data = data.embarques;
                    evolucaoChart.data.datasets[1].data = data.tendencia || [];
                    evolucaoChart.update();
                    console.log("Gráfico atualizado com sucesso");
                }
            }
        })
        .catch(error => {
            console.error("Erro ao carregar dados de evolução:", error);
            if (evolucaoChart) {
                evolucaoChart.data.labels = ["Erro ao carregar"];
                evolucaoChart.data.datasets[0].data = [0];
                evolucaoChart.data.datasets[1].data = [0];
                evolucaoChart.update();
            }
        });
}

// Carregar rankings top 5
function loadTopRankings() {
    console.log("Carregando top rankings com filtros:", currentFilters);
    
    const params = new URLSearchParams();
    const limit = currentFilters.limit || document.getElementById("limitFilter").value || "5";
    params.append("limit", limit);
    
    if (currentFilters.data_inicio) params.append("data_inicio", currentFilters.data_inicio);
    if (currentFilters.data_fim) params.append("data_fim", currentFilters.data_fim);
    if (currentFilters.origens && currentFilters.origens.length > 0) {
        currentFilters.origens.forEach(origem => params.append("origens", origem));
    }
    if (currentFilters.destinos && currentFilters.destinos.length > 0) {
        currentFilters.destinos.forEach(destino => params.append("destinos", destino));
    }
    
    // Top origens
    fetch(`/api/top_origens?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) return;
            
            const html = data.origens.map((origem, index) => `
                <div class="d-flex justify-content-between align-items-center py-2 ${index < 3 ? "fw-bold" : ""}">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">${index + 1}</span>
                        <span class="text-truncate">${origem}</span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">${data.embarques[index].toLocaleString("pt-BR")}</div>
                        <small class="text-muted">${data.percentuais[index]}%</small>
                    </div>
                </div>
            `).join("");
            
            document.getElementById("topOrigensList").innerHTML = html;
        });
    
    // Top destinos
    fetch(`/api/top_destinos?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) return;
            
            const html = data.destinos.map((destino, index) => `
                <div class="d-flex justify-content-between align-items-center py-2 ${index < 3 ? "fw-bold" : ""}">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">${index + 1}</span>
                        <span class="text-truncate">${destino}</span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">${data.embarques[index].toLocaleString("pt-BR")}</div>
                        <small class="text-muted">${data.percentuais[index]}%</small>
                    </div>
                </div>
            `).join("");
            
            document.getElementById("topDestinosList").innerHTML = html;
        });
}

// Inicializar Select2
function initializeSelect2() {
    $("#origemFilter").select2({
        placeholder: "Selecione as origens",
        allowClear: true,
        width: "100%",
        closeOnSelect: false
    });
    
    $("#destinoFilter").select2({
        placeholder: "Selecione os destinos",
        allowClear: true,
        width: "100%",
        closeOnSelect: false
    });
    
    $("#origemFilter").on("change", function() {
        atualizarIndicadorFiltros();
    });
    
    $("#destinoFilter").on("change", function() {
        atualizarIndicadorFiltros();
    });
    
    document.getElementById("dataInicio").addEventListener("change", atualizarIndicadorFiltros);
    document.getElementById("dataFim").addEventListener("change", atualizarIndicadorFiltros);
}

// Atualizar indicador de filtros
function atualizarIndicadorFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        limit: document.getElementById("limitFilter").value
    };
    
    let temFiltros = false;
    let filtrosTexto = [];
    
    if (dataInicio || dataFim) {
        temFiltros = true;
        if (dataInicio && dataFim) {
            filtrosTexto.push(`<span class="badge bg-primary">Período: ${dataInicio} - ${dataFim}</span>`);
        } else if (dataInicio) {
            filtrosTexto.push(`<span class="badge bg-primary">A partir de: ${dataInicio}</span>`);
        } else if (dataFim) {
            filtrosTexto.push(`<span class="badge bg-primary">Até: ${dataFim}</span>`);
        }
    }
    
    if (origens && origens.length > 0) {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-success">Origem: ${origens.length} selecionada(s)</span>`);
    }
    
    if (destinos && destinos.length > 0) {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-info">Destino: ${destinos.length} selecionado(s)</span>`);
    }
    
    const filtrosAtivos = document.getElementById("filtrosAtivos");
    const filtrosAtivosList = document.getElementById("filtrosAtivosList");
    
    if (temFiltros) {
        filtrosAtivosList.innerHTML = filtrosTexto.join(" ");
        filtrosAtivos.classList.remove("d-none");
    } else {
        filtrosAtivos.classList.add("d-none");
    }
}

// Mostrar seção de filtros
function showFiltrosSection() {
    fetch("/api/mesorregioes")
        .then(response => response.json())
        .then(data => {
            if (data.error) return;
            
            const origemSelect = document.getElementById("origemFilter");
            origemSelect.innerHTML = "<option value=\"\"></option>";
            data.origens.forEach(origem => {
                const option = document.createElement("option");
                option.value = origem;
                option.textContent = origem;
                origemSelect.appendChild(option);
            });
            
            const destinoSelect = document.getElementById("destinoFilter");
            destinoSelect.innerHTML = "<option value=\"\"></option>";
            data.destinos.forEach(destino => {
                const option = document.createElement("option");
                option.value = destino;
                option.textContent = destino;
                destinoSelect.appendChild(option);
            });
            
            $("#origemFilter").trigger("change");
            $("#destinoFilter").trigger("change");
            
            document.getElementById("filtrosSection").style.display = "block";
        });
}

// Aplicar filtros
function aplicarFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    
    if (dataInicio && dataFim && dataInicio > dataFim) {
        showToast("Data de início não pode ser maior que data de fim!", "warning");
        return;
    }
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        limit: document.getElementById("limitFilter").value
    };
    
    atualizarIndicadorFiltros();
    loadEvolucaoData();
    loadTopRankings();
    showToast("Filtros aplicados com sucesso!", "success");
}

// Resetar filtros
function resetarFiltros() {
    document.getElementById("dataInicio").value = "";
    document.getElementById("dataFim").value = "";
    $("#origemFilter").val(null).trigger("change");
    $("#destinoFilter").val(null).trigger("change");
    document.getElementById("limitFilter").value = "5";
    
    currentFilters = {};
    atualizarIndicadorFiltros();
    checkDataStatus();
    showToast("Filtros resetados!", "info");
}

// Definir período do gráfico
function setChartPeriod(period) {
    document.querySelectorAll("[onclick^=\"setChartPeriod\"]").forEach(btn => {
        btn.classList.remove("active");
    });
    event.target.classList.add("active");
    
    const today = new Date();
    let startDate = "";
    
    switch(period) {
        case "last6":
            startDate = new Date(today.getFullYear(), today.getMonth() - 6, 1);
            break;
        case "last12":
            startDate = new Date(today.getFullYear(), today.getMonth() - 12, 1);
            break;
        default:
            startDate = "";
    }
    
    if (startDate) {
        currentFilters.data_inicio = startDate.toISOString().slice(0, 7);
        document.getElementById("dataInicio").value = startDate.toISOString().slice(0, 7);
    } else {
        delete currentFilters.data_inicio;
        document.getElementById("dataInicio").value = "";
    }
    
    loadEvolucaoData();
    loadTopRankings();
}

// Atualizar estatísticas
function refreshStats() {
    checkDataStatus();
    showToast("Dados atualizados!", "success");
}

// Exportar todos os dados
function exportAllData() {
    const params = new URLSearchParams(currentFilters);
    const excelUrl = `/api/exportar_excel?${params.toString()}`;
    const link = document.createElement("a");
    link.href = excelUrl;
    link.download = `embarques_completo_${new Date().toISOString().slice(0, 10)}.xlsx`;
    link.click();
    
    showToast("Exportação iniciada!", "success");
}

// Mostrar filtros ativos
function mostrarFiltrosAtivos() {
    const filtrosAtivos = document.getElementById("filtrosAtivos");
    if (filtrosAtivos.classList.contains("d-none")) {
        filtrosAtivos.classList.remove("d-none");
    } else {
        filtrosAtivos.classList.add("d-none");
    }
}
</script>
';

$this->render("base", compact("title", "content", "extra_js"));
