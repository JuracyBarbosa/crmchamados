<?
//Verifica access
checkAccessPage('editasite','s','denied');

//declaro variavel campo ID
$namecampoid = 'idimg';

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$slide = select::seleciona('images',''.$namecampoid.' = ?',array($id));
}else{
	painel::alert('erro','Erro');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Slide</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			
			
			$nomeImg = $_POST['nome'];
			$imagem = $_FILES['imagem'];
			$imagem_atual = $_POST['imagem_atual'];
			$data = date('Y-m-d H:i:s');
			
			if(strrpos($nomeImg, " ") !== false){
					painel::alert('erro','Espaço em branco no nome não é permitido!');
			}else if($nomeImg == ''){
				painel::alert('erro','O campo nome está vázio!');
			}else if($imagem['name'] != ''){
				if(painel::slideValido($imagem)){
					painel::deleteSlide($imagem_atual);
					$imagem = upload::Slide($imagem);
					$arr = ['namecampoid'=>$namecampoid,'nameimg'=>$nomeImg,'slide'=>$imagem, 'datacadastro'=>$data,$namecampoid=>$id,'nome_tabela'=>'images'];
					update::array($arr);
					$slide = select::seleciona('images',''.$namecampoid.' = ?',array($id));
					painel::alert('sucesso','Slide editado junto com a imagem!');
				}else{
					painel::alert('erro','O Formato ou tamanho não são validos!');
				}
			}else if(strrpos($nomeImg, " ") !== false){
					painel::alert('erro','Espaço em branco no nome não é permitido!');
			}else if($nomeImg == ''){
				painel::alert('erro','O campo nome está vázio!');
			}else{
				$imagem = $imagem_atual;
				$arr = ['namecampoid'=>$namecampoid,'nameimg'=>$nomeImg,'slide'=>$imagem, 'datacadastro'=>$data,$namecampoid=>$id,'nome_tabela'=>'images'];
				update::array($arr);
				$slide = select::seleciona('images',''.$namecampoid.' = ?',array($id));
				painel::alert('sucesso','Nome do slide editado com sucesso!');
			}
		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nome" value="<? echo $slide['nameimg']; ?>" />
		</div>
		<div class="form_group">
			<label>Imagem</label>
			<input type="file" name="imagem" />
			<input type="hidden" name="imagem_atual" value="<? echo $slide['slide']; ?>" />
		</div>
		<div class="form_group">
			<input type="submit" name="acao" value="Atualizar" />
		</div>
	</form>
</div>