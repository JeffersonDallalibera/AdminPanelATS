<div class="page-header">
    <h1>Preview da Wiki</h1>
    <p class="subtitle">Revise o conteúdo antes de salvar no banco de dados</p>
</div>

<form action="/salvar_wiki.php" method="POST" class="form">
    <input type="hidden" name="filename" value="<?php echo htmlspecialchars($filename ?? ''); ?>">
    
    <div class="card">
        <div class="card-header">
            <h2>Informações da Wiki</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo ?? ''); ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="3" class="form-control"><?php echo htmlspecialchars($descricao ?? ''); ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Seções da Wiki</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($secoes)): ?>
                <?php foreach ($secoes as $index => $secao): ?>
                    <div class="secao-preview" data-nivel="<?php echo $secao['nivel']; ?>">
                        <input type="hidden" name="secoes[<?php echo $index; ?>][nivel]" value="<?php echo $secao['nivel']; ?>">
                        <input type="hidden" name="secoes[<?php echo $index; ?>][ordem]" value="<?php echo $secao['ordem']; ?>">
                        
                        <div class="form-group">
                            <label>
                                <?php echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $secao['nivel'] - 1); ?>
                                <?php echo $secao['nivel'] === 1 ? '📌' : '📍'; ?>
                                Título da Seção (Nível <?php echo $secao['nivel']; ?>):
                            </label>
                            <input type="text" name="secoes[<?php echo $index; ?>][titulo]" 
                                   value="<?php echo htmlspecialchars($secao['titulo']); ?>" 
                                   required class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Conteúdo:</label>
                            <textarea name="secoes[<?php echo $index; ?>][conteudo]" 
                                      rows="4" class="form-control"><?php echo htmlspecialchars($secao['conteudo']); ?></textarea>
                        </div>
                        
                        <?php if ($index < count($secoes) - 1): ?>
                            <hr class="secao-divider">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">Nenhuma seção foi identificada no arquivo.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-success">💾 Salvar Wiki</button>
        <a href="/gerar_wiki.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
