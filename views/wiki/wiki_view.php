<?php if (isset($error)): ?>
    <div class="page-header">
        <h1>Wiki não encontrada</h1>
    </div>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <a href="/dashboard.php" class="btn btn-primary">Voltar ao Dashboard</a>
<?php else: ?>
    <div class="page-header">
        <h1><?php echo htmlspecialchars($wiki['titulo']); ?></h1>
        <p class="subtitle"><?php echo htmlspecialchars($wiki['descricao'] ?? ''); ?></p>
        <div class="page-meta">
            <span>📅 Criado em: <?php echo date('d/m/Y H:i', strtotime($wiki['data_criacao'])); ?></span>
            <?php if ($wiki['data_atualizacao'] !== $wiki['data_criacao']): ?>
                <span>📝 Atualizado em: <?php echo date('d/m/Y H:i', strtotime($wiki['data_atualizacao'])); ?></span>
            <?php endif; ?>
            <span>📄 Arquivo: <?php echo htmlspecialchars($wiki['arquivo_original'] ?? 'N/A'); ?></span>
        </div>
    </div>
    
    <div class="wiki-navigation">
        <a href="/listar_wikis.php" class="btn btn-secondary">← Voltar para lista</a>
        <a href="/dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
    
    <?php if (!empty($wiki['secoes'])): ?>
        <div class="wiki-content">
            <!-- Índice de Seções -->
            <div class="card wiki-index">
                <div class="card-header">
                    <h2>📑 Índice</h2>
                </div>
                <div class="card-body">
                    <ul class="index-list">
                        <?php foreach ($wiki['secoes'] as $secao): ?>
                            <li class="index-item nivel-<?php echo $secao['nivel']; ?>">
                                <a href="#secao-<?php echo $secao['id']; ?>">
                                    <?php echo htmlspecialchars($secao['titulo']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Conteúdo das Seções -->
            <?php foreach ($wiki['secoes'] as $secao): ?>
                <div id="secao-<?php echo $secao['id']; ?>" class="card wiki-section">
                    <div class="card-header">
                        <h<?php echo $secao['nivel'] + 1; ?> class="section-title">
                            <?php echo $secao['nivel'] === 1 ? '📌' : '📍'; ?>
                            <?php echo htmlspecialchars($secao['titulo']); ?>
                        </h<?php echo $secao['nivel'] + 1; ?>>
                    </div>
                    <div class="card-body">
                        <div class="section-content">
                            <?php 
                            // Converte quebras de linha em <br> e mantém a formatação
                            echo nl2br(htmlspecialchars($secao['conteudo'])); 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Esta wiki não possui seções ainda.
        </div>
    <?php endif; ?>
    
    <div class="wiki-navigation">
        <a href="/listar_wikis.php" class="btn btn-secondary">← Voltar para lista</a>
        <a href="/dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
<?php endif; ?>
