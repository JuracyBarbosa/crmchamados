<?
//Verifica access
checkAccessPage('abre_chamado','s','denied');

if (isset($_GET['id'])) {
	$idchamado = (int)$_GET['id'];
	$chamado = select::seleciona('chamados', 'idchamado = ?', array($idchamado));
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa-sharp fa-solid fa-pen-to-square"></i> Responder Chamado ao Atendente</h2>
	<div class="form_group edit-chamado">
		<div class="form-chamado">
			<label>Solicitante:</label>
			<span><?
					$result = $chamado['id_user'];
					$sql = select::selected('usuarios', 'iduser = ' . $result . '');
					foreach ($sql as $key => $query) {
						echo $query['nomeuser'];
					} ?></span>
			<label>Departamento:</label>
			<span><?
					$dp = $query['id_dept'];
					$dpt = select::selected('departamento', 'iddept = ' . $dp . '');
					foreach ($dpt as $key => $result) {
						echo $result['nomedept'];
					} ?>
			</span>
		</div>
		<div class="form-chamado">
			<label>Data Abertura:</label>
			<span><?
					$data = date_create($chamado['data_abertura']);
					echo date_format($data, "d/m/Y H:i"); ?>
			</span>
			<label>Prioridade:</label>
			<span>
				<? $prioridade = $chamado['id_prioridade'];
				$prioridade = select::selected('prioridade', 'idprioridade =' . $prioridade . '');
				foreach ($prioridade as $key => $resultprd) {
					echo $resultprd['nomeprioridade'];
				}
				?>
			</span>
		</div>
		<div class="form-chamado">
			<label>Atendente:</label>
			<span><? $atendente = $chamado['id_operator'];
			$atendente = select::selected('operadores','idoperator = '.$atendente.'');
			foreach($atendente as $key => $atendente){
			echo $atendente['surname'];
			}?></span>
		</div>
		<div class="form-chamado">
			<label>Descrição:</label>
			<span><? echo $chamado['descricao']; ?></span>
		</div>
	</div>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			$arquivo = $_FILES['arquivo'];
			$solicitante = $_SESSION['iduser'];
			$seq_pla_status = 3;

			if ($_POST['resposta'] == '') {
				painel::alert('erro', 'Não pode ficar vazio a resposta.!');
			} else
				if ($_SESSION['iduser'] == $chamado['id_user']) {
				$acao = 'resposta_solicitante';
				$resposta1 = '';
				$resposta2 = $_POST['resposta'];
			}
			insert::movimentacaochamado($idchamado, $atendente['idoperator'], $resposta1, $resposta2);
			$dadosAtualizar = [
				'id_status' => $seq_pla_status,
			];
			update::atualizarChamado($dadosAtualizar, $idchamado);
			$nameAnexo = chamado::uploadFiles($arquivo, $idchamado, $solicitante, NULL, $acao);

			$dadosEnvio = select::dadosparaenvio($idchamado);
			foreach ($dadosEnvio as $key => $resultEmail) {
				$mailSolicitante = $resultEmail['email'];
				$mailNome = $resultEmail['solicitante'];
				$IDmessage = $resultEmail['idmessage'];
				$assunto = $resultEmail['descbreve'];
				$descricao = $resultEmail['descricao'];

				if ($mailSolicitante == '') {
					alert::alertaEmailNulo($idchamado, 'responderAtendente');
				} else {

					//Enviar e-mail
					$mail = new Email();
					$mail->addAttachment($nameAnexo);
					$mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
					$mail->addCustomHeader('In-Reply-To', $IDmessage);
					$mail->addAdress($mailSolicitante, $mailNome);
					$mail->addAdressCC(EMAIL_TI, 'TI');

					$img = '../images/logo.png';
					$mail->addEmbeddedImage($img, 'logo_ref');

					$info = array(
						'assunto' => $assunto,
						'corpo' => parametros::assinaturach('responderatendente', NULL, $idchamado, NULL, NULL, NULL, NULL)
					);

					$mail->formatarEmail($info);
					
					$envioOperador = $mail->enviarEmail();
					if ($envioOperador) {
						alert::alertaRespondeChamado($idchamado, $acao, null);
					}
				}
			}
		}
		?>
		<div>
			<textarea name="resposta"></textarea>
		</div>
		<div class="form-chamado">
			<label>Imagem:</label>
			<input type="file" name="arquivo[]" multiple="multiple" />
		</div>
		<div class="form_group btn-atualiza">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datah" value="<? echo $data ?>" />
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
		</div>
	</form>
</div>