<?php
/**
 * Configuração de Conexão com SQL Server
 * 
 * Este arquivo contém as configurações de conexão com o banco de dados SQL Server.
 * IMPORTANTE: Em produção, use variáveis de ambiente para dados sensíveis.
 */

return [
    'driver' => 'sqlsrv',
    'host' => 'localhost',
    'port' => '1433',
    'database' => 'AdminPanelATS',
    'username' => 'sa',
    'password' => 'YourStrong@Passw0rd',
    'charset' => 'utf8',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
