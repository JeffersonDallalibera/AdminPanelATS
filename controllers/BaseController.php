<?php
/**
 * Classe Base Controller
 * 
 * Classe base que fornece funcionalidades comuns para todos os controllers
 */

class BaseController {
    protected $config;
    
    /**
     * Construtor - Carrega as configurações
     */
    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';
        
        // Define o timezone
        date_default_timezone_set($this->config['timezone']);
    }
    
    /**
     * Renderiza uma view
     * 
     * @param string $view Nome da view
     * @param array $data Dados para passar para a view
     */
    protected function view($view, $data = []) {
        // Extrai os dados para variáveis
        extract($data);
        
        // Inclui o header
        require_once __DIR__ . '/../views/layouts/header.php';
        
        // Inclui a view específica
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View não encontrada: {$view}");
        }
        
        // Inclui o footer
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Retorna JSON
     * 
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redireciona para uma URL
     * 
     * @param string $url
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Valida upload de arquivo
     * 
     * @param array $file Array $_FILES
     * @param array $allowedExtensions Extensões permitidas
     * @param int $maxSize Tamanho máximo em bytes
     * @return array ['success' => bool, 'message' => string, 'filename' => string]
     */
    protected function validateUpload($file, $allowedExtensions = ['txt'], $maxSize = 5242880) {
        // Verifica se há erros no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erro no upload do arquivo.'];
        }
        
        // Verifica o tamanho
        if ($file['size'] > $maxSize) {
            $maxSizeMB = $maxSize / 1024 / 1024;
            return ['success' => false, 'message' => "Arquivo muito grande. Máximo: {$maxSizeMB}MB"];
        }
        
        // Verifica a extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Tipo de arquivo não permitido.'];
        }
        
        // Gera nome único para o arquivo
        $filename = uniqid() . '_' . basename($file['name']);
        
        return ['success' => true, 'filename' => $filename];
    }
    
    /**
     * Sanitiza string para prevenir XSS
     * 
     * @param string $string
     * @return string
     */
    protected function sanitize($string) {
        return htmlspecialchars(strip_tags($string), ENT_QUOTES, 'UTF-8');
    }
}
