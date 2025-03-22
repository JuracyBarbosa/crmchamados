<?
checkAccessPage('abre_chamado', 's', 'denied');
verificachamadoconcluido('chamado');

$solicitante = $_SESSION['iduser'];
$permissao = select::selectedcount('permissoes p', 'p.id_user = ' . $solicitante . ' And p.keyuser = "S"');
echo '<script>window.addEventListener("load", function() {
				var permissao = ' . json_decode($permissao) . ';
				validaocorrencia(permissao);
});
</script>';

?>
<div class="box_content">
	<h2><i class="fas fa-headset"></i>&nbsp; Abrir Chamado</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			$categoria = $_POST['categoria'];
			@$subcategoria = $_POST['subcategoria'];
			@$ocorrencia = $_POST['ocorrencia'];
			$prioridade = $_POST['prioridade'];
			$arquivo = $_FILES['arquivo'];
			$descbreve = $_POST['descbreve'];
			$descricao = $_POST['descricao'];
			$grupoemail = $_POST['grupoemail'];
			$status = $_POST['status'];

			$condicoes = true;

			if ($categoria == '') {
				painel::alert('aviso', 'Escolha a categoria');
				$condicoes = false;
			}

			if ($categoria == 2) {
				if ($subcategoria == '') {
					painel::alert('aviso', 'Você precisa selecionar uma subcategoria');
					$condicoes = false;
				}
				if ($ocorrencia !== NULL) {
					if ($ocorrencia == '') {
						painel::alert('aviso', 'Você precisa selecionar uma ocorrência');
						$condicoes = false;
					} else if (chamado::imagemValida($arquivo, $ocorrencia) == false) {
						$array = array(1, 2);
						if (in_array($ocorrencia, $array)) {
							//seleciona o nome do tipo de ocorrencia parametrizado para verificar se tem anexo.
							$selectocorrencia = select::selected('chamados_ocorrencia cho', 'cho.seq_pla_ocorrencia = ' . $ocorrencia . '');
							foreach ($selectocorrencia as $key => $resultocorrencia) {
								$nomeocorrencia = $resultocorrencia['nome_ocorrencia'];
							}
							painel::alert('aviso', 'Por favor, coloque o anexo da situação do seu chamado de <b>' . $nomeocorrencia . '</b>');
							$condicoes = false;
						} else {
							painel::alert('aviso', 'O tipo de anexo não é valido!<br> Verifique se é uma imagem .PNG, .JPG, .JPEG ou se é um arquivo .PDF e menor que 1.2MB');
							$condicoes = false;
						}
					}
				}
			}

			if ($prioridade == '') {
				painel::alert('aviso', 'Escolha sua prioridade');
				$condicoes = false;
			}

			if ($descbreve == '') {
				painel::alert('aviso', 'Faça uma breve descrição do assunto');
				$condicoes = false;
			}

			if ($descricao == '') {
				painel::alert('aviso', 'Descreva seu problema, dificuldade.');
				$condicoes = false;
			}

			if ($condicoes == true) {
				$seq_pla_chamado = chamado::cadastrarChamado($solicitante, $status, $prioridade, $categoria, $subcategoria, $ocorrencia, $descbreve, $descricao);
				$acao = 'aberturach';
				chamado::uploadFiles($arquivo, $seq_pla_chamado, $solicitante, null, $acao);

				$pegaEmail = select::dadosparaenvio($seq_pla_chamado);
				foreach ($pegaEmail as $key => $emailSolicitante) {
					$mailSolicitante = $emailSolicitante['email'];
					$mailNome = $emailSolicitante['solicitante'];

					// Valida se o solicitante tem e-mail cadastrado.
					if (!$mailSolicitante) {
						alert::alertaEmailNulo($seq_pla_chamado, 'abrirChamado');
					}

					//Enviar e-mail
					$mail = new Email();

					//pega os anexos do e-mail
					$pegaAnexo = select::pegaAnexo($seq_pla_chamado);
					foreach ($pegaAnexo as $key => $resultanexo) {
						$nameAnexo = $resultanexo['nomeanexo'];
						$mail->addAttachment($nameAnexo);
					}

					$IDmessage = $mail->message_ID(MessageID);
					$info = array(
						'assunto' => $descbreve,
						'corpo' => parametros::assinaturach('abrechamado', NULL, $seq_pla_chamado, $descricao, NULL, NULL, $mailNome)
					);
					$mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
					$mail->addAdress($mailSolicitante, $mailNome, $grupoemail);
					$mail->addAdress($grupoemail, 'Em copia');
					$mail->addAdressCC(EMAIL_TI, 'TI');

					$img = '../images/logo.png';
					$mail->addEmbeddedImage($img, 'logo_ref');

					$mail->formatarEmail($info);

					$envioSolicitante = $mail->enviarEmail();
					if ($envioSolicitante) {
						insert::sendmail($IDmessage, 'sent', $seq_pla_chamado);
						alert::alertaAbrirCh($seq_pla_chamado);
					}
				}
			}
		}
		?>

		<div class="form_group">
			<input type="hidden" name="solicitante" value="<? echo $solicitante ?>" />
			<input type="hidden" name="status" value="1" />
		</div>
		<div class="form_group">
			<label for="categoria">Categoria:</label>
			<select id="categoria" name="categoria" onchange="mostrasubcategorias()">
				<option value="" selected="">Selecione. .</option>
				<?
				$categoria = MySql::conectar()->prepare("SELECT * FROM chamados_categoria ORDER BY idcategoria ASC");
				$categoria->execute();
				$categoria = $categoria->fetchAll();
				foreach ($categoria as $key => $value) {
				?>
					<option value="<? echo $value['idcategoria'] ?>"><? echo $value['nomecategoria']; ?></option>
				<? } ?>
			</select>
		</div>
		<div id="selectsubcategoria" style="display: none; padding: 0px 50px;" class="form_group">
			<label for="subcategoria">Subcategoria:</label>
			<select id="subcategoria" name="subcategoria" onchange="mostraOcorrencia()">
			</select>
		</div>
		<div id="selectocorrencia" style="display: none; padding: 0px 50px;" class="form_group">
			<label for="ocorrencia">Ocorrência:</label>
			<select id="ocorrencia" name="ocorrencia">
			</select>
		</div>

		<div class="form_group">
			<label>Prioridade:</label>
			<select name="prioridade">
				<option value="" selected="">Selecione. .</option>
				<?
				$prioridade = MySql::conectar()->prepare("SELECT * FROM prioridade ORDER BY idprioridade ASC");
				$prioridade->execute();
				$prioridade = $prioridade->fetchAll();
				foreach ($prioridade as $key => $value) {
				?>
					<option value="<? echo $value['idprioridade'] ?>"><? echo $value['nomeprioridade']; ?></option>
				<? } ?>
			</select>
		</div>
		<div class="form_group">
			<label>Anexos:</label>
			<input type="file" name="arquivo[]" multiple />
		</div>
		<div class="form_group">
			<label>Descrição breve:</label>
			<input type="text" name="descbreve" value="<? echo @$descbreve ?>" />
		</div>
		<div class="form_group">
			<label>Descrição do chamado:</label>
			<textarea name="descricao"><? echo @$descricao ?></textarea>
		</div>
		<div class="form_group">
			<h2><i class="fa-solid fa-envelopes-bulk"></i> Deseja incluir outro E-mail para receber comunicado:</h2>
			<div class="form_group">
				<input type="radio" name="grupoemail" value="S" onclick="mostragrupoemail(this)" /> Sim
				<input type="radio" name="grupoemail" value="N" onclick="mostragrupoemail(this)" checked /> Não
			</div>
		</div>
		<div id="mostragrupoemail" style="display: none;" class="form_group">
			<div class="form_group">
				<label>Digite E-mail ou grupo de E-mail:</label>
				<input type="text" name="grupoemail" />
			</div>
			<br />
		</div>

		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>