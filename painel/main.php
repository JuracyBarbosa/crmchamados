<?
//include '../config.php';
include 'pages/header.php';  // Include the header

if (isset($_GET['loggout'])) {
	Painel::loggout();
}

// Main content of the dashboard
?>
<?php
// Verifique se a URL corresponde ao endpoint API
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'requisicaochamados') !== false) {
    // Não carregue o layout
    return;
}
?>
<script>
    const ANEXO_CHAMADO = "<?php echo ANEXO_CHAMADO; ?>";
    const INCLUDE_PATH_PAINEL = "<?php echo INCLUDE_PATH_PAINEL; ?>";
    const INCLUDE_PATH_PAINEL_PAGES = "<?php echo INCLUDE_PATH_PAINEL_PAGES; ?>";
</script>
<script src="<?php echo INCLUDE_PATH ?>js/jquery-3.5.1.min.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/jquery.mask.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/main.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/sweetalert2.all.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>js/fontawesome6.js"></script>
<script src="<? echo INCLUDE_PATH_PAINEL ?>js/chamados.js"></script>

<?php
include 'pages/footer.php';  // Include the footer

?>
