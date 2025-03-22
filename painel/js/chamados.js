let registroChamados = [];
try {
    const registroChamadosElement = document.getElementById('registroChamados');
    if (registroChamadosElement && registroChamadosElement.value) {
        registroChamados = JSON.parse(registroChamadosElement.value);
    }
} catch (error) {
    console.error("Erro ao carregar dados de chamados: JSON inválido", error);
    alert("Erro ao carregar dados de chamados. Por favor, tente novamente ou contate o suporte.");
}

const acoesPorStatus = {
    'Aberto': [
        { label: 'Atender', action: atenderChamado },
        { label: 'Encaminhar', action: encaminharChamado }
    ],
    'Em Atendimento': [
        { label: 'Transferir', action: transferirChamado },
        { label: 'Responder', action: responderSolicitante},
        { label: 'Concluir', action: concluiChamado }
    ],
    'Encaminhado': [
        { label: 'Concluir', action: concluiChamado },
        { label: 'Responder', action: responderSolicitante },
        { label: 'Transferir', action: transferirChamado }
    ]
};

function dateFormat(dataISO) {
    const data = new Date(dataISO);
    if (isNaN(data)) return 'Data inválida';
    
    const dia = data.getDate().toString().padStart(2, '0');
    const mes = (data.getMonth() + 1).toString().padStart(2, '0');
    const ano = data.getFullYear();
    const horas = data.getHours().toString().padStart(2, '0');
    const minutos = data.getMinutes().toString().padStart(2, '0');

    return `${dia}-${mes}-${ano} ${horas}:${minutos}`;
}

async function renderizarChamados(chamadosFiltrados) {
    const container = document.getElementById('chamadosContainer');
    if (!container) {
        console.error("Elemento 'chamadosContainer' não encontrado no DOM!");
        return;
    }

    // Exibir mensagem de carregamento
    container.innerHTML = '<p id="loadingMessage" class="loading-message"><center>Carregando...</center></p>';

    // Aguarda um tempo antes de iniciar a renderização (garantindo que os dados carreguem corretamente)
    await new Promise(resolve => setTimeout(resolve, 500)); // Aguarda 500ms antes do fade-out

    // Aplicar fade-out na mensagem de carregamento
    const loadingMessage = document.getElementById('loadingMessage');
    if (loadingMessage) {
        loadingMessage.classList.add('fade-out');
    }

    setTimeout(async () => {
        container.innerHTML = ''; // Remove a mensagem após o fade-out

        if (chamadosFiltrados.length === 0) {
            container.innerHTML = '<p><center>Nenhum chamado encontrado.</center></p>';
            return;
        }

        for (const chamado of chamadosFiltrados) {
            const chamadoDiv = document.createElement('div');
            chamadoDiv.className = 'grade_chamado form_group fade-in';

            // Placeholder do spinner antes de carregar os botões
            const botoesPlaceholder = `<div id="botoes-${chamado.idchamado}" class="spinner"></div>`;

            chamadoDiv.innerHTML = `
                ${gerarBloco(chamado, ['solicitante', 'departamento', 'data_abertura', 'idchamado'], { solicitante: 'Solicitante', departamento: 'Departamento', data_abertura: 'Data', idchamado: 'Solicitação' })}
                ${gerarBloco(chamado, ['status', 'prioridade', 'atendente', 'categoria', 'subcategoria', 'ocorrencia', 'chexterno'], { status: 'Status', prioridade: 'Prioridade', atendente: 'Atendente', categoria: 'Categoria', subcategoria: 'Subcategoria', ocorrencia: 'Ocorrência', chexterno: 'Chamado Externo' })}
                ${gerarBloco(chamado, ['descbreve'], { descbreve: 'Assunto' })}
                <div align="center"><label>Descrição do Chamado</label></div>
                <div class="descricao-chamado">${chamado.descricao ? `<span>${chamado.descricao}</span>` : ''}</div>
                ${botoesPlaceholder} <!-- Spinner enquanto os botões carregam -->
                ${gerarHistorico(chamado.historico, chamado.solicitante)}
                ${gerarAnexos(chamado.anexos)}
            `;

            container.appendChild(chamadoDiv);

            // Substituir o spinner pelos botões reais quando carregados
            const botoesAcoes = await gerarBotoesAcoes(chamado.status, chamado.idchamado, chamado.idatendente);
            document.getElementById(`botoes-${chamado.idchamado}`).outerHTML = botoesAcoes;
        }

    }, 500); // Tempo extra para garantir a remoção suave da mensagem
}


function gerarBloco(chamado, colunas, mapeamento) {
    return `<div>${colunas.map(col => chamado[col] ? `<label>${mapeamento[col]}:</label> ${chamado[col]} &nbsp;` : '').join('')}</div>`;
}

function gerarHistorico(historico = [], solicitante) {
    if (!historico.length) return '';

    return `<div>${historico.map(h => `
        ${h.resposta_atendente ? `<div class='grade-ch-resposta-at'><label>Atendente:</label> ${h.atendente} <label>Data:</label> ${dateFormat(h.data_movimentacao)}<br /><label>Respondeu:</label> ${h.resposta_atendente}</div>` : ''}
        ${h.resposta_solicitante ? `<div class='grade-ch-resposta-so'><label>Solicitante:</label> ${solicitante} <label>Data:</label> ${dateFormat(h.data_movimentacao)}<br /><label>Respondeu:</label> ${h.resposta_solicitante}</div>` : ''}
    `).join('')}</div>`;
}

function gerarAnexos(anexos) {
    if (!anexos?.detalhes?.length) return '';

    return `<div class='grade-ch-anexo'><label>Documentações anexas: </label>
        ${anexos.detalhes.map(anexo => 
            anexo.solicitante ? `<a target='_blank' href="${ANEXO_CHAMADO}${anexo.nomeanexo}">${anexo.solicitante}</a> &emsp; ` : '' +
            anexo.operador ? `<a target='_blank' href="${ANEXO_CHAMADO}${anexo.nomeanexo}">${anexo.operador}</a> &emsp; ` : ''
        ).join('')}
    </div>`;
}

async function getAtendente() {
    try {
        const response = await fetch(`${INCLUDE_PATH_PAINEL}api/getAtendente.php`);
        if (!response.ok) {
            throw new Error(`Erro ao buscar atendente: ${response.status}`);
        }

        const result = await response.json();
        if (result.status === 'success') {
            return result.atendente;
        } else {
            throw new Error(result.message || 'Atendente não encontrado.');
        }
    } catch (error) {
        console.error('Erro ao buscar atendente:', error.message);
        alert('Erro ao identificar o operador. \nErro: ' + error.message + '');
        return null;
    }
}

async function gerarBotoesAcoes(status, idchamado, atendenteChamado) {
    let html = '<div class="btn-acao-ch-container">';

    try {
        const atendenteAtual = await getAtendente(); // Obtem o atendente logado

        // Define uma função auxiliar para gerar botões
        const gerarBotoes = (acoes) => {
            return acoes.map(acao => {
                if (typeof acao.action === 'function') {
                    return `<button class="acao-btn" data-action="${acao.action.name}" data-id="${idchamado}">
                                ${acao.label}
                            </button>`;
                }
                return '';
            }).join('');
        };

        let acoes = [];

        if (atendenteAtual.isGestor) {
            // Gestores veem todos os botões para o status especificado
            acoes = acoesPorStatus[status] || [];
        } else if (status === 'Em Atendimento' && atendenteChamado === atendenteAtual.idoperator) {
            // Para "Em Atendimento", mostra ações apenas se o atendente for o responsável
            acoes = acoesPorStatus[status] || [];
        } else if (status !== 'Em Atendimento') {
            // Para outros status, exibe os botões normalmente
            acoes = acoesPorStatus[status] || [];
        } else {
            console.log(`Atendente não autorizado para o chamado ${idchamado}.`);
        }

        html += gerarBotoes(acoes);
        
        // Exibe o botão "Alterar" apenas para os status permitidos
        const statusComAlterar = ['Aberto']; // Lista de status que permitem o botão
        if (statusComAlterar.includes(status)) {
            html += `<button onclick="abrirModalAlterar(${idchamado})">Alterar</button>`;
        }

    } catch (error) {
        console.error('Erro ao validar atendente:', error.message);
    }

    html += '</div>';
    return html;
}

// Adiciona eventos de clique de forma dinâmica (para segurança contra XSS)
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('acao-btn')) {
        const actionName = event.target.getAttribute('data-action');
        const idchamado = event.target.getAttribute('data-id');
        if (typeof window[actionName] === 'function') {
            window[actionName](idchamado);
        } else {
            console.error(`Ação "${actionName}" não encontrada.`);
        }
    }
});

async function atenderChamado(idchamado) {
    try {
        const atendente = await getAtendente();
        if (!atendente) return;

        const response = await fetch(`${INCLUDE_PATH_PAINEL}api/requisicaochamados.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                idchamado: idchamado,
                atendente: atendente.idoperator,
                acao: 'atender'
            })
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === 'success') {
            const idChamado = result.id;
            const emailNulo = result.emailnull;
            if (emailNulo === 'sim') {
                alertEmailNulo(idChamado, 'emailNuloParaAtender');
            } else {
                alertRespondeChamado(idChamado,'atender','atendente');
            }            
        } else if (result.status === 'error' && result.current_status) {
            alert(`Erro: ${result.message}\nStatus atual do chamado: ${result.current_status}`);
        } else {
            alert(`Erro: ${result.message || 'Não foi possível atender o chamado.'}`);
        }
    } catch (error) {
        console.error('Erro:', error.message);
        alert('Ocorreu um erro ao atender o chamado. Tente novamente mais tarde.');
    }
}

function responderSolicitante(idchamado) {
    window.location.href = `${INCLUDE_PATH_PAINEL}responder_solicitante?ch=${idchamado}`;
}

function transferirChamado(idchamado) {
    window.location.href = `${INCLUDE_PATH_PAINEL}transferir_chamado?ch=${idchamado}`;
}

function encaminharChamado(idchamado) {
    window.location.href = `${INCLUDE_PATH_PAINEL}encaminhar_chamado?ch=${idchamado}`;
}

function concluiChamado(idchamado) {
    window.location.href = `${INCLUDE_PATH_PAINEL}concluir_chamado?ch=${idchamado}`;
}

function buscarChamadoStatus(idstatus) {
    const url = new URL(window.location.href);
    url.searchParams.set('status', idstatus);
    window.location.href = url.href;
}

document.querySelectorAll(".btn-brilho").forEach(button => {
    button.addEventListener('click', function () {
        const idstatus = this.getAttribute('data-status');
        buscarChamadoStatus(idstatus);
    });
});

function adicionarListener(id, evento, funcao) {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.addEventListener(evento, funcao);
    }
}

function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

// Substitui a chamada direta por uma versão com debounce
const filtrarChamadosDebounced = debounce(filtrarChamados, 400);

adicionarListener('categoriaFiltro', 'change', filtrarChamados);
adicionarListener('subcategoriaFiltro', 'change', filtrarChamados);
adicionarListener('ocorrenciaFiltro', 'change', filtrarChamados);
adicionarListener('solicitanteFiltro', 'change', filtrarChamados);
adicionarListener('codigoFiltro', 'input', filtrarChamadosDebounced);

function filtrarChamados() {
    const filtros = {
        categoria: document.getElementById('categoriaFiltro').value.trim().toLowerCase(),
        subcategoria: document.getElementById('subcategoriaFiltro').value.trim().toLowerCase(),
        ocorrencia: document.getElementById('ocorrenciaFiltro').value.trim().toLowerCase(),
        solicitante: document.getElementById('solicitanteFiltro').value.trim().toLowerCase(),
        codigoChamado: document.getElementById('codigoFiltro').value.trim()
    };

    const chamadosFiltrados = registroChamados.filter(chamado => {
        return Object.entries(filtros).every(([chave, valor]) => 
            valor === '' || 
            (chave === 'codigoChamado' ? String(chamado.idchamado).includes(valor) : String(chamado[chave]).toLowerCase() === valor)
        );
    });

    renderizarChamados(chamadosFiltrados);
}

// Inicialmente, renderizar todos os chamados
renderizarChamados(registroChamados);

//Função Modal
function abrirModalAlterar(idchamado) {
    let modalContainer = document.getElementById('modal-container');

    // Log para verificar se a div existe
    if (modalContainer) {
        console.log("Modal container encontrado no DOM.");
    } else {
        console.log("Modal container não encontrado. Criando dinamicamente.");
        modalContainer = document.createElement('div');
        modalContainer.id = 'modal-container';
        document.body.appendChild(modalContainer);
    }

    // Carregar o conteúdo do modal dinamicamente
    fetch('modals/alterar_chamado.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar o modal.');
            }
            return response.text();
        })
        .then(html => {
            modalContainer.innerHTML = html;
            const modal = document.getElementById('modal');
            modal.style.display = 'flex';

            // Preencher os campos do modal com os dados do chamado
            const chamado = registroChamados.find(c => c.idchamado === idchamado);
            if (chamado) {
                document.getElementById('idchamado').value = chamado.idchamado || '';
                document.getElementById('categoria').value = chamado.id_categoria || '';
                document.getElementById('subcategoria').value = chamado.seq_pla_subcategoria || '';
                document.getElementById('ocorrencia').value = chamado.seq_pla_ocorrencia || '';
                document.getElementById('prioridade').value = chamado.id_prioridade || '';
            }

            // Fechar o modal ao clicar em "Cancelar"
            const closeModalBtns = document.querySelectorAll('.close-btn');
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            });

            // Submeter o formulário
            const form = document.getElementById('alterar-form');
            form.addEventListener('submit', (e) => {
                e.preventDefault(); // Evita o comportamento padrão do formulário
                const idchamado = document.getElementById('idchamado').value.trim();
                const categoria = document.getElementById('categoria').value.trim();
                const subcategoria = document.getElementById('subcategoria').value.trim();
                const ocorrencia = document.getElementById('ocorrencia').value.trim();
                const prioridade = document.getElementById('prioridade').value.trim();

                // if (!categoria || !subcategoria || !ocorrencia || !prioridade) {
                //     alert('Por favor, preencha todos os campos obrigatórios.');
                //     return;
                // }

                console.log(`Alterações para o chamado ${idchamado}:`, { categoria, subcategoria, ocorrencia, prioridade });

                // Envia os dados para o backend
                fetch(`${INCLUDE_PATH_PAINEL}api/atualizar_chamado.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        idchamado,
                        categoria,
                        subcategoria,
                        ocorrencia,
                        prioridade
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na comunicação com o servidor.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message); // Exibe a mensagem de sucesso
                            modal.style.display = 'none';
                            location.reload(); // Atualiza a página
                        } else {
                            throw new Error(data.message); // Trata mensagens de erro do backend
                        }
                    })
                    .catch(error => {
                        alert(`Erro ao salvar alterações: ${error.message}`);
                        console.error('Erro:', error);
                    });
            });
        })
        .catch(error => {
            console.error(error);
            alert('Não foi possível carregar o modal.');
        });
}
