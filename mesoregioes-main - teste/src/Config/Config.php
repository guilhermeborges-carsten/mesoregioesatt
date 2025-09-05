<?php

namespace App\Config;

/**
 * Classe de configuração do sistema
 */
class Config
{
    private static $instance = null;
    private $config = [];

    private function __construct()
    {
        $this->loadConfig();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig()
    {
        $this->config = [
            'app' => [
                'name' => 'Dashboard Logístico',
                'version' => '2.0.0',
                'debug' => true,
                'timezone' => 'America/Sao_Paulo'
            ],
            'upload' => [
                'max_file_size' => 16 * 1024 * 1024, // 16MB
                'allowed_extensions' => ['xlsx', 'xls'],
                'upload_path' => 'uploads/',
                'temp_path' => 'temp/',
                'template_path' => 'templates/template_embarques.xlsx'
            ],
            'database' => [
                'host' => 'localhost',
                'dbname' => 'dashboard_logistico',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ],
            'mesorregioes' => [
                'Norte' => ['Acre', 'Amazonas', 'Rondônia', 'Roraima', 'Amapá', 'Pará', 'Tocantins'],
                'Nordeste' => ['Maranhão', 'Piauí', 'Ceará', 'Rio Grande do Norte', 'Pernambuco', 'Paraíba', 'Sergipe', 'Alagoas', 'Bahia'],
                'Centro-Oeste' => ['Mato Grosso', 'Mato Grosso do Sul', 'Goiás', 'Distrito Federal'],
                'Sudeste' => ['Minas Gerais', 'Espírito Santo', 'Rio de Janeiro', 'São Paulo'],
                'Sul' => ['Paraná', 'Santa Catarina', 'Rio Grande do Sul']
            ],
            'coordinates' => [
                // Coordenadas das mesorregiões brasileiras
                'ARARAQUARA' => [-21.7944, -48.1756],
                'BAURU' => [-22.3147, -49.0604],
                'CAMPINAS' => [-22.9064, -47.0616],
                'LITORAL SUL PAULISTA' => [-24.0059, -46.3028],
                'METROPOLITANA DE SÃO PAULO' => [-23.5505, -46.6333],
                'PIRACICABA' => [-22.7253, -47.6490],
                'PRESIDENTE PRUDENTE' => [-22.1276, -51.3856],
                'RIBEIRÃO PRETO' => [-21.1763, -47.8208],
                'SÃO JOSÉ DO RIO PRETO' => [-20.8115, -49.3752],
                'VALE DO PARAÍBA PAULISTA' => [-23.1864, -45.8842],
                'MARÍLIA' => [-22.2178, -49.9505],
                'ASSIS' => [-22.6619, -50.4116],
                'ITAPETININGA' => [-23.5917, -48.0531],
                'MACRO METROPOLITANA PAULISTA' => [-23.5505, -46.6333],
                'ARACATUBA' => [-21.2089, -50.4329],
                'SOROCABA' => [-23.5016, -47.4586],
                'JUNDIAI' => [-23.1857, -46.8974],
                'SANTOS' => [-23.9608, -46.3336],
                'SÃO JOSÉ DOS CAMPOS' => [-23.1864, -45.8842],
                'GUARULHOS' => [-23.4543, -46.5339],
                'OSASCO' => [-23.5320, -46.7920],
                'SANTO ANDRÉ' => [-23.6639, -46.5383],
                'SÃO BERNARDO DO CAMPO' => [-23.6944, -46.5654],
                
                // Minas Gerais
                'CENTRAL' => [-19.9167, -43.9345],
                'JUIZ DE FORA' => [-21.7645, -43.3492],
                'NORTE DE MINAS' => [-16.7214, -43.8646],
                'TRIÂNGULO MINEIRO' => [-18.9186, -48.2772],
                'VALE DO MUCURI' => [-18.8519, -41.9492],
                'VALE DO RIO DOCE' => [-19.9167, -43.9345],
                'ZONA DA MATA' => [-21.7645, -43.3492],
                'SUL/SUDOESTE DE MINAS' => [-21.1356, -44.2492],
                'CAMPO DAS VERTENTES' => [-21.1356, -44.2492],
                'METROPOLITANA DE BELO HORIZONTE' => [-19.9167, -43.9345],
                
                // Rio de Janeiro
                'CENTRAL FLUMINENSE' => [-22.9068, -43.1729],
                'LESTE FLUMINENSE' => [-22.9068, -43.1729],
                'METROPOLITANA DO RIO DE JANEIRO' => [-22.9068, -43.1729],
                'NOROESTE FLUMINENSE' => [-22.9068, -43.1729],
                'NORTE FLUMINENSE' => [-22.9068, -43.1729],
                'SERRANA' => [-22.9068, -43.1729],
                'SUL FLUMINENSE' => [-22.9068, -43.1729],
                
                // Paraná
                'CENTRO OCIDENTAL PARANAENSE' => [-25.4289, -49.2671],
                'CENTRO ORIENTAL PARANAENSE' => [-25.4289, -49.2671],
                'CENTRO SUL PARANAENSE' => [-25.4289, -49.2671],
                'METROPOLITANA DE CURITIBA' => [-25.4289, -49.2671],
                'NORDESTE PARANAENSE' => [-23.4200, -51.9400],
                'NORTE CENTRAL PARANAENSE' => [-23.4200, -51.9400],
                'NORTE PIONEIRO PARANAENSE' => [-23.4200, -51.9400],
                'OESTE PARANAENSE' => [-24.9550, -53.4550],
                'SUDOESTE PARANAENSE' => [-25.5400, -54.5800],
                'SUL PARANAENSE' => [-25.5400, -54.5800],
                
                // Santa Catarina
                'GRANDE FLORIANÓPOLIS' => [-27.5969, -48.5495],
                'NORTE CATARINENSE' => [-26.9180, -49.0660],
                'OESTE CATARINENSE' => [-27.0950, -52.6170],
                'SERRA CATARINENSE' => [-27.5969, -48.5495],
                'SUL CATARINENSE' => [-28.6800, -49.3700],
                'VALE DO ITAJAÍ' => [-26.9180, -49.0660],
                
                // Rio Grande do Sul
                'CENTRO ORIENTAL RIO GRANDENSE' => [-30.0346, -51.2177],
                'CENTRO OCIDENTAL RIO GRANDENSE' => [-30.0346, -51.2177],
                'METROPOLITANA DE PORTO ALEGRE' => [-30.0346, -51.2177],
                'NORDESTE RIO GRANDENSE' => [-29.6900, -53.8100],
                'NOROESTE RIO GRANDENSE' => [-27.7200, -54.1700],
                'SUDESTE RIO GRANDENSE' => [-31.7700, -52.3400],
                'SUDOESTE RIO GRANDENSE' => [-29.6900, -53.8100],
                
                // Bahia
                'CENTRO NORTE BAIANO' => [-12.9714, -38.5011],
                'CENTRO SUL BAIANO' => [-12.9714, -38.5011],
                'EXTREMO OESTE BAIANO' => [-12.9714, -38.5011],
                'METROPOLITANA DE SALVADOR' => [-12.9714, -38.5011],
                'NORDESTE BAIANO' => [-12.9714, -38.5011],
                'SUL BAIANO' => [-12.9714, -38.5011],
                'VALE SÃO FRANCISCO DA BAHIA' => [-12.9714, -38.5011],
                
                // Goiás
                'CENTRO GOIANO' => [-16.6864, -49.2653],
                'LESTE GOIANO' => [-16.6864, -49.2653],
                'NORDESTE GOIANO' => [-16.6864, -49.2653],
                'NOROESTE GOIANO' => [-16.6864, -49.2653],
                'SUL GOIANO' => [-16.6864, -49.2653],
                
                // Mato Grosso
                'CENTRO SUL MATO GROSSENSE' => [-15.6010, -56.0974],
                'NORDESTE MATO GROSSENSE' => [-15.6010, -56.0974],
                'NORTE MATO GROSSENSE' => [-15.6010, -56.0974],
                'SUDESTE MATO GROSSENSE' => [-15.6010, -56.0974],
                'SUDOESTE MATO GROSSENSE' => [-15.6010, -56.0974],
                
                // Mato Grosso do Sul
                'CENTRO NORTE DE MATO GROSSO DO SUL' => [-20.4486, -54.6295],
                'LESTE DE MATO GROSSO DO SUL' => [-20.4486, -54.6295],
                'PANTANAIS SUL MATO GROSSENSE' => [-20.4486, -54.6295],
                'SUDOESTE DE MATO GROSSO DO SUL' => [-20.4486, -54.6295],
                'SUL DE MATO GROSSO DO SUL' => [-20.4486, -54.6295],
                
                // Outros estados
                'DISTRITO FEDERAL' => [-15.7942, -47.8822],
                'ESPÍRITO SANTO' => [-20.2976, -40.2958],
                'PERNAMBUCO' => [-8.0476, -34.8770],
                'CEARÁ' => [-3.7172, -38.5433],
                'PARÁ' => [-1.4554, -48.4898],
                'AMAZONAS' => [-3.4168, -65.8561],
                'ACRE' => [-8.7619, -70.5511],
                'RONDÔNIA' => [-8.7619, -63.9039],
                'RORAIMA' => [2.8235, -60.6758],
                'AMAPÁ' => [0.9019, -52.0030],
                'TOCANTINS' => [-10.1750, -48.2982],
                'MARANHÃO' => [-2.5297, -44.3028],
                'PIAUÍ' => [-5.0892, -42.8016],
                'RIO GRANDE DO NORTE' => [-5.7945, -35.2120],
                'PARAÍBA' => [-7.1150, -34.8631],
                'SERGIPE' => [-10.9091, -37.0677],
                'ALAGOAS' => [-9.6498, -35.7089]
            ]
        ];
    }

    public function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }

    public function getAll()
    {
        return $this->config;
    }
}
