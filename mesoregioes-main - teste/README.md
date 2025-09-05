# Dashboard Logístico - Sistema de Análise de Embarques

Sistema completo de análise logística para visualização e análise de embarques entre mesorregiões do Brasil, convertido de Python/Flask para PHP.

## 🚀 Funcionalidades

### 📊 Análises Disponíveis
- **Dashboard Principal**: Visão geral com estatísticas rápidas e gráficos de evolução
- **Heatmap O-D**: Matriz de origem-destino com visualização de intensidade dos fluxos
- **Mapa de Fluxos**: Visualização geográfica dos fluxos logísticos no mapa do Brasil
- **Tabela Detalhada**: Dados tabulares com filtros avançados e paginação
- **Balanço de Embarques**: Análise do saldo (origem - destino) por mesorregião
- **Balanço de Clientes**: Análise do saldo por cliente e mesorregião
- **Análise de Clientes**: Insights e análise detalhada por cliente

### 🔧 Funcionalidades Técnicas
- **Upload de Arquivos Excel**: Suporte a .xlsx e .xls com validação
- **Filtros Avançados**: Por data, origem, destino, cliente e volume mínimo
- **Exportação**: Excel e CSV com dados filtrados
- **Visualizações Interativas**: Gráficos Chart.js e mapas Leaflet
- **Responsivo**: Interface adaptável para desktop e mobile
- **Tema Escuro/Claro**: Alternância de tema com persistência

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 7.4+**: Linguagem principal
- **Composer**: Gerenciamento de dependências
- **PhpSpreadsheet**: Processamento de arquivos Excel
- **Arquitetura MVC**: Organização do código

### Frontend
- **Bootstrap 5**: Framework CSS
- **Chart.js**: Gráficos interativos
- **Leaflet**: Mapas interativos
- **Select2**: Seleção múltipla avançada
- **JavaScript Vanilla**: Funcionalidades interativas

## 📁 Estrutura do Projeto

```
mesoregioes-main - teste/
├── index.php                 # Ponto de entrada da aplicação
├── composer.json            # Dependências PHP
├── README.md               # Documentação
├── src/
│   ├── Config/
│   │   ├── Config.php      # Configurações do sistema
│   │   └── coordinates.php # Coordenadas das mesorregiões
│   ├── Core/
│   │   ├── Router.php      # Sistema de roteamento
│   │   ├── Controller.php  # Classe base dos controladores
│   │   ├── View.php        # Sistema de views
│   │   └── Model.php       # Classe base dos modelos
│   ├── Controllers/
│   │   ├── DashboardController.php # Controlador das páginas
│   │   └── ApiController.php       # Controlador das APIs
│   └── Utils/
│       ├── ExcelProcessor.php      # Processamento de Excel
│       ├── DataProcessor.php       # Processamento de dados
│       └── ExportHelper.php        # Exportação de dados
├── templates/
│   ├── base.php            # Template base
│   ├── index.php           # Dashboard principal
│   ├── heatmap.php         # Página do heatmap
│   ├── mapa_fluxos.php     # Página do mapa
│   ├── tabela.php          # Página da tabela
│   ├── balanco.php         # Página do balanço
│   ├── balanco_clientes.php # Página do balanço de clientes
│   └── analise_clientes.php # Página de análise de clientes
├── static/
│   ├── css/
│   │   └── style.css       # Estilos customizados
│   ├── js/
│   │   └── main.js         # JavaScript principal
│   └── img/                # Imagens e ícones
└── uploads/                # Diretório de uploads
```

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- Composer
- Servidor web (Apache/Nginx)
- Extensão PHP: `php-zip`, `php-xml`, `php-gd`

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone <url-do-repositorio>
   cd mesoregioes-main
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Configure as permissões**
   ```bash
   chmod 755 uploads/
   chmod 755 temp/
   ```

4. **Configure o servidor web**
   - Apache: Configure o DocumentRoot para o diretório do projeto
   - Nginx: Configure o root para o diretório do projeto

5. **Acesse a aplicação**
   ```
   http://localhost/mesoregioes-main
   ```

## 📋 Formato dos Dados

O sistema espera arquivos Excel com as seguintes colunas:

| Coluna | Descrição | Exemplo |
|--------|-----------|---------|
| DATA | Data do embarque | 2023-01-15 |
| ANO | Ano do embarque | 2023 |
| MÊS | Mês do embarque | Janeiro |
| MES_NUM | Número do mês | 1 |
| TRECHO - FROTA PRÓPRIA | Trecho e cliente | São Paulo - Rio de Janeiro - Cliente ABC |
| MESORREGIÃO - ORIGEM | Mesorregião de origem | Metropolitana de São Paulo |
| MESORREGIÃO - DESTINO | Mesorregião de destino | Metropolitana do Rio de Janeiro |
| EMBARQUES | Quantidade de embarques | 150 |
| VALOR | Valor do embarque | 15000.00 |
| PESO | Peso total | 5000.50 |
| VOLUME | Volume total | 25.75 |

## 🔧 Configuração

### Configurações Principais
Edite o arquivo `src/Config/Config.php` para ajustar:

- **Tamanho máximo de upload**: `upload.max_file_size`
- **Extensões permitidas**: `upload.allowed_extensions`
- **Caminho de upload**: `upload.upload_path`
- **Coordenadas das mesorregiões**: `coordinates`

### Personalização
- **Tema**: Modifique `static/css/style.css`
- **Funcionalidades**: Edite `static/js/main.js`
- **Templates**: Modifique os arquivos em `templates/`

## 📊 APIs Disponíveis

### Endpoints Principais
- `GET /` - Dashboard principal
- `GET /heatmap` - Página do heatmap
- `GET /mapa_fluxos` - Página do mapa
- `GET /tabela` - Página da tabela
- `GET /balanco` - Página do balanço
- `GET /balanco_clientes` - Página do balanço de clientes
- `GET /analise_clientes` - Página de análise de clientes

### APIs de Dados
- `POST /api/upload` - Upload de arquivo Excel
- `GET /api/stats` - Estatísticas gerais
- `GET /api/evolucao_mensal` - Evolução mensal
- `GET /api/heatmap_data` - Dados do heatmap
- `GET /api/fluxos_mapa` - Dados do mapa
- `GET /api/tabela_dados` - Dados da tabela
- `GET /api/balanco_embarques` - Dados do balanço
- `GET /api/balanco_clientes` - Dados do balanço de clientes
- `GET /api/insights_clientes` - Insights de clientes
- `GET /api/exportar_excel` - Exportar Excel
- `GET /api/exportar_csv` - Exportar CSV
- `GET /api/download_template` - Download do template

## 🎯 Uso

1. **Upload de Dados**
   - Clique em "Upload" no menu
   - Selecione um arquivo Excel válido
   - Aguarde o processamento

2. **Análise de Dados**
   - Navegue pelas diferentes páginas de análise
   - Use os filtros para refinar os dados
   - Visualize os gráficos e mapas interativos

3. **Exportação**
   - Use os botões de exportação em cada página
   - Escolha entre Excel ou CSV
   - Os dados exportados respeitam os filtros aplicados

## 🔍 Filtros Disponíveis

- **Data**: Período de início e fim
- **Origem**: Mesorregiões de origem
- **Destino**: Mesorregiões de destino
- **Cliente**: Clientes específicos
- **Volume Mínimo**: Filtro por volume mínimo de embarques

## 📈 Visualizações

### Gráficos
- **Evolução Mensal**: Linha temporal dos embarques
- **Top Rankings**: Barras das principais origens/destinos
- **Balanço**: Gráficos de saldo por região/cliente

### Mapas
- **Fluxos Geográficos**: Linhas conectando origens e destinos
- **Intensidade**: Espessura das linhas baseada no volume
- **Clustering**: Agrupamento de marcadores próximos

### Tabelas
- **Dados Detalhados**: Informações completas dos embarques
- **Paginação**: Navegação por páginas
- **Ordenação**: Por qualquer coluna
- **Filtros**: Busca e filtros avançados

## 🛡️ Segurança

- Validação de tipos de arquivo
- Limite de tamanho de upload
- Sanitização de dados de entrada
- Validação de parâmetros de API

## 🐛 Solução de Problemas

### Problemas Comuns

1. **Erro de Upload**
   - Verifique o formato do arquivo (.xlsx ou .xls)
   - Confirme se o arquivo não excede 16MB
   - Verifique as permissões do diretório uploads/

2. **Erro de Processamento**
   - Verifique se todas as colunas necessárias estão presentes
   - Confirme se os dados estão no formato correto
   - Verifique os logs do servidor

3. **Problemas de Visualização**
   - Limpe o cache do navegador
   - Verifique se o JavaScript está habilitado
   - Confirme se todas as dependências estão carregadas

## 📝 Changelog

### v2.0.0
- Conversão completa de Python/Flask para PHP
- Nova arquitetura MVC
- Interface responsiva com Bootstrap 5
- Sistema de temas (claro/escuro)
- APIs REST completas
- Exportação para Excel e CSV
- Mapas interativos com Leaflet
- Gráficos interativos com Chart.js

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma issue no repositório
- Consulte a documentação
- Verifique os logs do sistema

---

**Dashboard Logístico v2.0.0** - Sistema completo de análise logística em PHP