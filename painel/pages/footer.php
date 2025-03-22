<?php
// Verifique se a URL corresponde ao endpoint API
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'requisicaochamados') !== false) {
    // Não carregue o layout
    return;
}
?>
<footer>
    <div class="center">
        <p>Todos os Direitos Reservados!!</p>
    </div>
</footer>
</body>
</html>