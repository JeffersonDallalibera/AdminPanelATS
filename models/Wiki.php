<?php
/**
 * Model Wiki
 * 
 * Gerencia as operações relacionadas às wikis
 */

require_once __DIR__ . '/BaseModel.php';

class Wiki extends BaseModel {
    protected $table = 'wiki';
    
    /**
     * Busca wikis ativas
     * 
     * @return array
     */
    public function getActive() {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY data_criacao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Busca uma wiki com suas seções
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithSections($id) {
        $wiki = $this->findById($id);
        if (!$wiki) {
            return false;
        }
        
        $sql = "SELECT * FROM wiki_secao WHERE wiki_id = ? ORDER BY ordem ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $wiki['secoes'] = $stmt->fetchAll();
        
        return $wiki;
    }
    
    /**
     * Cria uma wiki com suas seções
     * 
     * @param array $wikiData Dados da wiki
     * @param array $sections Array de seções
     * @return int|false ID da wiki criada
     */
    public function createWithSections($wikiData, $sections) {
        try {
            $this->db->beginTransaction();
            
            // Cria a wiki
            $wikiId = $this->create($wikiData);
            
            if (!$wikiId) {
                throw new Exception("Erro ao criar wiki");
            }
            
            // Cria as seções
            $sqlSection = "INSERT INTO wiki_secao (wiki_id, titulo, conteudo, ordem, nivel) VALUES (?, ?, ?, ?, ?)";
            $stmtSection = $this->db->prepare($sqlSection);
            
            foreach ($sections as $index => $section) {
                $stmtSection->execute([
                    $wikiId,
                    $section['titulo'],
                    $section['conteudo'],
                    $section['ordem'] ?? $index + 1,
                    $section['nivel'] ?? 1
                ]);
            }
            
            $this->db->commit();
            return $wikiId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Busca estatísticas das wikis
     * 
     * @return array
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_wikis,
                    COUNT(CASE WHEN ativo = 1 THEN 1 END) as wikis_ativas,
                    (SELECT COUNT(*) FROM wiki_secao) as total_secoes
                FROM {$this->table}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
