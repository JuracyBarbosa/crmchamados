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
	<h2><i class="fa-sharp fa-solid fa-pen-to-square"></i> Reavaliar nota do Chamado</h2>
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
			<label>Nota avaliada:</label>
			<span><b><? echo $chamado['nota'];?></b></span>
		</div>
		<div class="form-chamado">
			<label>Descrição do chamado:</label>
			<span><? echo $chamado['descricao']; ?></span>
		</div>
	</div>
	<form method="post" enctype="multipart/form-data">
		<?
        if (isset($_POST['acao'])) {
			@$nota = $_POST['nota'];
            $status = 5;
            $acao = 'revalidar';

            if (@$_POST['nota'] == '') {
                painel::alert('aviso', 'Escolha uma nova nota para este chamado');
            } else {
                update::revalidanota($status, $nota, $idchamado);
                alert::alertaRevalidaChamado($idchamado, $acao, null);
            }

			//Enviar e-mail
			// $mail = new Email();

			// $dadosEnvio = select::dadosparaenvio($idchamado);
			// foreach ($dadosEnvio as $key => $resultEmail)
			// 	$mailSolicitante = $resultEmail['email'];
			// 	$mailNome = $resultEmail['solicitante'];
			// 	$IDmessage = $resultEmail['idmessage'];
			// 	$assunto = $resultEmail['descbreve'];
			// 	$descricao = $resultEmail['descricao'];

			// 	if ($mailSolicitante == '') {
			// 		alert::alertaRespondeChamado($idchamado, $acao,null);
			// 	} else {
			// 	$mail->addAttachment($nameAnexo);
			// 	$mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
			// 	$mail->addCustomHeader('In-Reply-To', $IDmessage);
			// 	$mail->addAdress($mailSolicitante, $mailNome);
			// 	$mail->addAdressCC(EMAIL_TI, 'TI');

			// 	$img = '../images/logo.png';
			// 	$mail->addEmbeddedImage($img, 'logo_ref');

			// 	$info = array('assunto' => $assunto, 'corpo' => $resposta2 . parametros::assinaturach('abrechamado', NULL, NULL, NULL, NULL, NULL, NULL));

			// 	$mail->formatarEmail($info);

			// 	if ($mail->enviarEmail()) {
			// 		alert::alertaRespondeChamado($idchamado, $acao, null);
			// 	}
			// 	#Fim do envio do e-mail.
			// }
		}
		?>
		<div class="form-chamado">
            <label>Nota:</label>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-sad-cry" style="color: red;"></i><br /><input type="radio" name="nota" value="1" /><label>1</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-sad-tear" style="color: #FF4200;"></i><br /><input type="radio" name="nota" value="2" /><label>2</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-frown-open" style="color: #FF8B00;"></i><br /><input type="radio" name="nota" value="3" /><label>3</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-frown" style="color: #FFBD00;"></i><br /><input type="radio" name="nota" value="4" /><label>4</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-meh" style="color: #D4D712;"></i><br /><input type="radio" name="nota" value="5" /><label>5</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-squint" style="color: #AAD712;"></i><br /><input type="radio" name="nota" value="6" /><label>6</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-beam-sweat" style="color: #AEFF00;"></i><br /><input type="radio" name="nota" value="7" /><label>7</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-wide" style="color: #84D712;"></i><br /><input type="radio" name="nota" value="8" /><label>8</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-wink" style="color: #1CE800;"></i><br /><input type="radio" name="nota" value="9" /><label>9</label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-stars" style="color: green;"></i><br /><input type="radio" name="nota" value="10" /><label>10</label>
            </div>
        </div>
        <div class="clear"></div>

		<div class="form_group btn-atualiza">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datah" value="<? echo $data ?>" />
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
		</div>
	</form>
</div>