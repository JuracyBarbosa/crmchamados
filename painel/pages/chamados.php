<?php
session_start();

$status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status']), ENT_QUOTES, 'UTF-8') : '';
$statusCode = null;
$atendente = null;

$buscaAtendente = consulta::operador($_SESSION['iduser']);
if ($buscaAtendente && is_array($buscaAtendente)) {
    if ($status === 'atendendo') {
        $statusCode = 3;
        $atendente = $buscaAtendente['idoperator']; // Acessa diretamente o resultado
    }
}

//Consulta chamados
$chamados = select::chamados($statusCode ?? $status, $atendente);
$data = $chamados->fetchAll(PDO::FETCH_ASSOC);

// Limpeza de dados e processamento de anexos
foreach ($data as &$chamado) {
    // Pega ID do chamado
    $idChamado = $chamado['idchamado'];

    // Consulta o histórico do chamado
    $consultaHistoricos = select::historicochamados($idChamado);
    $chamado['historico'] = $consultaHistoricos;

    // Consulta os anexos do chamado
    $files = select::useranexo($idChamado);
    $rowfiles = count($files);
    $chamado['anexos'] = [
        'quantidade' => $rowfiles,
        'detalhes' => $files,
    ];

    // Limpeza de campos com valores nulos ou vazios
    foreach ($chamado as $coluna => $valor) {
        if (is_null($valor) || $valor === '') {
            unset($chamado[$coluna]);
        }
    }
}

unset($chamado);

//Painel chamados
//seleciona chamados abertos
$chamadosAbertos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '1'");
$chamadosAbertos->execute();
$chamadosAbertos = $chamadosAbertos->rowCount();

//seleciona chamados Encaminhados
$chamadosEncaminhados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '2'");
$chamadosEncaminhados->execute();
$chamadosEncaminhados = $chamadosEncaminhados->rowCount();

//seleciona chamados Em Atendimento
$chamadosEmatendimento = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '3'");
$chamadosEmatendimento->execute();
$chamadosEmatendimento = $chamadosEmatendimento->rowCount();

//seleciona chamados Aguardando retorno
$chamadosAguardando = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '8'");
$chamadosAguardando->execute();
$chamadosAguardando = $chamadosAguardando->rowCount();

//seleciona chamados Concluidos
$chamadosConcluidos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '4'");
$chamadosConcluidos->execute();
$chamadosConcluidos = $chamadosConcluidos->rowCount();

//seleciona chamados Finalizados
$chamadosFinalizados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '5'");
$chamadosFinalizados->execute();
$chamadosFinalizados = $chamadosFinalizados->rowCount();

//seleciona chamados Total
$totalchamados = MySql::conectar()->prepare("SELECT * FROM chamados");
$totalchamados->execute();
$totalchamados = $totalchamados->rowCount();

$chamadosdomes = consulta::chamadosdomes();

//FIM
?>
<div class="box_painel">
    <div class="box_painel_ch_single">
        <div class="box_painel_ch_wraper">
            <h2>Painel de chamados</h2>
            <div class="painel-list-chamados">
                <label>Abertos:</label>
                <span><? echo ($chamadosAbertos); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Encaminhados:</label>
                <span><? echo ($chamadosEncaminhados); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Sendo Atendido:</label>
                <span><? echo ($chamadosEmatendimento); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Aguardando Retorno:</label>
                <span><? echo ($chamadosAguardando); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Concluídos:</label>
                <span><? echo ($chamadosConcluidos); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Finalizados:</label>
                <span><? echo ($chamadosFinalizados); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Total de chamados:</label>
                <span><? echo ($totalchamados); ?></span>
            </div>
            <div class="painel-list-chamados">
                <label>Chamados do Mês:</label>
                <span><? echo $chamadosdomes; ?></span>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="container-filtros">
    <div id="filtros" class="container-select">
        <div class="div_group">
            <label>Categoria:</label>
            <select id="categoriaFiltro">
                <option value="" selected=""></option>
                <?
                $categoria = MySql::conectar()->prepare("SELECT * FROM chamados_categoria ORDER BY idcategoria ASC");
                $categoria->execute();
                $categoria = $categoria->fetchAll();
                foreach ($categoria as $key => $value) {
                ?>
                    <option value="<? echo $value['nomecategoria'] ?>"><? echo $value['nomecategoria']; ?></option>
                <? } ?>
            </select>
        </div>
        <div class="div_group">
            <label>Subcategoria:</label>
            <select id="subcategoriaFiltro">
                <option value="" selected=""></option>
                <?
                $subcategoria = MySql::conectar()->prepare("SELECT * FROM chamados_subcategoria ORDER BY seq_pla_subcategoria ASC");
                $subcategoria->execute();
                $subcategoria = $subcategoria->fetchAll();
                foreach ($subcategoria as $key => $value) {
                ?>
                    <option value="<? echo $value['nome_subcategoria'] ?>"><? echo $value['nome_subcategoria']; ?></option>
                <? } ?>
            </select>
        </div>
        <div class="div_group">
            <label>Ocorrência:</label>
            <select id="ocorrenciaFiltro">
                <option value="" selected=""></option>
                <?
                $sqlocorrencia = MySql::conectar()->prepare("SELECT * FROM chamados_ocorrencia ORDER BY seq_pla_ocorrencia ASC");
                $sqlocorrencia->execute();
                $ocorrencias = $sqlocorrencia->fetchAll();
                foreach ($ocorrencias as $key => $value) {
                ?>
                    <option value="<? echo $value['nome_ocorrencia'] ?>"><? echo $value['nome_ocorrencia']; ?></option>
                <? } ?>
            </select>
        </div>
        <div class="div_group">
            <label>Solicitante:</label>
            <select id="solicitanteFiltro">
                <option value="" selected=""></option>
                <?
                $sqlsolicitante = MySql::conectar()->prepare("SELECT * FROM usuarios u ORDER BY u.loginuser ASC");
                $sqlsolicitante->execute();
                $solicitantes = $sqlsolicitante->fetchAll();
                foreach ($solicitantes as $key => $value) {
                ?>
                    <option value="<? echo $value['nomeuser'] ?>"><? echo $value['loginuser']; ?></option>
                <? } ?>
            </select>
        </div>
        <div class="div_group">
            <label for="codigoFiltro">Chamado:</label>
            <input type="text" id="codigoFiltro" placeholder="">
        </div>
    </div>
    <div class="container-btn-brilho">
        <?php
        $statusPermitidos = [1, 2, 3, 4, 8];
        $listStatus = select::obterStatus($statusPermitidos);

        // Define a ordem personalizada dos botões de navegação
        $ordemDesejada = [1, 8, 2, 3, 4];

        // Ordena a lista de status com base na ordem personalizada
        usort($listStatus, function ($a, $b) use ($ordemDesejada) {
            $posA = array_search($a['idstatus'], $ordemDesejada);
            $posB = array_search($b['idstatus'], $ordemDesejada);
            return $posA - $posB;
        });

        $contador = 0;

        if (!empty($listStatus)):
            foreach ($listStatus as $statusResult):
                $contador++;
        ?>
                <button class="btn-brilho"
                    id="btnstatus_<?php echo $statusResult['idstatus']; ?>"
                    data-status="<?php echo htmlspecialchars($statusResult['idstatus']); ?>">
                    <?php echo htmlspecialchars($statusResult['nomestatus']); ?>
                </button>
                <?php
                //Botão estático entre os botões dinamicos
                if ($contador == 1):
                ?>
                    <button class="btn-brilho" id="btnstatus_atendendo" data-status="atendendo" onclick="buscarChamadoStatus('atendendo')">Atendendo</button>
        <?php
                endif;
            endforeach;
        endif;
        ?>
    </div>
</div>

<div class="box_content">
    <div id="chamadosContainer" class="wraper_table ch-sts-btn">
        <!-- Chamados serão renderizados aqui -->
    </div>
    <div id="modal-container"></div>
</div>

<!-- Campo oculto para passar os dados PHP para o JavaScript -->
<input type="hidden" id="registroChamados" value="<?php echo htmlspecialchars(json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8'); ?>" />