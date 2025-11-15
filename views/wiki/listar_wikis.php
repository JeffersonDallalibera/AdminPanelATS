<div class="page-header">
    <h1>📚 Lista de Wikis</h1>
    <p class="subtitle">Todas as wikis do sistema</p>
</div>

<div class="page-actions">
    <a href="/gerar_wiki.php" class="btn btn-primary">➕ Nova Wiki</a>
    <a href="/dashboard.php" class="btn btn-secondary">Dashboard</a>
</div>

<?php if (!empty($wikis)): ?>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Arquivo</th>
                    <th>Data de Criação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wikis as $wiki): ?>
                    <tr>
                        <td><?php echo $wiki['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($wiki['titulo']); ?></strong></td>
                        <td>
                            <?php 
                            $descricao = $wiki['descricao'] ?? '';
                            echo htmlspecialchars(substr($descricao, 0, 100)); 
                            echo strlen($descricao) > 100 ? '...' : ''; 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($wiki['arquivo_original'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($wiki['data_criacao'])); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $wiki['ativo'] ? 'success' : 'danger'; ?>">
                                <?php echo $wiki['ativo'] ? 'Ativa' : 'Inativa'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="/wiki_view.php?id=<?php echo $wiki['id']; ?>" class="btn btn-sm btn-primary">
                                👁️ Visualizar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="table-info">
        <p>Total de wikis: <strong><?php echo count($wikis); ?></strong></p>
    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <h2>Nenhuma wiki encontrada</h2>
        <p>Comece criando sua primeira wiki!</p>
        <a href="/gerar_wiki.php" class="btn btn-primary">➕ Criar primeira Wiki</a>
    </div>
<?php endif; ?>
