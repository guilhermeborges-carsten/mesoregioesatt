<?php

namespace DashboardLogistico\Controllers;

use DashboardLogistico\Core\Controller;

/**
 * Controlador principal do dashboard
 */
class DashboardController extends Controller
{
    /**
     * Página inicial
     */
    public function index()
    {
        $this->render('index');
    }

    /**
     * Página do heatmap
     */
    public function heatmap()
    {
        $this->render('heatmap');
    }

    /**
     * Página do mapa de fluxos
     */
    public function mapaFluxos()
    {
        $this->render('mapa_fluxos');
    }

    /**
     * Página da tabela
     */
    public function tabela()
    {
        $this->render('tabela');
    }

    /**
     * Página do balanço
     */
    public function balanco()
    {
        $this->render('balanco');
    }

    /**
     * Página do balanço por clientes
     */
    public function balancoClientes()
    {
        $this->render('balanco_clientes');
    }

    /**
     * Página de análise de clientes
     */
    public function analiseClientes()
    {
        $this->render('analise_clientes');
    }
}
