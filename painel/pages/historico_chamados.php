<?
//verifica access
checkaccesspageoperator('denied');
?>

<div class="box_content">
	<h2><i class="fa-solid fa-book-open-reader"></i>&nbsp; Histórico de Atendimentos</h2>
	
	<form method="post" enctype="multipart/form-data">
        <div class="box_painel">
            <div class="form_group box-painel-historico">
                <label>Usuário Solicitante:</label>
                <select name="solicitante">
                    <option value=""></option>
                    <?
                    $cargos = select::All('usuarios');
                    foreach($cargos as $key => $resultcargo){
                        echo '<option value="'.$resultcargo['iduser'].'">'.$resultcargo['nomeuser'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form_group box-painel-historico">
                <label>Atendente:</label>
                <select name="atendente">
                    <option value=""></option>
                    <?
                    $cargos = select::All('operadores');
                    foreach($cargos as $key => $resultcargo){
                        echo '<option value="'.$resultcargo['idoperator'].'">'.$resultcargo['surname'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form_group box-painel-historico">
                <label>Periodo de Abertura:</label>
                <input type="date" name="periodoabertura1" />
                <input type="date" name="periodoabertura2" />
            </div>
            <div class="form_group box-painel-historico">
                <label>Periodo de Fechamento:</label>
                <input type="date" name="periodofechamento1" />
                <input type="date" name="periodofechamento2" />
            </div>
            <div class="form_group box-painel-historico">
                <label>Inicio e Final:</label>
                <input type="date" name="dtabertura" />
                <input type="date" name="dtfechamento" />
            </div>
            <div class="form_group box-painel-historico">
                <label>Chamado:</label>
                <input type="text" name="pesquisaid" />
            </div>
            <div class="clear"></div>
        </div>
        <div class="form_group">
			<input type="submit" value="Consultar" />
		</div>
	</form>
    <?
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        $array[] = $_POST;

        foreach ($array as $key => $result) {

            $solicitante = $result['solicitante'];
            $atendente = $result['atendente'];
            $periodoabertura1 = $result['periodoabertura1'];
            $periodoabertura2 = $result['periodoabertura2'];
            $periodofechamento1 = $result['periodofechamento1'];
            $periodofechamento2 = $result['periodofechamento2'];
            $dtabertura = $result['dtabertura'];
            $dtfechamento = $result['dtfechamento'];
            $pesquisaid = $result['pesquisaid'];

            $resulta1 = select::historicochamado($solicitante, $atendente, $periodoabertura1, $periodoabertura2, $periodofechamento1, $periodofechamento2, $dtabertura, $dtfechamento, $pesquisaid);

            foreach ($resulta1 as $key => $filtro1) {

                $dtabertura = date_create($filtro1['data_abertura']);
                $dtfechamento = date_create($filtro1['data_fechamento']);
                $idchamado = $filtro1['idchamado'];

                $files =select::useranexo($idchamado);
				$rowfiles = count($files);

                echo "
                    <div>
                        <div class='grade_chamado form_group'>
                            <div>
                            <label>Solicitante: </label>" . $filtro1['solicitante'] . " <label> &nbsp Data de Abertura: </label>" . date_format($dtabertura, "d/m/Y H:i") . " <label>&nbsp Nº Solicitação: </label>" . $filtro1['idchamado'] . " <label> &nbsp Data de Fechamento: </label>" . date_format($dtfechamento, "d/m/Y H:i") . " 
                            </div>
                        <div>
                            <label>Status: </label>" . $filtro1['status'] . " <label> &nbsp Prioridade: </label>" . $filtro1['prioridade'] . "";
                if (!empty($filtro1['atendente'])) {
                    echo "<label> &nbsp Atendente: </label>" . $filtro1['atendente'] . "";
                }
                echo " <label> &nbsp Categoria: </label>" . $filtro1['categoria'] . "
                        </div>
                        <div>
                            <label>Nota: </label><b>".$filtro1['nota']."</b>
                        </div>
                        <div align='center'>
                            <label>Descrição do chamado</label>
                        </div>
                        <div class='descricao-chamado'>
                            <span>" . $filtro1['descricao'] . "</span>
                        </div>
                            ";
                if ($rowfiles > 0) {
					echo "<div class='grade-ch-anexo'><label>Documentações anexas: </label>";
					foreach($files as $key => $resultanexo){
						$file = $resultanexo['nomeanexo'];
						$requester = $resultanexo['solicitante'];
						$operator = $resultanexo['operador'];

						if($requester !== null){
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">".$requester."</a> &emsp; ";
						}
						if($operator !== null){
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">".$operator."</a> &emsp; ";
						}
						
					}
					echo "</div>";	
				}
                //query historico
                $historicoh = select::selected('historico_chamados', 'id_chamado =' . $filtro1['idchamado'] . ' ORDER BY data_movimentacao DESC');
                foreach ($historicoh as $key => $resulth) {
                    $datah = date_create($resulth['data_movimentacao']);
                    $pegaatendente = select::selected('operadores', 'idoperator = ' . $resulth['atendente'] . '');
					foreach ($pegaatendente as $key => $resultoperador)
						$attendant = $resultoperador['surname'];

                    if ($filtro1['status'] == 'Finalizado') {

                        if ($resulth['resposta_atendente'] > '0') {

                            echo "
                                    <div class='grade-ch-resposta-at'>
                                        <div>
                                            <label>Atendente:</label> " . $attendant . " &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
                                        </div>
                                            <label>Respondeu:</label> " . $resulth['resposta_atendente'] .
                            "</div>";
                        }
                    }

                    if ($resulth['resposta_solicitante'] > '0') {
                        echo "
                                <div class='grade-ch-resposta-so'>
                                    <div>
                                        <label>Solicitante:</label> " . $filtro1['solicitante'] . " &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
                                    </div>
                                        <label>Respondeu:</label> " . $resulth['resposta_solicitante'] . "
                                </div>";
                    }
                }
                echo "
                        </div>
					</div>";
            }
        }
    }
    
    ?>
</div>