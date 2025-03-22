<?php
session_start();

$idcargo = $_SESSION['id_cargo'];
$iddept = $_SESSION['id_dept'];
if ($idcargo == NULL) {
    $idcargo = 0;
}
if ($iddept == null) {
    $iddept = 0;
}

$pegacargo = painel::pegacargo($idcargo);
$pegadept = select::selected('departamento', 'iddept = ' . $iddept . '');
if ($pegadept == '') {
    $pegadept = 0;
}

//variaveis de verificação do menu
$opcao_administracao = ['listadept', 'listacargo', 'listacategoria', 'listaocorrencia'];
$idaccessop = $_SESSION['iduser'];

// Verifique se a URL corresponde ao endpoint API
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'requisicaochamados') !== false) {
    // Não carregue o layout
    return;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Painel de Controle</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/fontawesome_v5.13.css">
    <link href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css" rel="stylesheet" />
    <link rel="icon" href="<?php echo INCLUDE_PATH; ?>favicon.ico" type="image/x-icon" />
    <link href="<? echo INCLUDE_PATH_PAINEL ?>css/sweetalert2.css" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="menu_btn">
            <i class="fa fa-bars"></i>
        </div>
        <div class="loggout">
            <a href="<?php echo INCLUDE_PATH_PAINEL; ?>">Home <i class="fas fa-home"></i></a>
            <a href="<?php echo INCLUDE_PATH_PAINEL; ?>editar_meuperfil">Editar Perfil <i class="fa-solid fa-user-tie"></i></a>
            <a href="<?php echo INCLUDE_PATH_PAINEL; ?>?loggout">Sair <i class="fas fa-sign-out-alt"></i></a>
        </div>
        <div class="clear"></div>
    </header>
    <div class="container">
        <aside class="menu">
            <div class="box_usuario">
                <?
                if ($_SESSION['avataruser'] == '') {
                ?>
                    <div class="avatar_usuario">
                        <i class="fa fa-user"></i>
                    </div>
                <? } else { ?>
                    <div class="imagem_usuario">
                        <img src="<? if ($_SESSION['avataruser'] == '') {
                                        echo '<i class="fa-solid fa-user"></i>';
                                    } else {
                                        echo INCLUDE_PATH_AVATAR ?><? echo $_SESSION['avataruser'];
                                                                    } ?>" />
                    </div>
                <? } ?>
                <div class="nome_usuario">
                    <p><? echo $_SESSION['nomeuser']; ?></p>
                    <p><? foreach ($pegacargo as $key => $resultcargo) {
                            echo $resultcargo['nomecargo'];
                        } ?></p>
                    <p><? foreach ($pegadept as $key => $resultdept) {
                            echo $resultdept['nomedept'];
                        } ?></p>
                </div>
            </div>
            <nav class="itens_menu">
                <ul class="menu_wraper">
                    <h2 <? checkAccessMenu(['cadastracategoria', 'cadastradept', 'cadastracargo'], 's') ?>>Cadastro</h2>
                    <li><a <? selecionadoMenu('cadastrar_categoria');
                            checkAccess('cadastracategoria', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_categoria">Categorias</a></li>
                    <li><a <? selecionadoMenu('cadastrar_subcategoria');
                            checkAccess('cadastrasubcategoria', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_subcategoria">Subcategorias</a></li>
                    <li><a <? selecionadoMenu('cadastrar_ocorrencia');
                            checkAccess('cadastraocorrencia', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_ocorrencia">Ocorrencia</a></li>
                    <li><a <? selecionadoMenu('cadastrar_departamento');
                            checkAccess('cadastradept', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_departamento">Departamento</a></li>
                    <li><a <? selecionadoMenu('cadastrar_cargo');
                            checkAccess('cadastracargo', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_cargo">Cargo</a></li>
                </ul>
                <ul>
                    <h2 <? checkAccessMenu(['cadastrauser', 'listauser', 'cadastraoperador', 'listaoperador'], 's') ?>>Administração do Painel</h2>

                    <li><a <? selecionadoMenu('cadastrar_usuario');
                            checkAccess('cadastrauser', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_usuario">Cadastrar Usuário</a></li>
                    <li><a <? selecionadoMenu('listar_usuarios');
                            checkAccess('listauser', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_usuarios">Listar Usuários</a></li>
                    <li><a <? selecionadoMenu('cadastrar_operador');
                            checkAccess('cadastraoperador', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_operador">Cadastrar Operador</a></li>
                    <li><a <? selecionadoMenu('listar_operadores');
                            checkAccess('listaoperador', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_operadores">Listar Operadores</a></li>
                </ul>
                <ul>
                    <h2 <? checkAccessMenu($opcao_administracao, 's') ?>>Administração</h2>

                    <li><a <? selecionadoMenu('listar_departamentos');
                            checkAccess('listadept', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_departamentos">Listar Departamentos</a></li>
                    <li><a <? selecionadoMenu('listar_cargos');
                            checkAccess('listacargo', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_cargos">Listar Cargos</a></li>
                    <li><a <? selecionadoMenu('listar_categorias');
                            checkAccess('listacategoria', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_categorias">Listar Categorias</a></li>
                    <li><a <? selecionadoMenu('listar_subcategorias');
                            checkAccess('listasubcategoria', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_subcategorias">Listar Subcategorias</a></li>
                    <li><a <? selecionadoMenu('listar_ocorrencia');
                            checkAccess('listaocorrencia', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_ocorrencia">Listar Ocorrencia</a></li>
                </ul>
                <ul>
                    <h2 <? checkAccessMenu(['abre_chamado'], 's') ?>>Chamados</h2>

                    <li><a <? selecionadoMenu('cadastrar_chamado');
                            checkAccess('abre_chamado', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_chamado">Abrir Chamados</a></li>
                    <li><a <? selecionadoMenu('listar_chamados');
                            checkAccess('abre_chamado', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_chamados">Listar meus Chamados</a></li>
                    <li><a <? selecionadoMenu('historico_meuschamados');
                            checkAccess('abre_chamado', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>historico_meuschamados">Meu Histórico de Chamados</a></li>
                </ul>
                <ul>
                    <h2 <? checkAccessMenuOperador('idoperator') ?>>Gerenciamento do Operador</h2>

                    <li><a <? selecionadoMenu('chamados');
                            checkOperador($idaccessop); ?> href="<? echo INCLUDE_PATH_PAINEL ?>chamados">Painel de Chamados</a></li>
                    <li><a <? selecionadoMenu('historico_chamados');
                            checkOperador($idaccessop); ?> href="<? echo INCLUDE_PATH_PAINEL ?>historico_chamados">Histórico de Chamados</a></li>
                </ul>
                <ul>
                    <h2 <? checkAccessMenu(['editasite'], 's') ?>>Configuração do portal</h2>

                    <li><a <? selecionadoMenu('editar_site');
                            checkAccess('editasite', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>editar_site">Edita Site</a></li>
                    <li><a <? selecionadoMenu('cadastrar_slide');
                            checkAccess('editasite', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>cadastrar_slide">Cadastra Slide</a></li>
                    <li><a <? selecionadoMenu('listar_slides');
                            checkAccess('editasite', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_slides">Listar Slide</a></li>
                    <li><a <? selecionadoMenu('listar_servicos');
                            checkAccess('editasite', 's'); ?> href="<? echo INCLUDE_PATH_PAINEL ?>listar_servicos">Listar Serviços</a></li>
                </ul>
            </nav>
        </aside>
        <main class="content">
            <? Painel::carregarPagina(); ?>
        </main>
    </div>