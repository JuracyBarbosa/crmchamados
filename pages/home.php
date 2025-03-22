<section class="banner_container">
	<div style="background-image: url('<? echo INCLUDE_PATH; ?>images/bg_slide_01.png');" class="banner_single"></div>
	<div style="background-image: url('<? echo INCLUDE_PATH; ?>images/bg_slide_02.png');" class="banner_single"></div>
	<div style="background-image: url('<? echo INCLUDE_PATH; ?>images/bg_slide_03.png');" class="banner_single"></div>
		<div class="overlay"></div><!--Overlay-->
		<div class="center">
			<!--<form method="post">
				<h2>Qual seu Email ?</h2>
				<input type="email" name="email" required/>
				<input type="hidden" name="identificador" value="form_home"/>
				<input type="submit" name="acao" value="Cadastrar!">
			</form>-->
			<span>
				<h2>Intranet</h2>
			</span>
		</div>
	<div class="bullets">
		
	</div>
	</section><!--FIM Section Banner Principal-->
	
	<section class="descricao_autor"><!--Descricao do autor-->
		<div class="center">
			<div class="w50 left">
				<h2><? echo $infoSite['nome_autor']; ?></h2>
				<p><? echo $infoSite['descricao_autor']; ?></p>
			</div>
			<div class="w50 left">
				<!--Imagem-->
				<img class="right" src="<? echo INCLUDE_PATH; ?>images/bgautor.png" />
			</div>
			<div class="clear"></div>
		</div>
	</section><!--FIM Descricao do autor-->
	
	<section class="especialidades"><!--Especialidades-->
		<div class="center">
			<h2 class="title">Especialidades</h2>
			<div class="w33 left box_especialidade">
				<h3><i class="<? echo $infoSite['icone1']; ?>"></i></h3>
				<h4><? echo $infoSite['titulo1']; ?></h4>
				<p><? echo $infoSite['descricao1']; ?></p>
			</div>
			<div class="w33 left box_especialidade">
				<h3><i class="<? echo $infoSite['icone2']; ?>"></i></h3>
				<h4><? echo $infoSite['titulo2']; ?></h4>
				<p><? echo $infoSite['descricao2']; ?></p>
			</div>
			<div class="w33 left box_especialidade">
				<h3><i class="<? echo $infoSite['icone3']; ?>"></i></h3>
				<h4><? echo $infoSite['titulo3']; ?></h4>
				<p><? echo $infoSite['descricao3']; ?></p>
			</div>
			<div class="clear"></div>
		</div>
	</section><!-- FIMEspecialidades-->
	
	<section class="extras"><!--Extras-->
		<div class="center">
			<div id="depoimentos" class="w50 left depoimentos_container">
				<h2 class="title">Depoimentos dos nossos Clientes</h2>
				<?
				$sql = MySql::conectar()->prepare("SELECT * FROM tbl_site_depoimentos ORDER BY order_id ASC LIMIT 3");
				$sql->execute();
				$depoimentos = $sql->fetchAll();
				foreach($depoimentos as $key => $value){
					$data = date_create($value['data']);
				?>
				<div class="depoimento_single">
					<p class="depoimento_descricao">"<? echo $value['depoimento']; ?>"</p>
					<p class="nome_autor"><? echo $value['nome']; ?> - <? echo date_format($data,"d/m/Y"); ?></p>
				</div>
				<?}?>
			</div>
			<div id="servicos" class="w50 left servicos_container">
				<h2 class="title">Serviços</h2>
				<div class="servicos">
					<ul>
						<?
						$sql = MySql::conectar()->prepare("SELECT * FROM tbl_site_servicos ORDER BY order_id ASC LIMIT 3");
						$sql->execute();
						$servicos = $sql->fetchAll();
						foreach($servicos as $key => $value){
							$data = date_create($value['data']);
						?>
						<li><? echo $value['servico']; ?></li>
						<?}?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</section><!--FIM Extras-->