<?php
$title = 'Heatmap O-D - Análise Logística';
$content = '
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 text-primary">
                <i class="bi bi-grid-3x3-gap"></i> Heatmap Origem-Destino
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshHeatmap()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
                <button class="btn btn-success" onclick="exportHeatmapData()">
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
                        <label for="escalaCor" class="form-label">Escala de Cores</label>
                        <select class="form-select" id="escalaCor">
                            <option value="Blues">Azul (Blues)</option>
                            <option value="Reds">Vermelho (Reds)</option>
                            <option value="Greens">Verde (Greens)</option>
                            <option value="Oranges">Laranja (Oranges)</option>
                            <option value="Purples">Roxo (Purples)</option>
                            <option value="YlOrRd">Amarelo-Laranja-Vermelho</option>
                            <option value="YlGnBu">Amarelo-Verde-Azul</option>
                            <option value="RdYlBu">Vermelho-Amarelo-Azul</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="volumeMinimo" class="form-label">Volume Mínimo</label>
                        <input type="number" class="form-control" id="volumeMinimo" min="0" value="0">
                        <small class="form-text text-muted">Filtrar fluxos com volume mínimo</small>
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

<!-- Controles do Heatmap -->
<div class="row mb-4" id="heatmapControls" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">
                            <i class="bi bi-gear"></i> Controles do Heatmap
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 justify-content-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarValores" checked>
                                <label class="form-check-label" for="mostrarValores">
                                    Mostrar Valores
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarPercentuais">
                                <label class="form-check-label" for="mostrarPercentuais">
                                    Mostrar %
                                </label>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleHeatmapView()">
                                <i class="bi bi-arrows-fullscreen"></i> Alternar Visualização
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas do Heatmap -->
<div class="row mb-4" id="heatmapStats" style="display: none;">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-grid-3x3-gap text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Total de Fluxos</h5>
                <h2 class="text-primary mb-0" id="totalFluxos">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-arrow-up-right text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Fluxo Máximo</h5>
                <h2 class="text-success mb-0" id="fluxoMaximo">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-graph-up text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Fluxo Médio</h5>
                <h2 class="text-info mb-0" id="fluxoMedio">-</h2>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-percent text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Concentração</h5>
                <h2 class="text-warning mb-0" id="concentracao">-</h2>
            </div>
        </div>
    </div>
</div>

<!-- Heatmap Principal -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-grid-3x3-gap"></i> Matriz Origem-Destino
                </h5>
            </div>
            <div class="card-body">
                <div id="heatmapContainer" style="min-height: 500px;">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-arrow-clockwise"></i> Carregando heatmap...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Fluxos -->
<div class="row mb-4" id="topFluxosSection" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ol"></i> Top 10 Fluxos
                </h5>
            </div>
            <div class="card-body">
                <div id="topFluxosList">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-arrow-clockwise"></i> Carregando...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';

$extra_js = '
<script>
let heatmapData = null;
let currentFilters = {};

// Inicializar página
document.addEventListener("DOMContentLoaded", function() {
    checkDataStatus();
    initializeSelect2();
    initializeHeatmapControls();
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
                loadHeatmapData();
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
    document.getElementById("heatmapControls").style.display = "none";
    document.getElementById("heatmapStats").style.display = "none";
    document.getElementById("topFluxosSection").style.display = "none";
}

// Ocultar alerta de dados não carregados
function hideNoDataAlert() {
    document.getElementById("noDataAlert").classList.add("d-none");
    document.getElementById("heatmapControls").style.display = "block";
    document.getElementById("heatmapStats").style.display = "flex";
    document.getElementById("topFluxosSection").style.display = "block";
}

// Carregar dados do heatmap
function loadHeatmapData() {
    console.log("Carregando dados do heatmap com filtros:", currentFilters);
    
    const params = new URLSearchParams();
    if (currentFilters.data_inicio) params.append("data_inicio", currentFilters.data_inicio);
    if (currentFilters.data_fim) params.append("data_fim", currentFilters.data_fim);
    if (currentFilters.origens && currentFilters.origens.length > 0) {
        currentFilters.origens.forEach(origem => params.append("origens", origem));
    }
    if (currentFilters.destinos && currentFilters.destinos.length > 0) {
        currentFilters.destinos.forEach(destino => params.append("destinos", destino));
    }
    if (currentFilters.volume_minimo) params.append("volume_minimo", currentFilters.volume_minimo);
    
    fetch(`/api/heatmap_data?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            console.log("Dados do heatmap recebidos:", data);
            
            if (data.error) {
                console.error("Erro ao carregar dados do heatmap:", data.error);
                showHeatmapError();
                return;
            }
            
            heatmapData = data;
            renderHeatmap();
            updateHeatmapStats(data);
            loadTopFluxos(data);
        })
        .catch(error => {
            console.error("Erro ao carregar dados do heatmap:", error);
            showHeatmapError();
        });
}

// Renderizar heatmap
function renderHeatmap() {
    if (!heatmapData || !heatmapData.origens || !heatmapData.destinos) {
        console.error("Dados do heatmap inválidos");
        return;
    }
    
    const container = document.getElementById("heatmapContainer");
    if (!container) {
        console.error("Container do heatmap não encontrado");
        return;
    }
    
    const origens = heatmapData.origens;
    const destinos = heatmapData.destinos;
    const matriz = heatmapData.matriz;
    const escalaCor = currentFilters.escala_cor || "Blues";
    const mostrarValores = document.getElementById("mostrarValores").checked;
    const mostrarPercentuais = document.getElementById("mostrarPercentuais").checked;
    
    // Calcular valores máximos e mínimos
    const valores = matriz.flat().filter(v => v > 0);
    const maxValor = Math.max(...valores);
    const minValor = Math.min(...valores);
    
    // Criar tabela HTML
    let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-sm heatmap-table">
                <thead>
                    <tr>
                        <th class="text-center bg-light">Origem → Destino</th>
    `;
    
    // Cabeçalho com destinos
    destinos.forEach(destino => {
        html += `<th class="text-center bg-light">${destino}</th>`;
    });
    
    html += `
                    </tr>
                </thead>
                <tbody>
    `;
    
    // Linhas com origens
    origens.forEach((origem, i) => {
        html += `<tr><td class="fw-bold bg-light">${origem}</td>`;
        
        destinos.forEach((destino, j) => {
            const valor = matriz[i][j] || 0;
            const intensidade = valor > 0 ? (valor - minValor) / (maxValor - minValor) : 0;
            const cor = getColor(intensidade, escalaCor);
            const percentual = maxValor > 0 ? ((valor / maxValor) * 100).toFixed(1) : 0;
            
            let texto = "";
            if (mostrarValores && mostrarPercentuais) {
                texto = `${valor.toLocaleString("pt-BR")}<br><small>${percentual}%</small>`;
            } else if (mostrarValores) {
                texto = valor.toLocaleString("pt-BR");
            } else if (mostrarPercentuais) {
                texto = `${percentual}%`;
            }
            
            html += `
                <td class="text-center" style="background-color: ${cor}; color: ${getTextColor(cor)};">
                    ${texto}
                </td>
            `;
        });
        
        html += `</tr>`;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// Obter cor baseada na intensidade
function getColor(intensidade, escala) {
    const cores = {
        "Blues": ["#f7fbff", "#deebf7", "#c6dbef", "#9ecae1", "#6baed6", "#4292c6", "#2171b5", "#08519c", "#08306b"],
        "Reds": ["#fff5f0", "#fee0d2", "#fcbba1", "#fc9272", "#fb6a4a", "#ef3b2c", "#cb181d", "#a50f15", "#67000d"],
        "Greens": ["#f7fcf5", "#e5f5e0", "#c7e9c0", "#a1d99b", "#74c476", "#41ab5d", "#238b45", "#006d2c", "#00441b"],
        "Oranges": ["#fff5eb", "#fee6ce", "#fdd0a2", "#fdae6b", "#fd8d3c", "#f16913", "#d94801", "#a63603", "#7f2704"],
        "Purples": ["#fcfbfd", "#f2f0f7", "#e1e1ef", "#cbc9e2", "#9e9ac8", "#807dba", "#6a51a3", "#54278f", "#3f007d"],
        "YlOrRd": ["#ffffcc", "#ffeda0", "#fed976", "#feb24c", "#fd8d3c", "#fc4e2a", "#e31a1c", "#bd0026", "#800026"],
        "YlGnBu": ["#ffffd9", "#edf8b1", "#c7e9b4", "#7fcdbb", "#41b6c4", "#1d91c0", "#225ea8", "#253494", "#081d58"],
        "RdYlBu": ["#d73027", "#f46d43", "#fdae61", "#fee090", "#ffffbf", "#e0f3f8", "#abd9e9", "#74add1", "#4575b4"]
    };
    
    const escalaCores = cores[escala] || cores["Blues"];
    const index = Math.floor(intensidade * (escalaCores.length - 1));
    return escalaCores[index];
}

// Obter cor do texto baseada na cor de fundo
function getTextColor(backgroundColor) {
    // Converter hex para RGB
    const hex = backgroundColor.replace("#", "");
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    
    // Calcular luminância
    const luminancia = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
    return luminancia > 0.5 ? "#000000" : "#ffffff";
}

// Atualizar estatísticas do heatmap
function updateHeatmapStats(data) {
    if (!data || !data.matriz) return;
    
    const valores = data.matriz.flat().filter(v => v > 0);
    const totalFluxos = valores.length;
    const fluxoMaximo = Math.max(...valores);
    const fluxoMedio = valores.length > 0 ? valores.reduce((a, b) => a + b, 0) / valores.length : 0;
    const concentracao = fluxoMaximo > 0 ? ((fluxoMaximo / (valores.reduce((a, b) => a + b, 0) / valores.length)) * 100).toFixed(1) : 0;
    
    document.getElementById("totalFluxos").textContent = totalFluxos.toLocaleString("pt-BR");
    document.getElementById("fluxoMaximo").textContent = fluxoMaximo.toLocaleString("pt-BR");
    document.getElementById("fluxoMedio").textContent = Math.round(fluxoMedio).toLocaleString("pt-BR");
    document.getElementById("concentracao").textContent = concentracao + "%";
}

// Carregar top fluxos
function loadTopFluxos(data) {
    if (!data || !data.top_fluxos) return;
    
    const html = data.top_fluxos.map((fluxo, index) => `
        <div class="d-flex justify-content-between align-items-center py-2 ${index < 3 ? "fw-bold" : ""}">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary me-2">${index + 1}</span>
                <span class="text-truncate">${fluxo.origem} → ${fluxo.destino}</span>
            </div>
            <div class="text-end">
                <div class="fw-bold">${fluxo.embarques.toLocaleString("pt-BR")}</div>
                <small class="text-muted">${fluxo.percentual}%</small>
            </div>
        </div>
    `).join("");
    
    document.getElementById("topFluxosList").innerHTML = html;
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
    document.getElementById("escalaCor").addEventListener("change", atualizarIndicadorFiltros);
    document.getElementById("volumeMinimo").addEventListener("change", atualizarIndicadorFiltros);
}

// Inicializar controles do heatmap
function initializeHeatmapControls() {
    document.getElementById("mostrarValores").addEventListener("change", function() {
        if (heatmapData) renderHeatmap();
    });
    
    document.getElementById("mostrarPercentuais").addEventListener("change", function() {
        if (heatmapData) renderHeatmap();
    });
}

// Atualizar indicador de filtros
function atualizarIndicadorFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    const escalaCor = document.getElementById("escalaCor").value;
    const volumeMinimo = document.getElementById("volumeMinimo").value;
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        escala_cor: escalaCor,
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
    
    if (escalaCor && escalaCor !== "Blues") {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-warning">Escala: ${escalaCor}</span>`);
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
}

// Aplicar filtros
function aplicarFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    const escalaCor = document.getElementById("escalaCor").value;
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
        escala_cor: escalaCor,
        volume_minimo: volumeMinimo
    };
    
    atualizarIndicadorFiltros();
    loadHeatmapData();
    showToast("Filtros aplicados com sucesso!", "success");
}

// Resetar filtros
function resetarFiltros() {
    document.getElementById("dataInicio").value = "";
    document.getElementById("dataFim").value = "";
    $("#origemFilter").val(null).trigger("change");
    $("#destinoFilter").val(null).trigger("change");
    document.getElementById("escalaCor").value = "Blues";
    document.getElementById("volumeMinimo").value = "0";
    
    currentFilters = {};
    atualizarIndicadorFiltros();
    checkDataStatus();
    showToast("Filtros resetados!", "info");
}

// Alternar visualização do heatmap
function toggleHeatmapView() {
    const container = document.getElementById("heatmapContainer");
    if (container.style.fontSize === "12px") {
        container.style.fontSize = "14px";
        showToast("Visualização normal ativada", "info");
    } else {
        container.style.fontSize = "12px";
        showToast("Visualização compacta ativada", "info");
    }
}

// Atualizar heatmap
function refreshHeatmap() {
    loadHeatmapData();
    showToast("Heatmap atualizado!", "success");
}

// Exportar dados do heatmap
function exportHeatmapData() {
    const params = new URLSearchParams(currentFilters);
    const excelUrl = `/api/exportar_excel?${params.toString()}`;
    const link = document.createElement("a");
    link.href = excelUrl;
    link.download = `heatmap_embarques_${new Date().toISOString().slice(0, 10)}.xlsx`;
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

// Mostrar erro do heatmap
function showHeatmapError() {
    const container = document.getElementById("heatmapContainer");
    container.innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="mt-2">Erro ao carregar dados do heatmap</div>
        </div>
    `;
}
</script>

<style>
.heatmap-table {
    font-size: 12px;
    margin: 0;
}

.heatmap-table th,
.heatmap-table td {
    padding: 8px 4px;
    border: 1px solid #dee2e6;
    text-align: center;
    vertical-align: middle;
}

.heatmap-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 11px;
}

.heatmap-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

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
</style>
';

$this->render("base", compact("title", "content", "extra_js"));
