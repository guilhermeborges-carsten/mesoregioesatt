<?php
$title = 'Tabela Detalhada - Análise Logística';
$content = '
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 text-primary">
                <i class="bi bi-table"></i> Tabela Detalhada
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshTabela()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
                <button class="btn btn-success" onclick="exportTabelaData()">
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

<!-- Controles da Tabela -->
<div class="row mb-4" id="tabelaControls" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">
                            <i class="bi bi-gear"></i> Controles da Tabela
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 justify-content-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarResumo" checked>
                                <label class="form-check-label" for="mostrarResumo">
                                    Mostrar Resumo
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarDetalhes">
                                <label class="form-check-label" for="mostrarDetalhes">
                                    Mostrar Detalhes
                                </label>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleTabelaView()">
                                <i class="bi bi-arrows-fullscreen"></i> Alternar Visualização
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas da Tabela -->
<div class="row mb-4" id="tabelaStats" style="display: none;">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-list-ul text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Total de Registros</h5>
                <h2 class="text-primary mb-0" id="totalRegistros">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-truck text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Total de Embarques</h5>
                <h2 class="text-success mb-0" id="totalEmbarques">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Clientes Únicos</h5>
                <h2 class="text-info mb-0" id="totalClientes">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-graph-up text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Embarques Médios</h5>
                <h2 class="text-warning mb-0" id="embarquesMedios">-</h2>
            </div>
        </div>
    </div>
</div>

<!-- Tabela Principal -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table"></i> Dados Detalhados
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabelaDados">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Cliente</th>
                                <th>Embarques</th>
                                <th>Valor</th>
                                <th>Peso</th>
                                <th>Volume</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-arrow-clockwise"></i> Carregando dados...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <nav aria-label="Paginação da tabela" id="paginacao" style="display: none;">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" onclick="changePage(1)">Primeira</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" onclick="changePage(1)">Anterior</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#" onclick="changePage(1)">1</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" onclick="changePage(1)">Próxima</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" onclick="changePage(1)">Última</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
';

$extra_js = '
<script>
let tabelaData = null;
let currentFilters = {};
let currentPage = 1;
let totalPages = 1;
let itemsPerPage = 50;

// Inicializar página
document.addEventListener("DOMContentLoaded", function() {
    checkDataStatus();
    initializeSelect2();
    initializeTabelaControls();
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
                loadTabelaData();
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
    document.getElementById("tabelaControls").style.display = "none";
    document.getElementById("tabelaStats").style.display = "none";
}

// Ocultar alerta de dados não carregados
function hideNoDataAlert() {
    document.getElementById("noDataAlert").classList.add("d-none");
    document.getElementById("tabelaControls").style.display = "block";
    document.getElementById("tabelaStats").style.display = "flex";
}

// Carregar dados da tabela
function loadTabelaData() {
    console.log("Carregando dados da tabela com filtros:", currentFilters);
    
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
    params.append("page", currentPage);
    params.append("limit", itemsPerPage);
    
    fetch(`/api/tabela_dados?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            console.log("Dados da tabela recebidos:", data);
            
            if (data.error) {
                console.error("Erro ao carregar dados da tabela:", data.error);
                showTabelaError();
                return;
            }
            
            tabelaData = data;
            renderTabela(data);
            updateTabelaStats(data);
            updatePaginacao(data);
        })
        .catch(error => {
            console.error("Erro ao carregar dados da tabela:", error);
            showTabelaError();
        });
}

// Renderizar tabela
function renderTabela(data) {
    if (!data || !data.dados) {
        console.error("Dados da tabela inválidos");
        return;
    }
    
    const tbody = document.querySelector("#tabelaDados tbody");
    if (!tbody) {
        console.error("Tbody da tabela não encontrado");
        return;
    }
    
    if (data.dados.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="bi bi-inbox"></i> Nenhum dado encontrado
                </td>
            </tr>
        `;
        return;
    }
    
    const html = data.dados.map(registro => `
        <tr>
            <td>${formatarData(registro.data)}</td>
            <td>${registro.origem}</td>
            <td>${registro.destino}</td>
            <td>${registro.cliente}</td>
            <td class="text-end">${registro.embarques.toLocaleString("pt-BR")}</td>
            <td class="text-end">${formatarMoeda(registro.valor)}</td>
            <td class="text-end">${formatarPeso(registro.peso)}</td>
            <td class="text-end">${formatarVolume(registro.volume)}</td>
        </tr>
    `).join("");
    
    tbody.innerHTML = html;
}

// Atualizar estatísticas da tabela
function updateTabelaStats(data) {
    if (!data) return;
    
    document.getElementById("totalRegistros").textContent = data.total_registros.toLocaleString("pt-BR");
    document.getElementById("totalEmbarques").textContent = data.total_embarques.toLocaleString("pt-BR");
    document.getElementById("totalClientes").textContent = data.total_clientes.toLocaleString("pt-BR");
    document.getElementById("embarquesMedios").textContent = Math.round(data.embarques_medios).toLocaleString("pt-BR");
}

// Atualizar paginação
function updatePaginacao(data) {
    if (!data) return;
    
    totalPages = Math.ceil(data.total_registros / itemsPerPage);
    const paginacao = document.getElementById("paginacao");
    
    if (totalPages <= 1) {
        paginacao.style.display = "none";
        return;
    }
    
    paginacao.style.display = "block";
    
    let html = `
        <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(1)">Primeira</a>
        </li>
        <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Anterior</a>
        </li>
    `;
    
    // Calcular range de páginas para mostrar
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? "active" : ""}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
    
    html += `
        <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Próxima</a>
        </li>
        <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(${totalPages})">Última</a>
        </li>
    `;
    
    paginacao.querySelector("ul").innerHTML = html;
}

// Mudar página
function changePage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    
    currentPage = page;
    loadTabelaData();
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

// Inicializar controles da tabela
function initializeTabelaControls() {
    document.getElementById("mostrarResumo").addEventListener("change", function() {
        // Implementar lógica para mostrar/ocultar resumo
        console.log("Mostrar resumo:", this.checked);
    });
    
    document.getElementById("mostrarDetalhes").addEventListener("change", function() {
        // Implementar lógica para mostrar/ocultar detalhes
        console.log("Mostrar detalhes:", this.checked);
    });
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
    currentPage = 1; // Resetar para primeira página
    loadTabelaData();
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
    currentPage = 1;
    checkDataStatus();
    showToast("Filtros resetados!", "info");
}

// Alternar visualização da tabela
function toggleTabelaView() {
    const tabela = document.getElementById("tabelaDados");
    if (tabela.classList.contains("table-sm")) {
        tabela.classList.remove("table-sm");
        showToast("Visualização normal ativada", "info");
    } else {
        tabela.classList.add("table-sm");
        showToast("Visualização compacta ativada", "info");
    }
}

// Atualizar tabela
function refreshTabela() {
    loadTabelaData();
    showToast("Tabela atualizada!", "success");
}

// Exportar dados da tabela
function exportTabelaData() {
    const params = new URLSearchParams(currentFilters);
    const excelUrl = `/api/exportar_excel?${params.toString()}`;
    const link = document.createElement("a");
    link.href = excelUrl;
    link.download = `tabela_embarques_${new Date().toISOString().slice(0, 10)}.xlsx`;
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

// Mostrar erro da tabela
function showTabelaError() {
    const tbody = document.querySelector("#tabelaDados tbody");
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center text-muted py-4">
                <i class="bi bi-exclamation-triangle"></i> Erro ao carregar dados
            </td>
        </tr>
    `;
}

// Funções de formatação
function formatarData(data) {
    if (!data) return "-";
    const date = new Date(data);
    return date.toLocaleDateString("pt-BR");
}

function formatarMoeda(valor) {
    if (!valor) return "-";
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL"
    }).format(valor);
}

function formatarPeso(peso) {
    if (!peso) return "-";
    return new Intl.NumberFormat("pt-BR", {
        style: "unit",
        unit: "kilogram"
    }).format(peso);
}

function formatarVolume(volume) {
    if (!volume) return "-";
    return new Intl.NumberFormat("pt-BR", {
        style: "unit",
        unit: "cubic-meter"
    }).format(volume);
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

#tabelaDados {
    font-size: 14px;
}

#tabelaDados th {
    font-weight: 600;
    white-space: nowrap;
}

#tabelaDados td {
    vertical-align: middle;
}

.pagination {
    margin-top: 1rem;
}
</style>
';

$this->render("base", compact("title", "content", "extra_js"));
