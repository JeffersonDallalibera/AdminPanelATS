<?php
/**
 * Controller WikiController
 * 
 * Gerencia upload, parser e visualização de wikis
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Wiki.php';

class WikiController extends BaseController {
    private $wikiModel;
    private $uploadDir;
    
    public function __construct() {
        parent::__construct();
        $this->wikiModel = new Wiki();
        $this->uploadDir = __DIR__ . '/../assets/uploads/';
    }
    
    /**
     * Exibe o formulário de geração de wiki
     */
    public function gerarWiki() {
        $this->view('wiki/gerar_wiki', [
            'pageTitle' => 'Gerar Wiki'
        ]);
    }
    
    /**
     * Processa o upload e faz preview do arquivo TXT
     */
    public function preview() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/gerar_wiki.php');
            return;
        }
        
        // Valida o upload
        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            $this->view('wiki/gerar_wiki', [
                'error' => 'Erro ao fazer upload do arquivo.',
                'pageTitle' => 'Gerar Wiki'
            ]);
            return;
        }
        
        $validation = $this->validateUpload($_FILES['arquivo']);
        if (!$validation['success']) {
            $this->view('wiki/gerar_wiki', [
                'error' => $validation['message'],
                'pageTitle' => 'Gerar Wiki'
            ]);
            return;
        }
        
        // Move o arquivo para o diretório de uploads
        $filename = $validation['filename'];
        $uploadPath = $this->uploadDir . $filename;
        
        if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadPath)) {
            $this->view('wiki/gerar_wiki', [
                'error' => 'Erro ao salvar arquivo.',
                'pageTitle' => 'Gerar Wiki'
            ]);
            return;
        }
        
        // Faz o parse do arquivo
        $parseResult = $this->parseTxtFile($uploadPath);
        
        // Renderiza o preview
        $this->view('wiki/preview_wiki', [
            'titulo' => $parseResult['titulo'],
            'descricao' => $parseResult['descricao'],
            'secoes' => $parseResult['secoes'],
            'filename' => $filename,
            'pageTitle' => 'Preview da Wiki'
        ]);
    }
    
    /**
     * Salva a wiki no banco de dados
     */
    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/gerar_wiki.php');
            return;
        }
        
        $titulo = $this->sanitize($_POST['titulo'] ?? '');
        $descricao = $this->sanitize($_POST['descricao'] ?? '');
        $filename = $this->sanitize($_POST['filename'] ?? '');
        
        if (empty($titulo) || empty($filename)) {
            $this->view('wiki/gerar_wiki', [
                'error' => 'Dados inválidos.',
                'pageTitle' => 'Gerar Wiki'
            ]);
            return;
        }
        
        // Reconstrói as seções do POST
        $secoes = [];
        if (isset($_POST['secoes'])) {
            foreach ($_POST['secoes'] as $index => $secao) {
                $secoes[] = [
                    'titulo' => $this->sanitize($secao['titulo']),
                    'conteudo' => $this->sanitize($secao['conteudo']),
                    'ordem' => $index + 1,
                    'nivel' => intval($secao['nivel'] ?? 1)
                ];
            }
        }
        
        // Salva no banco
        $wikiData = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'arquivo_original' => $filename
        ];
        
        $wikiId = $this->wikiModel->createWithSections($wikiData, $secoes);
        
        if ($wikiId) {
            // Redireciona para visualização
            $this->redirect("/wiki_view.php?id={$wikiId}");
        } else {
            $this->view('wiki/gerar_wiki', [
                'error' => 'Erro ao salvar wiki no banco de dados.',
                'pageTitle' => 'Gerar Wiki'
            ]);
        }
    }
    
    /**
     * Exibe uma wiki
     */
    public function view() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id === 0) {
            $this->redirect('/dashboard.php');
            return;
        }
        
        $wiki = $this->wikiModel->getWithSections($id);
        
        if (!$wiki) {
            $this->view('wiki/wiki_view', [
                'error' => 'Wiki não encontrada.',
                'pageTitle' => 'Wiki não encontrada'
            ]);
            return;
        }
        
        $this->view('wiki/wiki_view', [
            'wiki' => $wiki,
            'pageTitle' => $wiki['titulo']
        ]);
    }
    
    /**
     * Lista todas as wikis
     */
    public function listar() {
        $wikis = $this->wikiModel->all('data_criacao', 'DESC');
        
        $this->view('wiki/listar_wikis', [
            'wikis' => $wikis,
            'pageTitle' => 'Lista de Wikis'
        ]);
    }
    
    /**
     * Parser de arquivo TXT para extrair título, descrição e seções
     * 
     * Formato esperado:
     * Primeira linha: Título da Wiki
     * Segunda linha: Descrição (opcional)
     * Linhas vazias são ignoradas
     * Linhas começando com # ou ## são títulos de seção
     * Demais linhas são conteúdo da seção
     * 
     * @param string $filepath
     * @return array
     */
    private function parseTxtFile($filepath) {
        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        
        $titulo = '';
        $descricao = '';
        $secoes = [];
        $currentSecao = null;
        $lineIndex = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Pula linhas vazias
            if (empty($line)) {
                continue;
            }
            
            // Primeira linha não vazia é o título
            if (empty($titulo)) {
                $titulo = $line;
                continue;
            }
            
            // Segunda linha não vazia é a descrição (se não começar com #)
            if (empty($descricao) && !preg_match('/^#+\s/', $line)) {
                $descricao = $line;
                continue;
            }
            
            // Identifica seções com # ou ##
            if (preg_match('/^(#+)\s+(.+)$/', $line, $matches)) {
                // Salva seção anterior se existir
                if ($currentSecao !== null) {
                    $secoes[] = $currentSecao;
                }
                
                // Cria nova seção
                $nivel = strlen($matches[1]); // Conta quantos # tem
                $currentSecao = [
                    'titulo' => $matches[2],
                    'conteudo' => '',
                    'nivel' => $nivel,
                    'ordem' => count($secoes) + 1
                ];
            } else {
                // Adiciona conteúdo à seção atual
                if ($currentSecao !== null) {
                    $currentSecao['conteudo'] .= $line . "\n";
                } else {
                    // Se ainda não tem seção, cria uma seção "Introdução"
                    $currentSecao = [
                        'titulo' => 'Introdução',
                        'conteudo' => $line . "\n",
                        'nivel' => 1,
                        'ordem' => 1
                    ];
                }
            }
        }
        
        // Adiciona a última seção
        if ($currentSecao !== null) {
            $secoes[] = $currentSecao;
        }
        
        // Se não tiver título, usa o nome do arquivo
        if (empty($titulo)) {
            $titulo = pathinfo($filepath, PATHINFO_FILENAME);
        }
        
        return [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'secoes' => $secoes
        ];
    }
}
