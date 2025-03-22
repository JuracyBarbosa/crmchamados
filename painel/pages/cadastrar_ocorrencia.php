<?
// Verifica access
checkAccessPage('cadastraocorrencia', 's', 'denied');

$consultacategoria = select::selected('chamados_categoria c', 'c.status = "A"');
$consultasubcategoria = select::selected('chamados_subcategoria sc', 'sc.status_subcategoria = "A"');

?>

<div class="box_content">
    <h2><i class="fas fa-edit"></i> Cadastrar Ocorrência</h2>
    <form method="post" enctype="multipart/form-data">
        <?
        if (isset($_POST['acao'])) {

            $nomeocorrencia = $_POST['nomeocorrencia'];
            $descocorrencia = $_POST['descocorrencia'];
            @$statusocorrencia = $_POST['statusocorrencia'];
            @$categoria = $_POST['categoria'];
            @$subcategoria = $_POST['subcategoria'];


            if ($nomeocorrencia == '') {
                painel::alert('aviso', 'Preencha o campo nome, com o nome da ocorrência');
            } elseif ($descocorrencia == '') {
                painel::alert('aviso', 'Preencha com uma observação do que se trata essa ocorrência');
            } elseif ($statusocorrencia == '') {
                painel::alert('aviso', 'Você precisa escolher um status para esse cadastro');
            } else {
                $nomeocorrencia = verificar::valida_dados($nomeocorrencia);
                $verificar = verificar::ocorrenciacadastrada($nomeocorrencia);

                if ($verificar == false) {

                    try {
                        $cadocorrencia = insert::cadastraOcorrencia($nomeocorrencia, $descocorrencia, $statusocorrencia, $categoria, $subcategoria);
                        
                        // Se chegou aqui, a inserção foi bem-sucedida
                        alert::cadastraocorrencia($nomeocorrencia);

                    } catch (Exception $ex) {
                        // Se houve algum erro durante a inserção, exiba o erro
                        painel::alert('erro', 'Algo deu errado no cadastro da ocorrência! Erro: ' . $ex->getMessage());
                    }

                } else {
                    painel::alert('erro', 'Ocorrência <b>' . $nomeocorrencia . '</b> já existe!');
                }
            }
        }
        ?>
        <div class="form_group">
            <label>Nome da Ocorrência:</label>
            <input type="text" name="nomeocorrencia" />
        </div>
        <div class="form_group">
            <label>Observação:</label>
            <textarea name="descocorrencia"></textarea>
        </div>
        <div class="form_group">
            <span>Status:</span>
            <input type="radio" name="statusocorrencia" value="A" /> Ativo
            <input type="radio" name="statusocorrencia" value="I" /> Inativo
        </div>
        <br />
        <h2><i class="fa-solid fa-arrow-right-arrow-left"></i> Associar ocorrência</h2>
        <div class="form_group">
            <label>Categorias:</label>
            <ul class="form_group_checkbox">
                <?
                while ($row = $consultacategoria->fetch()) {
                    echo '<li><input type="checkbox" name="categoria[]" value="' . $row['idcategoria'] . '"> ' . $row['nomecategoria'] . '</li>';
                }
                ?>
            </ul>
        </div>
        <div class="form_group">
            <label>Subcategorias:</label>
            <ul class="form_group_checkbox">
                <?
                while ($row = $consultasubcategoria->fetch()) {
                    echo '<li><input type="checkbox" name="subcategoria[]" value="' . $row['seq_pla_subcategoria'] . '"> ' . $row['nome_subcategoria'] . '</li>';
                }
                ?>
            </ul>
        </div>

        <div class="form_group_button">
            <input type="submit" name="acao" value="Cadastrar" />
        </div>
    </form>
</div>
