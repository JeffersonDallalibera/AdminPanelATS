<div class="page-header">
    <h1>Gerar Wiki</h1>
    <p class="subtitle">Upload de arquivo .txt para criar uma nova wiki</p>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Upload de Arquivo</h2>
    </div>
    <div class="card-body">
        <form action="/preview_wiki.php" method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="arquivo">Selecione o arquivo .txt:</label>
                <input type="file" id="arquivo" name="arquivo" accept=".txt" required class="form-control">
                <small class="form-text">
                    Formato do arquivo TXT:<br>
                    - Primeira linha: Título da Wiki<br>
                    - Segunda linha: Descrição (opcional)<br>
                    - Use # para títulos de seção (ex: # Introdução)<br>
                    - Use ## para subtítulos de seção<br>
                    - Demais linhas serão o conteúdo das seções
                </small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Preview da Wiki</button>
                <a href="/dashboard.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Exemplo de Arquivo .txt</h2>
    </div>
    <div class="card-body">
        <pre class="code-block">Manual do Sistema AdminPanelATS
Este é um guia completo para utilizar o sistema

# Introdução
Bem-vindo ao AdminPanelATS! Este sistema permite gerenciar wikis e tarefas de forma eficiente.

# Instalação
Para instalar o sistema, siga os seguintes passos:
1. Configure o banco de dados SQL Server
2. Configure o arquivo de conexão
3. Execute o script database_schema.sql

## Requisitos do Sistema
- PHP 7.4 ou superior
- SQL Server 2016 ou superior
- Apache ou Nginx

# Primeiros Passos
Após a instalação, acesse o dashboard para visualizar os KPIs do sistema.
</pre>
    </div>
</div>
