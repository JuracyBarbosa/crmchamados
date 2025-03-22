// JavaScript Document
/*
$(function() {
	var mensagem = 'Olá Mundo!';
	alert(mensagem);
})*/

//Menu

$(function() {
	$('nav.mobile').click(function(){
		var listMenu = $('nav.mobile ul');
		
		if(listMenu.is(':hidden') == true){
			var icone = $('.botao_menu_mobile').find('i');
			icone.removeClass('fa-bars');
			icone.addClass('fa-minus-square');
			listMenu.slideToggle();
		} else{
			var icones = $('.botao_menu_mobile i');
			icones.removeClass('fa-minus-square');
			icones.addClass('fa-bars');
			listMenu.slideToggle();
		}
	});
	
	if($('target').length > 0) {
		var elemento = '#'+$('target').attr('target');
		var divScroll = $(elemento).offset().top;
		$('html,body').animate({scrollTop:divScroll},1600);
	}
	
	carregarDinamico();
	function carregarDinamico(){
		$('[realtime]').click(function(){
			var pagina = $(this).attr('realtime');
			$('.container_principal').hide();
			$('.container_principal').load(include_path+'pages/'+pagina+'.php');
			
			setTimeout(function(){
				initialize();
				addMarker(-27.609959,-48.576585,'',"Minha casa",undefined,false);
			},1000);
			
			$('.container_principal').fadeIn(1000);
			window.history.pushState('','', contato);
			
			return false;
		})
	}
	
});

//Slide

$(function() {
	var curSlide = 0;
	var delay = 3;
	var maxSlide = $('.banner_single').length -1;
	
	initSlide();
	changeSlide();
	
	function initSlide() {
		$('.banner_single').hide();
		$('.banner_single').eq(0).show();
		for(var i = 0; i < maxSlide+1; i++) {
			var content = $('.bullets').html();
			if(i == 0)
				content+='<span class="active_slide"></span>';
			else
				content+='<span></span>';
			$('.bullets').html(content);
		}
	}
	
	function changeSlide(){
		setInterval(function() {
			$('.banner_single').eq(curSlide).stop().fadeOut(2000);
			curSlide++;
			if(curSlide > maxSlide)
				curSlide = 0;
			$('.banner_single').eq(curSlide).stop().fadeIn(2000);
			$('.bullets span').removeClass('active_slide');
			$('.bullets span').eq(curSlide).addClass('active_slide');
		},delay * 1000);
	}
	$('body').on('click','.bullets span',function() {
		var currentBullet = $(this);
		$('.banner_single').eq(curSlide).stop().fadeOut(1000);
		curSlide = currentBullet.index();
		$('.banner_single').eq(curSlide).stop().fadeIn(1000);
		$('.bullets span').removeClass('active_slide');
		currentBullet.addClass('active_slide');
	})
});

//constants, constantes

var include_path = $('base').attr('base');

$(function(){
	
	var atual = -1;
	var maximo = $('.box_especialidade').length - 1;
	var timer;
	var animacaoDelay = 3;
	
	executarAnimacao();
	function executarAnimacao(){
		$('.box_especialidade').hide();
		timer = setInterval(logicaAnimacao,animacaoDelay*300);
		
		function logicaAnimacao(){
			atual++;
			if(atual > maximo){
				clearInterval(timer);
				return false;
			}
			
			$('.box_especialidade').eq(atual).fadeIn();
		}
	}
});

$(function() {
	$('body').on('submit','form',function(){
		var form = $(this);
		
		$.ajax({
			beforeSend:function(){
				$('.overlay_loading').fadeIn();
			},
			url: include_path+'ajax/formularios.php',
			method: 'post',
			dataType: 'json',
			data: form.serialize()
		}).done(function(data){
			if(data.sucesso){
				$('.overlay_loading').fadeOut();
				$('.sucesso').fadeIn();
				setTimeout(function(){
					$('.sucesso').fadeOut();
				},3000)
			} else {
				$('.overlay_loading').fadeOut();
			}
		});
		return false;
	})
})






