<?

$usuariosOnline = painel::listarUsuariosOnline();

$pegarVisitasHoje = MySql::conectar()->prepare("SELECT * FROM tbl_admin_visitas");
$pegarVisitasHoje->execute();
$pegarVisitasHoje = $pegarVisitasHoje->rowCount();

$pegarVisitasTotais = MySql::conectar()->prepare("SELECT * FROM tbl_admin_visitas WHERE dia = ?");
$pegarVisitasTotais->execute(array(date('Y-m-d')));
$pegarVisitasTotais = $pegarVisitasTotais->rowCount();

if ($_SESSION['id_access'] == 1){

?>

<div class="box_content w100">
	<h2><i class="fa fa-home"></i> Painel de Controle - <? echo NOME_EMPRESA ?></h2>
	<div class="box_painel">
		<div class="box_painel_single">
			<div class="box_painel_wraper">
				<h2>Usuários Online</h2>
				<p><? echo count($usuariosOnline); ?></p>
			</div>
		</div>
		<div class="box_painel_single">
			<div class="box_painel_wraper">
				<h2>Total de Visitas Hoje</h2>
				<p><? echo $pegarVisitasTotais; ?></p>
			</div>
		</div>
		<div class="box_painel_single">
			<div class="box_painel_wraper">
				<h2>Visitas Totais</h2>
				<p><? echo $pegarVisitasHoje; ?></p>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div class="box_content w50 left">
	<h2><i class="fas fa-users"></i> Usuários Online no Site</h2>
	<div class="table_responsive">
		<div class="row">
			<div class="col">
				<span>IP</span>
			</div>
			<div class="col">
				<span>Ultima Ação</span>
			</div>
			<div class="clear"></div>
		</div>
		<?
			foreach ($usuariosOnline as $key => $value){
				
		?>
		<div class="row">
			<div class="col">
				<span><? echo $value['ip'] ?></span>
			</div>
			<div class="col">
				<span><? echo date('d/m/Y H:i:s',strtotime($value['ultima_acao'])) ?></span>
			</div>
			<div class="clear"></div>
		</div>
		<?}?>
	</div>
</div>
<div class="clear"></div>
<?
// Usuários comuns visualiza

}else if ($_SESSION['id_access'] == 2){

?>

<div class="box_content w100">
	<h2><i class="fa fa-home"></i> Olá! Seja bem vindo ao portal - <? echo $_SESSION['nomeuser'] ?></h2>
</div>
<? } ?>