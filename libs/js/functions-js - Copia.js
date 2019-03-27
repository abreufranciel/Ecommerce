//submeter formulario no envio da foto do perfil
function submeter (id)
{
	$(id).trigger('click');
}

function editarServico ( url )
{
	window.location = url;
}

//value - valor que será atribuído ao campo categoria
//element - elemnto que foi clicado
function atribuiCategoria (value, element)
{
	$('input[name="categoria-servico"]').val(value);
	$('.cat-service').removeClass('cat-service-active');
	$(element).addClass('cat-service-active');

}

function toggle (element)
{
	if( $(element).hasClass('fa-toggle-off') )
	{
		$('input[name="status-servico"]').val('on');
		$(element).removeClass('fa-toggle-off');
		$(element).addClass('fa-toggle-on');
	}
	else if ( $(element).hasClass('fa-toggle-on') )
	{
		$('input[name="status-servico"]').val('off');
		$(element).removeClass('fa-toggle-on');
		$(element).addClass('fa-toggle-off');
	}

}

function carregaCidades (nameElement1, nameElement2)
{
	var uf = $('select[name="' + nameElement1 + '"]').val();
	$.post("/petplace/php/ajax.php", {
		p: 'cidades', uf: uf
		}, function(content){

			$('select[name="' + nameElement2 + '"]').html(content);

		}, 'html');
}

function adquirir (current)
{
	$.post("/petplace/php/ajax.php", {
		p: 'verifica-login'
		}, function(content){
			if(!content.cod)
			{
				alert('Você precisa estar logado para continuar');
			}
			else if (content.cod)
			{
				var idServico = current.getAttribute('data-id-servico');
				var tituloServico = current.getAttribute('data-titulo-servico');
				var valorServico = current.getAttribute('data-valor-servico');
				var idPrestador = current.getAttribute('data-id-prestador');
				var idComprador = content.idComprador;

				//se id de comprador for igual id do prestador, exibir mensagem avisando que nao pode comprar da mesma loja
				//chamar a função envia pag seguro passando o id do prestador

				//realizar a parte da criação de pedido
				enviaPagseguro(idServico, tituloServico, valorServico, idComprador, idPrestador);
			}
	}, 'json');
}

function enviaPagseguro (idServico, tituloServico, valorServico, idComprador, idPrestador)
{
	
	$.post("/petplace/php/pagseguro.php", {
		
		idServico: idServico, idComprador: idComprador, idPrestador: idPrestador, titulo: tituloServico, valor: valorServico
		}, function(data){
			
			$("#code-" + idServico).val(data);
			$('#formPagseguro-' + idServico).submit();
	});
}

function abreModal (element)
{
	$(element).dialog({
		modal: true,
		title: 'Finalizar Compra',
		width: $(window).width() > 600 ? 600 : 'auto',
	});
}