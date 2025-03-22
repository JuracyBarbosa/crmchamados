<?
//Verifica access
checkAccessPage('editasite','s','denied');

//use arquivo/class WideImage
use WideImage\WideImage;
?>

<div class="box_content">
	<h2><i class="fas fa-images"></i> Cadastrar Slide</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			$nomeImg = $_POST['nome'];
			$imagem = $_FILES['imagem'];
			$data = date('Y-m-d H:i:s');
			
			if($nomeImg == ''){
				painel::alert('erro','O campo nome está vázio!');
			}else if($imagem['name'] = ''){
				painel::alert('erro','A imagem não pode ser vázio!');
			}else{
				if(painel::slideValido($imagem) == false){
					painel::alert('erro','A imagem não é valida! verifique se a imagem é .PNG, .JPG, .JPEG e menor que 1024kb');
				}else if(strrpos($nomeImg, " ") !== false){
					painel::alert('erro','Nome com espaço não é permitido!');
				}else if(painel::nomeImageExists($nomeImg)){
					painel::alert('erro','O nome da imagem já existe!');
				}else{
					//realiza upload da imagem
					$imagem = upload::Slide($imagem);
					//Aqui redimenciona a imagem já feito upload
					WideImage::load('uploads/slides/'.$imagem)->resize(1280)->saveToFile('uploads/slides/'.$imagem);
					$arr = ['nameimg'=>$nomeImg,'slide'=>$imagem, 'datacadastro'=>$data, 'order_id'=>'0','nome_tabela'=>'images'];
					painel::insert($arr);
					alert::uploadSlide($nomeImg);
				}
			}
		}
		?>
		<div class="form_group">
			<label>Nome do Slide:</label>
			<input type="text" name="nome" />
		</div>
		<div class="form_group">
			<label>Imagem:</label>
			<input type="file" name="imagem" />
		</div>
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>