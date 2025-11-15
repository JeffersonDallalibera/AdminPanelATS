<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'AdminPanelATS'; ?> - AdminPanelATS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/dashboard.php">AdminPanelATS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="/dashboard.php" class="<?php echo (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'dashboard') !== false) ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="/gerar_wiki.php" class="<?php echo (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'gerar_wiki') !== false) ? 'active' : ''; ?>">Gerar Wiki</a></li>
                <li><a href="/listar_wikis.php" class="<?php echo (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'listar_wikis') !== false) ? 'active' : ''; ?>">Wikis</a></li>
                <li><a href="/monitor_tarefas.php" class="<?php echo (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'monitor_tarefas') !== false) ? 'active' : ''; ?>">Monitor de Tarefas</a></li>
            </ul>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="container">
