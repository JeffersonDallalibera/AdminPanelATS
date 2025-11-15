<div class="page-header">
    <h1>Dashboard</h1>
    <p class="subtitle">Visão geral do sistema</p>
</div>

<!-- KPIs Cards -->
<div class="kpi-grid">
    <div class="kpi-card kpi-primary">
        <div class="kpi-icon">📚</div>
        <div class="kpi-content">
            <h3><?php echo $kpis['total_wikis']; ?></h3>
            <p>Total de Wikis</p>
            <small><?php echo $kpis['wikis_ativas']; ?> ativas</small>
        </div>
    </div>
    
    <div class="kpi-card kpi-info">
        <div class="kpi-icon">📄</div>
        <div class="kpi-content">
            <h3><?php echo $kpis['total_secoes']; ?></h3>
            <p>Seções de Wiki</p>
            <small>Total de seções criadas</small>
        </div>
    </div>
    
    <div class="kpi-card kpi-warning">
        <div class="kpi-icon">📋</div>
        <div class="kpi-content">
            <h3><?php echo $kpis['total_tarefas']; ?></h3>
            <p>Total de Tarefas</p>
            <small><?php echo $kpis['tarefas_pendentes']; ?> pendentes</small>
        </div>
    </div>
    
    <div class="kpi-card kpi-success">
        <div class="kpi-icon">✅</div>
        <div class="kpi-content">
            <h3><?php echo $kpis['taxa_conclusao']; ?>%</h3>
            <p>Taxa de Conclusão</p>
            <small><?php echo $kpis['tarefas_concluidas']; ?> concluídas</small>
        </div>
    </div>
</div>

<!-- Estatísticas de Tarefas -->
<div class="dashboard-section">
    <h2>Distribuição de Tarefas por Status</h2>
    <div class="stats-grid">
        <?php if (!empty($statusDistribution)): ?>
            <?php foreach ($statusDistribution as $stat): ?>
                <?php 
                $statusClass = '';
                $statusIcon = '';
                switch($stat['status']) {
                    case 'pendente':
                        $statusClass = 'status-pendente';
                        $statusIcon = '⏳';
                        break;
                    case 'em_andamento':
                        $statusClass = 'status-em-andamento';
                        $statusIcon = '⚙️';
                        break;
                    case 'concluida':
                        $statusClass = 'status-concluida';
                        $statusIcon = '✅';
                        break;
                    default:
                        $statusClass = 'status-default';
                        $statusIcon = '📌';
                }
                ?>
                <div class="stat-card <?php echo $statusClass; ?>">
                    <div class="stat-icon"><?php echo $statusIcon; ?></div>
                    <div class="stat-info">
                        <h4><?php echo ucfirst(str_replace('_', ' ', $stat['status'])); ?></h4>
                        <p class="stat-number"><?php echo $stat['quantidade']; ?></p>
                        <p class="stat-percent"><?php echo $stat['percentual']; ?>%</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">Nenhuma tarefa cadastrada ainda.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Wikis Recentes -->
<div class="dashboard-section">
    <h2>Wikis Recentes</h2>
    <?php if (!empty($recentWikis)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Data de Criação</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentWikis as $wiki): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($wiki['titulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($wiki['descricao'] ?? '', 0, 100)); ?><?php echo strlen($wiki['descricao'] ?? '') > 100 ? '...' : ''; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($wiki['data_criacao'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $wiki['ativo'] ? 'success' : 'danger'; ?>">
                                    <?php echo $wiki['ativo'] ? 'Ativa' : 'Inativa'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="/wiki_view.php?id=<?php echo $wiki['id']; ?>" class="btn btn-sm btn-primary">Visualizar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>Nenhuma wiki criada ainda.</p>
            <a href="/gerar_wiki.php" class="btn btn-primary">Criar primeira Wiki</a>
        </div>
    <?php endif; ?>
</div>

<!-- Tarefas Recentes -->
<div class="dashboard-section">
    <h2>Tarefas Recentes</h2>
    <?php if (!empty($recentTarefas)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTarefas as $tarefa): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($tarefa['titulo']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $tarefa['status'] === 'concluida' ? 'success' : 
                                        ($tarefa['status'] === 'em_andamento' ? 'warning' : 'secondary'); 
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $tarefa['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority priority-<?php echo $tarefa['prioridade']; ?>">
                                    <?php echo ucfirst($tarefa['prioridade']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?></td>
                            <td>
                                <a href="/monitor_tarefas.php" class="btn btn-sm btn-primary">Ver detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>Nenhuma tarefa criada ainda.</p>
            <a href="/monitor_tarefas.php" class="btn btn-primary">Criar primeira Tarefa</a>
        </div>
    <?php endif; ?>
</div>
