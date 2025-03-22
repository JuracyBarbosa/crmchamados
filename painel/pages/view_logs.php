<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Redireciona para a página de login
    exit;
}

require '../painel/api/config/log_handler.php'; // Inclui o arquivo de manipulação de logs

// Diretório onde estão os logs
$logDir = __DIR__ . '/../logs/';

// Processa o pedido de limpeza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logFile'])) {
    $logFile = basename($_POST['logFile']); // Evita injeção de diretório
    clearLog($logFile); // Limpa o log usando a função existente
    header("Location: view_logs?success=1&view=" . urlencode($logFile)); // Redireciona para manter o estado
    exit;
}

// Lista todos os arquivos de log no diretório
$logFiles = array_filter(scandir($logDir), function ($file) use ($logDir) {
    return is_file($logDir . $file) && preg_match('/\\.log$/', $file);
});

// Mensagem de sucesso
$successMessage = isset($_GET['success']) ? "Log limpo com sucesso!" : null;

// Identifica se há um log para visualizar
$currentViewLog = isset($_GET['view']) ? basename($_GET['view']) : null;
$logContent = $currentViewLog ? readLog($currentViewLog) : null;
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: rgb(184, 4, 4);
        color: white;
    }

    .success {
        color: green;
        margin-bottom: 10px;
    }

    .log-view {
        background: #333;
        color: #eee;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
    }

    button {
        padding: 10px 15px;
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    a {
        text-decoration: none;
        color: #007bff;
    }

    a:hover {
        color: #0056b3;
    }
</style>

<h1>Logs do Sistema</h1>

<?php if ($successMessage): ?>
    <p class="success"><?= htmlspecialchars($successMessage) ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Arquivo de Log</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logFiles as $logFile): ?>
            <tr>
                <td><?= htmlspecialchars($logFile) ?></td>
                <td>
                    <!-- Formulário para limpar o log -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="logFile" value="<?= htmlspecialchars($logFile) ?>">
                        <button type="submit">Limpar</button>
                    </form>
                    <!-- Link para visualizar o log -->
                    <a href="view_logs?view=<?= urlencode($logFile) ?>">Visualizar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($currentViewLog): ?>
    <h2>Visualizar Log: <?= htmlspecialchars($currentViewLog) ?></h2>
    <pre class="log-view"><?= htmlspecialchars($logContent) ?></pre>
<?php endif; ?>