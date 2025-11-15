<div class="page-header">
    <h1>📋 Monitor de Tarefas</h1>
    <p class="subtitle">Acompanhamento em tempo real das tarefas</p>
</div>

<div class="page-actions">
    <button id="btnNovaTarefa" class="btn btn-primary">➕ Nova Tarefa</button>
    <button id="btnAtualizarTarefas" class="btn btn-secondary">🔄 Atualizar</button>
    <span id="ultimaAtualizacao" class="text-muted"></span>
</div>

<!-- Estatísticas -->
<div id="statsContainer" class="kpi-grid">
    <div class="kpi-card kpi-secondary">
        <div class="kpi-icon">📋</div>
        <div class="kpi-content">
            <h3 id="statTotal">0</h3>
            <p>Total de Tarefas</p>
        </div>
    </div>
    
    <div class="kpi-card kpi-warning">
        <div class="kpi-icon">⏳</div>
        <div class="kpi-content">
            <h3 id="statPendentes">0</h3>
            <p>Pendentes</p>
        </div>
    </div>
    
    <div class="kpi-card kpi-info">
        <div class="kpi-icon">⚙️</div>
        <div class="kpi-content">
            <h3 id="statEmAndamento">0</h3>
            <p>Em Andamento</p>
        </div>
    </div>
    
    <div class="kpi-card kpi-success">
        <div class="kpi-icon">✅</div>
        <div class="kpi-content">
            <h3 id="statConcluidas">0</h3>
            <p>Concluídas</p>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="filters">
    <label>
        <input type="radio" name="filtroStatus" value="" checked> Todas
    </label>
    <label>
        <input type="radio" name="filtroStatus" value="pendente"> Pendentes
    </label>
    <label>
        <input type="radio" name="filtroStatus" value="em_andamento"> Em Andamento
    </label>
    <label>
        <input type="radio" name="filtroStatus" value="concluida"> Concluídas
    </label>
</div>

<!-- Lista de Tarefas -->
<div id="tarefasContainer">
    <div class="loading">Carregando tarefas...</div>
</div>

<!-- Modal: Nova Tarefa -->
<div id="modalNovaTarefa" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Nova Tarefa</h2>
            <button class="modal-close" onclick="fecharModalNovaTarefa()">&times;</button>
        </div>
        <form id="formNovaTarefa" class="modal-body">
            <div class="form-group">
                <label for="novaTarefaTitulo">Título:</label>
                <input type="text" id="novaTarefaTitulo" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="novaTarefaDescricao">Descrição:</label>
                <textarea id="novaTarefaDescricao" rows="4" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="novaTarefaPrioridade">Prioridade:</label>
                <select id="novaTarefaPrioridade" class="form-control">
                    <option value="baixa">Baixa</option>
                    <option value="media" selected>Média</option>
                    <option value="alta">Alta</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Criar Tarefa</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModalNovaTarefa()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Configuração de auto-atualização
let autoUpdateInterval = null;
const AUTO_UPDATE_SECONDS = 10; // Atualiza a cada 10 segundos

// Inicializa ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    carregarTarefas();
    carregarStats();
    iniciarAutoUpdate();
    
    // Event listeners
    document.getElementById('btnNovaTarefa').addEventListener('click', abrirModalNovaTarefa);
    document.getElementById('btnAtualizarTarefas').addEventListener('click', function() {
        carregarTarefas();
        carregarStats();
    });
    document.getElementById('formNovaTarefa').addEventListener('submit', criarTarefa);
    
    // Filtros
    document.querySelectorAll('input[name="filtroStatus"]').forEach(radio => {
        radio.addEventListener('change', carregarTarefas);
    });
});

// Carrega as tarefas via AJAX
function carregarTarefas() {
    const filtro = document.querySelector('input[name="filtroStatus"]:checked').value;
    const url = filtro ? `/api/tarefas.php?status=${filtro}` : '/api/tarefas.php';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTarefas(data.tarefas);
                atualizarTempoAtualizacao();
            } else {
                console.error('Erro ao carregar tarefas:', data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            document.getElementById('tarefasContainer').innerHTML = 
                '<div class="alert alert-danger">Erro ao carregar tarefas. Verifique a conexão.</div>';
        });
}

// Renderiza a lista de tarefas
function renderizarTarefas(tarefas) {
    const container = document.getElementById('tarefasContainer');
    
    if (tarefas.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>Nenhuma tarefa encontrada.</p></div>';
        return;
    }
    
    let html = '<div class="tarefas-list">';
    
    tarefas.forEach(tarefa => {
        const statusClass = getStatusClass(tarefa.status);
        const prioridadeClass = `priority-${tarefa.prioridade}`;
        
        html += `
            <div class="tarefa-card ${statusClass}">
                <div class="tarefa-header">
                    <h3>${escapeHtml(tarefa.titulo)}</h3>
                    <span class="priority ${prioridadeClass}">${capitalizarPrimeira(tarefa.prioridade)}</span>
                </div>
                <div class="tarefa-body">
                    <p>${escapeHtml(tarefa.descricao || 'Sem descrição')}</p>
                </div>
                <div class="tarefa-footer">
                    <div class="tarefa-meta">
                        <span>📅 ${formatarData(tarefa.data_criacao)}</span>
                        <span class="badge badge-${getStatusBadgeClass(tarefa.status)}">
                            ${formatarStatus(tarefa.status)}
                        </span>
                    </div>
                    <div class="tarefa-actions">
                        ${renderizarAcoes(tarefa)}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Renderiza os botões de ação conforme o status
function renderizarAcoes(tarefa) {
    let acoes = '';
    
    if (tarefa.status === 'pendente') {
        acoes += `<button onclick="alterarStatus(${tarefa.id}, 'em_andamento')" class="btn btn-sm btn-warning">Iniciar</button>`;
    }
    
    if (tarefa.status === 'em_andamento') {
        acoes += `<button onclick="alterarStatus(${tarefa.id}, 'concluida')" class="btn btn-sm btn-success">Concluir</button>`;
        acoes += `<button onclick="alterarStatus(${tarefa.id}, 'pendente')" class="btn btn-sm btn-secondary">Pausar</button>`;
    }
    
    if (tarefa.status === 'concluida') {
        acoes += `<button onclick="alterarStatus(${tarefa.id}, 'em_andamento')" class="btn btn-sm btn-warning">Reabrir</button>`;
    }
    
    return acoes;
}

// Altera o status de uma tarefa
function alterarStatus(id, novoStatus) {
    fetch('/api/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, status: novoStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            carregarTarefas();
            carregarStats();
        } else {
            alert('Erro ao atualizar status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar status da tarefa.');
    });
}

// Carrega estatísticas
function carregarStats() {
    fetch('/api/stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                document.getElementById('statTotal').textContent = data.stats.total || 0;
                document.getElementById('statPendentes').textContent = data.stats.pendentes || 0;
                document.getElementById('statEmAndamento').textContent = data.stats.em_andamento || 0;
                document.getElementById('statConcluidas').textContent = data.stats.concluidas || 0;
            }
        })
        .catch(error => console.error('Erro ao carregar stats:', error));
}

// Modal de nova tarefa
function abrirModalNovaTarefa() {
    document.getElementById('modalNovaTarefa').style.display = 'flex';
}

function fecharModalNovaTarefa() {
    document.getElementById('modalNovaTarefa').style.display = 'none';
    document.getElementById('formNovaTarefa').reset();
}

// Cria uma nova tarefa
function criarTarefa(e) {
    e.preventDefault();
    
    const dados = {
        titulo: document.getElementById('novaTarefaTitulo').value,
        descricao: document.getElementById('novaTarefaDescricao').value,
        prioridade: document.getElementById('novaTarefaPrioridade').value
    };
    
    fetch('/api/criar_tarefa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fecharModalNovaTarefa();
            carregarTarefas();
            carregarStats();
        } else {
            alert('Erro ao criar tarefa: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar tarefa.');
    });
}

// Auto-update
function iniciarAutoUpdate() {
    autoUpdateInterval = setInterval(() => {
        carregarTarefas();
        carregarStats();
    }, AUTO_UPDATE_SECONDS * 1000);
}

function atualizarTempoAtualizacao() {
    const agora = new Date();
    const texto = `Última atualização: ${agora.toLocaleTimeString('pt-BR')}`;
    document.getElementById('ultimaAtualizacao').textContent = texto;
}

// Funções auxiliares
function getStatusClass(status) {
    const classes = {
        'pendente': 'tarefa-pendente',
        'em_andamento': 'tarefa-em-andamento',
        'concluida': 'tarefa-concluida'
    };
    return classes[status] || '';
}

function getStatusBadgeClass(status) {
    const classes = {
        'pendente': 'secondary',
        'em_andamento': 'warning',
        'concluida': 'success'
    };
    return classes[status] || 'secondary';
}

function formatarStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatarData(dataString) {
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function capitalizarPrimeira(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
