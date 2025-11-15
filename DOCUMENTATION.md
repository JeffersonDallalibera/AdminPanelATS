# Documentação Técnica - AdminPanelATS

## Visão Geral

AdminPanelATS é um sistema de painel administrativo desenvolvido em PHP utilizando SQL Server como banco de dados. O sistema implementa o padrão arquitetural MVC (Model-View-Controller) e oferece funcionalidades para gerenciamento de documentação (wikis) e acompanhamento de tarefas.

## Arquitetura

### Padrão MVC

```
┌─────────────┐      ┌──────────────┐      ┌─────────┐
│   Browser   │─────▶│  Controller  │─────▶│  Model  │
│   (Client)  │◀─────│   (Logic)    │◀─────│  (Data) │
└─────────────┘      └──────────────┘      └─────────┘
                            │
                            ▼
                     ┌──────────┐
                     │   View   │
                     │  (HTML)  │
                     └──────────┘
```

### Componentes Principais

#### 1. Config (Configuração)
- **Database.php**: Classe Singleton para gerenciar conexão PDO com SQL Server
- **config.php**: Configurações gerais da aplicação
- **database.example.php**: Template de configuração do banco de dados

#### 2. Models (Camada de Dados)
- **BaseModel.php**: Classe abstrata com operações CRUD genéricas
- **Wiki.php**: Model para gerenciar wikis
- **Tarefa.php**: Model para gerenciar tarefas

#### 3. Controllers (Camada de Lógica)
- **BaseController.php**: Classe base com funcionalidades comuns
- **DashboardController.php**: Lógica do dashboard
- **WikiController.php**: Lógica de wikis (upload, parse, visualização)
- **TarefaController.php**: Lógica de tarefas e APIs AJAX

#### 4. Views (Camada de Apresentação)
- **layouts/**: Templates de header e footer
- **dashboard/**: Views do dashboard
- **wiki/**: Views de gerenciamento de wikis
- **tarefas/**: Views de tarefas

#### 5. Public (Entrada Pública)
- **Páginas PHP**: Entry points para cada funcionalidade
- **api/**: Endpoints RESTful para AJAX

## Fluxo de Dados

### 1. Geração de Wiki

```
Upload TXT → WikiController::preview()
              ↓
         parseTxtFile()
              ↓
    Extrai título, descrição, seções
              ↓
         Preview View
              ↓
    WikiController::salvar()
              ↓
    Wiki::createWithSections()
              ↓
    Salva no banco (transação)
```

### 2. Monitor de Tarefas (AJAX)

```
Página carrega → monitor_tarefas.php
                      ↓
              JavaScript inicializa
                      ↓
         ┌────────────┴────────────┐
         ▼                         ▼
  GET /api/tarefas.php    GET /api/stats.php
         │                         │
         ▼                         ▼
  TarefaController        TarefaController
         │                         │
         ▼                         ▼
  Tarefa::all()          Tarefa::getStats()
         │                         │
         └────────────┬────────────┘
                      ▼
              Retorna JSON
                      ▼
           Renderiza na tela
                      ▼
         Auto-atualiza (10s)
```

## Banco de Dados

### Esquema de Dados

```sql
┌──────────────┐
│     wiki     │
├──────────────┤
│ id (PK)      │
│ titulo       │
│ descricao    │
│ arquivo_orig │
│ data_criacao │
│ ativo        │
└──────────────┘
       │ 1
       │
       │ N
┌──────────────┐
│ wiki_secao   │
├──────────────┤
│ id (PK)      │
│ wiki_id (FK) │
│ titulo       │
│ conteudo     │
│ ordem        │
│ nivel        │
└──────────────┘

┌──────────────┐
│   tarefas    │
├──────────────┤
│ id (PK)      │
│ titulo       │
│ descricao    │
│ status       │
│ prioridade   │
│ data_criacao │
│ data_conclusao│
└──────────────┘
```

### Índices

Criados para otimizar consultas frequentes:
- `idx_wiki_ativo` - Filtro de wikis ativas
- `idx_wiki_secao_wiki_id` - Join de seções
- `idx_tarefas_status` - Filtro por status
- `idx_tarefas_prioridade` - Filtro por prioridade

## APIs

### GET /api/tarefas.php

Retorna lista de tarefas (com filtro opcional)

**Parâmetros:**
- `status` (opcional): pendente, em_andamento, concluida

**Resposta:**
```json
{
  "success": true,
  "tarefas": [
    {
      "id": 1,
      "titulo": "Tarefa exemplo",
      "status": "pendente",
      "prioridade": "alta",
      "data_criacao": "2025-01-01 10:00:00"
    }
  ]
}
```

### POST /api/criar_tarefa.php

Cria nova tarefa

**Body:**
```json
{
  "titulo": "Nova tarefa",
  "descricao": "Descrição detalhada",
  "prioridade": "media"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Tarefa criada com sucesso",
  "id": 123
}
```

### POST /api/update_status.php

Atualiza status de uma tarefa

**Body:**
```json
{
  "id": 123,
  "status": "em_andamento"
}
```

### GET /api/stats.php

Retorna estatísticas das tarefas

**Resposta:**
```json
{
  "success": true,
  "stats": {
    "total": 50,
    "pendentes": 20,
    "em_andamento": 15,
    "concluidas": 15
  },
  "distribution": [...]
}
```

## Parser de TXT

### Formato Esperado

```
Título da Wiki
Descrição opcional

# Seção Nível 1
Conteúdo da seção

## Subseção Nível 2
Conteúdo da subseção
```

### Algoritmo de Parse

1. Primeira linha não vazia → Título
2. Segunda linha não vazia (sem #) → Descrição
3. Linhas com `#` → Títulos de seção (nível = número de #)
4. Demais linhas → Conteúdo da seção atual

### Exemplo de Código

```php
private function parseTxtFile($filepath) {
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    foreach ($lines as $line) {
        if (preg_match('/^(#+)\s+(.+)$/', $line, $matches)) {
            // Nova seção
            $nivel = strlen($matches[1]);
            $titulo = $matches[2];
        } else {
            // Conteúdo
        }
    }
}
```

## Segurança

### Proteções Implementadas

1. **SQL Injection**
   - Uso de Prepared Statements em todas as queries
   - PDO com emulação desabilitada

2. **XSS (Cross-Site Scripting)**
   - Sanitização de output com `htmlspecialchars()`
   - Método `sanitize()` no BaseController

3. **CSRF (Cross-Site Request Forgery)**
   - Validação de métodos HTTP (GET/POST)
   - (Recomendado adicionar tokens CSRF em produção)

4. **Upload de Arquivos**
   - Validação de extensão (apenas .txt)
   - Validação de tamanho (5MB máx)
   - Nome de arquivo único (previne sobrescrita)

### Exemplo de Sanitização

```php
// Input
$titulo = $this->sanitize($_POST['titulo']);

// Output
echo htmlspecialchars($wiki['titulo']);
```

## Performance

### Otimizações Implementadas

1. **Database:**
   - Índices em campos frequentemente consultados
   - Views para queries complexas
   - Uso de prepared statements (cache de queries)

2. **Singleton Pattern:**
   - Uma única conexão com banco por requisição

3. **AJAX:**
   - Carregamento assíncrono de tarefas
   - Atualização parcial da página

### Métricas Recomendadas

- Tempo de carregamento: < 2s
- Resposta AJAX: < 500ms
- Tamanho da página: < 500KB

## Extensibilidade

### Adicionar Novo Module

1. **Criar Model** (`models/MeuModel.php`):
```php
class MeuModel extends BaseModel {
    protected $table = 'minha_tabela';
    
    public function minhaFuncao() {
        // Lógica específica
    }
}
```

2. **Criar Controller** (`controllers/MeuController.php`):
```php
class MeuController extends BaseController {
    private $model;
    
    public function __construct() {
        parent::__construct();
        $this->model = new MeuModel();
    }
    
    public function index() {
        $dados = $this->model->all();
        $this->view('minha_view/index', ['dados' => $dados]);
    }
}
```

3. **Criar View** (`views/minha_view/index.php`):
```php
<div class="page-header">
    <h1>Minha Funcionalidade</h1>
</div>
<!-- HTML da página -->
```

4. **Criar Entry Point** (`public/minha_pagina.php`):
```php
<?php
require_once __DIR__ . '/../controllers/MeuController.php';
$controller = new MeuController();
$controller->index();
```

## Testes

### Testes Manuais Recomendados

1. **Dashboard:**
   - [ ] KPIs exibem valores corretos
   - [ ] Listagens mostram dados recentes
   - [ ] Links funcionam

2. **Wiki:**
   - [ ] Upload aceita .txt
   - [ ] Parser extrai seções corretamente
   - [ ] Preview exibe formatação
   - [ ] Salvar cria registros no banco
   - [ ] Visualização mostra conteúdo

3. **Tarefas:**
   - [ ] Criar nova tarefa
   - [ ] Alterar status
   - [ ] Filtros funcionam
   - [ ] Auto-update atualiza a lista
   - [ ] Estatísticas corretas

### Testes de Segurança

1. **SQL Injection:**
```
# Tentar: ' OR '1'='1
# Deve: Retornar erro ou nada
```

2. **XSS:**
```
# Tentar: <script>alert('XSS')</script>
# Deve: Exibir como texto, não executar
```

## Troubleshooting

Ver `TROUBLESHOOTING.md` para problemas comuns e soluções.

## Roadmap Futuro

Possíveis melhorias:

- [ ] Sistema de autenticação e autorização
- [ ] Versionamento de wikis
- [ ] Comentários em wikis
- [ ] Notificações de tarefas
- [ ] Export de wikis para PDF
- [ ] API RESTful completa
- [ ] Interface responsiva mobile-first
- [ ] Testes automatizados (PHPUnit)
- [ ] CI/CD pipeline
- [ ] Docker containerization

## Referências

- [PHP Documentation](https://www.php.net/docs.php)
- [PDO SQL Server](https://www.php.net/manual/en/ref.pdo-sqlsrv.php)
- [MVC Pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
- [OWASP Security](https://owasp.org/www-project-top-ten/)

---

**Versão:** 1.0  
**Última atualização:** 2025  
**Autor:** Jefferson Dallalibera
