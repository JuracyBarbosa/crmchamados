
$(function(){
	var open = true;
	var windowSize = $(window)[0].innerWidth;
	var targetSizeMenu = (windowSize <= 400) ? 200 : 300;
	
	if(windowSize <= 768){
		$('.menu').css('width','0').css('padding','0');
		open = false;
	}
	
	$('.menu_btn').click(function(){
		if(open){
			// Fechar menu
			$('.menu').animate({'width':0,'padding':0},function(){
				open = false;
			});
			$('.content').css('width','100%');
			
			// Ajustar header apenas se a janela for menor ou igual a 768px
			if(windowSize <= 768) {
				$('.content,header').animate({'left':0},function(){
					open = false;
				});
			} else {
				$('.content').animate({'left':0},function(){
					open = false;
				});
			}
		}else{
			// Abrir menu
			$('.menu').css('display','block');
			$('.menu').animate({'width':targetSizeMenu+'px','padding':'10px'},function(){
				open = true;
			});
			if(windowSize > 768) {
				$('.content').css('width','calc(100% - ' + targetSizeMenu + 'px)');
				$('.content').animate({'left':targetSizeMenu+'px'},function(){
					open = true;
				});
			} else {
				$('.content,header').animate({'left':targetSizeMenu+'px'},function(){
					open = true;
				});
			}
		}
	});
	
	$(window).resize(function () {
		windowSize = $(window)[0].innerWidth;
		targetSizeMenu = (windowSize <= 400) ? 200 : 300;
		if (windowSize <= 768) {
			// Ajustar menu e header para estados compatíveis com janelas pequenas
			$('.menu').css('width', '0').css('padding', '0');
			$('.content').css('width', '100%').css('left', '0');
			$('header').css('left', '0');// Certificar que o header volta ao estado original
			open = false;
		} else {
			// Ajustar menu e conteúdo para estados compatíveis com janelas grandes
			$('.menu').css('width', targetSizeMenu + 'px').css('padding', '10px');
			$('.content').css('width', 'calc(100% - ' + targetSizeMenu + 'px)').css('left', targetSizeMenu + 'px');
			$('header').css('left', '0'); // Certificar que o header permanece fixo
			open = true;
		}
	});
});

//Função mask data
$('[formato=data]').mask('99/99/9999');

//função deletar algo
$('[actionBtn=delete]').click(function(){
	var txt;
	var r = confirm("Deseja excluir o registro ?");
	if (r == true) {
		return true;
	} else {
		return false;
	}
})

$('[actionBtn=aberto]').click(function(){
	$("#box").animate({
		height: '500px',
		opacity: '0.4'
	}, "slow");
	$("#box").animate({
		width: '250px',
		opacity: '0.8'
	}, "slow")
})

$("#btn-atender").click(function () {
	swal({
		title: "Atendendo!",
		text: "Seu chamado agora está sendo atendido!",
		icon: "success",
		button: "OK",
	})
});

//função para voltar a pagina com carregando as informações.
function GoBackWithRefresh(event) {
    if ('referrer' in document) {
        window.location = document.referrer;
        /* OR */
        //location.replace(document.referrer);
    } else {
        window.history.back();
    }
}

function mostraDIV($A) {
	if($A == 'registrachamado'){
		document.getElementById('minhadiv').style.display = 'block';
	}
	if($A == 'trocaroperador'){
		document.getElementById('divoperador').style.display = 'block';
	}
}

function ocultaDIV($B) {
	if($B == 'registrachamado'){
		document.getElementById('minhadiv').style.display = 'none';
	}
	if($B == 'trocaroperador'){
		document.getElementById('divoperador').style.display = 'none';
	}
}

function mostragrupoemail(grupoemail){
	var selectgrupoemail = grupoemail.value;
	var mostradiv = document.getElementById('mostragrupoemail');

	if (selectgrupoemail == 'S') {
		mostradiv.style.display = 'block';
	} else {
		mostradiv.style.display = 'none';
	}
}

function mostrasubcategorias() {
	var selectcategoria = document.getElementById('categoria').value;
	var selectsubcategoria = document.getElementById('selectsubcategoria');
	var selectocorrencia = document.getElementById('selectocorrencia');

	if (selectcategoria == 2) {
		selectsubcategoria.style.display = 'block';
		document.getElementById('ocorrencia').selectedIndex = 0;

			// Fazer uma requisição AJAX para obter as subcategorias relacionadas à categoria selecionada
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if (xhr.status === 200) {
						// Atualizar o select de subcategoria com as opções retornadas
						var subcategoriaSelect = document.getElementById('subcategoria');
						subcategoriaSelect.innerHTML = xhr.responseText;
		
					} else {
						console.error('Erro na requisição AJAX');
					}
				}
			};
			xhr.open('GET', 'requisicao?seq_pla_categoria=' + selectcategoria, true);
			xhr.send();


	} else if (selectcategoria == 6) {
		document.getElementById('subcategoria').selectedIndex = 0;
		selectsubcategoria.style.display = 'none';
		selectocorrencia.style.display = 'block';

			// Fazer uma requisição AJAX para obter as subcategorias relacionadas à categoria selecionada
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if (xhr.status === 200) {
						// Atualizar o select de subcategoria com as opções retornadas
						var subcategoriaSelect = document.getElementById('ocorrencia');
						subcategoriaSelect.innerHTML = xhr.responseText;
		
					} else {
						console.error('Erro na requisição AJAX');
					}
				}
			};
			xhr.open('GET', 'requisicao?seq_pla_ocorrencia=' + "categoriabi", true);
			xhr.send();

	} else {
		selectsubcategoria.style.display = 'none';
		selectocorrencia.style.display ='none';
		limparselecoes();
	}
}

function mostraOcorrencia() {
	var selectsubcategoria = document.getElementById('subcategoria').value;
	var selectocorrencia = document.getElementById('selectocorrencia');
	var ocorrenciaSelect = document.getElementById('ocorrencia');

	if (selectsubcategoria !== '') {
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function () {
			if (xhr.readyState === XMLHttpRequest.DONE) {
				if (xhr.status === 200) {
					ocorrenciaSelect.innerHTML = xhr.responseText;
					if (xhr.responseText.includes('Nenhuma ocorrência encontrada')){
						selectocorrencia.style.display = 'none';
					} else {
						selectocorrencia.style.display = 'block';
					}
				} else {
					console.error('Erro na requisição AJAX');
				}
			}
		};
		xhr.open('GET', 'requisicao?seq_pla_ocorrencia=' + encodeURIComponent(selectsubcategoria), true);
		xhr.send();

	} else {
		//selectsubcategoria.style.display = 'none';
		selectocorrencia.style.display ='none';
		limparselecoes();
	}
}

function limparselecoes() {
	document.getElementById('subcategoria').selectedIndex = 0;
	document.getElementById('ocorrencia').selectedIndex = 0;
}

//validação do SELECT OPTION da pagina de abertura de chamado.
function validaocorrencia(permissao) {
	document.getElementById("ocorrencia").addEventListener("change", function() {
		var ocorrenciaselecionada = this.value;
		console.log(permissao);
		
		if(permissao === 0){
			if (ocorrenciaselecionada === "1" || ocorrenciaselecionada === "2") {
				Swal.fire({
					title: "AVISO !!",
					html: 'Você não tem permissão de <b>Key User</b> para abrir chamados de sistemas com essa ocorrência',
					icon: "warning",
					confirmButtonText: 'Ok',
					allowOutsideClick: false
				}).then((result) => {
					document.getElementById("ocorrencia").value = "";
				})
			}
		}
	});
}

// Garante desativação de status do formulario quando responder solicitante.
document.addEventListener('DOMContentLoaded', function () {
    const trocaOperadorRadios = document.querySelectorAll('[name="divoperador"]');
    const statusInputs = document.querySelectorAll('[name="status"]');

    function toggleStatusInputs() {
        const selectedRadio = document.querySelector('[name="divoperador"]:checked');
        
        // Verifica se algum rádio está selecionado
        if (!selectedRadio) return; // Sai da função se não houver rádio selecionado

        const selectedValue = selectedRadio.value;
        if (selectedValue === 'S') {
            // Desativa os inputs de status se "Sim" estiver selecionado
            statusInputs.forEach(input => {
                input.disabled = true;
                input.checked = false; // Remove seleção
            });
        } else if (selectedValue === 'N') {
            // Reativa os inputs de status se "Não" estiver selecionado
            statusInputs.forEach(input => {
                input.disabled = false;
            });
        }
    }

    // Adiciona o evento change aos rádios
    trocaOperadorRadios.forEach(radio => {
        radio.addEventListener('change', toggleStatusInputs);
    });

    // Executa a lógica inicial ao carregar a página
    toggleStatusInputs();
});

//Eventos de alerta utilizando swal2
function alertErro($msg) {
	Swal.fire({
		title: "Algo deu errado !!",
		html: $msg,
		icon: "error",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if(result.value){
			location.href = 'INCLUDE_PATCH_PAINEL';
		}
	})
}

function alertWarning($msg) {
	Swal.fire({
		title: "Atenção!",
		html: $msg,
		icon: "warning",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if(result.value){
			location.href = 'INCLUDE_PATCH_PAINEL';
		}
	})
}

function alertUpSlide($img) {
	Swal.fire({
		title: "Slide cadastrado",
		html: "Imagem <b>" + $img + "</b> enviado com sucesso !",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_slides';
		}
	})
}

function alertDelSlide($img) {
	Swal.fire({
		title: "Slide Deletado",
		html: "Imagem <b>" + $img + "</b> deletada com sucesso !",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_slides';
		}
	})
}

function alertDelService($msg) {
	Swal.fire({
		title: "Serviço Deletado",
		html: "Serviço <b>" + $msg + "</b> deletado com sucesso !",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_servicos';
		}
	})
}

function alertUpService($msg) {
	Swal.fire({
		title: "Serviço Editado",
		html: "O serviço <b>" + $msg + "</b> foi editado com sucesso !",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_servicos';
		}
	})
}

function alertAbrirChamado($id) {
	Swal.fire({
		title: "Chamado aberto !",
		html: "Chamado <b>" + $id + "</b> aberto com sucesso, aguarde que um atendente ira te atender!",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_chamados';
		}
	})
}

function alertEmailNulo($seq_pla_chamado, $acao) {
	if ($acao == 'abrirChamado') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Não encontramos seu e-mail no seu cadastro para receber notificações!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado cadastrado com sucesso!<br /> Seu chamado entrou na fila de atendimento!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'listar_chamados';
					}
				});
			}
		});
	} else if ($acao == 'responderAtendente') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Não encontramos seu e-mail no seu cadastro para receber notificações!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado respondido com sucesso!<br /> Seu chamado foi respondido, logo o atendente irá te retornar!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'listar_chamados';
					}
				});
			}
		});
	} else if ($acao == 'responderSolicitante') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Não encontramos o e-mail do solicitante nos cadastros para receber notificações!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado respondido com sucesso!<br /> Lembre-se de passar feednack para o colaborador!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'emailNuloParaAtender') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "E-mail do solicitante não cadastrado para receber notificações!!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Iniciado atendimento ao chamado!<br /> Lembre-se de passar feedback ao usuário!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'encaminharSolicitante') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "E-mail do solicitante não cadastrado para receber notificações!!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado encaminhado com sucesso!<br /> Lembre-se de passar feedback ao usuário!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'encaminharPrestador') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Seu e-mail não está cadastrado para receber notificações!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado encaminhado com sucesso!<br /> Lembre-se de passar feedback ao usuário!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'encaminharSolOpe') {
		Swal.fire({
			title: "E-mails não encontrados!",
			html: "Não foi encontrado e-mail para o solicitante e operador!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado encaminhado com sucesso!<br /> Lembre-se de passar feedback ao usuário!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'concluirChamado') {
		Swal.fire({
			title: "E-mail não encontrados!",
			html: "Não foi encontrado e-mail para o solicitante!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado Concluído com sucesso!<br /> Lembre-se de passar feedback ao usuário!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'chamados';
					}
				});
			}
		});
	} else if ($acao == 'finalizaChamado') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Não encontramos seu e-mail nos cadastros para receber notificações<br />Verifique com o departamento de T.I!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $seq_pla_chamado + "</b>",
					html: "Chamado <b>finalizado</b> com sucesso!<br /> O departamento de T.I agradece o contato!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'listar_chamados';
					}
				});
			}
		});
	}
}

function alertRespondeChamado($id,$acao,$funcao) {
	if ($acao == 'cancelar') {
		Swal.fire({
			title: "Cancelado!",
			html: "Chamado cancelado com sucesso!<br>" +
				"ID do chamado <b>" + $id + "</b>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_chamados';
			}
		})
	}else if($acao == 'devolverchamado'){
		Swal.fire({
			title: "Devolvido!",
			html: "Chamado devolvido ao atendente!<br>" +
				"ID do chamado <b>" + $id + "</b><br>"+
				"Aguarde que logo será retornado atendimento!",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_chamados';
				
			}
		})
	}else if ($acao == 'emailNuloParaRetorno') {
		Swal.fire({
			title: "E-mail não encontrado!",
			html: "Não encontramos seu e-mail cadastrado<br />para receber notificações!",
			icon: "warning",
			confirmButtonText: 'Ok'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Chamado <b>" + $id + "</b> Retornado com sucesso!<br>",
					html: "Aguarde que logo será retornado atendimento!",
					icon: "success",
					confirmButtonText: 'Ok'
				}).then((result) => {
					if (result.isConfirmed) {
						location.href = 'listar_chamados';
					}
				});
			}
		});
	}
	else if($acao == 'atender'){
		Swal.fire({
			title: "Atendendo!",
			html: "Você iniciou o atendimento!<br>" +
				"ID do chamado <b>" + $id + "</b><br>"+
				"Não esqueça de sempre responder o andamento!",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				if($funcao == 'atendente'){
					location.href = 'chamados';
				}else{
					location.href = 'chamados';
				}
			}
		})
	}else if($acao == 'encaminhar'){
		Swal.fire({
			title: "Encaminhado!",
			html: "Este chamado foi encaminhado com sucesso!<br>" +
				"ID do chamado <b>" + $id + "</b><br>"+
				"Não esqueça de sempre responder o andamento!",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				if($funcao == 'encaminhado'){
					location.href = 'chamados';
				}else{
					location.href = 'chamados';
				}
			}
		})
	}else if ($acao == 'resposta_atendente') {
		Swal.fire({
			title: "Respondido!",
			html: "Chamado respondido ao Solicitante!<br>" +
				"ID do chamado <b>" + $id + "</b>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				if($funcao == 'atendente'){
					location.href = 'chamados';
				}else{
					location.href = 'chamados';
				}
			}
		})
	} else if ($acao == 'resposta_solicitante') {
		Swal.fire({
			title: "Feedback enviado!",
			html: "Chamado respondido ao atendente!<br>" +
				"ID do chamado <b>" + $id + "</b>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_chamados';
			}
		})
	} else if ($acao == 'concluir') {
		Swal.fire({
			title: "Concluido!",
			html: "Chamado concluido com sucesso!<br>" +
				"ID do chamado <b>" + $id + "</b><br>" +
				"Chamado devolvido ao solicitante para validação!<br>" +
				"Chamado temporariamente encerrado!",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				if($funcao == 'atendente'){
					location.href = 'chamados';
				}else{
					location.href = 'chamados';
				}
			}
		})
	} else if ($acao == 'finalizar') {
		Swal.fire({
			title: "Finalizado!",
			html: "Chamado encerrado com sucesso!<br>" +
				"ID do chamado <b>" + $id + "</b>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_chamados';
			}
		})
	} else if ($acao == 'revalidar') {
		Swal.fire({
			title: "Reavaliado!",
			html: "Chamado reavaliado com sucesso!<br>" +
				"ID do chamado <b>" + $id + "</b>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_chamados';
			}
		})
	}
}

function alertatribuichamado($msg) {
	Swal.fire({
		title: "Atribuido !",
		html: "Chamado <b>"+$msg+"</b> foi atribuido e encaminhado para o prestador com sucesso!<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'chamados';
		}
	})
}

function alertCadDepartamento($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Departamento <b>"+$msg+"</b> cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_departamentos';
		}
	})
}

function alertCadCargo($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Cargo <b>"+$msg+"</b> cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_cargos';
		}
	})
}

function alertDelCargo($msg) {
	Swal.fire({
		title: "Excluido !",
		html: "Cargo <b>"+$msg+"</b> excluido com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_cargos';
		}
	})
}

function alertCadCategoriaCh($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Categoria <b>"+$msg+"</b> cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_categorias';
		}
	})
}

function alertcadocorrencia($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Ocorrência <b>"+$msg+"</b> cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_ocorrencia';
		}
	})
}

function alertCadsubCategoriaCh($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Subcategoria <b>"+$msg+"</b> cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_subcategorias';
		}
	})
}

function alertEditCategoria($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Categoria <b>"+$msg+"</b> editada com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_categorias';
		}
	})
}

function alertEditSubCategoria($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Subcategoria <b>"+$msg+"</b> editada com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_subcategorias';
		}
	})
}

function alerteditocorrencia($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Ocorrência <b>"+$msg+"</b> editada com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_ocorrencia';
		}
	})
}

function alertEditDept($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Departamento <b>"+$msg+"</b> editado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_departamentos';
		}
	})
}

function alertEditCargo($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Cargo <b>"+$msg+"</b> editado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_cargos';
		}
	})
}

function alertDelDept($msg){
	const Toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
		  toast.addEventListener('mouseenter', Swal.stopTimer)
		  toast.addEventListener('mouseleave', Swal.resumeTimer)
		}
	  })
	  
	  Toast.fire({
		icon: 'success',
		html: "Departamento <b>"+$msg+"</b> deletado com sucesso!"
	  }).then(() => location.href = 'listar_departamentos');
}

function alertCadUser($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Usuário <b>"+$msg+"</b> Cadastrado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_usuarios';
		}
	})
}

function alertEditUser($msg,$acao) {
	if($acao == ''){
		Swal.fire({
			title: "Editado !",
			html: "Usuário <b>"+$msg+"</b> editado com sucesso<br>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_usuarios';
			}
		})
	}else if($acao > '0'){
		Swal.fire({
			title: "Editado com avatar !",
			html: "Usuário <b>"+$msg+"</b> editado junto com novo avatar.<br>",
			icon: "success",
			confirmButtonText: 'Ok',
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				location.href = 'listar_usuarios';
			}
		})
	}
}

function alertEditOp($msg) {
	Swal.fire({
		title: "Editado !",
		html: "Operador <b>"+$msg+"</b> editado com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_operadores';
		}
	})
}

function alertDelUser($msg) {
	Swal.fire({
		title: "Deletado !",
		html: "Usuário <b>"+$msg+"</b> excluido com sucesso<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_usuarios';
		}
	})
}

function alertOperator($msg) {
	Swal.fire({
		title: "Cadastrado !",
		html: "Operador <b>"+$msg+"</b> cadastrado com sucesso!<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_operadores';
		}
	})
}

function alertDelOperator($msg) {
	Swal.fire({
		title: "Deletado !",
		html: "Operador <b>"+$msg+"</b> deletado com sucesso!<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'listar_operadores';
		}
	})
}

function alertFinalChamado($msg) {
	Swal.fire({
		title: "Finalizado !",
		html: "Chamado <b>"+$msg+"</b> encerrado<br>",
		icon: "success",
		confirmButtonText: 'Ok',
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			location.href = 'painel_chamados';
		}
	})
}


