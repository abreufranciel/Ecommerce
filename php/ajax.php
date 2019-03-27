<?php 
require "../conn.php";

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
function consultaServico ($param, $value, $con)
{
    session_start();

    $qtdRegistros = 5;
	$numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
	$limite = $numPagina * $qtdRegistros;
	
    if($param == 'titulo')
    {
        $query = 'Select * from servico where id_prest_serv = :id and titulo LIKE :titulo';
        $query1 = 'Select * from servico where id_prest_serv = :id and titulo LIKE :titulo LIMIT :inicio, :fim';
        $array = array ( ':id' => $_SESSION['id'], ':titulo' => '%' . $value . '%' );
        $array1 = array (':id' => $_SESSION['id'], ':titulo' => '%' . $value . '%', ':inicio' => $limite, ':fim' => $qtdRegistros );
    }
    else if ($param == 'categoria')
    {
        $query = 'Select * from servico where id_prest_serv = :id and id_categoria = :categoria';
        $query1 = 'Select * from servico where id_prest_serv = :id and id_categoria = :categoria LIMIT :inicio, :fim';
        $array = array ( ':id' => $_SESSION['id'], ':categoria' => $value );
        $array1 = array (':id' => $_SESSION['id'], ':categoria' => $value, ':inicio' => $limite, ':fim' => $qtdRegistros );
    }
	
	$select1 = $con->prepare($query);
	$select1->execute( $array );

	$servicos1 = $select1->fetchAll(PDO::FETCH_ASSOC);

	$qtdServicos = count($servicos1);
	
	$select = $con->prepare($query1);
	$select->execute( $array1 );

	$servicos = $select->fetchAll(PDO::FETCH_ASSOC);

	if($servicos) :
    
    
	$html = '<table class="table-services" cellspacing="0">
		<tr>
			<th>Data</th>
			<th>Titulo</th>
			<th>Categoria</th>
			<th>Status</th>			
		</tr>';
	foreach ($servicos as $key => $servico) : 
	    
    	$url = 'painel.php?p=servicos&v=editar&id=' . $servico['id']; 
        $id = $servico['id'];
    	$html .= '<tr onclick="editarServico(' . $url . ');" class="tr-hover">';
	
			$categoria = $con->prepare('Select titulo from categoria where id = :id'); 
			$categoria->execute( array (':id' => $servico['id_categoria']) );
			$resCategoria = $categoria->fetch(PDO::FETCH_ASSOC);
		
			$html .= '<td>' . $servico['data'] . '</td>';
			$html .= '<td>' . $servico['titulo'] . '</td>';
			$html .= '<td>' . $resCategoria['titulo'] . '</td>';
			$publicado = ($servico['publicado'] == 'on') ? 'Publicado' : 'Não Publicado';
			$html .= '<td>' . $publicado . '</td>';
		$html .= '</tr>';
	endforeach;
	$html .= '</table>';
	
	$html .= '<div class="pagination-admin">';
		$html .= '<ul>';
			if ($numPagina > 0 )
			{
				$paginaAnterior = $numPagina - 1;
				
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaAnterior) ) ) . '"><</a></li>';
			
			}

			if( $qtdServicos >= $qtdRegistros )
			{
				$total = $qtdServicos/$qtdRegistros;

				for ($i=0; $i < $total; $i++) 
				{ 
					$url = 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$i) ) ); 
					$html .= '<li><a href="' . $url . '">' . ($i + 1) . '</a></li>';
				}

			}
			$totalPaginas = $qtdServicos/$qtdRegistros; 

			if ( $numPagina < ($totalPaginas - 1) )
			{
				$paginaSeguinte = $numPagina + 1;
				
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaSeguinte) ) ) .'">></a></li>';
				
			}
		
		$html .= '</ul>';
	$html .= '</div>';
    
    echo $html;

	else :

		echo 'Não há registros referente a pesquisa';

	endif;
}

function consultaPedido ($param, $value, $con)
{
    session_start();

$qtdRegistros = 5;
$numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
$limite = $numPagina * $qtdRegistros;

if($param == 'status')
{
	if($value == 1)
	{
		$query = "SELECT * from pedido where id_prestador = :id and status = 2 and valido = 1";
		$query1 = "SELECT * from pedido where id_prestador = :id and status = 2 and valido = 1 LIMIT :inicio, :fim";
	}
	else if ($value == 2)
	{
		$query = "SELECT * from pedido where id_prestador = :id and status = 1 and valido = 0";
		$query1 = "SELECT * from pedido where id_prestador = :id and status = 1 and valido = 0 LIMIT :inicio, :fim";
	}
	else if ($value == 3)
	{
		$query = "SELECT * from pedido where id_prestador = :id and status = 2 and valido = 0";
		$query1 = "SELECT * from pedido where id_prestador = :id and status = 2 and valido = 0 LIMIT :inicio, :fim";
	}
	$array = array ( ':id' => $_SESSION['id'] );
	$array1 = array ( ':id' => $_SESSION['id'], ':inicio' => $limite, ':fim' => $qtdRegistros);

}
else if ($param == 'data')
{
    $query = "SELECT * from pedido where id_prestador = :id and data = :data";
	$query1 = "SELECT * from pedido where id_prestador = :id and data = :data LIMIT :inicio, :fim";
	
	$array = array ( ':id' => $_SESSION['id'], ':data' => $value );
	$array1 = array ( ':id' => $_SESSION['id'], ':data' => $value, ':inicio' => $limite, ':fim' => $qtdRegistros);
}
else if ($param == 'cpf')
{
    $selectComprador = $con->prepare("SELECT id from usuario where cpf = :cpf");
    $selectComprador->execute( array (':cpf' => $value) );
    $resultCPF = $selectComprador->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT * from pedido where id_prestador = :id and id_comprador = :id_comprador";
	$query1 = "SELECT * from pedido where id_prestador = :id and id_comprador = :id_comprador LIMIT :inicio, :fim";
	
	$array = array ( ':id' => $_SESSION['id'], ':id_comprador' => $resultCPF['id'] );
	$array1 = array ( ':id' => $_SESSION['id'], ':id_comprador' => $resultCPF['id'], ':inicio' => $limite, ':fim' => $qtdRegistros);
}
$selecionaPedidos = $con->prepare($query);
$selecionaPedidos->execute( $array );

$pedidosTotal = $selecionaPedidos->fetchAll(PDO::FETCH_ASSOC);

$qtdPedidos = count($pedidosTotal);

$selecionaPedidosLimite = $con->prepare($query1);
$selecionaPedidosLimite->execute( $array1 );

$pedidos = $selecionaPedidosLimite->fetchALL(PDO::FETCH_ASSOC);


if($pedidos) :

    $html = '';
	foreach ($pedidos as $key => $pedido) 
	{
		$selecionaServico = $con->prepare('SELECT * from servico where id = :id');
		$selecionaServico->execute( array (':id' => $pedido['id_servico']) );
		$servico = $selecionaServico->fetch(PDO::FETCH_ASSOC);

		$selecionaComprador = $con->prepare('SELECT * from usuario where id = :id');
		$selecionaComprador->execute( array (':id' => $pedido['id_comprador']) );
		$comprador = $selecionaComprador->fetch(PDO::FETCH_ASSOC);

		$title = '';
		$cor = '';
		$form = '';
		if( $pedido['status'] == 1 && $pedido['valido'] == 0 )
		{
			$title = 'Inválido, aguardando pagamento';
			$cor = 'orange';
			$status = "Inválido - Aguardando Pagamento";
			//aguardado pagamento, invalido
		}
		else if ($pedido['status'] == 2 && $pedido['valido'] == 0)
		{
			$title = 'Inválido, voucher usado';
			$cor = 'red';
			$status = "Inválido - Usado";
			//usado, inválido
		}
		else if ($pedido['status'] == 2 && $pedido['valido'] == 1 )
		{
			//valido
			$title = 'Válido, pagamento efetuado';
			$cor = 'green';
			$form = 
			'<form method="post" action="voucher.php" style="float: right;">
				<input type="hidden" name="id-pedido" value="' . $pedido['id'] . '">
				<input type="submit" value="Usar" class="btn-pp-p">
			</form>';
			$status = "Válido";
		}

		$html .= '<div class="header-item-admin">';
			$html .= $pedido['voucher']; 
			$html .= $form;
			$html .= '<div class="clear"></div>';
		$html .= '</div>
				<div class="box-item-admin">
					<table>
						<tr>
							<td>Status</td>
							<td>'. $status .'</td>
						</tr>
						<tr>
							<td>Data</td>
							<td>'. date('d/m/Y', strtotime($pedido['data'])) .'</td>
						</tr>
						<tr>
							<td>Serviço</td>
							<td>'. $servico['titulo'] .'</td>
						</tr>
						<tr>
							<td>Valor</td>
							<td>'. number_format($pedido['valor_servico'], 2, ',', '.') .'</td>
						</tr>
						<tr>
							<td>Comprador</td>
							<td>'. $comprador['nome'] . '<br/> ' . $comprador['cpf']. '</td>
						</tr>
					</table>
				</div>';
		}

	$html .= '
	<div class="pagination-admin">
		<ul>';

			if ($numPagina > 0 )
			{
				$paginaAnterior = $numPagina - 1;
			
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaAnterior) ) ) .'"><</a></li>';
			}

			if( $qtdPedidos >= $qtdRegistros )
			{
				$total = $qtdPedidos/$qtdRegistros;

				for ($i=0; $i < $total; $i++) 
				{ 
					$ur = 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$i) ) ); 
					$html .= '<li><a href="' . $ur . '">' . ($i + 1) . '</a></li>';
				}

			}
			$totalPaginas = $qtdPedidos/$qtdRegistros; 

			if ( $numPagina < ($totalPaginas - 1) )
			{
				$paginaSeguinte = $numPagina + 1;
			
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaSeguinte) ) ) .'">></a></li>';
			}
			$html .= '
		</ul>
	</div>';
    
    echo $html;
    
    else:
        
        echo 'Não há registros referente a pesquisa';
    
    endif;
}

function consultaCompra ($param, $value, $con)
{
	session_start();
	$qtdRegistros = 5;
    $numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
    $limite = $numPagina * $qtdRegistros;


    if($param == 'voucher')
    {
    	$query = 'SELECT * from pedido where id_comprador = :id and voucher LIKE :pesquisa';
    	$query1 = 'SELECT * from pedido where id_comprador = :id and voucher LIKE :pesquisa LIMIT :inicio, :fim';
    	$array = array (':id' => $_SESSION['id'], ':pesquisa' => '%' . $value . '%');
    	$array1 = array (':id' => $_SESSION['id'], ':pesquisa' => '%' . $value . '%', ':inicio' => $limite, ':fim' => $qtdRegistros );
    }
    else if ($param == 'data')
    {
        $query = 'SELECT * from pedido where id_comprador = :id and data = :data';
    	$query1 = 'SELECT * from pedido where id_comprador = :id and data = :data LIMIT :inicio, :fim';
    	$array = array (':id' => $_SESSION['id'], ':data' => $value);
    	$array1 = array (':id' => $_SESSION['id'], 'data' => $value, ':inicio' => $limite, ':fim' => $qtdRegistros );
    }
	
	$selecionaPedidos = $con->prepare($query);
	$selecionaPedidos->execute( $array );

	$pedidosTotal = $selecionaPedidos->fetchAll(PDO::FETCH_ASSOC);

	$qtdPedidos = count($pedidosTotal);

	$selecionaPedidosLimite = $con->prepare($query1);
	$selecionaPedidosLimite->execute( $array1 );

	$pedidos = $selecionaPedidosLimite->fetchAll(PDO::FETCH_ASSOC);

	if(isset($pedidos)) :

	$html = '
	<table class="table-orders" cellspacing="0">
		<tr>
			<th>Voucher</th>
			<th>Serviço</th>
			<th>Data</th>
			<th>Valor</th>
			<th>Vendido por</th>
		</tr>';

			foreach ($pedidos as $key => $pedido) 
			{
				$selecionaServico = $con->prepare('SELECT * from servico where id = :id');
				$selecionaServico->execute( array (':id' => $pedido['id_servico']) );
				$servico = $selecionaServico->fetch(PDO::FETCH_ASSOC);

				$selecionaPrestador = $con->prepare('SELECT * from usuario where id = :id');
				$selecionaPrestador->execute( array (':id' => $pedido['id_prestador']) );
				$prestador = $selecionaPrestador->fetch(PDO::FETCH_ASSOC);

				$html .= '<tr class="tr-hover">';
				$html .= '<td>' . $pedido['voucher'] . '</td>';
				$html .= '<td>' . $servico['titulo'] . '</td>';
				$html .= '<td>' . date('d/m/Y', strtotime($pedido['data'])) . '</td>';
				$html .= '<td>R$ ' . number_format($pedido['valor_servico'], 2, ',', '.') . '</td>';
				$html .= '<td>' . $prestador['nome'] . '</td>';
				$html .= '</tr>';
			}


	$html .= '</table>


	<div class="pagination-admin">
		<ul>';

			if ($numPagina > 0 )
			{
				$paginaAnterior = $numPagina - 1;
				
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaAnterior) ) ) .'"><</a></li>';
			}
		

			if( $qtdPedidos >= $qtdRegistros )
			{
				$total = $qtdPedidos/$qtdRegistros;

				for ($i=0; $i < $total; $i++) 
				{ 
					$ur = 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$i) ) ); 
					$html .= '<li><a href="' . $ur . '">' . ($i + 1) . '</a></li>';
				}

			}
			$totalPaginas = $qtdPedidos/$qtdRegistros; 

			if ( $numPagina < ($totalPaginas - 1) )
			{
				$paginaSeguinte = $numPagina + 1;
	
				$html .= '<li><a href="painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaSeguinte) ) ) . '">></a></li>';
				
			}
		$html .= '
		</ul>
	</div>
	<div class="clear"></div>';

	echo $html;

	else:

		echo 'Não há registros referente a pesquisa';

	endif;
}

$p = $_POST['p'];
if ( $p == "cidades" )
{
	$uf = $_POST['uf'];

	$cidade = $con->prepare('Select Id, Nome from cidades where Uf = :uf');
			$cidade->execute( array ( ':uf' => $uf ) );

			$cidadeAll =  $cidade->fetchAll(PDO::FETCH_ASSOC);

			$cidades = '';

			foreach ($cidadeAll as $key => $value) 
			{
				$cidades .= '<option value="' . $value['Id'] . '">' . $value['Nome'] . '</option>';
			}

	echo $cidades;

	exit();
}

else if ( $p == 'excluir-servico' )
{
	$id = $_POST['id'];

	$deleteServico = $con->prepare('Delete from servico where id = :id');
	$result = $deleteServico->execute (array ( ':id' => $id ) );

	if ($result)
	{
		echo '<p class="sucess">Serviço excluído com sucesso!</p>';
	}

	exit();
}

else if ( $p == 'verifica-login' )
{
	session_start();

	if( !isset($_SESSION['logado']) || ( isset($_SESSION['logado']) && !$_SESSION['logado'] ) )
	{
		$return['cod']  = 0;

		echo json_encode($return);
	}

	else if($_SESSION['logado'])
	{
		$return['cod'] = 1;
		$return['idComprador'] = $_SESSION['id'];
		
		echo json_encode($return);
	}
}

else if ($p == 'consulta-preco')
{

	$id = $_POST['idServico'];
	$kg = $_POST['kg'];
	
	$select = $con->prepare('Select kg_acresc from servico where id = :id ');
	$select->execute( array (':id' => $id) );

	$result = $select->fetch(PDO::FETCH_ASSOC);

	if($result)
	{
		$dados = unserialize($result['kg_acresc']);
		echo  number_format($dados[$kg], 2, ',', '.');
	}
}
else if ($p == 'consulta-titulo-servico')
{
    $value = $_POST['value'];
    consultaServico('titulo', $value, $con);
    
}
else if ($p == 'consulta-categoria-servico')
{
    $value = $_POST['value'];
    consultaServico('categoria', $value, $con);
}
else if ($p == 'consulta-status-pedido')
{
    $value = $_POST['value'];
    consultaPedido('status', $value, $con);
}
else if ($p == 'consulta-data-pedido')
{
    $value = $_POST['value'];
    consultaPedido('data', $value, $con);
}
else if ($p == 'consulta-cpf-pedido')
{
    $value = $_POST['value'];
    consultaPedido('cpf', $value, $con);
}
else if ($p == 'consulta-voucher-compra')
{
    $value = $_POST['value'];
    consultaCompra('voucher', $value, $con);
}
else if ($p == 'consulta-nome-compra')
{
    $value = $_POST['value'];
    consultaCompra('nome', $value, $con);
}
else if ($p == 'consulta-data-compra')
{
    $value = $_POST['value'];
    consultaCompra('data', $value, $con);
}