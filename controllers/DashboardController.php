<?php
/**
 * Controller Dashboard
 * 
 * Gerencia a página principal com KPIs
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Wiki.php';
require_once __DIR__ . '/../models/Tarefa.php';

class DashboardController extends BaseController {
    private $wikiModel;
    private $tarefaModel;
    
    public function __construct() {
        parent::__construct();
        $this->wikiModel = new Wiki();
        $this->tarefaModel = new Tarefa();
    }
    
    /**
     * Exibe a página do dashboard
     */
    public function index() {
        // Busca estatísticas
        $wikiStats = $this->wikiModel->getStats();
        $tarefaStats = $this->tarefaModel->getStats();
        $statusDistribution = $this->tarefaModel->getStatusDistribution();
        
        // Busca dados recentes
        $recentWikis = $this->wikiModel->all('data_criacao', 'DESC');
        if (count($recentWikis) > 5) {
            $recentWikis = array_slice($recentWikis, 0, 5);
        }
        
        $recentTarefas = $this->tarefaModel->getRecent(5);
        
        // Calcula KPIs
        $kpis = [
            'total_wikis' => $wikiStats['total_wikis'] ?? 0,
            'wikis_ativas' => $wikiStats['wikis_ativas'] ?? 0,
            'total_secoes' => $wikiStats['total_secoes'] ?? 0,
            'total_tarefas' => $tarefaStats['total'] ?? 0,
            'tarefas_pendentes' => $tarefaStats['pendentes'] ?? 0,
            'tarefas_em_andamento' => $tarefaStats['em_andamento'] ?? 0,
            'tarefas_concluidas' => $tarefaStats['concluidas'] ?? 0,
            'tarefas_alta_prioridade' => $tarefaStats['alta_prioridade'] ?? 0,
        ];
        
        // Calcula taxa de conclusão
        if ($kpis['total_tarefas'] > 0) {
            $kpis['taxa_conclusao'] = round(($kpis['tarefas_concluidas'] / $kpis['total_tarefas']) * 100, 1);
        } else {
            $kpis['taxa_conclusao'] = 0;
        }
        
        // Renderiza a view
        $this->view('dashboard/index', [
            'kpis' => $kpis,
            'statusDistribution' => $statusDistribution,
            'recentWikis' => $recentWikis,
            'recentTarefas' => $recentTarefas,
            'pageTitle' => 'Dashboard'
        ]);
    }
}
