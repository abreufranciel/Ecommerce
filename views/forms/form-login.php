<div class="title">
	<p>Entrar</p>
</div>
<div id="box-login">
	<div class="msg" id="msg-login"></div>
	<form method="post" action="">
		<label>Email</label><br/>
		<input type="text" name="email_login" class="input-line">
		<br/>
		<label>Senha</label><br/>
		<input type="password" name="senha_login" class="input-line">
		<br/>
		<input type="submit" name="logar" value="Entrar" id="logar" class="btn-pp-p">
		<br>
		<a href="" class="forget-pass">Esqueci minha senha</a>
	</form>
</div>
    <div class="row">
        <div class="col-lg-4 offset-lg-4" style="text-align: center;">
            <span class="separator-g" style="display: inline-block; vertical-align: middle;"></span>
    	    <span style="display: inline-block; text-align: center; vertical-align: middle;" >ou</span>
    	    <span class="separator-g"  style="display: inline-block; vertical-align: middle;"></span>
        </div>
    </div>
<div class="title">
	<p>Registrar-se</p>
</div>
<div id="box-register">
	<div class="msg" id="msg-register"></div>
	<form method="post" action="">
		<label>Nome</label><br/>
		<input type="text" name="nome_registro" required="" class="input-line">
		<br/>
		<label>CPF</label><br/>
		<input type="text" name="cpf_registro" required="" class="input-line">
		<br/>
		<label>Estado</label><br/>
		<select name="uf_registro" required="" class="input-line" onchange="carregaCidades('uf_registro', 'cidade_registro');">
			<?php 

					$uf = $con->prepare('Select Nome, UF from estados');
					$uf->execute();

					$ufAll =  $uf->fetchAll(PDO::FETCH_ASSOC);

					foreach ($ufAll as $key => $value) 
					{
						echo '<option value="' . $value['UF'] . '">' . $value['Nome'] . '</option>';
					}

				?>
		</select>
		<br/>
		<label>Cidade</label><br/>
		<select name="cidade_registro" class="input-line" required="">
			
		</select>
		<br/>
		<label>Email</label><br/>
		<input type="text" name="email_registro" required="" class="input-line">
		<br/>
		<label>Senha</label><br/>
		<input type="password" name="senha_registro" required="" class="input-line">
		<br/>
		<label>Confirmar Senha</label><br/>
		<input type="password" name="senha_conf_registro" required="" class="input-line">
		<br/>
		<input type="submit" name="registro" value="Cadastrar" id="registro" class="btn-pp-p">
	</form>
</div>
</div>

