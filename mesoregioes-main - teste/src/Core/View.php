<?php

namespace DashboardLogistico\Core;

/**
 * Sistema de views simples
 */
class View
{
    private $templatePath = 'templates/';
    private $layout = 'base.php';

    public function render($template, $data = [])
    {
        $templateFile = $this->templatePath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Template não encontrado: {$template}");
        }

        // Extrair variáveis para o escopo da view
        extract($data);

        // Incluir layout se especificado
        if ($this->layout) {
            $this->renderWithLayout($templateFile, $data);
        } else {
            include $templateFile;
        }
    }

    private function renderWithLayout($templateFile, $data)
    {
        $layoutFile = $this->templatePath . $this->layout;
        
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout não encontrado: {$this->layout}");
        }

        // Capturar conteúdo da view
        ob_start();
        extract($data);
        include $templateFile;
        $content = ob_get_clean();

        // Renderizar layout com conteúdo
        extract($data);
        include $layoutFile;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function setTemplatePath($path)
    {
        $this->templatePath = rtrim($path, '/') . '/';
    }

    /**
     * Gera URL para assets
     */
    public function asset($path)
    {
        return '/' . ltrim($path, '/');
    }

    /**
     * Gera URL para rotas
     */
    public function url($path = '')
    {
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Escapa HTML
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formata número
     */
    public function formatNumber($number, $decimals = 0)
    {
        return number_format($number, $decimals, ',', '.');
    }

    /**
     * Formata percentual
     */
    public function formatPercentage($value, $total, $decimals = 1)
    {
        if ($total == 0) return '0%';
        $percentage = ($value / $total) * 100;
        return number_format($percentage, $decimals, ',', '.') . '%';
    }
}
