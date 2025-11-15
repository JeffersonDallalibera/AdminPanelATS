<?php
/**
 * API: Retorna todas as tarefas ou filtra por status
 */

require_once __DIR__ . '/../../controllers/TarefaController.php';

$controller = new TarefaController();

if (isset($_GET['status'])) {
    $controller->getTarefasByStatus();
} else {
    $controller->getTarefas();
}
