<?php
/**
 * Model Tarefa
 * 
 * Gerencia as operações relacionadas às tarefas
 */

require_once __DIR__ . '/BaseModel.php';

class Tarefa extends BaseModel {
    protected $table = 'tarefas';
    
    /**
     * Busca tarefas por status
     * 
     * @param string $status
     * @return array
     */
    public function getByStatus($status) {
        $sql = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY prioridade DESC, data_criacao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca tarefas recentes
     * 
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT TOP {$limit} * FROM {$this->table} ORDER BY data_criacao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Atualiza o status de uma tarefa
     * 
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $data = ['status' => $status, 'data_atualizacao' => date('Y-m-d H:i:s')];
        
        // Se o status for 'concluida', atualiza a data de conclusão
        if ($status === 'concluida') {
            $data['data_conclusao'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Busca estatísticas das tarefas
     * 
     * @return array
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'pendente' THEN 1 END) as pendentes,
                    COUNT(CASE WHEN status = 'em_andamento' THEN 1 END) as em_andamento,
                    COUNT(CASE WHEN status = 'concluida' THEN 1 END) as concluidas,
                    COUNT(CASE WHEN prioridade = 'alta' THEN 1 END) as alta_prioridade
                FROM {$this->table}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Busca distribuição de tarefas por status
     * 
     * @return array
     */
    public function getStatusDistribution() {
        $sql = "SELECT 
                    status,
                    COUNT(*) as quantidade,
                    CAST(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM {$this->table}) AS DECIMAL(5,2)) as percentual
                FROM {$this->table}
                GROUP BY status
                ORDER BY quantidade DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
