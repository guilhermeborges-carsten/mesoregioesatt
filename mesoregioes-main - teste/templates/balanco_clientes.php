<?php
$title = 'Balanço de Clientes - Análise Logística';
$content = '
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 text-primary">
                <i class="bi bi-people"></i> Balanço de Clientes
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshBalancoClientes()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
                <button class="btn btn-success" onclick="exportBalancoClientesData()">
                    <i class="bi bi-download"></i> Exportar
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
                        <label for="clienteFilter" class="form-label">Cliente</label>
                        <select class="form-select" id="clienteFilter" multiple>
                            <!-- Preenchido via JavaScript -->
                        </select>
                        <small class="form-text text-muted">Selecione um ou mais clientes</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="volumeMinimo" class="form-label">Volume Mínimo</label>
                        <input type="number" class="form-control" id="volumeMinimo" min="0" value="0">
                        <small class="form-text text-muted">Filtrar registros com volume mínimo</small>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
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

<!-- Estatísticas do Balanço de Clientes -->
<div class="row mb-4" id="balancoClientesStats" style="display: none;">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Total de Clientes</h5>
                <h2 class="text-primary mb-0" id="totalClientes">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-arrow-up text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Saldo Positivo</h5>
                <h2 class="text-success mb-0" id="saldoPositivo">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-arrow-down text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Saldo Negativo</h5>
                <h2 class="text-danger mb-0" id="saldoNegativo">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-graph-up text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Saldo Médio</h5>
                <h2 class="text-info mb-0" id="saldoMedio">-</h2>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Balanço de Clientes -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up"></i> Gráfico de Balanço por Cliente
                </h5>
            </div>
            <div class="card-body">
                <canvas id="balancoClientesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Balanço de Clientes -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table"></i> Tabela de Balanço de Clientes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabelaBalancoClientes">
                        <thead class="table-dark">
                            <tr>
                                <th>Cliente</th>
                                <th class="text-end">Embarques Origem</th>
                                <th class="text-end">Embarques Destino</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-end">% do Total</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-arrow-clockwise"></i> Carregando dados...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
';

$extra_js = '
<script>
let balancoClientesData = null;
let currentFilters = {};
let balancoClientesChart = null;

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
                loadBalancoClientesData();
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
    document.getElementById("balancoClientesStats").style.display = "none";
}

// Ocultar alerta de dados não carregados
function hideNoDataAlert() {
    document.getElementById("noDataAlert").classList.add("d-none");
    document.getElementById("balancoClientesStats").style.display = "flex";
}

// Carregar dados do balanço de clientes
function loadBalancoClientesData() {
    console.log("Carregando dados do balanço de clientes com filtros:", currentFilters);
    
    const params = new URLSearchParams();
    if (currentFilters.data_inicio) params.append("data_inicio", currentFilters.data_inicio);
    if (currentFilters.data_fim) params.append("data_fim", currentFilters.data_fim);
    if (currentFilters.origens && currentFilters.origens.length > 0) {
        currentFilters.origens.forEach(origem => params.append("origens", origem));
    }
    if (currentFilters.destinos && currentFilters.destinos.length > 0) {
        currentFilters.destinos.forEach(destino => params.append("destinos", destino));
    }
    if (currentFilters.clientes && currentFilters.clientes.length > 0) {
        currentFilters.clientes.forEach(cliente => params.append("clientes", cliente));
    }
    if (currentFilters.volume_minimo) params.append("volume_minimo", currentFilters.volume_minimo);
    
    fetch(`/api/balanco_clientes?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            console.log("Dados do balanço de clientes recebidos:", data);
            
            if (data.error) {
                console.error("Erro ao carregar dados do balanço de clientes:", data.error);
                showBalancoClientesError();
                return;
            }
            
            balancoClientesData = data;
            renderBalancoClientes(data);
            updateBalancoClientesStats(data);
            createBalancoClientesChart(data);
        })
        .catch(error => {
            console.error("Erro ao carregar dados do balanço de clientes:", error);
            showBalancoClientesError();
        });
}

// Renderizar balanço de clientes
function renderBalancoClientes(data) {
    if (!data || !data.balanco) {
        console.error("Dados do balanço de clientes inválidos");
        return;
    }
    
    const tbody = document.querySelector("#tabelaBalancoClientes tbody");
    if (!tbody) {
        console.error("Tbody da tabela não encontrado");
        return;
    }
    
    if (data.balanco.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-inbox"></i> Nenhum dado encontrado
                </td>
            </tr>
        `;
        return;
    }
    
    const html = data.balanco.map(item => {
        const saldo = item.embarques_origem - item.embarques_destino;
        const percentual = item.percentual || 0;
        const status = saldo > 0 ? "Positivo" : saldo < 0 ? "Negativo" : "Neutro";
        const statusClass = saldo > 0 ? "text-success" : saldo < 0 ? "text-danger" : "text-muted";
        const statusIcon = saldo > 0 ? "bi-arrow-up" : saldo < 0 ? "bi-arrow-down" : "bi-dash";
        
        return `
            <tr>
                <td>${item.cliente}</td>
                <td class="text-end">${item.embarques_origem.toLocaleString("pt-BR")}</td>
                <td class="text-end">${item.embarques_destino.toLocaleString("pt-BR")}</td>
                <td class="text-end fw-bold ${statusClass}">${saldo.toLocaleString("pt-BR")}</td>
                <td class="text-end">${percentual.toFixed(1)}%</td>
                <td class="text-center">
                    <span class="badge bg-${saldo > 0 ? "success" : saldo < 0 ? "danger" : "secondary"}">
                        <i class="bi ${statusIcon}"></i> ${status}
                    </span>
                </td>
            </tr>
        `;
    }).join("");
    
    tbody.innerHTML = html;
}

// Atualizar estatísticas do balanço de clientes
function updateBalancoClientesStats(data) {
    if (!data || !data.balanco) return;
    
    const balanco = data.balanco;
    const totalClientes = balanco.length;
    const saldoPositivo = balanco.filter(item => (item.embarques_origem - item.embarques_destino) > 0).length;
    const saldoNegativo = balanco.filter(item => (item.embarques_origem - item.embarques_destino) < 0).length;
    const saldoMedio = balanco.length > 0 ? 
        balanco.reduce((sum, item) => sum + (item.embarques_origem - item.embarques_destino), 0) / balanco.length : 0;
    
    document.getElementById("totalClientes").textContent = totalClientes.toLocaleString("pt-BR");
    document.getElementById("saldoPositivo").textContent = saldoPositivo.toLocaleString("pt-BR");
    document.getElementById("saldoNegativo").textContent = saldoNegativo.toLocaleString("pt-BR");
    document.getElementById("saldoMedio").textContent = Math.round(saldoMedio).toLocaleString("pt-BR");
}

// Criar gráfico de balanço de clientes
function createBalancoClientesChart(data) {
    if (!data || !data.balanco) return;
    
    const canvas = document.getElementById("balancoClientesChart");
    if (!canvas) return;
    
    const ctx = canvas.getContext("2d");
    if (!ctx) return;
    
    // Destruir gráfico anterior se existir
    if (balancoClientesChart) {
        balancoClientesChart.destroy();
    }
    
    const balanco = data.balanco;
    const labels = balanco.map(item => item.cliente);
    const saldos = balanco.map(item => item.embarques_origem - item.embarques_destino);
    
    // Calcular cores baseadas no saldo
    const cores = saldos.map(saldo => {
        if (saldo > 0) return "#28a745"; // Verde para positivo
        if (saldo < 0) return "#dc3545"; // Vermelho para negativo
        return "#6c757d"; // Cinza para neutro
    });
    
    try {
        balancoClientesChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Saldo (Origem - Destino)",
                    data: saldos,
                    backgroundColor: cores,
                    borderColor: cores,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Saldo: ${context.parsed.y.toLocaleString("pt-BR")}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: "rgba(0,0,0,0.1)"
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
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
        
        console.log("Gráfico de balanço de clientes criado com sucesso");
        
    } catch (error) {
        console.error("Erro ao criar gráfico de balanço de clientes:", error);
    }
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
    
    $("#clienteFilter").select2({
        placeholder: "Selecione os clientes",
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
    
    $("#clienteFilter").on("change", function() {
        atualizarIndicadorFiltros();
    });
    
    document.getElementById("dataInicio").addEventListener("change", atualizarIndicadorFiltros);
    document.getElementById("dataFim").addEventListener("change", atualizarIndicadorFiltros);
    document.getElementById("volumeMinimo").addEventListener("change", atualizarIndicadorFiltros);
}

// Atualizar indicador de filtros
function atualizarIndicadorFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    const clientes = $("#clienteFilter").val();
    const volumeMinimo = document.getElementById("volumeMinimo").value;
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        clientes: clientes,
        volume_minimo: volumeMinimo
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
    
    if (clientes && clientes.length > 0) {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-warning">Cliente: ${clientes.length} selecionado(s)</span>`);
    }
    
    if (volumeMinimo && parseInt(volumeMinimo) > 0) {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-danger">Volume Mín: ${volumeMinimo}</span>`);
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
    
    // Carregar clientes
    fetch("/api/clientes")
        .then(response => response.json())
        .then(data => {
            if (data.error) return;
            
            const clienteSelect = document.getElementById("clienteFilter");
            clienteSelect.innerHTML = "<option value=\"\"></option>";
            data.clientes.forEach(cliente => {
                const option = document.createElement("option");
                option.value = cliente;
                option.textContent = cliente;
                clienteSelect.appendChild(option);
            });
            
            $("#clienteFilter").trigger("change");
        });
}

// Aplicar filtros
function aplicarFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    const clientes = $("#clienteFilter").val();
    const volumeMinimo = document.getElementById("volumeMinimo").value;
    
    if (dataInicio && dataFim && dataInicio > dataFim) {
        showToast("Data de início não pode ser maior que data de fim!", "warning");
        return;
    }
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        clientes: clientes,
        volume_minimo: volumeMinimo
    };
    
    atualizarIndicadorFiltros();
    loadBalancoClientesData();
    showToast("Filtros aplicados com sucesso!", "success");
}

// Resetar filtros
function resetarFiltros() {
    document.getElementById("dataInicio").value = "";
    document.getElementById("dataFim").value = "";
    $("#origemFilter").val(null).trigger("change");
    $("#destinoFilter").val(null).trigger("change");
    $("#clienteFilter").val(null).trigger("change");
    document.getElementById("volumeMinimo").value = "0";
    
    currentFilters = {};
    atualizarIndicadorFiltros();
    checkDataStatus();
    showToast("Filtros resetados!", "info");
}

// Atualizar balanço de clientes
function refreshBalancoClientes() {
    loadBalancoClientesData();
    showToast("Balanço de clientes atualizado!", "success");
}

// Exportar dados do balanço de clientes
function exportBalancoClientesData() {
    const params = new URLSearchParams(currentFilters);
    const excelUrl = `/api/exportar_balanco_clientes_excel?${params.toString()}`;
    const link = document.createElement("a");
    link.href = excelUrl;
    link.download = `balanco_clientes_${new Date().toISOString().slice(0, 10)}.xlsx`;
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

// Mostrar erro do balanço de clientes
function showBalancoClientesError() {
    const tbody = document.querySelector("#tabelaBalancoClientes tbody");
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-exclamation-triangle"></i> Erro ao carregar dados
            </td>
        </tr>
    `;
}
</script>

<style>
.filtros-ativos {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.filtros-ativos .badge {
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
}

#tabelaBalancoClientes {
    font-size: 14px;
}

#tabelaBalancoClientes th {
    font-weight: 600;
    white-space: nowrap;
}

#tabelaBalancoClientes td {
    vertical-align: middle;
}
</style>
';

$this->render("base", compact("title", "content", "extra_js"));
