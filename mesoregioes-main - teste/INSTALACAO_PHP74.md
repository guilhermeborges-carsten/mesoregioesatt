# 🚀 Instalação para PHP 7.4

## ✅ **SISTEMA COMPATÍVEL COM PHP 7.4**

O sistema foi totalmente adaptado para funcionar perfeitamente com PHP 7.4!

### 📋 **Requisitos do Sistema:**
- **PHP 7.4** ou superior
- **Composer** (gerenciador de dependências PHP)
- **Servidor web** (Apache, Nginx, ou servidor de desenvolvimento)

### 🔧 **Instalação Passo a Passo:**

#### 1. **Instalar Dependências:**
```bash
composer install
```

#### 2. **Configurar Servidor Web:**
- **Apache**: Configure o DocumentRoot para apontar para a pasta do projeto
- **Nginx**: Configure o root para apontar para a pasta do projeto
- **Servidor de Desenvolvimento**: Use o servidor embutido do PHP:
```bash
php -S localhost:8000
```

#### 3. **Verificar Permissões:**
```bash
# No Linux/Mac
chmod 755 uploads/
chmod 755 temp/

# No Windows (PowerShell)
icacls uploads /grant Everyone:F
icacls temp /grant Everyone:F
```

### 📁 **Estrutura do Projeto:**
```
mesoregioes-main - teste/
├── composer.json              # Dependências PHP 7.4 compatíveis
├── index.php                 # Entry point
├── src/
│   ├── Config/              # Configurações
│   ├── Core/                # Classes base (Model, Controller, Router, View)
│   ├── Controllers/         # Controllers (API e Dashboard)
│   ├── Models/              # Models (EmbarquesModel)
│   └── Utils/               # Utilitários
├── static/                  # Assets frontend
├── templates/               # Templates PHP + template CSV
└── uploads/                 # Diretório de uploads
```

### 🎯 **Funcionalidades Disponíveis:**

#### **📊 Dashboard Principal:**
- Upload de arquivos Excel (.xlsx, .xls)
- Visualização de estatísticas gerais
- Gráficos de evolução mensal
- Análise de clientes

#### **🗺️ Visualizações:**
- **Heatmap**: Matriz de origem x destino
- **Mapa de Fluxos**: Visualização geográfica
- **Tabela Detalhada**: Dados paginados com filtros
- **Balanços**: Análise de embarques e clientes

#### **📈 Relatórios:**
- Exportação para Excel (.xlsx)
- Exportação para CSV
- Balanços formatados
- Template de exemplo

### 🔧 **Configurações Específicas PHP 7.4:**

#### **Dependências Otimizadas:**
- `phpoffice/phpspreadsheet: ^1.24` (compatível com PHP 7.4)
- `vlucas/phpdotenv: ^5.4` (compatível com PHP 7.4)
- `monolog/monolog: ^2.8` (compatível com PHP 7.4)

#### **Sintaxe Compatível:**
- ✅ Sem propriedades tipadas (PHP 8.0+)
- ✅ Sem union types (PHP 8.0+)
- ✅ Sem match expressions (PHP 8.0+)
- ✅ Sem named arguments (PHP 8.0+)
- ✅ Array destructuring (PHP 7.1+)
- ✅ Namespaces completos para Exception

### 🚀 **Como Usar:**

1. **Acesse o sistema** no navegador
2. **Faça upload** de um arquivo Excel com dados de embarques
3. **Explore as visualizações** disponíveis
4. **Exporte relatórios** conforme necessário

### 📝 **Formato do Arquivo Excel:**
```
DATA        | ORIGEM                    | DESTINO                   | CLIENTE    | VOLUME
2024-01-15  | METROPOLITANA DE SÃO PAULO| CAMPINAS                  | Cliente A  | 100
2024-01-16  | CAMPINAS                  | RIBEIRÃO PRETO            | Cliente B  | 150
```

### ⚡ **Performance:**
- Sistema otimizado para PHP 7.4
- Processamento eficiente de dados
- Interface responsiva
- Exportação rápida

### 🛠️ **Solução de Problemas:**

#### **Erro de Permissão:**
```bash
chmod 755 uploads/ temp/
```

#### **Erro de Composer:**
```bash
composer install --no-dev
```

#### **Erro de Upload:**
- Verifique se a pasta `uploads/` existe
- Verifique permissões de escrita
- Verifique limite de upload no PHP

### 🎉 **Sistema Pronto!**
O sistema está totalmente funcional e compatível com PHP 7.4!
