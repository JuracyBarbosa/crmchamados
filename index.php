<? include('config.php'); ?>
<? site::updateUsuarioOnline(); ?>
<? site::contadorUsuarios(); ?>

<?
$infoSite = MySql::conectar()->prepare("SELECT * FROM tbl_site_config");
$infoSite->execute();
$infoSite = $infoSite->fetch();
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="<? echo INCLUDE_PATH; ?>favicon.ico" type="image/x-icon" />
	<meta name="Description" content="Descrição do site">
	<meta name="Keywords" content="palavra_chave do site">
	
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<? echo INCLUDE_PATH; ?>css/fontawesome_v5.13.css">
	<link href="<? echo INCLUDE_PATH; ?>css/style.css" rel="stylesheet" />
	
	<title><? echo $infoSite['titulo']; ?></title>
</head>

<body>
	<base base="<? echo INCLUDE_PATH; ?>"/>
	<?
		$url = isset($_GET['url']) ? $_GET['url'] : 'home';
		switch ($url) {
			case 'depoimentos':
				echo '<target target="depoimentos"/>';
				break;
				
			case 'servicos':
				echo '<target target="servicos"/>';
				break;
		}
	?>
	
	<div class="sucesso">Formulário enviado com Sucesso!!</div>
	<div class="overlay_loading">
		<img src="<? echo INCLUDE_PATH ?>images/ajax-loader.gif" />
	</div>
	
	<header><!--Cabeçalho-->
		<div class="center"><!--Center-->
			<div class="logo left"><a href="<? echo INCLUDE_PATH; ?>"><img src="<? echo INCLUDE_PATH ?>images/logo.png"></a></div>
			<nav class="desktop right">
				<ul>
					<li><a href="<? echo INCLUDE_PATH; ?>">Inicio</a></li>
					<!--DELETAR<li><a href="#">TI CPD</a>
						<ul>
							<li><a realtime="abrirchamado" href="<? echo INCLUDE_PATH; ?>abrir_chamado">Chamados</a></li>
						</ul>
					</li>-->
					<li><a href="<? echo INCLUDE_PATH; ?>servicos">Serviços</a></li>
					<li><a realtime="login" href="<? echo INCLUDE_PATH_PAINEL; ?>">Login</a></li>
				</ul>
			</nav>
			<nav class="mobile right">
				<div class="botao_menu_mobile">
					<i class="fas fa-bars"></i>
				</div>
				<ul>
					<li><a href="<? echo INCLUDE_PATH; ?>">Inicio</a></li>
					<li><a href="<? echo INCLUDE_PATH; ?>depoimentos">Sobre</a></li>
					<li><a href="<? echo INCLUDE_PATH; ?>servicos">Serviços</a></li>
					<li><a realtime="contato" href="<? echo INCLUDE_PATH; ?>contato">Contato</a></li>
				</ul>
			</nav>
			<div class="clear"></div>
		</div><!--FIM Center-->
	</header><!--FIM Cabeçalho-->
	
	<div class="container_principal">
	<?
		if(file_exists('pages/'.$url.'.php')) {
			include('pages/'.$url.'.php');
		}else{
			if($url != 'depoimentos' && $url != 'servicos') {
				$pagina404 = true;
				include('pages/404.php');
			}else{
				include('pages/home.php');
			}
		}
	?>
	</div>
	
	<footer <? if(isset($pagina404) && $pagina404 == true) echo 'class="fixed"'; ?>>
		<div class="center">
			<p>Todos os Direitos Reservados!</p>
		</div>
	</footer>
	<script src="<? echo INCLUDE_PATH; ?>js/jquery-3.5.1.min.js"></script>
	<script src="<? echo INCLUDE_PATH; ?>js/funcoes.js"></script>
	<script src="<? echo INCLUDE_PATH; ?>js/fontawesome6.js"></script>
	<?
		if($url == 'home' || $url == '') {
	?>
	<script src="<? echo INCLUDE_PATH; ?>js/slide.js"></script>
	<?
		}
	?>
	<?
		if($url == 'contato') {
	?>
	<? } ?>
	
</body>
</html>









