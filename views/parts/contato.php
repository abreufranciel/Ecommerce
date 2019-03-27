<h2 class="title" style="margin-top: 50px">Fale Conosco</h2>
<div class="separator"></div>
<div class="row">
    <form method="post" action="" class="col-lg-4 offset-lg-4 col-10 offset-1" style="padding: 25px 0 50px 0;">
        <input type="text" name="nome" placeholder="Nome" required="" class="col-lg-12 input-light" />
        <input type="text" name="email" placeholder="E-mail" required="" class="col-lg-12 input-light" />
        <input type="text" name="assunto" placeholder="Assunto" required="" class="col-lg-12 input-light" />
        <textarea name="mensagem" placeholder="Mensagem" required="" rows=5 class="col-lg-12 input-light"></textarea>
        <input type="submit" value="Enviar" name="enviar-msg" class="col-lg-2 offset-lg-5 btn-pp-p" />
    </form>
</div>
<?php

if(isset($_POST['enviar-msg']))
{
    $to      = 'contato@mypetplace.online';
    $subject = $_POST['assunto'];
    $message = $_POST['mensagem'];
    $headers = 'From: ' . $_POST['email'] . "\n" .
    'Reply-To: ' . $_POST['email']  . "\n" .
    'X-Mailer: PHP/' . phpversion();


    if(mail($to, $subject, $message))
      {
        echo '<div style="margin-bottom: 15px;" class="alert-success col-lg-4 offset-lg-4 col-10 offset-1">Agradecemos sua mensagem!</div>';
      }
  
}

?>