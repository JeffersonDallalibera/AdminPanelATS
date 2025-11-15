<?php
/**
 * Classe Database - Gerenciamento de Conexão com SQL Server
 * 
 * Esta classe implementa o padrão Singleton para garantir uma única conexão
 * com o banco de dados SQL Server usando PDO.
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;

    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        $configFile = __DIR__ . '/../config/database.php';
        
        // Se não existir database.php, usa o example
        if (!file_exists($configFile)) {
            $configFile = __DIR__ . '/../config/database.example.php';
        }
        
        $this->config = require $configFile;
        $this->connect();
    }

    /**
     * Estabelece a conexão com o banco de dados SQL Server
     */
    private function connect() {
        try {
            $dsn = sprintf(
                "%s:Server=%s,%s;Database=%s",
                $this->config['driver'],
                $this->config['host'],
                $this->config['port'],
                $this->config['database']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    /**
     * Retorna a instância única da classe (Singleton)
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     * 
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne deserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
