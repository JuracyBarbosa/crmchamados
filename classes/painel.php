<?

class painel {
	
	public static function pegacargo($idcargo){
		$sql = MySql::conectar()->prepare("SELECT * FROM cargo WHERE idcargo = $idcargo");
		$sql->execute();

		return $sql->fetchAll();
	}
	
	public static function logado(){
		return isset($_SESSION['login']) ? true : false;
	}
	
	public static function loggout(){
		setcookie('lembrar',true,time()-1,'/');
		session_destroy();
		header('Location: '.INCLUDE_PATH);
	}
	
	public static function carregarPagina(){
		if(isset($_GET['url'])){
			$url = explode('/',$_GET['url']);
			if(file_exists('pages/'.$url[0].'.php')){
				include('pages/'.$url[0].'.php');
			}else{
				//Quando a pagina não existe.
				header('Location: '.INCLUDE_PATH_PAINEL);
			}
		}else{
			include('pages/home.php');
		}
	}
	
	public static function listarUsuariosOnline(){
		self::limparUsuariosOnline();
		$sql = MySql::conectar()->prepare("SELECT * FROM tbl_admin_online");
		$sql->execute();
		return $sql->fetchAll();
	}
	
	public static function limparUsuariosOnline(){
		$date = date('Y-m-d H:i:s');
		$sql = MySql::conectar()->exec("DELETE FROM tbl_admin_online WHERE ultima_acao < '$date' - INTERVAL 1 MINUTE");
	}
	
	public static function alert($tipo,$mensagem){
		if($tipo == 'sucesso'){
			echo '<div class="box_alert sucesso"><i class="fa fa-check"></i> '.$mensagem.'</div>';
		}else if($tipo == 'erro'){
			echo '<div class="box_alert erro"><i class="fa fa-times"></i> '.$mensagem.'</div>';
		}else if($tipo == 'info'){
			echo '<div class="box_alert info"><i class="fa-solid fa-info"></i> '.$mensagem.'</div>';
		}else if($tipo == 'aviso'){
			echo '<div class="box_alert aviso"><i class="fa-solid fa-exclamation"></i> '.$mensagem.'</div>';
		}
	}
	
	public static function avatarValida($imagem){
		if($imagem['type'] == 'avatar/jpeg' ||
		  $imagem['type'] == 'avatar/jpg' ||
		  $imagem['type'] == 'avatar/png' ){
			$tamanho = intval($imagem['size']/1024);
			if($tamanho < 1024){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
	
	/* public static function uploadFile($file){
		$files = $_FILES['avatar']['name'];
		$extensao = pathinfo($files, PATHINFO_EXTENSION);
		$imagemNome = uniqid().'.'.$extensao;
		if(move_uploaded_file($file['tmp_name'],BASE_DIR_PAINEL.'/uploads/'.$imagemNome))
			return $imagemNome;
		else
			return false;
	} */
	
	public static function deleteFile($file){
		@unlink('uploads/'.$file);
	}
	
	public static function slideValido($imagem){
		if($imagem['type'] == 'image/jpeg' ||
		  $imagem['type'] == 'image/jpg' ||
		  $imagem['type'] == 'image/png' ){
			$tamanho = intval($imagem['size']/1024);
			if($tamanho < 950){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function deleteSlide($file){
		@unlink('uploads/slides/'.$file);
	}
	
	public static function insert($arr){
		$certo = true;
		$nome_tabela = $arr['nome_tabela'];
		$query = "INSERT INTO $nome_tabela VALUES (null";
		
		foreach($arr as $key => $value){
			$nome = $key;
			$valor = $value;
			if($nome == 'acao' || $nome == 'nome_tabela')
				continue;
			if($value == ''){
				$certo = false;
				break;
			}
			$query.=",?";
			$parametros[] = $value;
		}
		$query.=")";
		if($certo == true){
			$sql = MySql::conectar()->prepare($query);
			$sql->execute($parametros);
			$lastId = MySql::conectar()->lastInsertId();
			$sql = MySql::conectar()->prepare("UPDATE $nome_tabela SET order_id = ? WHERE idimg = $lastId");
			$sql->execute(array($lastId));
		}
		return $certo;
	}
	
	public static function selectAllpg($tabela,$start = null,$end = null){
		if($start == null && $end == null)
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY order_id ASC");
		else
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY order_id ASC LIMIT $start,$end");
		
		$sql->execute();
		
		return $sql->fetchAll();
	}
	
	public static function redirect($url){
		echo '<script>location.href="'.$url.'"</script>';
		
		die();
	}
	
	/* Metodo especifico para selecionar um registro */
	/* public static function select($table,$query = '',$arr = ''){
		if($query != false){
			$sql = MySql::conectar()->prepare("SELECT * FROM $table WHERE $query");
			$sql->execute($arr);
		}else{
			$sql = MySql::conectar()->prepare("SELECT * FROM $table");
			$sql->execute();
		}
		return $sql->fetch();
	} */
	
	public static function orderItem($tabela,$orderType,$idItem,$namecampoid){
		if($orderType == 'up'){
			$infoItemAtual = select::order($tabela,''.$namecampoid.'=?',array($idItem));
			$order_id = $infoItemAtual['order_id'];
			$itemBefore = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE order_id < $order_id ORDER BY order_id DESC LIMIT 1");
			$itemBefore->execute();
			if($itemBefore->rowCount() == 0)
				return;
			$itemBefore = $itemBefore->fetch();
			update::array(array('nome_tabela'=>$tabela, 'namecampoid'=>$namecampoid, $namecampoid=>$itemBefore[$namecampoid],'order_id'=>$infoItemAtual['order_id']));
			update::array(array('nome_tabela'=>$tabela, 'namecampoid'=>$namecampoid, $namecampoid=>$infoItemAtual[$namecampoid],'order_id'=>$itemBefore['order_id']));

		}else if($orderType == 'down'){
			$infoItemAtual = select::order($tabela,''.$namecampoid.'=?',array($idItem));
			$order_id = $infoItemAtual['order_id'];
			$itemAfter = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE order_id > $order_id ORDER BY order_id ASC LIMIT 1");
			$itemAfter->execute();
			if($itemAfter->rowCount() == 0)
				return;
			$itemAfter = $itemAfter->fetch();
			update::array(array('nome_tabela'=>$tabela, 'namecampoid'=>$namecampoid, $namecampoid=>$itemAfter[$namecampoid],'order_id'=>$infoItemAtual['order_id']));
			update::array(array('nome_tabela'=>$tabela, 'namecampoid'=>$namecampoid, $namecampoid=>$infoItemAtual[$namecampoid],'order_id'=>$itemAfter['order_id']));
		}
	}
	
	public static function nomeImageExists($nomeImg){
		$sql = MySql::conectar()->prepare("SELECT idimg FROM images WHERE nameimg = ?");
		$sql->execute(array($nomeImg));
		if($sql->rowCount() == 1)
			return true;
		else
			return false;
	}
}

class chamado {
	public static function cadastrarChamado($solicitante,$status,$prioridade,$categoria,$subcategoria,$ocorrencia,$descbreve,$descricao){
		if ($subcategoria == '' && $ocorrencia == ''){
			$subcategoria = NULL;
			$ocorrencia = NULL;
		}
		$sql = MySql::conectar()->prepare("INSERT INTO chamados VALUES (null,?,now(),?,?,?,?,?,?,?,null,null,null,null,null,null,null,null,null,null,null,null,null,null)");
		$sql->execute(array($solicitante,$status,$prioridade,$categoria,$subcategoria,$ocorrencia,$descbreve,$descricao));

		$lastId = MySql::conectar()->lastInsertId();

		return $lastId;
	}

	public static function updateatender($arr,$single = false){
		$certo = true;
		$primeiro = false;
		$nome_tabela = $arr['nome_tabela'];
		$query = "UPDATE $nome_tabela SET ";
		
		foreach($arr as $key => $value){
			$nome = $key;
			$valor = $value;
			if($nome == 'atender' || $nome == 'nome_tabela' || $nome == 'idchamado')
				continue;
			if($value == ''){
				$certo = false;
				break;
			}
			
			if($primeiro == false){
				$primeiro = true;
				$query.="$nome=?";
			}else{
				$query.=",$nome=?";
			}
			$parametros[] = $value;
		}
		
		if($certo == true){
			if($single == false){
				$parametros[] = $arr['idchamado'];
				$sql = MySql::conectar()->prepare($query.' WHERE idchamado=?');
				$sql->execute($parametros);
			}else{
				$sql = MySql::conectar()->prepare($query);
				$sql->execute($parametros);
			}
		}
		return $certo;
	}

	public static function uploadFile($file){
		$files = $_FILES['imagem']['name'];
		$extensao = pathinfo($files, PATHINFO_EXTENSION);
		$imagemNome = uniqid().'.'.$extensao;
		if(move_uploaded_file($file['tmp_name'],BASE_DIR_PAINEL.'/uploads/chamados/'.$imagemNome))
			return $imagemNome;
		else
			return false;
	}

	public static function uploadFiles($file, $idchamado, $solicitante, $atendente, $acao){
		// Define o diretório de upload
		$diretorio = BASE_DIR_PAINEL . '/uploads/chamados/';

		// Verifica se o diretório existe, cria se necessário
		if (!is_dir($diretorio)) {
			if (!mkdir($diretorio, 0755, true)) {
				error_log("Falha ao criar o diretório: $diretorio");
				return false;
			}
		}

		// Valida o parâmetro de arquivo
		if (empty($file) || !isset($file['name'])) {
			error_log("Nenhum arquivo válido recebido.");
			return false;
		}

		// Processa cada arquivo enviado
		foreach ($file['name'] as $controle => $nome_arquivo) {
			// Ignora arquivos com erro
			if ($file['error'][$controle] !== UPLOAD_ERR_OK) {
				error_log("Erro ao processar arquivo $nome_arquivo. Código de erro: " . $file['error'][$controle]);
				continue;
			}

			// Obtém a extensão do arquivo
			$extensao = pathinfo($nome_arquivo, PATHINFO_EXTENSION);

			// Define o nome do arquivo com base na ação
			switch ($acao) {
				case 'aberturach':
				case 'resposta_atendente':
				case 'resposta_solicitante':
				case 'devolverchamado':
					$imagemNome = 'chamado-' . $idchamado . '_REG[' . uniqid() . '].' . $extensao;
					break;
				default:
					error_log("Ação inválida: $acao. Arquivo $nome_arquivo ignorado.");
					continue 2;
			}

			// Move o arquivo para o diretório de destino
			$caminhoCompleto = $diretorio . $imagemNome;
			if (move_uploaded_file($file['tmp_name'][$controle], $caminhoCompleto)) {
				error_log("Arquivo $nome_arquivo enviado com sucesso para $caminhoCompleto.");

				// Registra o arquivo no banco de dados
				try {
					$sql = MySql::conectar()->prepare("INSERT INTO anexo VALUES (null, now(), ?, ?, ?, ?, ?, null, null)");
					$sql->execute([$imagemNome, $diretorio, $idchamado, $solicitante, $atendente]);
				} catch (Exception $e) {
					error_log("Erro ao salvar informações do arquivo no banco de dados: " . $e->getMessage());
					return false;
				}
			} else {
				error_log("Falha ao mover o arquivo $nome_arquivo para $caminhoCompleto.");
				return false;
			}
		}

		return true;
	}

	public static function selectchamados($tabela, $sts, $idoperador) {
		if ($idoperador > 0) {
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE id_status = '$sts' AND id_operator = '$idoperador' ORDER BY idchamado DESC");
			$sql->execute();
		} else {
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE id_status = '$sts' ORDER BY idchamado DESC");
			$sql->execute();
		}
		return $sql->fetchAll();
	}

	public static function imagemValida($arquivos, $ocorrencia) {
		// Permitir abrir chamado sem anexo em certas ocorrências
		$permitidoSemAnexo = [1, 2];
		if (in_array($ocorrencia, $permitidoSemAnexo) && empty($arquivos['name'][0])) {
			return false;
		}
	
		// Tipos permitidos
		$tiposPermitidos = [
			'image/jpg', 'image/jpeg', 'image/png',
			'application/pdf', 'text/plain',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		];
	
		// Tamanho máximo em bytes (1.2MB)
		$tamanhoMaximo = 2 * 1024 * 1024;
	
		// Validação de cada arquivo
		foreach ($arquivos['name'] as $indice => $nome) {
			$tipo = $arquivos['type'][$indice] ?? '';
			$erro = $arquivos['error'][$indice] ?? 0;
			$tamanho = $arquivos['size'][$indice] ?? 0;
	
			// Verificar se houve erro no upload
			if ($erro !== UPLOAD_ERR_OK) {
				// Caso especial: arquivo não enviado
				if ($erro === UPLOAD_ERR_NO_FILE) {
					if (in_array($ocorrencia, $permitidoSemAnexo)) {
						return false;
					}
					continue;
				}
				return false; // Outros erros
			}
	
			// Verificar tipo de arquivo
			if (!in_array($tipo, $tiposPermitidos)) {
				return false;
			}
	
			// Verificar tamanho do arquivo
			if ($tamanho > $tamanhoMaximo) {
				return false;
			}
		}
	
		return true; // Todos os arquivos são válidos
	}
}

class verificar {

	public static function valida_dados($dados)
	{
		// Sanitiza strings
		$sanitize = function ($valor) {
			return trim(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'));
		};

		// Valida dados
		if (!is_array($dados)) {
			return $sanitize($dados); // Sanitiza string diretamente
		}

		// Processa arrays recursivamente
		foreach ($dados as $key => $valor) {
			$dados[$key] = is_array($valor)
				? self::valida_dados($valor)  // Chamada recursiva
				: $sanitize($valor);          // Sanitiza valor individual
		}

		return $dados;
	}	
	
	//Verifica se tem usuario cadastrado com mesmo login inserido
	public static function userExists($user){
		$sql = MySql::conectar()->prepare("SELECT iduser FROM usuarios WHERE loginuser = ?");
		$sql->execute(array($user));
		if($sql->rowCount() == 1)
			return true;
		else
			return false;
	}

	//verifica se existe apelido do operador já inserido
	public static function operadorExists($surname, $operador){
		$sql = MySql::conectar()->prepare("SELECT surname, id_user FROM operadores WHERE surname = ? OR id_user = ?");
		$sql->execute(array($surname, $operador));
		if($sql->rowCount() == 1)
			return true;
		else
			return false;
	}

	//verificar se já existe a ocorrência cadastrada
	public static function ocorrenciacadastrada($nomeocorrencia){

		$sql = MySql::conectar()->prepare("SELECT oc.nome_ocorrencia FROM chamados_ocorrencia oc WHERE oc.nome_ocorrencia = ?");
		$sql->execute(array($nomeocorrencia));
		if ($sql->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}
}

class select {
	// select sem validação empty
	public static function select($tabela, $comando) {
		$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE $comando");
		$sql->execute();
	
		return $sql->fetchAll(PDO::FETCH_ASSOC); // Retorna os resultados como um array associativo
	}
	
	// Metodo especifico para selecionar um registro
	public static function seleciona($table,$query = '',$arr = ''){
		if($query != false){
			$sql = MySql::conectar()->prepare("SELECT * FROM $table WHERE $query");
			$sql->execute($arr);
		}else{
			$sql = MySql::conectar()->prepare("SELECT * FROM $table");
			$sql->execute();
		}
		return $sql->fetch();
	}

	public static function selected($tabela,$comando){
		$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE $comando");
		$sql->execute();

		if(empty($sql)){
			return false;
		}else{
			return $sql;
		}
	}

	//Seleciona trazendo resultados
	public static function selecione($tabela, $comando) {
		$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE $comando");
		$sql->execute();
	
		// Obter os resultados como array
		$result = $sql->fetchAll();
	
		if (empty($result)) {
			// Retorna falso se não houver registros
			return false;
		}
	
		// Retorna os resultados
		return $result;
	}

	//Esse seleciona com mais robustes, forma melhorada.
	public static function selecionar($tabela, $condicao, $parametros = [], $alias = null){
		try {
			// Verifica se um alias foi fornecido
			$tableName = $alias ? "`$tabela` AS `$alias`" : "`$tabela`";

			// Construa a consulta com ou sem alias
			$sql = MySql::conectar()->prepare("SELECT * FROM $tableName WHERE $condicao");

			// Execute a consulta com os parâmetros fornecidos
			$sql->execute($parametros);

			// Obtenha os resultados como array associativo
			$result = $sql->fetchAll(PDO::FETCH_ASSOC);

			// Retorna `false` se a consulta não retornar resultados
			return $result ?: false;
		} catch (PDOException $e) {
			// Lança uma exceção para erros na execução da consulta
			throw new Exception("Erro ao executar consulta: " . $e->getMessage());
		}
	}

	//Realiza uma consulta com JOIN
	public static function selectJoin($sql, $parametros = []) {
		try {
			// Conecta ao banco de dados
			$stmt = MySql::conectar()->prepare($sql);
	
			// Executa a consulta com os parâmetros fornecidos
			$stmt->execute($parametros);
	
			// Obtem os resultados como array associativo
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
			// Retorna os resultados ou false caso não haja
			return $result ?: [];
		} catch (PDOException $e) {
			// Lança uma exceção para erros na execução da consulta
			throw new Exception("Erro ao executar consulta: " . $e->getMessage());
		}
	}

	//seleciona operador para editar
	public static function operador($idoperator){
		$sql = MySql::conectar()->prepare("SELECT
			op.idoperator,
			op.datecadastro,
			op.datealteracao,
			op.id_user,
			op.surname,
			( SELECT ac.nomeaccess FROM access ac WHERE ac.idaccess = op.id_access ) AS access,
			op.prestador_servico 
		FROM
			operadores op 
		WHERE
			op.idoperator = $idoperator");

		$sql->execute();
		return $sql->fetch();
	}

	//select usuario que enviou anexo
	public static function useranexo($idchamado){
		$sql = MySql::conectar()->prepare("SELECT
			an.idanexo,
			an.datacadastro,
			an.nomeanexo,
			an.caminho,
			an.id_chamado,
			( SELECT us.loginuser FROM usuarios us WHERE us.iduser = an.id_user) AS solicitante,
			( SELECT op.surname FROM operadores op WHERE op.idoperator = an.id_operator) AS operador,
			an.dataexclusao,
			an.userdelete 
		FROM
			anexo an 
		WHERE
			an.id_chamado = $idchamado");

		$sql->execute();
		return $sql->fetchAll();
	}

	//selected com count
	public static function selectedcount($tabela,$comando){
		$sql = MySql::conectar()->prepare("SELECT * FROM $tabela WHERE $comando");
		$sql->execute();
		return count($sql->fetchAll());
	}

	public static function listorderid($tabela, $id, $start = null, $end = null){
		if($start == null && $end == null){
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY $id' ASC");
		}else{
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY $id ASC LIMIT $start,$end");
		}
		$sql->execute();
		
		return $sql->fetchAll();
	}

	public static function listuseraccess($start = null, $end = null){
		if($start == null && $end == null){
			$sql = MySql::conectar()->prepare("SELECT
			us.iduser AS iduser,
			us.loginuser AS login,
			us.nomeuser AS nome,
			us.datacaduser AS datacadastro,
			( SELECT IF ( pm.abre_chamado = 's', 'Sim', 'Não' ) FROM permissoes pm WHERE us.iduser = pm.id_user ) AS abre_chamado,
			( SELECT IF ( pm.keyuser = 's', 'Sim', 'Não' ) FROM permissoes pm WHERE us.iduser = pm.id_user ) AS keyuser,
			( SELECT IF ( op.id_access = 3, 'Gestor', 'Operador' ) FROM operadores op WHERE us.iduser = op.id_user ) AS operador 
		FROM
			usuarios us 
		ORDER BY
			US.iduser ASC");
		}else{
			$sql = MySql::conectar()->prepare("SELECT
			us.iduser AS iduser,
			us.loginuser AS login,
			us.nomeuser AS nome,
			us.datacaduser AS datacadastro,
			( SELECT IF ( pm.abre_chamado = 's', 'Sim', 'Não' ) FROM permissoes pm WHERE us.iduser = pm.id_user ) AS abre_chamado,
			( SELECT IF ( pm.keyuser = 's', 'Sim', 'Não' ) FROM permissoes pm WHERE us.iduser = pm.id_user ) AS keyuser,
			( SELECT IF ( op.id_access = 3, 'Gestor', 'Operador' ) FROM operadores op WHERE us.iduser = op.id_user ) AS operador 
		FROM
			usuarios us 
		ORDER BY
			US.iduser ASC LIMIT $start,$end");
		}
		$sql->execute();
		
		return $sql->fetchAll();
	}

	//Lista operadores em orderID
	public static function listOperadoresOrderID($start = null, $end = null){
		$sql = MySql::conectar()->prepare("
		SELECT
			op.idoperator,
			op.datecadastro,
			op.datealteracao,
			op.id_user,
			op.surname,
			( SELECT acs.nomeaccess FROM access acs WHERE op.id_access = acs.idaccess ) AS funcao
		FROM
			operadores op 
		ORDER BY
			op.idoperator ASC
			LIMIT $start,$end");

		$sql->execute();
		
		return $sql->fetchAll();
	}

	//Verifica operador
	public static function operator($idoperador){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE idoperator = $idoperador");
		$sql->execute();

		return ($sql->fetchAll());
	}

	public static function orderby($tabela, $idtbl, $start = null, $end = null){
		if($start == null && $end == null){
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY $idtbl ASC");
		}else{
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY $idtbl ASC LIMIT $start,$end");
		}
		$sql->execute();
		
		return $sql->fetchAll();
	}

	public static function orderocorrencia($start = null, $end = null){
		if($start == null && $end == null){
			$sql = MySql::conectar()->prepare("SELECT
			o.seq_pla_ocorrencia,
			o.data_cadastro,
			o.nome_ocorrencia 
		FROM
			chamados_ocorrencia o 
		WHERE
			o.status_ocorrencia = 'A' 
		ORDER BY
			o.nome_ocorrencia ASC");
		}else{
			$sql = MySql::conectar()->prepare("SELECT
			o.seq_pla_ocorrencia,
			o.data_cadastro,
			o.nome_ocorrencia 
		FROM
			chamados_ocorrencia o 
		WHERE
			o.status_ocorrencia = 'A' 
		ORDER BY
			o.nome_ocorrencia ASC,
			o.seq_pla_ocorrencia LIMIT $start,$end");
		}
		$sql->execute();
		
		return $sql->fetchAll();
	}

	public static function orderid($tabela, $start = null, $end = null){
		if($start == null && $end == null){
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY order_id ASC");
		}else{
			$sql = MySql::conectar()->prepare("SELECT * FROM $tabela ORDER BY order_id ASC LIMIT $start,$end");
		}
		$sql->execute();
		
		return $sql->fetchAll();
	}
	
	public static function order($table,$query,$arr){
		$sql = MySql::conectar()->prepare("SELECT * FROM $table WHERE $query");
		$sql->execute($arr);
		
		return $sql->fetch();
	}

	public static function All($tabela){

		$sql = MySql::conectar()->prepare("SELECT * FROM $tabela");
		$sql->execute();
		
		return $sql->fetchAll();
	}

	//busca operadores atendentes
	public static function atendente(){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE id_access = 4 OR 3");
		$sql->execute();

		return $sql->fetchAll();
	}

	public static function historicochamado($filtro1, $filtro2, $filtro3, $filtro4, $filtro5, $filtro6, $filtro7, $filtro8, $filtro9) {

		$comando1 = ' AND ch.id_user = ' . $filtro1 . '';
		$comando2 = ' AND ch.id_operator = ' . $filtro2 . '';
		$comando3 = ' AND date( ch.data_abertura ) BETWEEN "'.$filtro3.'" AND "'.$filtro4.'"';
		$comando4 = ' AND date( ch.data_fechamento ) BETWEEN "'.$filtro5.'" AND "'.$filtro6.'"';
		$comando5 = ' AND date( ch.data_abertura ) >= "'.$filtro7.'" AND date( ch.data_fechamento ) <= "'.$filtro8.'"';
		$comando6 = ' AND idchamado LIKE '.$filtro9.'';
		//$comando7 = ' AND date( ch.data_fechamento ) <= "'.$filtro8.'"';

		$query = "SELECT
			idchamado,
			data_abertura,
			( SELECT nomestatus FROM status st WHERE ch.id_status = st.idstatus) AS status,
			( SELECT nomeprioridade FROM prioridade prd WHERE ch.id_prioridade = prd.idprioridade ) AS prioridade,
			( SELECT nomecategoria FROM chamados_categoria cat WHERE ch.id_categoria = cat.idcategoria) AS categoria,
			( SELECT nomeuser FROM usuarios us WHERE ch.id_user = us.iduser ) AS solicitante,
			( SELECT surname FROM operadores op WHERE ch.id_operator = op.idoperator ) AS atendente,
			descricao,
			data_fechamento,
			nota
		FROM
			chamados ch 
		WHERE
			ch.id_status = '5' ";
		if ($filtro1 > 0) {
			$query .= $comando1;
		} else {
			$query .= '';
		}
		if ($filtro2 > 0) {
			$query .= $comando2;
		} else {
			$query .= '';
		}
		if ($filtro3 > 0 && $filtro4 > 0){
			$query .= $comando3;
		}
		if ($filtro5 > 0 && $filtro6 > 0){
			$query .= $comando4;
		}
		if ($filtro7 > 0 && $filtro8 > 0){
			$query .= $comando5;
		}
		if ($filtro9 > 0) {
			$query .= $comando6;
		}
		$query .= ' LIMIT 50';
		
		$sql = MySql::conectar()->prepare($query);
		$sql->execute();
		
		return $sql->fetchAll();
	}

	//Seleciona chamados do solicitante
	public static function meuschamados($status, $solicitante) {
		$sql = MySql::conectar()->prepare("SELECT
			c.idchamado,
			c.id_user,
			d.nomedept AS departamento,
			c.data_abertura,
			s.nomestatus AS status,
			c.id_status,
			p.nomeprioridade AS prioridade,
			c.id_prioridade,
			c.id_categoria,
			c.seq_pla_subcategoria,
			sc.nome_subcategoria AS subcategoria,
			c.seq_pla_ocorrencia,
			oc.nome_ocorrencia AS ocorrencia,
			c.descbreve,
			c.descricao,
			c.atribuido,
			c.id_attribute,
			o.surname AS atendente,
			c.id_operator,
			c.data_concluido,
			c.retornado,
			c.id_return,
			c.seq_pla_ch_externo,
			c.data_fechamento,
			c.nota,
			c.cancelado,
			c.data_cancelamento,
			c.motivo_cancelamento 
		FROM
			chamados c
			JOIN usuarios u ON c.id_user = u.iduser
			JOIN departamento d ON u.id_dept = d.iddept
			JOIN status s ON c.id_status = s.idstatus
			JOIN prioridade p ON c.id_prioridade = p.idprioridade
			LEFT JOIN chamados_subcategoria sc ON c.seq_pla_subcategoria = sc.seq_pla_subcategoria
			LEFT JOIN chamados_ocorrencia oc ON c.seq_pla_ocorrencia = oc.seq_pla_ocorrencia
			LEFT JOIN operadores o ON c.id_operator = o.idoperator 
		WHERE
			c.id_status = '$status'
			AND c.id_user = $solicitante
		ORDER BY
			c.idchamado DESC;");
		$sql->execute();

		return $sql->fetchAll();
	}

	public static function pegaAnexo($idchamado){
		$sql = MySql::conectar()->prepare("SELECT * FROM anexo anx WHERE anx.id_chamado = $idchamado ORDER BY idanexo DESC");
		$sql->execute();

		return $sql->fetchAll();
	}

	public static function dadosparaenvio($idchamado){
		$sql = MySql::conectar()->prepare("SELECT
			ch.idchamado,
			( SELECT us.loginuser FROM usuarios us WHERE us.iduser = ch.id_user ) AS 'solicitante',
			( SELECT op.surname FROM operadores op WHERE op.idoperator = ch.id_operator ) AS 'atendente',
			( SELECT us.email FROM usuarios us WHERE us.iduser = ch.id_user ) AS 'email',
			( SELECT emc.idmessage FROM email_chamados emc WHERE emc.id_chamado = ch.idchamado ) AS 'idmessage',
			ch.descbreve,
			ch.descricao
		FROM
			chamados ch 
		WHERE
			ch.idchamado = $idchamado");
			$sql->execute();

		return $sql->fetchAll();
	}

	#Select painel chamados
	public static function chamados($status, $atendente) {
		// Construção condicional para o atendente
		$comando1 = ' AND ch.id_operator = :atendente';

		// Query base
		$query = "
        SELECT
			ch.idchamado,
			DATE_FORMAT(ch.data_abertura, '%d/%m/%Y %H:%i') AS data_abertura,
			st.nomestatus AS status,
			ch.id_prioridade,
			prd.nomeprioridade AS prioridade,
			ch.id_categoria,
			cat.nomecategoria AS categoria,
			ch.seq_pla_subcategoria,
			subcat.nome_subcategoria AS subcategoria,
			ch.seq_pla_ocorrencia,
			oc.nome_ocorrencia AS ocorrencia,
			us.nomeuser AS solicitante,
			ch.id_user AS idsolicitante,
			dp.nomedept AS departamento,
			op.surname AS atendente,
			op.prestador_servico AS prestador_servico,
			op.id_access AS idacesso_operador,
			ch.id_operator AS idatendente,
			ch.descbreve,
			ch.descricao,
			chex.cod_chamado AS chexterno,
			ch.data_fechamento,
			ch.nota
		FROM
			chamados ch
			LEFT JOIN status st ON ch.id_status = st.idstatus
			LEFT JOIN prioridade prd ON ch.id_prioridade = prd.idprioridade
			LEFT JOIN chamados_categoria cat ON ch.id_categoria = cat.idcategoria
			LEFT JOIN chamados_subcategoria subcat ON ch.seq_pla_subcategoria = subcat.seq_pla_subcategoria
			LEFT JOIN chamados_ocorrencia oc ON ch.seq_pla_ocorrencia = oc.seq_pla_ocorrencia
			LEFT JOIN usuarios us ON ch.id_user = us.iduser
			LEFT JOIN departamento dp ON us.id_dept = dp.iddept
			LEFT JOIN operadores op ON ch.id_operator = op.idoperator
			LEFT JOIN (
				SELECT 
					ce1.seq_pla_chamado, 
					ce1.cod_chamado
				FROM 
					chamados_externo ce1
				WHERE 
					ce1.seq_pla_chamado_externo = (
						SELECT MAX(ce2.seq_pla_chamado_externo)
						FROM chamados_externo ce2
						WHERE ce2.seq_pla_chamado = ce1.seq_pla_chamado
					)
			) chex ON chex.seq_pla_chamado = ch.idchamado
		WHERE
			ch.id_status = :status";

		// Adiciona a condição do atendente, se necessário
		if ($atendente > 0) {
			$query .= $comando1;
		}

		// Condição extra para status 5 (nota menor ou igual a 7)
		if ($status == 5) {
			$query .= " AND ch.nota <= 7";
		}

		// Ordenação condicional
		if ($status == 1) {
			$query .= " ORDER BY prd.nomeprioridade = 'Imediato' DESC;";
		} else {
			$query .= " ORDER BY ch.idchamado ASC;";
		}

		// Prepara e executa a query
		$sql = MySql::conectar()->prepare($query);
		$sql->bindParam(':status', $status, PDO::PARAM_INT);
		if ($atendente > 0) {
			$sql->bindParam(':atendente', $atendente, PDO::PARAM_INT);
		}
		$sql->execute();

		return $sql;
	}

	public static function obterStatus($seq_pla_status = []) {
		$sql = "SELECT * FROM status s";
		if (!empty($seq_pla_status)) {
			$placeholders = implode(',', array_fill(0, count($seq_pla_status), '?'));
			$sql .= " WHERE s.idstatus IN ($placeholders)";
		}
		$stmt = MySql::conectar()->prepare($sql);

		if (!empty($seq_pla_status)) {
			$stmt->execute($seq_pla_status);
		} else {
			$stmt->execute();
		}

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function obterSubcategorias($categoriaId) {
        $sql = MySql::conectar()->prepare("SELECT
			cs.seq_pla_subcategoria,
			cs.nome_subcategoria 
		FROM
			chamados_subcategoria cs 
		WHERE
			cs.seq_pla_categoria = ?");
			$sql->execute([$categoriaId]);
			$subcategorias = $sql->fetchAll();

        // Construa as opções HTML para as subcategorias
        $html = '<option value="">Selecione...</option>';
        foreach ($subcategorias as $subcategoria) {
            $html .= '<option value="' . $subcategoria['seq_pla_subcategoria'] . '">' . $subcategoria['nome_subcategoria'] . '</option>';
        }

        // Retorne o HTML com as opções das subcategorias
        return $html;
    }

	public static function obterOcorrencia($seq_pla_subcategoria) {
		if ($seq_pla_subcategoria == 'categoriabi') {
			$seq_pla_subcategoria = 6;
			$query = "SELECT
			lo.seq_pla_ocorrencia,
			o.nome_ocorrencia
		FROM
			ch_liga_ocor_cat_subcat lo
			JOIN chamados_ocorrencia o ON lo.seq_pla_ocorrencia = o.seq_pla_ocorrencia
		WHERE
			lo.seq_pla_categoria = ?";
		} else {
			$query = "SELECT
			lo.seq_pla_ocorrencia,
			o.nome_ocorrencia
		FROM
			ch_liga_ocor_cat_subcat lo
			JOIN chamados_ocorrencia o ON lo.seq_pla_ocorrencia = o.seq_pla_ocorrencia
		WHERE
			lo.seq_pla_subcategoria = ?";
		}

        $sql = MySql::conectar()->prepare($query);
        $sql->execute([$seq_pla_subcategoria]);
        $ocorrencia = $sql->fetchAll();

		if (empty($ocorrencia)) {
			return '<option value="NULL">Nenhuma ocorrência encontrada</option>';
		}

        $html = '<option value="">Selecione...</option>';
        foreach ($ocorrencia as $ocorrencias) {
            $html .= '<option value="' . htmlspecialchars($ocorrencias['seq_pla_ocorrencia']) . '">' . $ocorrencias['nome_ocorrencia'] . '</option>';
        }

        return $html;
    }

	//Seleciona histórico do chamado
	public static function historicochamados ($idchamado){
		$query = "SELECT
			hc.idhistorico,
			hc.id_chamado,
			( SELECT op.surname FROM operadores op WHERE hc.atendente = op.idoperator ) AS atendente,
			hc.data_movimentacao,
			hc.resposta_atendente,
			hc.resposta_solicitante 
		FROM
			historico_chamados hc 
		WHERE
			hc.id_chamado = :idchamado
		
		ORDER BY
			hc.data_movimentacao DESC";

		$sql = MySql::conectar()->prepare($query);
		$sql->bindValue(':idchamado', $idchamado, PDO::PARAM_INT);
		$sql->execute();
		
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

	//seleciona ligação de ocorrêcias e suas subcategorias com nome.
	public static function ligaocorrencia ($idocorrencia) {
		$query = "SELECT
			o.seq_pla_ocorrencia,
			o.nome_ocorrencia,
			lo.seq_pla_liga_ocorrencia,
			lo.seq_pla_subcategoria,
			sc.nome_subcategoria 
		FROM
			chamados_ocorrencia o
			LEFT JOIN ch_liga_ocor_cat_subcat lo ON lo.seq_pla_ocorrencia = o.seq_pla_ocorrencia
			LEFT JOIN chamados_subcategoria sc ON sc.seq_pla_subcategoria = lo.seq_pla_subcategoria 
		WHERE
			o.seq_pla_ocorrencia = $idocorrencia";

		$sql = MySql::conectar()->prepare($query);
		$sql->execute();

		return $sql;
	}
}

class insert {
	//cadastro de departamentos
	public static function cadDepartamento($nomedept, $obsdept, $datacad, $statusdept){
		$sql = MySql::conectar()->prepare("INSERT INTO departamento VALUES (null,?,?,?,?)");
		$sql->execute(array($nomedept, $obsdept, $datacad, $statusdept));

		alert::CadDept($nomedept);
	}

	//Cadastro de cargos
	public static function cadastracargo($nomecargo,$departamento,$statuscargo){
		$sql = MySql::conectar()->prepare("INSERT INTO cargo VALUES (null,?,?,now(),?)");
		$sql->execute(array($nomecargo,$departamento,$statuscargo));

		alert::CadCargo($nomecargo);
	}

	//Cadastro de categorias do chamado
	public static function cadcategoria($nomecategoria,$userinsert,$desccategoria,$statuscategoria){
		$sql = MySql::conectar()->prepare("INSERT INTO chamados_categoria VALUES (null,now(),?,?,?,?,null,null)");
		$sql->execute(array($nomecategoria,$userinsert,$desccategoria,$statuscategoria));

		return true;
	}

	//Cadastro de ocorrencia de chamados
	public static function cadastraOcorrencia($nomeocorrencia, $descocorrencia, $statusocorrencia, $seq_pla_categoria = NULL, $seq_pla_subcategoria = NULL){
		$conn = NULL;
		try {
			$nomeocorrencia = verificar::valida_dados($nomeocorrencia);
			$descocorrencia = verificar::valida_dados($descocorrencia);
			$statusocorrencia = verificar::valida_dados($statusocorrencia);

			$sql = "INSERT INTO chamados_ocorrencia VALUES (null,now(),?,?,?)";

			$conn = MySql::conectar();
			$stmt = $conn->prepare($sql);
			$conn->beginTransaction();

			if (!$stmt->execute(array($nomeocorrencia, $descocorrencia, $statusocorrencia))) {
				throw new Exception('Erro ao registrar ocorrência!');
			}
			//pega ultimo ID inserido
			$seq_pla_ocorrencia = $conn->lastInsertId();

			//comita a operação de cadastrar ocorrencia
			$conn->commit();

			//Inseri a ligação caso tenha valores enviados
			if ($seq_pla_categoria !== NULL && $seq_pla_subcategoria !== NULL) {
				//inseri a ligação da ocorrencia com categorias e subcategorias
				insert::ligaOcorCatSubcat($seq_pla_ocorrencia, $seq_pla_categoria, $seq_pla_subcategoria);
			}

			//retorna verdadero caso dê tudo certo.
			return true;

		} catch(PDOException $e) {
			//Faz Rollback em caso de erro específico do PDO
			if ($conn instanceof PDO) {
				$conn->rollBack();
			}
			error_log('Erro na conexão na tentativa de cadastro: ' . $e->getMessage());
			throw new Exception('Erro na conexão de cadastro: ' . $e->getMessage());

		} catch(Exception $e){
			if ($conn instanceof PDO) {
				$conn->rollBack();
			}
			error_log('Erro ao cadastrar ocorrência: ' . $e->getMessage());
			throw new Exception('Erro na gravação da ocorrência: ' . $e->getMessage());

		} finally {
			if ($conn !== NULL) {
				$conn = NULL;
			}
		}
	}

	//cadastro de ligação da ocorrencia com a subcategoria
	public static function ligaOcorCatSubcat($seq_pla_ocorrencia, $seq_pla_categoria = NULL, $seq_pla_subcategoria = NULL) {
		$conn = NULL;
		try {
			// Validando os dados
			$seq_pla_ocorrencia = verificar::valida_dados($seq_pla_ocorrencia);
			if ($seq_pla_categoria !== NULL) {
				$seq_pla_categoria = array_map(array('verificar', 'valida_dados'), $seq_pla_categoria);
			}
			if ($seq_pla_subcategoria !== NULL) {
				$seq_pla_subcategoria = array_map(array('verificar', 'valida_dados'), $seq_pla_subcategoria);
			}

			$conn = MySql::conectar();
			$sql = "INSERT INTO ch_liga_ocor_cat_subcat VALUES ";
			$values = [];
			$params = [];

			// Calcula o número máximo de inserções necessárias
			$num_categorias = is_array($seq_pla_categoria) ? count($seq_pla_categoria) : 0;
			$num_subcategorias = is_array($seq_pla_subcategoria) ? count($seq_pla_subcategoria) : 0;
			$insercoes = max($num_categorias, $num_subcategorias, 1);// Pelo menos uma inserção necessária

			// Itera sobre o número máximo de inserções
			for ($i = 0; $i < $insercoes; $i++) {
				$categoria = $i < $num_categorias ? $seq_pla_categoria[$i] : NULL;
				$subcategoria = $i < $num_subcategorias ? $seq_pla_subcategoria[$i] : NULL;

				// Verifica se categoria e subcategoria foram fornecidas antes de adicioná-las à consulta
				if ($categoria !== NULL || $subcategoria !== NULL) {
					$values[] = "(null, now(), ?, ?, ?)";
					$params[] = $seq_pla_ocorrencia;
					$params[] = $categoria;
					$params[] = $subcategoria;
				}
			}

			if (count($values) == 0) {
				throw new Exception('Pelo menos uma categoria ou subcategoria deve ser fornecida!');
			}

			$sql .= implode(", ", $values);
			$stmt = $conn->prepare($sql);

			// Iniciando a transação
			$conn->beginTransaction();

			// Executando a query com os parâmetros
			if (!$stmt->execute($params)) {
				throw new Exception('Erro ao registrar ligação da ocorrência!');
			}

			// Commit da transação
			$conn->commit();
			return true;
		} catch (Exception $e) {
			// Em caso de erro, faz rollback na transação
			if ($conn !== null) {
				$conn->rollBack();
			}
			throw new Exception($e->getMessage()); // Retorna o erro para ser tratado externamente
		}
	}

	//Cadastro de sub categorias do chamado
	public static function cadsubcategoria($nomesubcategoria, $categoria, $descsubcategoria, $status){
		$sql = MySql::conectar()->prepare("INSERT INTO chamados_subcategoria VALUES (null, now(), ?, ?, ?, ?)");
		$sql->execute(array($nomesubcategoria, $categoria, $descsubcategoria, $status));

		return true;
	}
	
	//Cadastrar usuário
	public static function cadUsuario($userlogin, $usersenha, $usernome, $email, $idaccess, $usercargo, $userdepartamento, $userimg){
		$sql = MySql::conectar()->prepare("INSERT INTO usuarios VALUES (null,?,?,?,?,?,?,?,?,now())");
		$sql->execute(array($userlogin, $usersenha, $usernome, $email, $idaccess, $usercargo, $userdepartamento, $userimg));

		//alert::alertaCadUsuario($usernome);
	}

	//cadastra operadodor
	public static function cadastraoperador($operador, $apelido, $designacao, $prestaservico){
		$sql = MySql::conectar()->prepare("INSERT INTO operadores VALUES (null,now(),null,?,?,?,?)");
		$sql->execute(array($operador, $apelido, $designacao, $prestaservico));

		return $sql;
	}

	//Atribui chamado
	public static function atribuiChamado($atribuidor, $idatendente, $idchamado){
		// Validação básica dos parâmetros
		if (empty($atribuidor) || !is_numeric($idatendente) || !is_numeric($idchamado)) {
			throw new InvalidArgumentException("Parâmetros inválidos!");
		}

		try {
			// Conexão com o banco
			$pdo = MySql::conectar();

			// Preparação e execução da query
			$sql = $pdo->prepare(
				"INSERT INTO chamados_atribuicoes (seq_pla_atribuicao, data_atribuicao, seq_pla_operador_origem, seq_pla_operador_destino, seq_pla_chamados)
             VALUES (null, now(), ?, ?, ?)"
			);
			$sql->execute(array($atribuidor, $idatendente, $idchamado));

			// Retorna o último ID inserido
			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			// Log de erro e exceção para tratamento posterior
			error_log($e->getMessage());
			throw new RuntimeException("Erro ao atribuir o chamado.");
		}
	}

	//Registra chamado SLA
	public static function andamentoChamado($seq_pla_chamado, $seq_pla_operador, $seq_pla_usuario, $seq_pla_status, $status_registro) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Validação e sanitização dos dados
			$dados = verificar::valida_dados([
				'seq_pla_chamado' => $seq_pla_chamado,
				'seq_pla_operador' => $seq_pla_operador,
				'seq_pla_usuario' => $seq_pla_usuario,
				'seq_pla_status' => $seq_pla_status,
				'status_registro' => $status_registro,
			]);
	
			// Verifica campos obrigatórios
			foreach (['seq_pla_chamado', 'seq_pla_operador', 'seq_pla_usuario', 'seq_pla_status'] as $key) {
				if (empty($dados[$key])) {
					throw new InvalidArgumentException("Campo $key é obrigatório e não pode estar vazio.");
				}
			}
	
			// Inicia uma transação somente se nenhuma estiver ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Query de inserção
			$sql = $pdo->prepare("
				INSERT INTO chamados_andamento 
				(seq_pla_chamado, seq_pla_operador, seq_pla_usuario, seq_pla_status, status_registro, data_inicio) 
				VALUES (?, ?, ?, ?, ?, NOW())
			");
			$sql->execute([
				$dados['seq_pla_chamado'],
				$dados['seq_pla_operador'],
				$dados['seq_pla_usuario'],
				$dados['seq_pla_status'],
				$dados['status_registro']
			]);
	
			// Obtém o ID do registro inserido
			$lastInsertId = $pdo->lastInsertId();
	
			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			// Retorna o ID do registro inserido
			return $lastInsertId;
		} catch (Exception $e) {
			// Reverte a transação somente se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao inserir andamento: " . $e->getMessage());
			return false;
		}
	}

	//Realiza a inserção de histórico das movimentações dos chamados
	public static function movimentacoesChamados(array $dados){
		$pdo = MySql::conectar();
		$transacaoIniciada = false;

		try {
			// Validação básica dos dados
			if (empty($dados) || !is_array($dados)) {
				throw new InvalidArgumentException("Os dados fornecidos devem ser um array associativo válido.");
			}

			// Verifica campos obrigatórios
			$camposObrigatorios = ['seq_pla_chamado'];
			foreach ($camposObrigatorios as $campo) {
				if (empty($dados[$campo])) {
					throw new InvalidArgumentException("Campo $campo é obrigatório e não pode estar vazio.");
				}
			}

			// Inicia uma transação se não houver uma ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}

			// Obter as colunas e os valores
			$colunas = array_keys($dados);
			$valores = array_values($dados);

			// Adiciona a data de início automaticamente, se não estiver presente
			if (!isset($dados['data_movimentacao'])) {
				$colunas[] = 'data_movimentacao';
				$valores[] = date('Y-m-d H:i:s'); // Adiciona a data atual
			}

			// Montar a query dinamicamente
			$colunasString = implode(', ', $colunas);
			$placeholders = implode(', ', array_fill(0, count($valores), '?'));

			$sql = $pdo->prepare(
				"INSERT INTO chamados_movimentacoes ($colunasString) VALUES ($placeholders)"
			);

			// Executa a query com os valores
			$sql->execute($valores);

			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}

			// Retorna o ID do registro inserido
			return true;
		} catch (Exception $e) {
			// Reverte a transação somente se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao inserir andamento: " . $e->getMessage());
			throw new RuntimeException("Erro ao registrar movimentação.");
		}
	}

	public static function registraTrocaOperador(array $dados) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Validação básica dos dados
			if (empty($dados) || !is_array($dados)) {
				throw new InvalidArgumentException("Os dados fornecidos devem ser um array associativo válido.");
			}
	
			// Verifica campos obrigatórios
			$camposObrigatorios = ['seq_pla_chamado'];
			foreach ($camposObrigatorios as $campo) {
				if (empty($dados[$campo])) {
					throw new InvalidArgumentException("Campo $campo é obrigatório e não pode estar vazio.");
				}
			}
	
			// Inicia uma transação se não houver uma ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Obter as colunas e os valores
			$colunas = array_keys($dados);
			$valores = array_values($dados);
	
			// Adiciona a data de troca automaticamente, se não estiver presente
			if (!isset($dados['data_troca'])) {
				$colunas[] = 'data_troca';
				$valores[] = date('Y-m-d H:i:s'); // Adiciona a data atual
			}
	
			// Montar a query dinamicamente
			$colunasString = implode(', ', $colunas);
			$placeholders = implode(', ', array_fill(0, count($valores), '?'));
	
			$sql = $pdo->prepare(
				"INSERT INTO chamados_troca_operador ($colunasString) VALUES ($placeholders)"
			);
	
			// Executa a query com os valores
			$sql->execute($valores);
	
			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			// Retorna o ID do registro inserido
			return $pdo->lastInsertId();
		} catch (Exception $e) {
			// Reverte a transação somente se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao registrar troca de operador: " . $e->getMessage());
			throw new RuntimeException("Erro ao registrar troca de operador.");
		}
	}
	
	//inclusão de permissões de access
	public static function includaccess($arr){
		$certo = true;
		$tbl = 'permissoes';
		$query = "INSERT INTO $tbl VALUES (null";

		foreach($arr as $key => $value){
			if($value == ''){
				$certo = false;
				break;
			}
			$query.=",?";
			$parametros[] = $value;
		}
		$query.=")";
		if($certo == true){
			$sql = MySql::conectar()->prepare($query);
			$sql->execute($parametros);
		}
		return $certo;
	}

	//insert chamados
	public static function atendechamado($idchamado, $atendente){
		$sql = MySql::conectar()->prepare("INSERT INTO historico_chamados VALUES (null,?,?,now(),null,null)");
		$sql->execute(array($idchamado, $atendente));
	}

	//insert atende chamado #avaliar para remover está repetida
	public static function movimentacaochamado($id,$atendente,$resposta1,$resposta2){
		$sql = MySql::conectar()->prepare("INSERT INTO historico_chamados VALUES (null,?,?,now(),?,?)");
		$sql->execute(array($id,$atendente,$resposta1,$resposta2));
	}

	//insert chamado compass
	public static function chamadocompass($registrou, $seq_pla_ch, $id_operator, $id_chamado){
		$sql = MySql::conectar()->prepare("INSERT INTO chamados_externo VALUES (null,?,?,?,?,now())");
		$sql->execute(array($registrou, $seq_pla_ch, $id_operator, $id_chamado));
	}

	//insert atende chamado
	public static function devolveHistoricoChamado($id, $atendente, $resposta){
		// Obtém a conexão com o banco de dados
		$pdo = MySql::conectar();
		$transacaoIniciada = false;

		try {
			// Inicia uma transação se não houver uma ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}

			// Prepara a consulta SQL
			$sql = $pdo->prepare("
            INSERT INTO historico_chamados (idhistorico, id_chamado, atendente, data_movimentacao, resposta_solicitante)
            VALUES (null, ?, ?, now(), ?)
			");
			
			// Executa a consulta
			$sql->execute([$id, $atendente, $resposta]);

			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}

			// Retorna sucesso
			return true;
		} catch (Exception $e) {
			// Reverte a transação somente se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}

			// Opcional: registra o erro (log ou debug)
			error_log("Erro ao devolver chamado: " . $e->getMessage());

			// Retorna falha
			return false;
		}
	}

	//grava retorno do chamado
	public static function retornaChamado($id, $solicitante, $atendente){
		// Obtém a conexão com o banco de dados
		$pdo = MySql::conectar();

		try {
			// Inicia a transação
			$pdo->beginTransaction();

			// Prepara a consulta SQL
			$sql = $pdo->prepare("
            INSERT INTO chamados_retornados 
            (seq_pla_retorno, data_retorno, seq_pla_chamado, seq_pla_usuario, seq_pla_operador)
            VALUES (null, now(), ?, ?, ?)
        ");

			// Executa a consulta
			$sql->execute([$id, $solicitante, $atendente]);

			// Obtém o ID da última inserção
			$lastInsertId = $pdo->lastInsertId();

			// Confirma a transação
			$pdo->commit();

			// Retorna sucesso e o ID da inserção
			return $lastInsertId;
		} catch (Exception $e) {
			// Reverte a transação em caso de erro
			$pdo->rollBack();

			// Opcional: registra o erro (log ou debug)
			error_log("Erro ao retornar chamado: " . $e->getMessage());

			// Retorna false em caso de falha
			return false;
		}
	}

	//insert finalização chamado
	public static function finalizaChamado($idchamado, $acao, $atendente, $feedback) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Valida os parâmetros
			if (empty($idchamado) || empty($acao) || empty($atendente)) {
				throw new InvalidArgumentException("Parâmetros obrigatórios estão ausentes.");
			}
	
			// Processa os feedbacks com base na ação
			$responseat = null;
			$responsesol = null;
	
			if ($acao === 'concluir') {
				$responseat = $feedback;
			} elseif ($acao === 'finalizar') {
				$responsesol = $feedback;
			} else {
				throw new InvalidArgumentException("Ação inválida: {$acao}. Apenas 'concluir' ou 'finalizar' são permitidas.");
			}
	
			// Inicia uma transação somente se nenhuma estiver ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Prepara e executa a query de inserção
			$sql = $pdo->prepare("
				INSERT INTO historico_chamados 
				(idhistorico, id_chamado, atendente, data_movimentacao, resposta_atendente, resposta_solicitante) 
				VALUES (null, ?, ?, now(), ?, ?)
			");
			$result = $sql->execute([$idchamado, $atendente, $responseat, $responsesol]);
	
			// Verifica se a execução foi bem-sucedida
			if (!$result) {
				throw new Exception("Erro ao inserir no histórico de chamados.");
			}
	
			// Finaliza a transação se foi iniciada nesta função
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			return true;
		} catch (Exception $e) {
			// Reverte a transação apenas se foi iniciada nesta função
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro na função finalizachamado: " . $e->getMessage());
			return false;
		}
	}

	//insert registro de emails enviados
	public static function sendmail($idmessage, $status, $idchamado){
		$sql = MySql::conectar()->prepare("INSERT INTO email_chamados (idemail, dataenvio, idmessage, email_status, id_chamado) VALUES (null,now(),?,?,?)");
		if($sql->execute(array($idmessage, $status, $idchamado))){
			return true;
		} else {
			return false;
		}
	}
}

class upload {

	//Upload dos avatar dos usuarios
	public static function avatar($file, $userlogin){
		if($userlogin == '' or null){
			$nameimg = uniqid();
		}else{
			$nameimg = $userlogin;
		}
		$files = $_FILES['avatar']['name'];
		$extensao = pathinfo($files, PATHINFO_EXTENSION);
		$imagemNome = $nameimg.'.'.$extensao;
		if(move_uploaded_file($file['tmp_name'],BASE_DIR_PAINEL.'/uploads/usuarios/avatar/'.$imagemNome))
			return $imagemNome;
		else
			return false;
	}

	//upload slide do site/portal
	public static function Slide($file){
		$files = $_FILES['imagem']['name'];
		$extensao = pathinfo($files, PATHINFO_EXTENSION);
		$imagemNome = uniqid().'.'.$extensao;
		if(move_uploaded_file($file['tmp_name'],BASE_DIR_PAINEL.UPLOAD_SAVE_SLIDE.$imagemNome))
			return $imagemNome;
		else
			return alert::Error('Aconteceu algo errado no upload do slide, return: false');
	}

}

class update {
	//Update departamento
	public function updateDept($nomedept, $descdept, $iddept){
		$sql = MySql::conectar()->prepare("UPDATE departamento SET nomedept = ?, descricaodept = ? WHERE iddept = ?");
		$sql->execute(array($nomedept, $descdept, $iddept));

		alert::alertaEditarDept($nomedept);
	}

	//Update de Cargos
	public static function updatecargo($nomecargo, $departamento, $status, $idcargo){
		$sql = MySql::conectar()->prepare("UPDATE cargo SET nomecargo = ?, id_dept = ?, statuscargo = ? WHERE idcargo = ?");
		$sql->execute(array($nomecargo, $departamento, $status, $idcargo));

		alert::alertaEditarCargo($nomecargo);
	}

	//Update das categorias de chamados
	public static function categoria($nomecategoria, $desccategoria, $status, $idcategoria){
		$sql = MySql::conectar()->prepare("UPDATE chamados_categoria SET nomecategoria = ?, desccategoria = ?, status = ? WHERE idcategoria = ?");
		$sql->execute(array($nomecategoria, $desccategoria, $status, $idcategoria));

		alert::alertaEditarCategoria($nomecategoria);
	}

	//Update das subcategorias de chamados
	public static function subcategoria($nomesubcategoria, $descsubcategoria, $status, $idsubcategoria){
		$sql = MySql::conectar()->prepare("UPDATE chamados_subcategoria SET nome_subcategoria = ?, desc_subcategoria = ?, status_subcategoria = ? WHERE seq_pla_subcategoria = ?");
		$sql->execute(array($nomesubcategoria, $descsubcategoria, $status, $idsubcategoria));

		alert::alertaEditarSubCategoria($nomesubcategoria);
	}

	//Update das ocorrencias de chamados
	public static function ocorrencia($nomeocorrencia, $descocorrencia, $statusocorrencia, $idocorrencia){
		try{
			function valida_up_ocorrencia($dados){
				$dados = trim($dados);
				$dados = stripslashes($dados);
				$dados = htmlspecialchars($dados);
				return $dados;
			}

			$nomeocorrencia = valida_up_ocorrencia($nomeocorrencia);
			$descocorrencia = valida_up_ocorrencia($descocorrencia);
			$statusocorrencia = valida_up_ocorrencia($statusocorrencia);

			if (isset($nomeocorrencia, $descocorrencia, $statusocorrencia)) {
				$sql = MySql::conectar()->prepare("UPDATE chamados_ocorrencia SET nome_ocorrencia = ?, desc_ocorrencia = ?, status_ocorrencia = ? WHERE seq_pla_ocorrencia = ?");
				$insert = $sql->execute(array($nomeocorrencia, $descocorrencia, $statusocorrencia, $idocorrencia));
			} else {
				throw new Exception('Erro ao realizar update da ocorrencia!');
			}

			if ($insert == true){
				return true;
			} else {
				return false;
			}
		}catch(Exception $e){
			return $e->getMessage();
		}

		alert::alertaeditaocorrencia($nomeocorrencia);
	}

	//Update usuarios
	public function updateUsuario($userlogin, $usersenha, $username, $email, $nivelaccess, $imagem, $iduser){
		$sql = MySql::conectar()->prepare("UPDATE usuarios SET loginuser = ?, senhauser = ?, nomeuser = ?, email = ?, id_access = ?, avataruser = ?  WHERE iduser = ?");
		if($sql->execute(array($userlogin, $usersenha, $username, $email, $nivelaccess, $imagem, $iduser))){
			return true;
		}else{
			return false;
		}
	}

	//update de operador
	public static function operador($nomeoperador, $nivelaccess, $prestaservico, $idoperador){
		$sql = MySql::conectar()->prepare("UPDATE operadores SET datealteracao = now(), id_access = ?, prestador_servico = ? WHERE idoperator = ?");
		$sql->execute(array($nivelaccess, $prestaservico, $idoperador));

		alert::alertaEditarOp($nomeoperador);
	}

	//Registra chamado
	public static function registrachamado($idstatus, $idatendente, $idchamado){
		$sql = MySql::conectar()->prepare("UPDATE chamados SET id_status = ?, id_operator = ? WHERE idchamado = ?");
		$sql->execute(array($idstatus, $idatendente, $idchamado));
	}

	// Atualiza chamado externo
	public static function chamadoExterno($cod_chamado, $seq_pla_chamado){
		$query = MySql::conectar()->prepare("UPDATE chamados_externo SET cod_chamado = ? WHERE seq_pla_chamado_externo = ?");
		$query->execute(array($cod_chamado, $seq_pla_chamado));
	}

	//Registra 
	public static function reavaliarchamado($idstatus, $nota, $idchamado){
		$sql = MySql::conectar()->prepare("UPDATE chamados SET id_status = ?, nota = ? WHERE idchamado = ?");
		$sql->execute(array($idstatus, $nota, $idchamado));
	}

	//Fecha atendimento de um chamado que foi iniciado em seu fluxo normal
	public static function fechaAtendimento($acao, $status_registro, $idatendimento) {
		try {
			// Se o ID de atendimento for NULL, retorna true imediatamente
			if (is_null($idatendimento)) {
				error_log("ID de atendimento é nulo. Nenhuma operação será executada.");
				return true;
			}
	
			// Validação inicial dos dados
			$dados = verificar::valida_dados([
				'acao' => $acao,
				'status_registro' => $status_registro,
				'idatendimento' => $idatendimento,
			]);
	
			// Garantir que o ID seja válido (não <= 0)
			if ((int)$dados['idatendimento'] <= 0) {
				throw new InvalidArgumentException("ID de atendimento inválido.");
			}
	
			$pdo = MySql::conectar();
			$transacaoAtiva = $pdo->inTransaction();
			if (!$transacaoAtiva) {
				$pdo->beginTransaction();
			}
	
			// Verifica a ação para decidir quais campos atualizar
			if ($dados['acao'] == 'F') {
				$sql = "UPDATE chamados_andamento 
						SET data_fim = NOW(), seq_pla_status = ?, status_registro = ? 
						WHERE seq_pla_atendimento = ?";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([5, $dados['status_registro'], $dados['idatendimento']]);
			} else {
				$sql = "UPDATE chamados_andamento 
						SET data_fim = NOW(), status_registro = ? 
						WHERE seq_pla_atendimento = ?";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([$dados['status_registro'], $dados['idatendimento']]);
			}
	
			// Atualiza a duração do atendimento
			$sql_duracao = "UPDATE chamados_andamento 
							SET duracao_atendimento = TIMESTAMPDIFF(MINUTE, data_inicio, data_fim) 
							WHERE seq_pla_atendimento = ?";
			$stmt_duracao = $pdo->prepare($sql_duracao);
			$stmt_duracao->execute([$dados['idatendimento']]);
	
			if (!$transacaoAtiva) {
				$pdo->commit();
			}
	
			return true;
		} catch (Exception $e) {
			if (isset($pdo) && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao fechar atendimento: " . $e->getMessage());
			return false;
		}
	}	

	//Registra atribuição chamado
	public static function registrarAtribuicaoch(int $seq_pla_status, int $seq_pla_atribuicao, $atribuido, int $seq_pla_operador, int $seq_pla_chamado): bool{
		try {
			$sql = MySql::conectar()->prepare(
				"UPDATE chamados 
                SET id_status = ?, id_attribute = ?, atribuido = ?, id_operator = ? 
                WHERE idchamado = ?"
			);

			$resultado = $sql->execute([$seq_pla_status, $seq_pla_atribuicao, $atribuido, $seq_pla_operador, $seq_pla_chamado]);

			return $resultado; // Retorna true se a execução for bem-sucedida
		} catch (PDOException $e) {
			error_log("Erro ao atualizar chamado: " . $e->getMessage());
			return false; // Retorna false em caso de erro
		}
	}

	//altera atendente da tabela chamados
	public static function alteraOperador($status, $idoperator, $trocaOperador, $idchamado) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Inicia uma transação somente se nenhuma estiver ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Query de atualização
			$sql = $pdo->prepare("
				UPDATE chamados 
				SET id_status = ?, id_operator = ?, trocou_operador = ?
				WHERE idchamado = ?
			");
			$sql->execute([$status, $idoperator, $trocaOperador, $idchamado]);
	
			// Verifica se pelo menos uma linha foi alterada
			if ($sql->rowCount() === 0) {
				throw new Exception("Nenhuma linha foi alterada no UPDATE do operador.");
			}
	
			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			return true;
		} catch (Exception $e) {
			// Reverte a transação apenas se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao atualizar operador: " . $e->getMessage());
			return false;
		}
	}

	// Atualiza chamado conforme
	public static function atualizarChamado(array $dados, $seq_pla_chamado) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Inicia uma transação apenas se nenhuma estiver ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Valida o array de dados
			if (empty($dados) || !is_array($dados)) {
				throw new InvalidArgumentException("O array de dados está vazio ou inválido.");
			}
	
			// Monta a query dinamicamente
			$campos = [];
			$valores = [];
	
			foreach ($dados as $campo => $valor) {
				$campos[] = "{$campo} = ?";
				$valores[] = $valor;
			}
	
			$valores[] = $seq_pla_chamado; // Adiciona o ID ao final para a cláusula WHERE
	
			// Constrói a query
			$query = "UPDATE chamados SET " . implode(", ", $campos) . " WHERE idchamado = ?";
			$stmt = $pdo->prepare($query);
	
			// Executa a query
			$stmt->execute($valores);
	
			// Verifica se pelo menos uma linha foi alterada
			if ($stmt->rowCount() === 0) {
				throw new Exception("Nenhuma linha foi alterada no UPDATE do chamado.");
			}
	
			// Confirma a transação
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			return true;
		} catch (Exception $e) {
			// Reverte a transação em caso de erro
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro ao atualizar chamado: " . $e->getMessage());
			return false;
		}
	}	

	//Devolve chamado
	public static function devolveChamado($status, $seq_pla_operador, $retorno, $idchamado){
		// Obtém a conexão com o banco de dados
		$pdo = MySql::conectar();
		$transacaoIniciada = false;

		try {
			// Inicia uma transação se não houver uma ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}

			// Prepara a consulta SQL
			$sql = $pdo->prepare("
            UPDATE chamados 
            SET id_status = ?, id_operator = ?, retornado = ?
            WHERE idchamado = ?
        ");

			// Executa a consulta
			$sql->execute([$status, $seq_pla_operador, $retorno, $idchamado]);

			// Confirma a transação apenas se foi iniciada aqui
			if ($transacaoIniciada) {
				$pdo->commit();
			}

			// Retorna sucesso
			return true;
		} catch (Exception $e) {
			// Reverte a transação somente se foi iniciada aqui
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}

			// Opcional: registra o erro (log ou debug)
			error_log("Erro ao devolver chamado: " . $e->getMessage());

			// Retorna falha
			return false;
		}
	}

	//Conclui Chamado
	public static function concluirChamado($status, $idchamado) {
		$pdo = MySql::conectar();
		$transacaoIniciada = false;
	
		try {
			// Valida os parâmetros
			if (empty($status) || empty($idchamado)) {
				throw new InvalidArgumentException("Parâmetros obrigatórios estão ausentes.");
			}
	
			// Inicia uma transação somente se nenhuma estiver ativa
			if (!$pdo->inTransaction()) {
				$pdo->beginTransaction();
				$transacaoIniciada = true;
			}
	
			// Prepara e executa a query de atualização
			$sql = $pdo->prepare("
				UPDATE chamados 
				SET id_status = ?, data_concluido = now() 
				WHERE idchamado = ?
			");
			$result = $sql->execute([$status, $idchamado]);
	
			// Verifica se o UPDATE foi bem-sucedido
			if (!$result || $sql->rowCount() === 0) {
				throw new Exception("Erro ao concluir o chamado ou nenhuma linha foi alterada.");
			}
	
			// Finaliza a transação se foi iniciada nesta função
			if ($transacaoIniciada) {
				$pdo->commit();
			}
	
			return true;
		} catch (Exception $e) {
			// Reverte a transação apenas se foi iniciada nesta função
			if ($transacaoIniciada && $pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro na função concluirchamado: " . $e->getMessage());
			return false;
		}
	}

	//Finaliza Chamado
	public static function finalizarchamado($status, $nota, $idchamado){
		$sql = MySql::conectar()->prepare("UPDATE chamados SET id_status = ?, data_fechamento = now(), nota = ? WHERE idchamado = ?");
		if($sql->execute(array($status, $nota, $idchamado))){
			return true;
		}else{
			return false;
		}
	}

	//reavalia a nota do chamado
	public static function revalidanota($status, $nota, $idchamado){
		$sql = MySql::conectar()->prepare("UPDATE chamados SET id_status = ?, nota = ? WHERE idchamado = ?");
		if($sql->execute(array($status, $nota, $idchamado))){
			return true;
		}else{
			return false;
		}
	}

	//Cancela Chamado
	public static function cancelachamado($status, $cancelado, $motivo, $idchamado){
		$sql = MySql::conectar()->prepare("UPDATE chamados SET id_status = ?, cancelado = ?, data_cancelamento = now(), motivo_cancelamento = ? WHERE idchamado = ?");
		if($sql->execute(array($status, $cancelado, $motivo, $idchamado))){
			return true;
		}else{
			return false;
		}
	}

	//Update em array, metodo dinamico.
	public static function array($arr){
		$primeiro = false;
		$certo = true;
		$tbl = $arr['nome_tabela'];
		$namecampoid = $arr['namecampoid'];
		$query = "UPDATE $tbl SET ";

		foreach($arr as $key => $result){
			if($key == 'acao' || $key == 'nome_tabela' || $key == 'namecampoid' || $key == 'id'){
				continue;
			}if($result == ''){
				$certo = false;
				break;
			}
			//$primeiro sign. primeira vez inserindo infomração, começa como false porque o valor dele é true, que na segunda vez se tiver já entra com virgula
			if($primeiro == false){
				$primeiro = true;
				$query.="$key = ?";
			}else{
				$query.=",$key = ?";
			}
			$parametros[] = $result;
		}
		if($certo == true){
			$parametros[] = $arr[$namecampoid];
			$sql = MySql::conectar()->prepare($query.' WHERE '.$namecampoid.' = ?');
			$sql->execute($parametros);
			return $certo;
		}
	}

	public static function site($arr,$single = false){
		$certo = true;
		$primeiro = false;
		$nome_tabela = $arr['nome_tabela'];
		$query = "UPDATE $nome_tabela SET ";
		
		foreach($arr as $key => $value){
			$nome = $key;
			$valor = $value;
			if($nome == 'acao' || $nome == 'nome_tabela')
				continue;
			if($value == ''){
				$certo = false;
				break;
			}
			
			if($primeiro == false){
				$primeiro = true;
				$query.="$nome=?";
			}else{
				$query.=",$nome=?";
			}
			$parametros[] = $value;
		}
		if($certo == true){
			if($single == false){
				$parametros[] = $arr['id'];
				$sql = MySql::conectar()->prepare($query.' WHERE id=?');
				$sql->execute($parametros);
			}else{
				$sql = MySql::conectar()->prepare($query);
				$sql->execute($parametros);
			}
		}
		return $certo;
	}

	//atualização de access
	public static function access($arr, $single = false){
		$proceed = true;
		$primeiroinsert = false;
		$tbl = 'permissoes';
		$query = "UPDATE $tbl SET ";

		foreach($arr as $key => $value){
			if($value == ''){
				$proceed = false;
				break;
			}
			if($primeiroinsert == false){
				$primeiroinsert = true;
				$query.="$key=?";
			}else{
				$query.=",$key=?";
			}
			$parametros[] = $value;
		}
		if($proceed == true){
			if($single == false){
				$parametros[] = $arr['idpermissao'];
				$sql = MySql::conectar()->prepare($query.' WHERE idpermissao = ?');
				$sql->execute($parametros);
			}else{
				$sql = MySql::conectar()->prepare($query);
				$sql->execute($parametros);
			}
		}
		return $proceed;
	}

	//Realiza atendimento do chamdo
	public static function atendeChamado($idchamado, $atendente, $status) {
		try {
			if (!is_numeric($idchamado) || empty($atendente) || !is_numeric($status)) {
				throw new Exception("Parâmetros inválidos: idchamado=$idchamado, atendente=$atendente, status=$status");
			}
	
			$query = MySql::conectar()->prepare(
				"UPDATE chamados SET id_status = :id_status, id_operator = :id_operator WHERE idchamado = :idchamado"
			);
			$query->bindParam(':idchamado', $idchamado, PDO::PARAM_INT);
			$query->bindParam(':id_operator', $atendente, PDO::PARAM_STR);
			$query->bindParam(':id_status', $status, PDO::PARAM_INT);
	
			if ($query->execute()) {
				$linhasAfetadas = $query->rowCount();
				if ($linhasAfetadas > 0) {
					return $linhasAfetadas; // Retorna o número de linhas afetadas
				} else {
					throw new Exception("Nenhum registro foi atualizado para idchamado=$idchamado.");
				}
			} else {
				throw new Exception("Falha ao executar a query de atualização para idchamado=$idchamado.");
			}
		} catch (Exception $e) {
			error_log("Erro ao atender chamado: " . $e->getMessage());
			throw $e; // Lança a exceção para ser tratada pela `operacaoDinamicaConjunta`
		}
	}	
	
	//Atualiza os chamados da tela de ajuste
	public static function ajustaChamados($idchamado, $categoria = null, $subcategoria = null, $ocorrencia = null, $prioridade = null) {
		try {
			if (!is_numeric($idchamado) || $idchamado <= 0) {
				return ['status' => 'error', 'success' => false, 'message' => 'ID do chamado inválido!'];
			}
	
			$campos = [];
			$valores = [':idchamado' => $idchamado];
	
			// Inclui todos os campos, mesmo os vazios, para permitir a limpeza de valores
			$campos[] = 'id_categoria = :categoria';
			$valores[':categoria'] = $categoria !== '' ? $categoria : null;
	
			$campos[] = 'seq_pla_subcategoria = :subcategoria';
			$valores[':subcategoria'] = $subcategoria !== '' ? $subcategoria : null;
	
			$campos[] = 'seq_pla_ocorrencia = :ocorrencia';
			$valores[':ocorrencia'] = $ocorrencia !== '' ? $ocorrencia : null;
	
			$campos[] = 'id_prioridade = :prioridade';
			$valores[':prioridade'] = $prioridade !== '' ? $prioridade : null;
	
			$sql = 'UPDATE chamados SET ' . implode(', ', $campos) . ' WHERE idchamado = :idchamado';
	
			error_log("SQL Gerada: $sql");
			error_log("Valores: " . json_encode($valores));
	
			$query = MySql::conectar()->prepare($sql);
			$query->execute($valores);
	
			$rowsAffected = $query->rowCount();
			if ($rowsAffected === 0) {
				return ['status' => 'error', 'success' => false, 'message' => 'Nenhum registro encontrado ou atualizado.'];
			}
	
			return ['status' => 'success', 'success' => true, 'message' => 'Chamado atualizado com sucesso!'];
	
		} catch (Exception $e) {
			error_log("Erro ao atualizar chamado: " . $e->getMessage());
			http_response_code(500);
			return ['status' => 'error', 'success' => false, 'message' => 'Erro ao processar o chamado: ' . $e->getMessage()];
		}
	}		
}

class delete {

	//delete usuario
	public static function usuario($tabela,$colunaID,$id=false,$showname){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		
		$sql->execute();
		alert::alertaDeletaUsuario($showname);
	}

	//Delete categorias de chamados
	public static function categoria($tabela,$colunaID,$id=false,$showname){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		
		$sql->execute();
		alert::alertaDeleteCategoria($showname);
	}

	//Deleta as ligações das ocorrêcnias com as subcategorias chamados
	public static function ligaocorrencia($tabela,$colunaID,$id=false){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		
		$sql->execute();
		return true;
	}

	//delete departamento
	public static function departamento($tabela,$colunaID,$id=false,$showname){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		
		$sql->execute();
		alert::alertaDeleteDept($showname);
	}

	//Excluir registro
	public static function excluir($tabela,$colunaID,$id=false,$showname,$destino){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		$sql->execute();
		
		if($destino == 'servico'){
			alert::DeleteServico($showname);
		}
		if($destino == 'operador'){
			alert::deleteOperador($showname);
		}
		if($destino == 'cargo'){
			alert::deleteCargo($showname);
		}
	}

	//delet slide
	public static function slide($tabela,$colunaID,$id=false,$showname){
		if($id == false){
			alert::Error($id);
		}else{
			$sql = MySql::conectar()->prepare("DELETE FROM $tabela WHERE $colunaID = $id");
		}
		
		$sql->execute();
		alert::DeleteSlide($showname);
	}

	//deletar arquivo avatar do usuario
	public static function avatar($file){
		@unlink(DELET_AVATAR.$file);
	}
}

class alert {
	
	public static function Error($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertErro(msg);};</script>';
	}

	public static function Warning($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertWarning(msg);};</script>';
	}

	public static function uploadSlide($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertUpSlide(msg);};</script>';
	}

	public static function DeleteSlide($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelSlide(msg);};</script>';
	}

	public static function DeleteServico($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelService(msg);};</script>';
	}

	public static function UpdateServico($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertUpService(msg);};</script>';
	}

	//cadastro departamento
	public static function CadDept($msgd){
		echo '<script>const msg = '.json_encode($msgd).'; window.onload = function(){alertCadDepartamento(msg);};</script>';
	}

	public static function alertaEditarDept($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertEditDept(msg);};</script>';
	}

	//update cargos
	public static function alertaEditarCargo($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertEditCargo(msg);};</script>';
	}

	public static function alertaDeleteDept($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelDept(msg);};</script>';
	}

	//cadastro cargos
	public static function CadCargo($msgm){
		echo '<script>const msg = '.json_encode($msgm).'; window.onload = function(){alertCadCargo(msg);};</script>';
	}

	//cadastro de categoria de chamados
	public static function Cadcategoria($msgm){
		echo '<script>const msg = '.json_encode($msgm).'; window.onload = function(){alertCadCategoriaCh(msg);};</script>';
	}

	//cadastro de ocorrencia de chamados
	public static function cadastraocorrencia($msgm){
		echo '<script>const msg = '.json_encode($msgm).'; window.onload = function(){alertcadocorrencia(msg);};</script>';
	}

	//cadastro de sub categoria de chamados
	public static function Cadsubcategoria($msgm){
		echo '<script>const msg = '.json_encode($msgm).'; window.onload = function(){alertCadsubCategoriaCh(msg);};</script>';
	}

	//update de categorias
	public static function alertaEditarCategoria($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertEditCategoria(msg);};</script>';
	}

	//update de subcategorias
	public static function alertaEditarSubCategoria($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertEditSubCategoria(msg);};</script>';
	}

	public static function alertaeditaocorrencia($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alerteditocorrencia(msg);};</script>';
	}

	public static function alertaDeleteCategoria($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelCategoria(msg);};</script>';
	}

	public static function alertaCadUsuario($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertCadUser(msg);};</script>';
	}

	public static function EditarUsuario($msg, $acao){
		echo '<script>const msg = '.json_encode($msg).'; const acao = '.json_encode($acao).'; window.onload = function(){alertEditUser(msg, acao);};</script>';
	}

	//edita operador
	public static function alertaEditarOp($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertEditOp(msg);};</script>';
	}

	public static function alertaDeletaUsuario($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelUser(msg);};</script>';
	}
	public static function cadOperador($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertOperator(msg);};</script>';
	}

	public static function deleteOperador($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelOperator(msg);};</script>';
	}

	public static function deleteCargo($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertDelCargo(msg);};</script>';
	}

	public static function alertaAbrirCh($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertAbrirChamado(msg);};</script>';
	}

	public static function alertAtendeChamado($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertAtendChamado(msg);};</script>';
	}

	public static function alertaEmailNulo($seq_pla_chamado, $acao){
		echo '<script>const seq_pla_chamado = '.json_encode($seq_pla_chamado).'; const acao = '.json_encode($acao).'; window.onload = function(){alertEmailNulo(seq_pla_chamado, acao);};</script>';
	}

	public static function alertaRespondeChamado($id, $acao, $funcao){
		echo '<script>const id = '.json_encode($id).'; const acao = '.json_encode($acao).'; const funcao = '.json_encode($funcao).'; window.onload = function(){alertRespondeChamado(id, acao, funcao);};</script>';
	}

	public static function alertaRevalidaChamado($id, $acao, $funcao){
		echo '<script>const id = '.json_encode($id).'; const acao = '.json_encode($acao).'; const funcao = '.json_encode($funcao).'; window.onload = function(){alertrevalidaChamado(id, acao, funcao);};</script>';
	}

	public static function alertaAtribuirChamado($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertatribuichamado(msg);};</script>';
	}

	public static function alertaFinalizaChamado($msg){
		echo '<script>const msg = '.json_encode($msg).'; window.onload = function(){alertFinalChamado(msg);};</script>';
	}
}

class consulta {

	//Consulta permissão
	public static function permissao($coluna, $sessao, $modulo, $check){
		$sql = MySql::conectar()->prepare("SELECT * FROM permissoes WHERE $modulo = '$check' AND $coluna = $sessao");
		$sql->execute();
		return count($sql->fetchAll());
	}

	//Consulta Permissões para menu
	public static function permissaoMenu($coluna, $sessao, $modulo, $check){
		//separo meu array
		$module = implode(",",$modulo);
		//crio condição com array
		$condicao = implode(" = '$check' OR ",$modulo)." = '$check'";
		$sql = MySql::conectar()->prepare("SELECT $module FROM permissoes WHERE $coluna = $sessao AND ($condicao)");
		$sql->execute();
		return count($sql->fetchAll());
	}

	//consulta operador
	public static function operador($idoperador) {
		$sql = MySql::conectar()->prepare("SELECT o.idoperator, o.surname, o.id_access FROM operadores o WHERE o.id_user = :id_user");
		$sql->bindParam(':id_user', $idoperador, PDO::PARAM_INT);
		$sql->execute();
		
		return $sql->fetch(PDO::FETCH_ASSOC); // Retorna uma única linha como array associativo
	}
	

	public static function pegaoperador($idoperador, $access){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE id_user = $idoperador");
		$sql->execute();
		return $sql->fetchAll();
	}

	//consulta operador
	public static function operator($idoperador){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE id_user = $idoperador");
		$sql->execute();
		
		return count($sql->fetchAll());
	}

	//Consulta Permissões para menu
	public static function permissaoMenuOperador($coluna, $idoperador){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE $coluna = $idoperador");
		$sql->execute();
		return count($sql->fetchAll());
	}

	//Consulta permissão operador [verificar se funciona]
	public static function permissaoOperador($idacesso){
		$sql = MySql::conectar()->prepare("SELECT * FROM operadores WHERE id_user = $idacesso");
		$sql->execute();
		return count($sql->fetchAll());
	}

	//Consulta chamados dentro do mês atual
	public static function chamadosdomes(){
		$sql = MySql::conectar()->prepare("SELECT * FROM chamados ch WHERE	MONTH ( ch.data_abertura ) = MONTH (CURRENT_DATE ()) AND YEAR ( ch.data_abertura ) = YEAR ( CURRENT_DATE ());");

		$sql->execute();
		return count($sql->fetchAll());
	}

	//Consultar se já existe a categoria cadastrada
	public static function cadastrocategoria($nomecategoria, $idcategoria = NULL) {
		$sql = "SELECT * FROM chamados_categoria cat WHERE cat.nomecategoria = '$nomecategoria'";
		if ($idcategoria !== NULL) {
			$sql .= ' AND idcategoria != '.$idcategoria.'';
		}
		
		$sql = MySql::conectar()->prepare($sql);
		$sql->execute();
		return count($sql->fetchAll());
	}

	//Consultar se já existe a sub categoria cadastrada
	public static function cadastrosubcategoria($nomesubcategoria, $idsubcategoria = NULL) {
		$sql = "SELECT * FROM chamados_subcategoria subcat WHERE subcat.nome_subcategoria = '$nomesubcategoria'";
		if ($idsubcategoria !== NULL) {
			$sql .= ' AND seq_pla_subcategoria != '.$idsubcategoria.'';
		}
		
		$sql = MySql::conectar()->prepare($sql);
		$sql->execute();
		return count($sql->fetchAll());
	}

	//consulta chamado se já foi atendido para continuar o processo de atender.
	public static function chamadoAtendido($idchamado) {
		try {
			// Consulta para buscar o status atual do chamado
			$query = "SELECT id_status FROM chamados WHERE idchamado = :idchamado";
			$stmt = MySql::conectar()->prepare($query);
	
			// Bind do parâmetro
			$stmt->bindParam(':idchamado', $idchamado, PDO::PARAM_INT);
	
			// Executa a query
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
			// Verifica se o status foi encontrado
			if ($result && !empty($result['id_status'])) {
				return (int) $result['id_status']; // Retorna o status atual
			}
			return false; // Não encontrou status
		} catch (PDOException $e) {
			error_log("Erro ao verificar status do chamado: " . $e->getMessage());
			return false;
		}
	}		
}

class parametros{

    public static function assinaturach($tipo, $nota, $idchamado, $descricao, $observacao, $atendente, $solicitante){

		if ($tipo == 'abrechamado') {
			$atender = '<br />
						Olá!<br />
						Seu chamado foi aberto com sucesso!<br /><br />
						<b>Solicitante: </b>'.$solicitante.'<br />
						<b>Chamado: </b>'.$idchamado.'<br /><br />
						<b>Descrição: </b>'.$descricao.'<br /><br /><br />
						Aguarde que em breve um atendente irá lhe atender!<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>CRM
									<p>Sistema de Gestão de Chamados TI</p>
								</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'encaminharchamado') {
			$atender = '<br />
						Seu chamado está sendo encaminhado para o atendente '.$atendente.' para ser atendido na sequência.<br /><br />
						<b>ID Chamado: </b>'.$idchamado.'<br />
						<b>Atendente: </b>'.$atendente.'<br /><br />
						Em breve será feito um retorno sobre o atendimento!<br /><br /><br />
						
						Ficamos à disposição.<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'atribuirChamado') {
			$atender = '<br />
						Chamado <b>'.$idchamado.'</b> está sendo atribuido para o atendente <b>'.$atendente.'</b> para ser atendido.<br /><br /><br />
						<b>ID Chamado: </b>'.$idchamado.'<br />
						<b>Atendente: </b>'.$atendente.'<br /><br />
						Em breve será feito um retorno sobre o atendimento!<br /><br /><br />
						
						Ficamos à disposição.<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'enviachamadoaoprestador') {
			$atender = '<br />
						Esse e-mail foi enviado automaticamente pelo sistema CRM de chamados da <b>'.NOME_EMPRESA.'</b>!<br />
						<b>'.$atendente.'</b> Por gentileza, poderia verificar a solicitação abaixo do colaborador <b>'.$solicitante.'</b>.<br /><br />
						<b>Chamado: </b>'.$descricao.'<br /><br />
						<b>Observação: </b>'.$observacao.'<br /><br /><br />
						
						Ficamos à disposição.<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}
		
		if ($tipo == 'atender') {
			$atender = '<br />
						Seu chamado está nesse momento sendo atendido por '.$atendente.'!<br />
						Em breve será feito um retorno sobre o atendimento!<br /><br />
						
						Ficamos à disposição.<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'respondersolicitante') {
			$atender = '<br />
						Seu chamado Nº: <b>'.$idchamado.'</b> foi movimentado, acesse o portal e veja o andamento!<br />
						<br /><br />
						Ficamos à disposição.<br /><br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'responderatendente') {
			$atender = '<br />
						O chamado Nº: <b>'.$idchamado.'</b> foi movimentado, acesse o portal e conduza o chamado para a solução !<br />
						<br /><br />
						<br />
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'concluirchamado') {
			$atender = '<br />
						Seu chamado Nº: <b>'.$idchamado.'</b> está nesse momento sendo <b>concluido</b>, não se esqueça de conferir e avaliar nosso atendimento!</b>
						<p>Ficamos à disposição.</p>
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'finalizarchamado') {
			$atender = '<br />
						O chamado Nº: <b>'.$idchamado.'</b> foi finalizado pelo solicitante <i>'.$solicitante.'</i>.
						<p>Foi finalizado com a <b>Nota: </b>'.$nota.'</p>
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}

		if ($tipo == 'notabaixa') {
			$atender = '<br />
						O solicitante '.$solicitante.' concedeu uma nota abaixo do esperado!
						<p>O chamado <b>'.$idchamado.'</b> recebeu a nota <b>'.$nota.'</b>!<p>
						<table>
							<tr style="width="100%"; height:68.25pt;">
								<td style="height:68.25pt;"><img src="cid:logo_ref" width="305px" height="106px"></td>
								<td></td>
								<td style="line-height:115%"><b>Departamento de TI</td>
							</tr>
						</table>
						';

			return $atender;
		}
    }
}

class transacoes {
	//Realiza transação unificada
	public static function operacaoConjunta($paramsUpdate1, $paramsInsert2, $paramsInsert3, $paramsInsert1, $paramsUpdate2) {
        try {
            $pdo = MySql::conectar();
            $pdo->beginTransaction();

            // Verifica se a transação foi iniciada
            if (!$pdo->inTransaction()) {
                throw new Exception("Transação não foi iniciada corretamente.");
            }

            // Executa o UPDATE realizando a atualização do primeiro registro de atendimento
            $updateSuccess = update::fechaAtendimento(...$paramsUpdate1);
            if (!$updateSuccess) {
                throw new Exception("Erro no UPDATE.");
            }

            // Executa o segundo INSERT realizando a inserção do novo atendimento do chamado
            $idInsert2 = insert::andamentoChamado(...$paramsInsert2);
            if (!$idInsert2) {
                throw new Exception("Erro no segundo INSERT.");
            }

			// Captura o lastInsertId do andamentoChamado
			$seq_pla_atendimento = $idInsert2;

			// Adiciona o lastInsertId ao conjunto de parâmetros para movimentacoesChamados
			$paramsInsert3['seq_pla_atendimento'] = $seq_pla_atendimento;

			// Executa o registro da movimentação
            $idInsert3 = insert::movimentacoesChamados($paramsInsert3);
            if (!$idInsert3) {
                throw new Exception("Erro no segundo INSERT.");
            }

			$paramsInsert1['seq_pla_atendimento'] = $seq_pla_atendimento;
			// Executa o primeiro INSERT registrando a troca de operador
            $idInsert1 = insert::registraTrocaOperador($paramsInsert1);
            if (!$idInsert1) {
                throw new Exception("Erro no primeiro INSERT.");
            }

			//Executa a alteração do operador do chamado na chamados
			$updateOperador = update::alteraOperador(...$paramsUpdate2);
			if (!$updateOperador) {
				throw new Exception("Erro ao alterar operador.");
			}

            // Confirma a transação
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erro na operação conjunta: " . $e->getMessage());
            return "Erro: " . $e->getMessage();
        }
    }

	//realiza novo registro de chamados e atualiza o novo operador do chamado
	public static function novoAndamentoOperador($paramsInsert, $paramsUpdate) {
		try {
			$pdo = MySql::conectar();
			$pdo->beginTransaction();
	
			// Verifica se a transação foi iniciada
			if (!$pdo->inTransaction()) {
				throw new Exception("Transação não foi iniciada corretamente.");
			}
	
			// Realiza o INSERT no andamento do chamado
			$idInsert = insert::andamentoChamado(...$paramsInsert);
			if (!$idInsert) {
				throw new Exception("Erro ao inserir o andamento do chamado.");
			}
	
			// Realiza o UPDATE do operador do chamado
			$updateOperador = update::alteraOperador(...$paramsUpdate);
			if (!$updateOperador) {
				throw new Exception("Erro ao atualizar o operador do chamado.");
			}
	
			// Confirma a transação
			$pdo->commit();
			return true;
		} catch (Exception $e) {
			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}
			error_log("Erro na operação conjunta (andamento e operador): " . $e->getMessage());
			return "Erro: " . $e->getMessage();
		}
	}
	
	//Operação conjunta dinamica
	// public static function operacaoDinamicaConjunta(array $operacoes) {
	// 	try {
	// 		$pdo = MySql::conectar();
	// 		$pdo->beginTransaction();
	
	// 		if (!$pdo->inTransaction()) {
	// 			throw new Exception("Transação não foi iniciada corretamente.");
	// 		}
	
	// 		// Executa cada operação
	// 		foreach ($operacoes as $operacao) {
	// 			error_log("Executando operação: " . json_encode($operacao));
	
	// 			if (!isset($operacao['funcao'], $operacao['parametros'])) {
	// 				throw new Exception("Operação inválida: " . json_encode($operacao));
	// 			}
	
	// 			$resultado = call_user_func_array($operacao['funcao'], $operacao['parametros']);
	// 			error_log("Resultado da operação: " . json_encode($resultado));
	
	// 			if (!$resultado) {
	// 				throw new Exception("Erro na operação: " . $operacao['funcao']);
	// 			}
	// 		}
	
	// 		$pdo->commit();
	// 		return true;
	// 	} catch (Exception $e) {
	// 		if ($pdo->inTransaction()) {
	// 			$pdo->rollBack();
	// 		}
	// 		error_log("Erro na operação dinâmica: " . $e->getMessage());
	// 		return false;
	// 	}
	// }

	public static function operacaoDinamicaConjunta(array $operacoes) {
		try {
			$pdo = MySql::conectar();
			$pdo->beginTransaction();
	
			if (!$pdo->inTransaction()) {
				throw new Exception("Transação não foi iniciada corretamente.");
			}
	
			error_log("Transação iniciada para operações dinâmicas.");
			$resultados = [];
	
			foreach ($operacoes as $index => $operacao) {
				error_log("Iniciando operação #$index: " . json_encode($operacao));
	
				// Valida a estrutura da operação
				if (!isset($operacao['funcao'], $operacao['parametros'])) {
					throw new Exception("Operação inválida: " . json_encode($operacao));
				}

				$parametros = $operacao['parametros'];
				array_walk_recursive($parametros, function (&$valor) use ($resultados) {
					if (is_string($valor) && str_starts_with($valor, '$')) {
						$referencia = substr($valor, 1);
						if (isset($resultados[$referencia])) {
							$valor = $resultados[$referencia];
						} else {
							throw new Exception("Referência inválida: $valor");
						}
					}
				});

				try {
					// Executa a função dinâmica com os parâmetros fornecidos
					$resultado = call_user_func_array($operacao['funcao'], $parametros);
					error_log("Operação #$index executada: " . $operacao['funcao']);

					// Verifica se o resultado da operação é `false`, indicando falha
					if ($resultado === false) {
						throw new Exception("Erro na operação: " . $operacao['funcao']);
					}

					// Armazena o resultado da operação
					$resultados[(string)$index] = $resultado;
					error_log("Resultado da operação #$index: " . json_encode($resultado));
				} catch (Exception $e) {
					error_log("Erro durante a execução da operação #$index: " . $e->getMessage());
					throw $e;
				}
			}
	
			// Se todas as operações foram bem-sucedidas, commit da transação
			$pdo->commit();
			error_log("Transação concluída com sucesso.");
			return $resultados;
		} catch (Exception $e) {
			// Se qualquer operação falhar, rollback da transação
			if (isset($pdo) && $pdo->inTransaction()) {
				$pdo->rollBack();
				error_log("Transação revertida devido a erro: " . $e->getMessage());
			}
	
			error_log("Erro final na operação dinâmica: " . $e->getMessage());
			echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
			return []; // Retorna array vazio para indicar falha geral
		}
	}
}

?>