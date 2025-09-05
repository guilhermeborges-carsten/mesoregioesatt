<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($title ?? 'Dashboard Logístico - Mesorregiões') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $this->asset('static/css/style.css') ?>">
    
    <?php if (isset($extra_css)): ?>
        <?= $extra_css ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <!-- Logo da Empresa -->
            <div class="navbar-brand d-flex align-items-center me-4">
                <div class="company-logo me-3">
                    <img src="<?= $this->asset('static/img/logo.png') ?>" alt="Logo da Empresa" class="logo-img" onerror="this.style.display='none'">
                </div>
                <a class="navbar-brand-text" href="<?= $this->url('/') ?>">
                    <i class="bi bi-truck"></i> Dashboard Logístico
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/') ?>">
                            <i class="bi bi-house"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/heatmap') ?>">
                            <i class="bi bi-grid-3x3-gap"></i> Heatmap
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/mapa_fluxos') ?>">
                            <i class="bi bi-geo-alt"></i> Mapa de Fluxos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/tabela') ?>">
                            <i class="bi bi-table"></i> Tabela
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/balanco') ?>">
                            <i class="bi bi-calculator"></i> Balanço
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/balanco_clientes') ?>">
                            <i class="bi bi-building"></i> Balanço por Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('/analise_clientes') ?>">
                            <i class="bi bi-graph-up"></i> Análise de Clientes
                        </a>
                    </li>
                </ul>
                
                <!-- Upload de arquivo -->
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-upload"></i> Upload
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="bi bi-file-earmark-excel"></i> Carregar Excel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="downloadTemplate()">
                                    <i class="bi bi-download"></i> Baixar Template
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal de Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Carregar Arquivo Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Selecione o arquivo Excel (.xlsx ou .xls)</label>
                            <input type="file" class="form-control" id="fileInput" name="file" accept=".xlsx,.xls" required>
                            <div class="form-text">O arquivo deve conter as colunas: TRECHO - FROTA PRÓPRIA, MESORREGIÃO - ORIGEM, MESORREGIÃO - DESTINO, MÊS, EMBARQUES</div>
                        </div>
                    </form>
                    <div id="uploadProgress" class="progress d-none">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="uploadFile()">
                        <i class="bi bi-upload"></i> Carregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Container principal -->
    <main class="container-fluid mt-3">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <p class="mb-0">Dashboard Logístico - Análise de Embarques entre Mesorregiões Brasileiras</p>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Custom JS -->
    <script src="<?= $this->asset('static/js/main.js') ?>"></script>
    
    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>
</html>
