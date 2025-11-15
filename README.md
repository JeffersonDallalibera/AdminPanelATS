# AdminPanelATS

Sistema de painel administrativo em PHP + SQL Server com funcionalidades de gerenciamento de wikis e monitoramento de tarefas.

## 📋 Características

- **Dashboard com KPIs**: Visualização de métricas e estatísticas do sistema
- **Gerador de Wikis**: Upload de arquivos .txt com parser automático para criar wikis estruturadas
- **Monitor de Tarefas**: Sistema de acompanhamento de tarefas com atualização AJAX em tempo real
- **Arquitetura MVC**: Organização simples e modular do código
- **SQL Server**: Conexão via PDO com suporte a SQL Server

## 🚀 Tecnologias

- PHP 7.4+
- SQL Server 2016+
- PDO (PHP Data Objects)
- JavaScript (AJAX para atualizações em tempo real)
- HTML5 & CSS3

## 📁 Estrutura do Projeto

```
AdminPanelATS/
├── config/              # Configurações e conexão com banco de dados
│   ├── Database.php     # Classe de conexão (Singleton pattern)
│   ├── config.php       # Configurações gerais
│   └── database.example.php  # Exemplo de configuração do banco
├── models/              # Models (camada de dados)
│   ├── BaseModel.php    # Model base com operações CRUD
│   ├── Wiki.php         # Model de wikis
│   └── Tarefa.php       # Model de tarefas
├── views/               # Views (camada de apresentação)
│   ├── layouts/         # Templates de layout
│   ├── dashboard/       # Views do dashboard
│   ├── wiki/            # Views de wikis
│   └── tarefas/         # Views de tarefas
├── controllers/         # Controllers (camada de lógica)
│   ├── BaseController.php      # Controller base
│   ├── DashboardController.php # Controller do dashboard
│   ├── WikiController.php      # Controller de wikis
│   └── TarefaController.php    # Controller de tarefas
├── public/              # Arquivos públicos acessíveis
│   ├── api/             # Endpoints API para AJAX
│   ├── dashboard.php    # Página do dashboard
│   ├── gerar_wiki.php   # Página de geração de wiki
│   ├── wiki_view.php    # Visualização de wiki
│   └── monitor_tarefas.php  # Monitor de tarefas
├── assets/              # Recursos estáticos
│   ├── css/             # Estilos CSS
│   ├── js/              # Scripts JavaScript
│   └── uploads/         # Diretório de uploads
└── database_schema.sql  # Script de criação do banco de dados
```

## 🔧 Instalação

### Pré-requisitos

1. PHP 7.4 ou superior com extensões:
   - PDO
   - sqlsrv (SQL Server driver)
   - mbstring

2. SQL Server 2016 ou superior

3. Servidor web (Apache ou Nginx)

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/JeffersonDallalibera/AdminPanelATS.git
   cd AdminPanelATS
   ```

2. **Configure o banco de dados**
   
   Execute o script SQL no SQL Server:
   ```bash
   sqlcmd -S localhost -U sa -P YourPassword -i database_schema.sql
   ```

3. **Configure a conexão com o banco**
   
   Copie e edite o arquivo de configuração:
   ```bash
   cp config/database.example.php config/database.php
   ```
   
   Edite `config/database.php` com suas credenciais:
   ```php
   return [
       'driver' => 'sqlsrv',
       'host' => 'localhost',
       'port' => '1433',
       'database' => 'AdminPanelATS',
       'username' => 'seu_usuario',
       'password' => 'sua_senha',
       // ...
   ];
   ```

4. **Configure permissões**
   ```bash
   chmod 755 -R assets/uploads/
   ```

5. **Configure o servidor web**
   
   Para Apache, o arquivo `.htaccess` já está configurado.
   
   Para Nginx, adicione ao seu arquivo de configuração:
   ```nginx
   location / {
       try_files $uri $uri/ /public/$uri /public/index.php?$query_string;
   }
   ```

6. **Acesse o sistema**
   
   Abra seu navegador em: `http://localhost/dashboard.php`

## 📖 Uso

### Dashboard

Acesse `/dashboard.php` para visualizar:
- Total de wikis e seções
- Estatísticas de tarefas
- Wikis e tarefas recentes
- Taxa de conclusão de tarefas

### Gerar Wiki

1. Acesse `/gerar_wiki.php`
2. Faça upload de um arquivo .txt seguindo o formato:
   ```
   Título da Wiki
   Descrição da wiki (opcional)
   
   # Seção Principal
   Conteúdo da seção principal
   
   ## Subseção
   Conteúdo da subseção
   ```
3. Revise o preview
4. Salve a wiki no banco de dados

### Monitor de Tarefas

Acesse `/monitor_tarefas.php` para:
- Visualizar todas as tarefas
- Criar novas tarefas
- Atualizar status das tarefas
- Acompanhar estatísticas em tempo real (atualização a cada 10 segundos)

### Visualizar Wikis

- Lista de wikis: `/listar_wikis.php`
- Visualizar wiki específica: `/wiki_view.php?id={id}`

## 🗄️ Banco de Dados

### Tabelas Principais

**wiki**
- Armazena informações das wikis
- Campos: id, titulo, descricao, arquivo_original, data_criacao, data_atualizacao, ativo

**wiki_secao**
- Armazena seções de cada wiki
- Campos: id, wiki_id, titulo, conteudo, ordem, nivel

**tarefas**
- Armazena as tarefas do sistema
- Campos: id, titulo, descricao, status, prioridade, data_criacao, data_atualizacao, data_conclusao

### Views Úteis

- `vw_dashboard_kpis`: KPIs do dashboard
- `vw_tarefas_estatisticas`: Estatísticas de tarefas por status

## 🔐 Segurança

- Validação de entrada de dados
- Proteção contra SQL Injection via PDO prepared statements
- Sanitização de saída para prevenir XSS
- Validação de upload de arquivos
- Headers de segurança configurados

## 🛠️ Desenvolvimento

### Adicionar Nova Funcionalidade

1. Crie o model em `models/`
2. Crie o controller em `controllers/`
3. Crie a view em `views/`
4. Crie a página pública em `public/`

### Padrões de Código

- PSR-4 para autoloading de classes
- Comentários em português
- Código limpo e modular
- Separação de responsabilidades (MVC)

## 📝 API Endpoints

Para integração via AJAX:

- `GET /api/tarefas.php` - Lista todas as tarefas
- `GET /api/tarefas.php?status={status}` - Lista tarefas por status
- `POST /api/criar_tarefa.php` - Cria nova tarefa
- `POST /api/update_status.php` - Atualiza status de tarefa
- `GET /api/stats.php` - Retorna estatísticas

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é fornecido como está, sem garantias.

## 👤 Autor

Jefferson Dallalibera

## 🙏 Agradecimentos

Projeto desenvolvido como sistema de painel administrativo com funcionalidades de gestão de conhecimento (wikis) e tarefas.