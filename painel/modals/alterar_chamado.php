<?php
require_once '../../classes/painel.php';
require_once __DIR__ . '/../../config.php';
session_start();
?>
<!-- Modal para ajustar os chamados-->
<div id="modal" class="modal">
    <div class="modal-content">
        <h2>Ajustar chamado</h2>
        <form id="alterar-form">
            <label for="status">Id Chamado</label>
            <input type="text" id="idchamado" name="idchamado" value="" readonly>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria">
                <option value="" selected>Selecione...</option>
                <?php
                $categorias = MySql::conectar()->prepare("SELECT * FROM chamados_categoria ORDER BY idcategoria ASC");
                $categorias->execute();
                $categorias = $categorias->fetchAll();

                foreach ($categorias as $categoria) {
                    echo "<option value='{$categoria['idcategoria']}'>{$categoria['nomecategoria']}</option>";
                }
                ?>
            </select>

            <label for="subcategoria">Subcategoria</label>
            <select id="subcategoria" name="subcategoria">
                <option value="" selected=""></option>
                <?
                $categoria = MySql::conectar()->prepare("SELECT * FROM chamados_subcategoria ORDER BY seq_pla_subcategoria ASC");
                $categoria->execute();
                $categoria = $categoria->fetchAll();
                foreach ($categoria as $key => $value) {
                ?>
                    <option value="<? echo $value['seq_pla_subcategoria'] ?>"><? echo $value['nome_subcategoria']; ?></option>
                <? } ?>
            </select>

            <label for="ocorrencia">Ocorrência</label>
            <select id="ocorrencia" name="ocorrencia">
                <option value="" selected=""></option>
                <?
                $categoria = MySql::conectar()->prepare("SELECT * FROM chamados_ocorrencia ORDER BY seq_pla_ocorrencia ASC");
                $categoria->execute();
                $categoria = $categoria->fetchAll();
                foreach ($categoria as $key => $value) {
                ?>
                    <option value="<? echo $value['seq_pla_ocorrencia'] ?>"><? echo $value['nome_ocorrencia']; ?></option>
                <? } ?>
            </select>

            <label for="prioridade">Prioridade</label>
            <select id="prioridade" name="prioridade">
                <option value="" selected=""></option>
                <?
                $categoria = MySql::conectar()->prepare("SELECT * FROM prioridade ORDER BY idprioridade ASC");
                $categoria->execute();
                $categoria = $categoria->fetchAll();
                foreach ($categoria as $key => $value) {
                ?>
                    <option value="<? echo $value['idprioridade'] ?>"><? echo $value['nomeprioridade']; ?></option>
                <? } ?>
            </select>

            <button type="submit" class="save">Salvar</button>
            <button type="button" class="close-btn">Cancelar</button>
        </form>
    </div>
</div>