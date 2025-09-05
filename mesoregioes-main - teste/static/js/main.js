// ========================================
// SISTEMA DE ANÁLISE LOGÍSTICA - PHP
// JavaScript Principal
// ========================================

// Variáveis globais
let appData = {
    hasData: false,
    currentFilters: {},
    charts: {},
    currentTheme: 'light'
};

// ========================================
// INICIALIZAÇÃO DA APLICAÇÃO
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando aplicação...');
    
    initializeApp();
    setupEventListeners();
    checkDataStatus();
    initializeTheme();
    setupKeyboardShortcuts();
    
    console.log('Aplicação inicializada com sucesso');
});

// Inicializar aplicação
function initializeApp() {
    // Configurar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Configurar popovers do Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Configurar Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione uma opção...'
        });
    }
}

// Configurar event listeners
function setupEventListeners() {
    // Upload de arquivo
    const fileInput = document.getElementById('excelFile');
    if (fileInput) {
        fileInput.addEventListener('change', handleFileUpload);
    }
    
    // Botão de upload
    const uploadBtn = document.getElementById('uploadBtn');
    if (uploadBtn) {
        uploadBtn.addEventListener('click', uploadFile);
    }
    
    // Botão de download do template
    const templateBtn = document.getElementById('downloadTemplateBtn');
    if (templateBtn) {
        templateBtn.addEventListener('click', downloadTemplate);
    }
    
    // Botão de fechar modal
    const closeModalBtn = document.getElementById('closeModalBtn');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeUploadModal);
    }
    
    // Redimensionamento da janela
    window.addEventListener('resize', debounce(handleWindowResize, 250));
    
    // Atalhos de teclado
    document.addEventListener('keydown', handleKeyboardShortcuts);
}

// ========================================
// VERIFICAÇÃO DE STATUS DOS DADOS
// ========================================

function checkDataStatus() {
    console.log('Verificando status dos dados...');
    
    fetch('/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.log('Dados não carregados:', data.error);
                appData.hasData = false;
                updateUIForNoData();
            } else {
                console.log('Dados carregados com sucesso:', data);
                appData.hasData = true;
                updateUIForDataLoaded(data);
            }
        })
        .catch(error => {
            console.error('Erro ao verificar dados:', error);
            appData.hasData = false;
            updateUIForNoData();
        });
}

// Atualizar UI quando não há dados
function updateUIForNoData() {
    const noDataAlert = document.getElementById('noDataAlert');
    if (noDataAlert) {
        noDataAlert.classList.remove('d-none');
    }
    
    // Ocultar seções que dependem de dados
    const sectionsToHide = [
        'statsCards', 'filtrosSection', 'heatmapControls', 'mapaControls',
        'tabelaControls', 'balancoStats', 'analiseClientesStats'
    ];
    
    sectionsToHide.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'none';
        }
    });
}

// Atualizar UI quando há dados carregados
function updateUIForDataLoaded(data) {
    const noDataAlert = document.getElementById('noDataAlert');
    if (noDataAlert) {
        noDataAlert.classList.add('d-none');
    }
    
    // Mostrar seções que dependem de dados
    const sectionsToShow = [
        'statsCards', 'filtrosSection', 'heatmapControls', 'mapaControls',
        'tabelaControls', 'balancoStats', 'analiseClientesStats'
    ];
    
    sectionsToShow.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'block';
        }
    });
    
    // Atualizar estatísticas se disponível
    if (data.total_embarques !== undefined) {
        updateQuickStats(data);
    }
}

// ========================================
// UPLOAD DE ARQUIVOS
// ========================================

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validar tipo de arquivo
    if (!isValidExcelFile(file)) {
        showToast('Por favor, selecione um arquivo Excel válido (.xlsx ou .xls)', 'error');
        return;
    }
    
    // Validar tamanho do arquivo (máximo 10MB)
    if (file.size > 10 * 1024 * 1024) {
        showToast('Arquivo muito grande. Tamanho máximo: 10MB', 'error');
        return;
    }
    
    console.log('Arquivo selecionado:', file.name, 'Tamanho:', file.size);
    updateUploadButton(true);
}

function uploadFile() {
    const fileInput = document.getElementById('excelFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showToast('Por favor, selecione um arquivo Excel', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    
    updateUploadButton(false, 'Enviando...');
    
    fetch('/api/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Arquivo carregado com sucesso!', 'success');
            closeUploadModal();
            checkDataStatus();
            
            // Recarregar página após 1 segundo para atualizar todos os dados
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.error || 'Erro ao carregar arquivo', 'error');
        }
    })
    .catch(error => {
        console.error('Erro no upload:', error);
        showToast('Erro ao carregar arquivo. Tente novamente.', 'error');
    })
    .finally(() => {
        updateUploadButton(true);
    });
}

function isValidExcelFile(file) {
    const validTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];
    
    const validExtensions = ['.xlsx', '.xls'];
    const fileName = file.name.toLowerCase();
    
    return validTypes.includes(file.type) || 
           validExtensions.some(ext => fileName.endsWith(ext));
}

function updateUploadButton(enabled, text = 'Enviar Arquivo') {
    const uploadBtn = document.getElementById('uploadBtn');
    if (uploadBtn) {
        uploadBtn.disabled = !enabled;
        uploadBtn.innerHTML = enabled ? 
            `<i class="bi bi-upload"></i> ${text}` : 
            `<span class="spinner-border spinner-border-sm me-2"></span>${text}`;
    }
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
    
    // Limpar input de arquivo
    const fileInput = document.getElementById('excelFile');
    if (fileInput) {
        fileInput.value = '';
    }
}

// ========================================
// DOWNLOAD DE TEMPLATE
// ========================================

function downloadTemplate() {
    const link = document.createElement('a');
    link.href = '/api/download_template';
    link.download = 'template_embarques.xlsx';
    link.click();
    
    showToast('Download do template iniciado!', 'info');
}

// ========================================
// SISTEMA DE NOTIFICAÇÕES
// ========================================

function showToast(message, type = 'info', duration = 5000) {
    // Remover toasts existentes
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => {
        const bsToast = bootstrap.Toast.getInstance(toast);
        if (bsToast) {
            bsToast.dispose();
        }
        toast.remove();
    });
    
    // Criar novo toast
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-${getToastIcon(type)} text-${type} me-2"></i>
                <strong class="me-auto">Sistema</strong>
                <small class="text-muted">agora</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Mostrar toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: duration
    });
    
    toast.show();
}

function getToastIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// ========================================
// SISTEMA DE TEMA
// ========================================

function initializeTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
}

function toggleTheme() {
    const newTheme = appData.currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
}

function setTheme(theme) {
    appData.currentTheme = theme;
    localStorage.setItem('theme', theme);
    
    // Aplicar tema ao body
    document.body.setAttribute('data-bs-theme', theme);
    
    // Atualizar ícone do botão de tema
    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeBtn) {
        const icon = themeBtn.querySelector('i');
        if (icon) {
            icon.className = theme === 'light' ? 'bi bi-moon' : 'bi bi-sun';
        }
    }
    
    // Atualizar gráficos se existirem
    updateChartsTheme(theme);
}

function updateChartsTheme(theme) {
    // Atualizar tema dos gráficos Chart.js
    Object.values(appData.charts).forEach(chart => {
        if (chart && typeof chart.update === 'function') {
            chart.options.plugins.legend.labels.color = theme === 'dark' ? '#fff' : '#000';
            chart.options.scales.x.grid.color = theme === 'dark' ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
            chart.options.scales.y.grid.color = theme === 'dark' ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
            chart.update();
        }
    });
}

// ========================================
// ATALHOS DE TECLADO
// ========================================

function setupKeyboardShortcuts() {
    // Não fazer nada aqui, os atalhos são tratados no handleKeyboardShortcuts
}

function handleKeyboardShortcuts(event) {
    // Ctrl/Cmd + U: Upload
    if ((event.ctrlKey || event.metaKey) && event.key === 'u') {
        event.preventDefault();
        const uploadModal = document.getElementById('uploadModal');
        if (uploadModal) {
            const bsModal = new bootstrap.Modal(uploadModal);
            bsModal.show();
        }
    }
    
    // Ctrl/Cmd + R: Refresh
    if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
        event.preventDefault();
        if (typeof refreshData === 'function') {
            refreshData();
        }
    }
    
    // Ctrl/Cmd + E: Export
    if ((event.ctrlKey || event.metaKey) && event.key === 'e') {
        event.preventDefault();
        if (typeof exportData === 'function') {
            exportData();
        }
    }
    
    // Ctrl/Cmd + T: Toggle Theme
    if ((event.ctrlKey || event.metaKey) && event.key === 't') {
        event.preventDefault();
        toggleTheme();
    }
    
    // Escape: Fechar modais
    if (event.key === 'Escape') {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const bsModal = bootstrap.Modal.getInstance(openModal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }
}

// ========================================
// UTILITÁRIOS
// ========================================

// Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Formatação de números
function formatNumber(number, locale = 'pt-BR') {
    if (typeof number !== 'number') return '0';
    return new Intl.NumberFormat(locale).format(number);
}

// Formatação de moeda
function formatCurrency(value, currency = 'BRL', locale = 'pt-BR') {
    if (typeof value !== 'number') return 'R$ 0,00';
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(value);
}

// Formatação de data
function formatDate(date, locale = 'pt-BR') {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString(locale);
}

// Formatação de data e hora
function formatDateTime(date, locale = 'pt-BR') {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleString(locale);
}

// Validação de email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validação de CPF
function isValidCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    if (cpf.length !== 11) return false;
    
    // Verificar se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validar dígitos verificadores
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Validação de CNPJ
function isValidCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]/g, '');
    if (cnpj.length !== 14) return false;
    
    // Verificar se todos os dígitos são iguais
    if (/^(\d)\1{13}$/.test(cnpj)) return false;
    
    // Validar primeiro dígito verificador
    let sum = 0;
    let weight = 2;
    for (let i = 11; i >= 0; i--) {
        sum += parseInt(cnpj.charAt(i)) * weight;
        weight = weight === 9 ? 2 : weight + 1;
    }
    let remainder = sum % 11;
    let digit1 = remainder < 2 ? 0 : 11 - remainder;
    if (digit1 !== parseInt(cnpj.charAt(12))) return false;
    
    // Validar segundo dígito verificador
    sum = 0;
    weight = 2;
    for (let i = 12; i >= 0; i--) {
        sum += parseInt(cnpj.charAt(i)) * weight;
        weight = weight === 9 ? 2 : weight + 1;
    }
    remainder = sum % 11;
    let digit2 = remainder < 2 ? 0 : 11 - remainder;
    if (digit2 !== parseInt(cnpj.charAt(13))) return false;
    
    return true;
}

// ========================================
// REQUISIÇÕES HTTP
// ========================================

function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return fetch(url, finalOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            throw error;
        });
}

// ========================================
// MANIPULAÇÃO DE DADOS
// ========================================

function updateQuickStats(data) {
    const stats = [
        { id: 'totalEmbarques', value: data.total_embarques },
        { id: 'totalOrigens', value: data.total_origens },
        { id: 'totalDestinos', value: data.total_destinos },
        { id: 'periodo', value: `${data.periodo_inicio} - ${data.periodo_fim}` }
    ];
    
    stats.forEach(stat => {
        const element = document.getElementById(stat.id);
        if (element) {
            if (stat.id === 'periodo') {
                element.textContent = stat.value;
            } else {
                element.textContent = formatNumber(stat.value);
            }
        }
    });
}

// ========================================
// REDIMENSIONAMENTO DE JANELA
// ========================================

function handleWindowResize() {
    // Atualizar gráficos se existirem
    Object.values(appData.charts).forEach(chart => {
        if (chart && typeof chart.resize === 'function') {
            chart.resize();
        }
    });
    
    // Atualizar mapas se existirem
    if (window.mapa && typeof window.mapa.invalidateSize === 'function') {
        window.mapa.invalidateSize();
    }
}

// ========================================
// EXPORTAÇÃO DE DADOS
// ========================================

function exportToExcel(filters = {}) {
    const params = new URLSearchParams(filters);
    const url = `/api/exportar_excel?${params.toString()}`;
    
    const link = document.createElement('a');
    link.href = url;
    link.download = `embarques_${new Date().toISOString().slice(0, 10)}.xlsx`;
    link.click();
    
    showToast('Exportação para Excel iniciada!', 'success');
}

function exportToCSV(filters = {}) {
    const params = new URLSearchParams(filters);
    const url = `/api/exportar_csv?${params.toString()}`;
    
    const link = document.createElement('a');
    link.href = url;
    link.download = `embarques_${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    
    showToast('Exportação para CSV iniciada!', 'success');
}

// ========================================
// FUNÇÕES GLOBAIS PARA COMPATIBILIDADE
// ========================================

// Funções que podem ser chamadas de qualquer lugar
window.showToast = showToast;
window.formatNumber = formatNumber;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;
window.exportToExcel = exportToExcel;
window.exportToCSV = exportToCSV;
window.toggleTheme = toggleTheme;
window.checkDataStatus = checkDataStatus;

// ========================================
// INICIALIZAÇÃO FINAL
// ========================================

console.log('Sistema de Análise Logística - JavaScript carregado com sucesso!');