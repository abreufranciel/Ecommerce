<?php 
require_once 'conn.php';

session_start();

function validaCPF($cpf = null) {

	// Verifica se um número foi informado
	if(empty($cpf)) {
		return false;
	}

	// Elimina possivel mascara
	$cpf = preg_replace("/[^0-9]/", "", $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
	
	// Verifica se o numero de digitos informados é igual a 11 
	if (strlen($cpf) != 11) {
		return false;
	}
	// Verifica se nenhuma das sequências invalidas abaixo 
	// foi digitada. Caso afirmativo, retorna falso
	else if ($cpf == '00000000000' || 
		$cpf == '11111111111' || 
		$cpf == '22222222222' || 
		$cpf == '33333333333' || 
		$cpf == '44444444444' || 
		$cpf == '55555555555' || 
		$cpf == '66666666666' || 
		$cpf == '77777777777' || 
		$cpf == '88888888888' || 
		$cpf == '99999999999') {
		return false;
	 // Calcula os digitos verificadores para verificar se o
	 // CPF é válido
	 } else {   
		
		for ($t = 9; $t < 11; $t++) {
			
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) {
				return false;
			}
		}

		return true;
	}
}
 
$_SESSION['logado'] = false;
 
 
// Verifica se a origem da requisição é do mesmo domínio da aplicação
$action = $_POST['action'];

if ($action == 'login') :

	// Recebe os dados do formulário
	$email  = ( !empty($_POST['email']) ) ? $_POST['email'] : null;
	$senha  = ( !empty($_POST['senha']) ) ? md5($_POST['senha']) : null;;
	 
	 
	// Validações de preenchimento e-mail e senha se foi preenchido o e-mail
	if (empty($email)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Prencha seu email!</p>';
		echo json_encode($return);
		exit();
	endif;
	 
	if (empty($senha)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Preencha sua senha!</p>';
		echo json_encode($return);
		exit();
	endif;
	 
	// Verifica se o formato do e-mail é válido
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Formato de e-mail inválido!</p>';
		echo json_encode($return);
		exit();
	endif;
	 
	// Valida os dados do usuário com o banco de dados
	$sql = "SELECT * FROM usuario WHERE email = :email and senha = :senha";
	$consult = $con->prepare($sql);
	$consult->execute( array (
		':email' => $email,
		':senha' => $senha
		)
	);
	$result = $consult->fetch(PDO::FETCH_OBJ);
	 
	 
	// 
	if( !empty($result) ):
		$_SESSION['id'] = $result->id;
		$_SESSION['nome'] = $result->nome;
		$_SESSION['cpf'] = $result->cpf;
		$_SESSION['email'] = $result->email;
		$_SESSION['senha'] = $result->senha;
		$_SESSION['imagem'] = $result->imagem;
		$_SESSION['capa'] = $result->capa;
		$_SESSION['cidade'] = $result->cidade;
		$_SESSION['uf'] = $result->uf;
		$_SESSION['logado'] = true;
	endif;
	 
	 
	// Se logado envia código 1, senão retorna mensagem de erro para o login
	if ($_SESSION['logado'] == true):
		$return['cod'] = 1;
		echo json_encode($return);
		exit();
	else:
		$return['cod'] = 0;
		$return['msg'] = '<p class="error">Dados não conferem!</p>';
		echo json_encode($return);
		exit();
	endif;

elseif ($action == 'register') :

	//Recebe os dados dos formulários
	$name = ( !empty($_POST['nome']) ) ? $_POST['nome'] : null; 
	$cpf = ( !empty($_POST['cpf']) ) ? $_POST['cpf'] : null;
	$city = ( !empty($_POST['cidade']) ) ? $_POST['cidade'] : null; 
	$uf =  ( !empty($_POST['uf']) ) ? $_POST['uf'] : null;
	$email = ( !empty($_POST['email']) ) ? $_POST['email'] : null;
	$pass = ( !empty($_POST['senha']) ) ? $_POST['senha'] : null;
	$confPass = ( !empty($_POST['senhaConf']) ) ? $_POST['senhaConf'] : null;

	if(empty($name)) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Informe um nome!</p>';
 		echo json_encode($return);
		exit();
	endif;

	if(empty($cpf)) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="error">Informe um CPF!</p>';
 		echo json_encode($return);
		exit();
	endif;

	if(!validaCPF($cpf)) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Informe um CPF válido!</p>';
 		echo json_encode($return);
		exit();
	endif;

	// Verifica se o estado foi selecionado
	if( $uf == 'none' ) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Informe um estado!</p>';
 		echo json_encode($return);
		exit();
	endif;

	// 
	if (empty($email)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="error">Informe um e-mail!</p>';
		echo json_encode($return);
		exit();
	endif;

	// Verifica se o formato do e-mail é válido
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Formato de e-mail inválido!</p>';
		echo json_encode($return);
		exit();
	endif;

	//
	if (empty($pass)):
		$return['cod'] = 0;
		$return['msg'] = '<p class="error">Insira uma senha!</p>';
		echo json_encode($return);
		exit();
	endif;

	//verifica se a senha tem menos que 6 caracteres
	if( strlen($pass) < 6 ) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="alert">Senha precisa ter 6 ou mais caracteres!</p>';
 		echo json_encode($return);
		exit();
	endif;
	
	// Verifica se as senhas são iguais
	if( $pass != $confPass) :
		$return['cod'] = 0;
		$return['msg'] = '<p class="error">Senhas não conferem!</p>';
 		echo json_encode($return);
		exit();
	endif;

	// Verifica se o email já está cadastrado
	$sqlEmail = "Select * from usuario where email = :email";
	$consultEmail = $con->prepare($sqlEmail);
	$consultEmail->execute( array ( ':email' => $email ) );

	if( $consultEmail->rowCount() > 0  ) :

		$return['cod'] = 0;
		$return['msg'] = '<p class="error">E-mail já cadastrado na base dados!</p>';
		echo json_encode($return);
		exit();

	else :

		// Insere os dados no banco, caso esteja tudo certo
		$sql = "Insert into usuario (nome, cpf, cidade, uf, email, senha, vendedor, verificado) values (:name, :cpf, :city, :uf, :email, :pass, 0, 0)";

		$insert = $con->prepare($sql);
		$result = $insert->execute( array (
			':name' => $name,
			':cpf' => $cpf,
			':city' => $city,
			':uf' => $uf,
			':email' => $email,
			':pass' => md5($pass)
			)
		);

		if($result) :

			$_SESSION['id'] = $con->lastInsertId();
			$_SESSION['nome'] = $name;
			$_SESSION['cpf'] = $cpf;
			$_SESSION['email'] = $email;
			$_SESSION['senha'] = md5($pass);
			$_SESSION['cidade'] = $city;
			$_SESSION['logado'] = true;

			$return['cod'] = 1;

			echo json_encode($return);
			
			//envia email para confirmação
    	    $cor = " '#02c1b1' ";
            $destino = $email;
            $assunto = "Confirmar E-mail";
            $mensagem = "
            <html>
                <body style='background-color: #f2f2f2;'>
                    <div style='background-color: #02c1b1; padding: 20px; color: #fff; width: 80%; margin: 20px auto 0 auto;'>PETPLACE</div>
                    <div style='background-color: #fff; padding: 20px; width: 80%; margin: 0 auto 20px auto;'>
                        <p style='font-size: 12px; text-align: center;'> Olá, nós da PetPlace estamos muito feliz de ter você conosco. Mas antes de tudo, precisamos confirmar que é você mesmo. <br/>
                        <a href='http://mypetplace.online/painel.php?pconf=" . $con->lastInsertId() . "'>Clique aqui</a> para confirmar seu e-mail!
                        </p>
                    </div>
                </body>
            </html>
    
            ";
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: PetPlace <contato@mypetplace.online>' . "\r\n";
            
            mail($destino, $assunto, $mensagem, $headers);
			exit();

		elseif (!$result) :

			$return['cod'] = 0;
			$return['msg'] = '<p class="error">Um erro inesperado aconteceu!</p>';

			echo json_encode($return);
			exit();

		endif;

	endif;

endif;