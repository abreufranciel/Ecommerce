jQuery(document).ready(function()
{
	$ = jQuery;

	$('.editor-wyg').trumbowyg({
		btns: ['bold', 'italic', 'underline', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull']
	});

	//ao clicar em logar, usa ajax para verificação de login
	$('#logar').on('click', function (e)
	{
		var emailLogin = $('input[name="email_login"]').val();
		var passLogin = $('input[name="senha_login"]').val();

		$.post("/login.php", {
		action: 'login', email: emailLogin, senha: passLogin
		}, function(response){

			console.log(response);
			if( response.cod == 1)
			{
				window.location.href = 'painel.php';
			}
			else if (response.cod == 0)
			{
				$('#msg-login').html(response.msg);
				$('#msg-login').show();
			}
		}, 'json');

		e.preventDefault();
	});

	//chamada ajax do cadastro
	$('#registro').on('click', function (e)
	{
		var nameRegister  = $('input[name="nome_registro"]').val();
		var cityRegister  = $('select[name="cidade_registro"]').val();
		var ufRegister    = $('select[name="uf_registro"]').val();
		var cpfRegister   = $('input[name="cpf_registro"]').val();
		var emailRegister = $('input[name="email_registro"]').val();
		var passRegister  = $('input[name="senha_registro"]').val();
		var passConfRegister = $('input[name="senha_conf_registro"]').val();

		$.post("/login.php", {
		action: 'register', nome: nameRegister, cidade: cityRegister, uf: ufRegister, email: emailRegister, senha: passRegister, senhaConf: passConfRegister, cpf: cpfRegister
		}, function(response){
			if( response.cod == 1)
			{
				window.location.href = 'painel.php';
			}
			else if ( response.cod == 0) 
			{
				$('#msg-register').html(response.msg);
				$('#msg-register').show();
			}
		}, 'json');

		e.preventDefault();
	});

	//exibir menu mobile
	$('.bar-menu-mobile').on('click', function ()
	{
		$('.menu-mobile').slideToggle('slow');
	});

	//abrir janela para upload de foto perfil
	$('.photo-user').on('click', function ()
	{
		$('#envia-foto-usuario').trigger('click');
	});

	$('input[name="whats-vendedor"]').mask('(00) 0 0000 - 0000');
	$('input[name="hora-inicio"], input[name="hora-fim"]').mask('00:00');
	$('input[name="cpf_registro"], input[name="cpf-comprador"], input[name="cpf-usuario"]').mask('000.000.000-00');

	//ativar campos vendedor
	$('#ativar-modo-ven').change(function() 
	{
        if($(this).is(":checked")) {
           
           $('select[name="tipo-vendedor"], input[name="hora-inicio"], input[name="hora-fim"], input[name="insta-vendedor"], input[name="face-vendedor"], input[name="whats-vendedor"], input[name="apikey-vendedor"], textarea[name="descricao-vendedor"]').removeAttr('disabled');
   		}
   		else
   		{
   			$('select[name="tipo-vendedor"], input[name="hora-inicio"], input[name="hora-fim"], input[name="insta-vendedor"], input[name="face-vendedor"], input[name="whats-vendedor"], input[name="apikey-vendedor"], textarea[name="descricao-vendedor"]').attr('disabled', 'disabled');
   		}
   	});

   	$('select[name="tipo-pet"]').change(function()
   	{
   		//se for gato
   		if($(this).val() == 0)
   		{
   			$('.cat').show();
   			$('.dog').hide();
   		}
   		else if ($(this).val() == 1)
   		{
   			$('.dog').show();
   			$('.cat').hide();
   		}
   	});

   	$('input[name="tipo-valor"]').on('click', function ()
   	{
   		if ( $(this).val() == 1 )
   		{
   			$('.acrescimo-servico').css('display', 'none');
   			$('.valor-servico').css('display', 'block');
   		}
   		else if ( $(this).val() == 0 )
   		{
   			$('.acrescimo-servico').css('display', 'block');
   			$('.valor-servico').css('display', 'none');
   		}
   	});

   	$('.tab').on('click', function ()
   	{
   		$('.tab').removeClass('active-tab');
   		$(this).addClass('active-tab');
   	});

   	$('#delete-servico').on('click', function (e) 
   	{
   		if(confirm('Deseja mesmo excluir?'))
   		{
   			var id = $('input[name="id-servico"]').val();
   			$.post("/php/ajax.php", {
			p: 'excluir-servico', id: id
			}, function(content){

				var a = '<a href="painel?p=servicos">Voltar para lista</a>';

				$('.div-service').html(content + '<br>' + a);

			}, 'html');

   		}
   		else
   		{
   			e.preventDefault();
   		}
   	});

   	$('.btn-see').on('click', function(e)
   	{
   		var id = $(this).attr('data-id');

   		$('div[data-id='+ id +']').toggle();

		if( $('div[data-id='+ id +']').is(':visible') ) 
		{
		  $(this).html('Ver Menos');
		} 
		else 
		{
		  $(this).html('Ver Serviço');
		}

   	});

   	$('.consulta-preco').on('click', function (e)
   	{
   		id = this.getAttribute('data-id');
   		var idServico = $('input[name="id-servico-c"]').val();
   		var kg = $('select[name="kg-cachorro"]').val();
   		$.post("/php/ajax.php", {
			p: 'consulta-preco', kg: kg, idServico: idServico
			}, function(content){

				//atribui valor somente para os elementos com a classe .btn-buy, dentro
				//do box do respectivo serviço
				$('#valor-servico-'+id).val(parseInt(content))
				$('#box-service-' + id + ' .btn-buy').attr('data-valor-servico', parseInt(content));
				$('#box-service-' + id + ' .price-service, .service-td-id-' + id).html('R$ ' + content);

			}, 'html');
   		e.preventDefault();
   	});

   	$('#add-foto').on('click', function(e)
   	{

   		var qtd = $('.img-galeria').length;

   		qtd = qtd + 1;

   		var element = '#img-'+qtd;

		$('<div class="preview-img"><input type="file" name="imagem-galeria[]" class="img-galeria none" id="img-'+ qtd +'"></div>').insertBefore($(this));

		$(element).trigger('click');

		$(element).on('change', function()
		{
			var input = 'img-' + qtd;
			var oFReader = new FileReader();
	        oFReader.readAsDataURL(document.getElementById(input).files[0]);

	        oFReader.onload = function (oFREvent) {
				var src =  oFREvent.target.result;
				$('<i class="fas fa-minus-circle delete-img"></i><img width="100px" height="100px" src="' + src + '" id="preview-' + qtd + '">').insertBefore($(element));
	        };
		});


   		e.preventDefault();
   	});

   	$('form[name="galeria"]').on('click', '.delete-img', function ()
   	{
   		$(this).parent().remove();
   	});
    
    
    // pesquisa serviço por titulo
    $('#pesquisa-titulo-servico').on('keyup', function ()
    {
       var titulo = $(this).val(); 
       	$.post("/php/ajax.php", {
    		p: 'consulta-titulo-servico', value: titulo
    		}, function(content){
    
                //console.log(content);
                $('.register-services').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-categoria-servico').on('change', function ()
    {
       var categoria = $(this).val(); 
       	$.post("/php/ajax.php", {
    		p: 'consulta-categoria-servico', value: categoria
    		}, function(content){
    
                //console.log(content);
                $('.register-services').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-status-pedido').on('change', function ()
    {
       var status = $(this).val(); 
       	$.post("/php/ajax.php", {
    		p: 'consulta-status-pedido', value: status
    		}, function(content){
    
                $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-data-pedido').on('change', function ()
    {
       var data = $(this).val(); 
      	$.post("/php/ajax.php", {
    		p: 'consulta-data-pedido', value: data
    		}, function(content){
    
                $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-cpf-pedido').on('change', function ()
    {
       var cpf = $(this).val(); 
      	$.post("/php/ajax.php", {
    		p: 'consulta-cpf-pedido', value: cpf
    		}, function(content){
    
            $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-voucher-compra').on('keyup', function ()
    {
       var voucher = $(this).val(); 
      	$.post("/php/ajax.php", {
    		p: 'consulta-voucher-compra', value: voucher
    		}, function(content){
    
            $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-nome-compra').on('keyup', function ()
    {
       var nome= $(this).val(); 
      	$.post("/php/ajax.php", {
    		p: 'consulta-nome-compra', value: nome
    		}, function(content){
    
            $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    $('#pesquisa-data-compra').on('change', function ()
    {
       var data = $(this).val(); 
      	$.post("/php/ajax.php", {
    		p: 'consulta-data-compra', value: data
    		}, function(content){
    
            $('.box-orders').html(content);
    
    		}, 'html');
    });
    
    
    
    $('.btn-see').on('click', function ()
    {
        var title = $(this).attr('data-title');
        $("meta[property='og\\:title']").attr("content", title);
        //$("meta[property='og\\:description']").attr("content", description);
    });
 

});

