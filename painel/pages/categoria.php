<!-- Select de Categoria -->
<div class="form_group">
    <label for="categoria">Categoria:</label>
    <select name="categoria" id="categoria">
        <option value="" selected>Selecione...</option>
        <?php
        $categoria = MySql::conectar()->prepare("SELECT * FROM chamados_categoria ORDER BY idcategoria ASC");
        $categoria->execute();
        $categoria = $categoria->fetchAll();
        foreach ($categoria as $value) {
            echo '<option value="'.$value['idcategoria'].'">'.$value['nomecategoria'].'</option>';
        }
        ?>
    </select>
</div>

<!-- Select de Subcategoria -->
<div class="form_group">
    <label for="subcategoria">Subcategoria:</label>
    <select name="subcategoria" id="subcategoria">
        <option value="" selected>Selecione uma categoria primeiro</option>
    </select>
</div>

<script>
    // Quando o select de categoria é alterado

    //var subcategoriaSelect = document.getElementById('subcategoria');

    document.getElementById('categoria').addEventListener('change', function() {
        var categoriaId = this.value; // Obter o valor da categoria selecionada

        // Fazer uma requisição AJAX para obter as subcategorias relacionadas à categoria selecionada
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Atualizar o select de subcategoria com as opções retornadas
                    var subcategoriaSelect = document.getElementById('subcategoria');
                    subcategoriaSelect.innerHTML = xhr.responseText;
                    console.log('Resposta da requisição AJAX:', xhr.responseText);

                } else {
                    console.error('Erro na requisição AJAX');
                }
            }
        };
        xhr.open('GET', 'obter_subcategoria?idcategoria=' + categoriaId, true);
        xhr.send();
    });
</script>
