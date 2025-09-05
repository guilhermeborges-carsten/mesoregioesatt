# 🚀 Instalação SEM Composer (PHP 7.4)

## ✅ **SISTEMA FUNCIONANDO SEM COMPOSER!**

O sistema foi adaptado para funcionar **sem necessidade do Composer**!

### 📋 **Requisitos Mínimos:**
- **PHP 7.4** ou superior
- **Servidor web** (Apache, Nginx, ou servidor de desenvolvimento)
- **Extensões PHP**: `mbstring`, `fileinfo`, `json`

### 🔧 **Instalação Rápida:**

#### 1. **Copiar Arquivos:**
- Copie todos os arquivos para o diretório do servidor web
- Certifique-se de que a estrutura de pastas está correta

#### 2. **Configurar Permissões:**
```bash
# No Linux/Mac
chmod 755 uploads/
chmod 755 temp/

# No Windows (PowerShell)
icacls uploads /grant Everyone:F
icacls temp /grant Everyone:F
```

#### 3. **Iniciar Servidor:**
```bash
# Servidor de desenvolvimento PHP
php -S localhost:8000

# Ou configurar Apache/Nginx
```

### 📁 **Estrutura do Projeto:**
```
mesoregioes-main - teste/
├── index.php                 # Entry point (sem Composer)
├── src/
│   ├── Config/              # Configurações
│   ├── Core/                # Classes base
│   ├── Controllers/         # Controllers
│   ├── Models/              # Models
│   └── Utils/               # Utilitários (incluindo SimpleExcelProcessor)
├── static/                  # Assets frontend
├── templates/               # Templates + template CSV
└── uploads/                 # Diretório de uploads
```

### 🎯 **Funcionalidades Disponíveis:**

#### **📊 Upload de Arquivos:**
- ✅ **Arquivos CSV** (funcionamento completo)
- ✅ **Arquivos Excel** (requer PhpSpreadsheet via Composer)
- ✅ Validação de formato e tamanho
- ✅ Template de exemplo em CSV

#### **📈 Dashboard Completo:**
- ✅ Estatísticas gerais
- ✅ Gráficos de evolução mensal
- ✅ Análise de clientes
- ✅ Heatmap de fluxos
- ✅ Mapa geográfico
- ✅ Tabela detalhada
- ✅ Balanços de embarques

#### **📋 Exportação:**
- ✅ **CSV** (funcionamento completo)
- ✅ **Excel** (requer PhpSpreadsheet via Composer)

### 🔧 **Como Funciona sem Composer:**

#### **1. Carregamento de Classes:**
- Classes incluídas diretamente no `index.php`
- Sem dependência do autoloader do Composer
- Detecção automática de dependências disponíveis

#### **2. Processamento de Arquivos:**
- **CSV**: Processamento nativo do PHP
- **Excel**: Fallback para CSV quando PhpSpreadsheet não disponível

#### **3. Arquitetura MVC:**
- **Model**: `EmbarquesModel` gerencia dados
- **View**: Templates PHP para apresentação
- **Controller**: `ApiController` e `DashboardController`

### 📝 **Formato do Arquivo CSV:**
```csv
DATA,ORIGEM,DESTINO,CLIENTE,VOLUME
2024-01-15,METROPOLITANA DE SÃO PAULO,CAMPINAS,Cliente A,100
2024-01-16,CAMPINAS,RIBEIRÃO PRETO,Cliente B,150
2024-01-17,RIBEIRÃO PRETO,METROPOLITANA DE SÃO PAULO,Cliente C,200
```

### 🚀 **Como Usar:**

1. **Acesse o sistema** no navegador
2. **Faça upload** de um arquivo CSV com dados de embarques
3. **Explore as visualizações** disponíveis
4. **Exporte relatórios** em CSV

### ⚡ **Vantagens desta Versão:**

- ✅ **Sem dependências externas**
- ✅ **Instalação simples**
- ✅ **Funcionamento imediato**
- ✅ **Compatível com PHP 7.4**
- ✅ **Processamento nativo de CSV**

### 🛠️ **Solução de Problemas:**

#### **Erro de Permissão:**
```bash
chmod 755 uploads/ temp/
```

#### **Erro de Upload:**
- Verifique se a pasta `uploads/` existe
- Verifique permissões de escrita
- Verifique limite de upload no PHP

#### **Erro de Classe não Encontrada:**
- Verifique se todos os arquivos estão no lugar correto
- Verifique se o `index.php` está incluindo todas as classes

### 🎉 **Sistema Pronto!**
O sistema está funcionando **sem Composer** e compatível com **PHP 7.4**!

### 📞 **Para Suporte:**
- Use arquivos CSV para melhor compatibilidade
- Instale o Composer posteriormente para suporte completo a Excel
- Verifique logs de erro do PHP para diagnóstico
