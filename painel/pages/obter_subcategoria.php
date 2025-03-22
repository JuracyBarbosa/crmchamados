<?php

class Subcategorias {
    public static function obterSubcategorias($categoriaId) {
        // Consulta SQL para obter as subcategorias relacionadas à categoria selecionada
        $sql = MySql::conectar()->prepare("SELECT sc.seq_pla_subcategoria, sc.nome_subcategoria FROM chamados_subcategoria sc WHERE sc.seq_pla_subcategoria = ?");
        $sql->execute([$categoriaId]);
        $subcategorias = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Construa as opções HTML para as subcategorias
        $html = '<option value="">Selecione...</option>';
        foreach ($subcategorias as $subcategoria) {
            $html .= '<option value="' . $subcategoria['seq_pla_subcategoria'] . '">' . $subcategoria['nome_subcategoria'] . '</option>';
        }

        // Retorne o HTML com as opções das subcategorias
        return $html;
    }
}

// Verifica se o parâmetro 'categoria_id' foi enviado na requisição GET
if (isset($_GET['idcategoria'])) {
    // Obtém o ID da categoria da requisição GET
    $categoriaId = $_GET['idcategoria'];

    // Chama o método da classe Subcategorias para obter as subcategorias
    $htmlSubcategorias = Subcategorias::obterSubcategorias($categoriaId);

    // Retorna o HTML com as opções das subcategorias
    echo $htmlSubcategorias;
    echo '<script>console.log("HTML retornado pelo PHP:", ' . json_encode($htmlSubcategorias) . ');</script>';
} else {
    // Caso não seja fornecido o ID da categoria, retorna uma resposta de erro
    http_response_code(400);
    echo "ID da categoria não fornecido.";
}
?>
