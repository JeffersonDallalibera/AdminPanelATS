<?php
/**
 * Controller TarefaController
 * 
 * Gerencia o monitor de tarefas com atualização AJAX
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Tarefa.php';

class TarefaController extends BaseController {
    private $tarefaModel;
    
    public function __construct() {
        parent::__construct();
        $this->tarefaModel = new Tarefa();
    }
    
    /**
     * Exibe o monitor de tarefas
     */
    public function monitor() {
        $this->view('tarefas/monitor_tarefas', [
            'pageTitle' => 'Monitor de Tarefas'
        ]);
    }
    
    /**
     * API: Retorna todas as tarefas em JSON (para AJAX)
     */
    public function getTarefas() {
        $tarefas = $this->tarefaModel->all('data_criacao', 'DESC');
        $this->json(['success' => true, 'tarefas' => $tarefas]);
    }
    
    /**
     * API: Retorna tarefas por status em JSON (para AJAX)
     */
    public function getTarefasByStatus() {
        $status = $_GET['status'] ?? '';
        
        if (empty($status)) {
            $this->json(['success' => false, 'message' => 'Status não informado'], 400);
            return;
        }
        
        $tarefas = $this->tarefaModel->getByStatus($status);
        $this->json(['success' => true, 'tarefas' => $tarefas]);
    }
    
    /**
     * API: Atualiza o status de uma tarefa
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método não permitido'], 405);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($data['id'] ?? 0);
        $status = $this->sanitize($data['status'] ?? '');
        
        if ($id === 0 || empty($status)) {
            $this->json(['success' => false, 'message' => 'Dados inválidos'], 400);
            return;
        }
        
        // Valida status
        $statusValidos = ['pendente', 'em_andamento', 'concluida', 'cancelada'];
        if (!in_array($status, $statusValidos)) {
            $this->json(['success' => false, 'message' => 'Status inválido'], 400);
            return;
        }
        
        $result = $this->tarefaModel->updateStatus($id, $status);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao atualizar status'], 500);
        }
    }
    
    /**
     * API: Cria uma nova tarefa
     */
    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método não permitido'], 405);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $titulo = $this->sanitize($data['titulo'] ?? '');
        $descricao = $this->sanitize($data['descricao'] ?? '');
        $prioridade = $this->sanitize($data['prioridade'] ?? 'media');
        
        if (empty($titulo)) {
            $this->json(['success' => false, 'message' => 'Título é obrigatório'], 400);
            return;
        }
        
        // Valida prioridade
        $prioridadesValidas = ['baixa', 'media', 'alta'];
        if (!in_array($prioridade, $prioridadesValidas)) {
            $prioridade = 'media';
        }
        
        $tarefaData = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'prioridade' => $prioridade,
            'status' => 'pendente'
        ];
        
        $id = $this->tarefaModel->create($tarefaData);
        
        if ($id) {
            $this->json(['success' => true, 'message' => 'Tarefa criada com sucesso', 'id' => $id]);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao criar tarefa'], 500);
        }
    }
    
    /**
     * API: Retorna estatísticas das tarefas
     */
    public function getStats() {
        $stats = $this->tarefaModel->getStats();
        $distribution = $this->tarefaModel->getStatusDistribution();
        
        $this->json([
            'success' => true,
            'stats' => $stats,
            'distribution' => $distribution
        ]);
    }
}
