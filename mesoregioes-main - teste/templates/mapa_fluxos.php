<?php
$title = 'Mapa de Fluxos - Análise Logística';
$content = '
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 text-primary">
                <i class="bi bi-geo-alt"></i> Mapa de Fluxos
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshMapa()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
                <button class="btn btn-success" onclick="exportMapaData()">
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
                        <label for="topN" class="form-label">Top N Fluxos</label>
                        <select class="form-select" id="topN">
                            <option value="10" selected>Top 10</option>
                            <option value="20">Top 20</option>
                            <option value="50">Top 50</option>
                            <option value="100">Top 100</option>
                            <option value="0">Todos</option>
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

<!-- Controles do Mapa -->
<div class="row mb-4" id="mapaControls" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">
                            <i class="bi bi-gear"></i> Controles do Mapa
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 justify-content-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarLabels" checked>
                                <label class="form-check-label" for="mostrarLabels">
                                    Mostrar Labels
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarFluxos" checked>
                                <label class="form-check-label" for="mostrarFluxos">
                                    Mostrar Fluxos
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarOrigens" checked>
                                <label class="form-check-label" for="mostrarOrigens">
                                    Mostrar Origens
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrarDestinos" checked>
                                <label class="form-check-label" for="mostrarDestinos">
                                    Mostrar Destinos
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="agruparMarcadores">
                                <label class="form-check-label" for="agruparMarcadores">
                                    Agrupar Marcadores
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas do Mapa -->
<div class="row mb-4" id="mapaStats" style="display: none;">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-arrow-right text-primary" style="font-size: 1.5rem;"></i>
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
                    <i class="bi bi-graph-up text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="card-title text-muted mb-1">Fluxo Médio</h5>
                <h2 class="text-warning mb-0" id="fluxoMedio">-</h2>
            </div>
        </div>
    </div>
</div>

<!-- Mapa Principal -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-geo-alt"></i> Mapa de Fluxos Logísticos
                </h5>
            </div>
            <div class="card-body p-0">
                <div id="mapaContainer" style="height: 600px; width: 100%;">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-arrow-clockwise"></i> Carregando mapa...
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
                    <i class="bi bi-list-ol"></i> Principais Fluxos
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
let mapa = null;
let fluxosLayer = null;
let origensLayer = null;
let destinosLayer = null;
let clusterGroup = null;
let currentFilters = {};

// Inicializar página
document.addEventListener("DOMContentLoaded", function() {
    checkDataStatus();
    initializeSelect2();
    initializeMapaControls();
    initializeMapa();
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
                loadMapaData();
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
    document.getElementById("mapaControls").style.display = "none";
    document.getElementById("mapaStats").style.display = "none";
    document.getElementById("topFluxosSection").style.display = "none";
}

// Ocultar alerta de dados não carregados
function hideNoDataAlert() {
    document.getElementById("noDataAlert").classList.add("d-none");
    document.getElementById("mapaControls").style.display = "block";
    document.getElementById("mapaStats").style.display = "flex";
    document.getElementById("topFluxosSection").style.display = "block";
}

// Inicializar mapa
function initializeMapa() {
    console.log("Inicializando mapa...");
    
    // Destruir mapa anterior se existir
    if (mapa) {
        mapa.remove();
    }
    
    // Criar novo mapa
    mapa = L.map("mapaContainer").setView([-14.235, -51.9253], 4);
    
    // Adicionar camada de tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
        maxZoom: 18
    }).addTo(mapa);
    
    // Inicializar grupos de camadas
    fluxosLayer = L.layerGroup().addTo(mapa);
    origensLayer = L.layerGroup().addTo(mapa);
    destinosLayer = L.layerGroup().addTo(mapa);
    
    // Inicializar cluster group
    clusterGroup = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50
    });
    
    console.log("Mapa inicializado com sucesso");
}

// Carregar dados do mapa
function loadMapaData() {
    console.log("Carregando dados do mapa com filtros:", currentFilters);
    
    const params = new URLSearchParams();
    if (currentFilters.data_inicio) params.append("data_inicio", currentFilters.data_inicio);
    if (currentFilters.data_fim) params.append("data_fim", currentFilters.data_fim);
    if (currentFilters.origens && currentFilters.origens.length > 0) {
        currentFilters.origens.forEach(origem => params.append("origens", origem));
    }
    if (currentFilters.destinos && currentFilters.destinos.length > 0) {
        currentFilters.destinos.forEach(destino => params.append("destinos", destino));
    }
    if (currentFilters.top_n) params.append("top_n", currentFilters.top_n);
    if (currentFilters.volume_minimo) params.append("volume_minimo", currentFilters.volume_minimo);
    
    fetch(`/api/fluxos_mapa?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            console.log("Dados do mapa recebidos:", data);
            
            if (data.error) {
                console.error("Erro ao carregar dados do mapa:", data.error);
                showMapaError();
                return;
            }
            
            renderMapa(data);
            updateMapaStats(data);
            loadTopFluxos(data);
        })
        .catch(error => {
            console.error("Erro ao carregar dados do mapa:", error);
            showMapaError();
        });
}

// Renderizar mapa
function renderMapa(data) {
    if (!data || !data.fluxos) {
        console.error("Dados do mapa inválidos");
        return;
    }
    
    // Limpar camadas existentes
    fluxosLayer.clearLayers();
    origensLayer.clearLayers();
    destinosLayer.clearLayers();
    clusterGroup.clearLayers();
    
    const fluxos = data.fluxos;
    const coordenadas = data.coordenadas || {};
    
    // Calcular valores máximos e mínimos para normalização
    const valores = fluxos.map(f => f.embarques);
    const maxValor = Math.max(...valores);
    const minValor = Math.min(...valores);
    
    // Renderizar fluxos
    if (document.getElementById("mostrarFluxos").checked) {
        fluxos.forEach(fluxo => {
            const origem = fluxo.origem;
            const destino = fluxo.destino;
            const embarques = fluxo.embarques;
            
            const coordOrigem = coordenadas[origem];
            const coordDestino = coordenadas[destino];
            
            if (coordOrigem && coordDestino) {
                // Calcular intensidade da linha (espessura)
                const intensidade = (embarques - minValor) / (maxValor - minValor);
                const espessura = Math.max(2, Math.min(10, 2 + intensidade * 8));
                
                // Calcular cor da linha
                const cor = getColorByIntensity(intensidade);
                
                // Criar linha do fluxo
                const polyline = L.polyline([coordOrigem, coordDestino], {
                    color: cor,
                    weight: espessura,
                    opacity: 0.7
                });
                
                // Adicionar popup
                polyline.bindPopup(`
                    <div class="text-center">
                        <h6>${origem} → ${destino}</h6>
                        <p class="mb-0"><strong>Embarques:</strong> ${embarques.toLocaleString("pt-BR")}</p>
                    </div>
                `);
                
                fluxosLayer.addLayer(polyline);
            }
        });
    }
    
    // Renderizar origens
    if (document.getElementById("mostrarOrigens").checked) {
        const origensUnicas = [...new Set(fluxos.map(f => f.origem))];
        origensUnicas.forEach(origem => {
            const coord = coordenadas[origem];
            if (coord) {
                const marker = L.circleMarker(coord, {
                    radius: 8,
                    fillColor: "#28a745",
                    color: "#fff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                });
                
                marker.bindPopup(`
                    <div class="text-center">
                        <h6>Origem: ${origem}</h6>
                        <p class="mb-0">Clique para ver detalhes</p>
                    </div>
                `);
                
                if (document.getElementById("agruparMarcadores").checked) {
                    clusterGroup.addLayer(marker);
                } else {
                    origensLayer.addLayer(marker);
                }
            }
        });
    }
    
    // Renderizar destinos
    if (document.getElementById("mostrarDestinos").checked) {
        const destinosUnicos = [...new Set(fluxos.map(f => f.destino))];
        destinosUnicos.forEach(destino => {
            const coord = coordenadas[destino];
            if (coord) {
                const marker = L.circleMarker(coord, {
                    radius: 8,
                    fillColor: "#17a2b8",
                    color: "#fff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                });
                
                marker.bindPopup(`
                    <div class="text-center">
                        <h6>Destino: ${destino}</h6>
                        <p class="mb-0">Clique para ver detalhes</p>
                    </div>
                `);
                
                if (document.getElementById("agruparMarcadores").checked) {
                    clusterGroup.addLayer(marker);
                } else {
                    destinosLayer.addLayer(marker);
                }
            }
        });
    }
    
    // Adicionar cluster group ao mapa se ativado
    if (document.getElementById("agruparMarcadores").checked) {
        mapa.addLayer(clusterGroup);
    }
    
    // Ajustar zoom para mostrar todos os fluxos
    if (fluxos.length > 0) {
        const group = new L.featureGroup([fluxosLayer, origensLayer, destinosLayer, clusterGroup]);
        mapa.fitBounds(group.getBounds().pad(0.1));
    }
}

// Obter cor baseada na intensidade
function getColorByIntensity(intensidade) {
    const cores = [
        "#e3f2fd", "#bbdefb", "#90caf9", "#64b5f6", 
        "#42a5f5", "#2196f3", "#1e88e5", "#1976d2", 
        "#1565c0", "#0d47a1"
    ];
    
    const index = Math.floor(intensidade * (cores.length - 1));
    return cores[index];
}

// Atualizar estatísticas do mapa
function updateMapaStats(data) {
    if (!data || !data.fluxos) return;
    
    const fluxos = data.fluxos;
    const origens = [...new Set(fluxos.map(f => f.origem))];
    const destinos = [...new Set(fluxos.map(f => f.destino))];
    const valores = fluxos.map(f => f.embarques);
    const fluxoMedio = valores.length > 0 ? valores.reduce((a, b) => a + b, 0) / valores.length : 0;
    
    document.getElementById("totalFluxos").textContent = fluxos.length.toLocaleString("pt-BR");
    document.getElementById("totalOrigens").textContent = origens.length;
    document.getElementById("totalDestinos").textContent = destinos.length;
    document.getElementById("fluxoMedio").textContent = Math.round(fluxoMedio).toLocaleString("pt-BR");
}

// Carregar top fluxos
function loadTopFluxos(data) {
    if (!data || !data.fluxos) return;
    
    const fluxos = data.fluxos.slice(0, 10); // Top 10
    
    const html = fluxos.map((fluxo, index) => `
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
    document.getElementById("topN").addEventListener("change", atualizarIndicadorFiltros);
    document.getElementById("volumeMinimo").addEventListener("change", atualizarIndicadorFiltros);
}

// Inicializar controles do mapa
function initializeMapaControls() {
    document.getElementById("mostrarLabels").addEventListener("change", function() {
        // Implementar lógica para mostrar/ocultar labels
        console.log("Mostrar labels:", this.checked);
    });
    
    document.getElementById("mostrarFluxos").addEventListener("change", function() {
        if (mapa) loadMapaData();
    });
    
    document.getElementById("mostrarOrigens").addEventListener("change", function() {
        if (mapa) loadMapaData();
    });
    
    document.getElementById("mostrarDestinos").addEventListener("change", function() {
        if (mapa) loadMapaData();
    });
    
    document.getElementById("agruparMarcadores").addEventListener("change", function() {
        if (mapa) loadMapaData();
    });
}

// Atualizar indicador de filtros
function atualizarIndicadorFiltros() {
    const dataInicio = document.getElementById("dataInicio").value;
    const dataFim = document.getElementById("dataFim").value;
    const origens = $("#origemFilter").val();
    const destinos = $("#destinoFilter").val();
    const topN = document.getElementById("topN").value;
    const volumeMinimo = document.getElementById("volumeMinimo").value;
    
    currentFilters = {
        data_inicio: dataInicio,
        data_fim: dataFim,
        origens: origens,
        destinos: destinos,
        top_n: topN,
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
    
    if (topN && topN !== "10") {
        temFiltros = true;
        filtrosTexto.push(`<span class="badge bg-warning">Top: ${topN}</span>`);
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
    const topN = document.getElementById("topN").value;
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
        top_n: topN,
        volume_minimo: volumeMinimo
    };
    
    atualizarIndicadorFiltros();
    loadMapaData();
    showToast("Filtros aplicados com sucesso!", "success");
}

// Resetar filtros
function resetarFiltros() {
    document.getElementById("dataInicio").value = "";
    document.getElementById("dataFim").value = "";
    $("#origemFilter").val(null).trigger("change");
    $("#destinoFilter").val(null).trigger("change");
    document.getElementById("topN").value = "10";
    document.getElementById("volumeMinimo").value = "0";
    
    currentFilters = {};
    atualizarIndicadorFiltros();
    checkDataStatus();
    showToast("Filtros resetados!", "info");
}

// Atualizar mapa
function refreshMapa() {
    loadMapaData();
    showToast("Mapa atualizado!", "success");
}

// Exportar dados do mapa
function exportMapaData() {
    const params = new URLSearchParams(currentFilters);
    const excelUrl = `/api/exportar_excel?${params.toString()}`;
    const link = document.createElement("a");
    link.href = excelUrl;
    link.download = `mapa_fluxos_${new Date().toISOString().slice(0, 10)}.xlsx`;
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

// Mostrar erro do mapa
function showMapaError() {
    const container = document.getElementById("mapaContainer");
    container.innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="mt-2">Erro ao carregar dados do mapa</div>
        </div>
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

#mapaContainer {
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>
';

$this->render("base", compact("title", "content", "extra_js"));
