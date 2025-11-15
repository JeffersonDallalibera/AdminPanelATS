-- ============================================
-- Script de Criação do Banco de Dados
-- AdminPanelATS - SQL Server
-- ============================================

-- Criar banco de dados (executar separadamente se necessário)
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'AdminPanelATS')
BEGIN
    CREATE DATABASE AdminPanelATS;
END
GO

USE AdminPanelATS;
GO

-- ============================================
-- Tabela: wiki
-- Armazena as wikis principais
-- ============================================
IF OBJECT_ID('wiki', 'U') IS NOT NULL
    DROP TABLE wiki;
GO

CREATE TABLE wiki (
    id INT IDENTITY(1,1) PRIMARY KEY,
    titulo NVARCHAR(255) NOT NULL,
    descricao NVARCHAR(MAX),
    arquivo_original NVARCHAR(255),
    data_criacao DATETIME DEFAULT GETDATE(),
    data_atualizacao DATETIME DEFAULT GETDATE(),
    ativo BIT DEFAULT 1
);
GO

-- ============================================
-- Tabela: wiki_secao
-- Armazena as seções de cada wiki
-- ============================================
IF OBJECT_ID('wiki_secao', 'U') IS NOT NULL
    DROP TABLE wiki_secao;
GO

CREATE TABLE wiki_secao (
    id INT IDENTITY(1,1) PRIMARY KEY,
    wiki_id INT NOT NULL,
    titulo NVARCHAR(255) NOT NULL,
    conteudo NVARCHAR(MAX),
    ordem INT DEFAULT 0,
    nivel INT DEFAULT 1,
    FOREIGN KEY (wiki_id) REFERENCES wiki(id) ON DELETE CASCADE
);
GO

-- ============================================
-- Tabela: tarefas
-- Armazena as tarefas do sistema
-- ============================================
IF OBJECT_ID('tarefas', 'U') IS NOT NULL
    DROP TABLE tarefas;
GO

CREATE TABLE tarefas (
    id INT IDENTITY(1,1) PRIMARY KEY,
    titulo NVARCHAR(255) NOT NULL,
    descricao NVARCHAR(MAX),
    status NVARCHAR(50) DEFAULT 'pendente',
    prioridade NVARCHAR(50) DEFAULT 'media',
    data_criacao DATETIME DEFAULT GETDATE(),
    data_atualizacao DATETIME DEFAULT GETDATE(),
    data_conclusao DATETIME NULL
);
GO

-- ============================================
-- Índices para melhor performance
-- ============================================
CREATE INDEX idx_wiki_ativo ON wiki(ativo);
CREATE INDEX idx_wiki_data_criacao ON wiki(data_criacao);
CREATE INDEX idx_wiki_secao_wiki_id ON wiki_secao(wiki_id);
CREATE INDEX idx_wiki_secao_ordem ON wiki_secao(ordem);
CREATE INDEX idx_tarefas_status ON tarefas(status);
CREATE INDEX idx_tarefas_prioridade ON tarefas(prioridade);
GO

-- ============================================
-- Dados de exemplo para testes
-- ============================================

-- Inserir wiki de exemplo
INSERT INTO wiki (titulo, descricao, arquivo_original)
VALUES 
    ('Manual do Sistema', 'Documentação completa do sistema AdminPanelATS', 'manual_sistema.txt'),
    ('Guia de Início Rápido', 'Primeiros passos para usar o sistema', 'guia_inicio.txt');
GO

-- Inserir seções de exemplo
DECLARE @wiki_id1 INT = (SELECT TOP 1 id FROM wiki WHERE titulo = 'Manual do Sistema');
DECLARE @wiki_id2 INT = (SELECT TOP 1 id FROM wiki WHERE titulo = 'Guia de Início Rápido');

INSERT INTO wiki_secao (wiki_id, titulo, conteudo, ordem, nivel)
VALUES 
    (@wiki_id1, 'Introdução', 'Este é o manual completo do sistema AdminPanelATS. Aqui você encontrará todas as informações necessárias para utilizar o sistema.', 1, 1),
    (@wiki_id1, 'Requisitos do Sistema', 'PHP 7.4+, SQL Server 2016+, Apache/Nginx', 2, 1),
    (@wiki_id1, 'Instalação', 'Passos para instalação do sistema.', 3, 1),
    (@wiki_id2, 'Bem-vindo', 'Bem-vindo ao AdminPanelATS!', 1, 1),
    (@wiki_id2, 'Primeiro Acesso', 'Como fazer seu primeiro acesso ao sistema.', 2, 1);
GO

-- Inserir tarefas de exemplo
INSERT INTO tarefas (titulo, descricao, status, prioridade)
VALUES 
    ('Configurar ambiente de desenvolvimento', 'Instalar PHP, SQL Server e configurar o ambiente', 'concluida', 'alta'),
    ('Criar estrutura do banco de dados', 'Definir tabelas e relacionamentos', 'concluida', 'alta'),
    ('Desenvolver dashboard', 'Criar página inicial com KPIs', 'em_andamento', 'alta'),
    ('Implementar upload de wikis', 'Criar funcionalidade de upload e parser de TXT', 'em_andamento', 'media'),
    ('Criar monitor de tarefas', 'Desenvolver tela de monitoramento com AJAX', 'pendente', 'media'),
    ('Adicionar autenticação', 'Sistema de login e permissões', 'pendente', 'baixa');
GO

-- ============================================
-- Views úteis
-- ============================================

-- View: KPIs do Dashboard
IF OBJECT_ID('vw_dashboard_kpis', 'V') IS NOT NULL
    DROP VIEW vw_dashboard_kpis;
GO

CREATE VIEW vw_dashboard_kpis AS
SELECT 
    (SELECT COUNT(*) FROM wiki WHERE ativo = 1) AS total_wikis,
    (SELECT COUNT(*) FROM wiki_secao) AS total_secoes,
    (SELECT COUNT(*) FROM tarefas) AS total_tarefas,
    (SELECT COUNT(*) FROM tarefas WHERE status = 'pendente') AS tarefas_pendentes,
    (SELECT COUNT(*) FROM tarefas WHERE status = 'em_andamento') AS tarefas_em_andamento,
    (SELECT COUNT(*) FROM tarefas WHERE status = 'concluida') AS tarefas_concluidas;
GO

-- View: Estatísticas de Tarefas
IF OBJECT_ID('vw_tarefas_estatisticas', 'V') IS NOT NULL
    DROP VIEW vw_tarefas_estatisticas;
GO

CREATE VIEW vw_tarefas_estatisticas AS
SELECT 
    status,
    COUNT(*) as quantidade,
    CAST(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM tarefas) AS DECIMAL(5,2)) as percentual
FROM tarefas
GROUP BY status;
GO

PRINT 'Banco de dados criado com sucesso!';
PRINT 'Total de wikis: ' + CAST((SELECT COUNT(*) FROM wiki) AS VARCHAR);
PRINT 'Total de seções: ' + CAST((SELECT COUNT(*) FROM wiki_secao) AS VARCHAR);
PRINT 'Total de tarefas: ' + CAST((SELECT COUNT(*) FROM tarefas) AS VARCHAR);
