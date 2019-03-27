<?php require_once 'conn.php'; ?>
<?php require_once 'php/functions.php'; ?>


<title>
    
    <?php
        echo 'PetPlace';
        
        if(isset ($_GET['p']))
        {
            $p = $_GET['p'];
            
            switch ($p) 
            {
                case 'contato' :
                    echo ' - Fale Conosco';
                    break;
                case 'petserv' :
                    echo ' - Prestadores de Serviços';
                    break;
                case 'perfil' :
                    echo ' - Serviços';
                    break;
                case 'pedidos' :
                    echo ' - Meus Pedidos';
                    break;
                case 'compras' :
                    echo ' - Minhas Compras';
                    break;
                case 'servicos' :
                    echo ' - Meus Serviços';
                    break;
                case 'meu-perfil' :
                    echo ' - Meu Perfil';
                    break;
                case 'imagens' :
                    echo ' - Mídia';
                    break;
                    
                    
                    default :
                        echo 'PetPlace';
            }
        }
    
    ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<!-- estilos -->
<link rel="stylesheet" type="text/css" href="libs/ext/fontawesome/css/all.css">
<link rel="stylesheet" type="text/css" href="libs/js/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="libs/ext/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="libs/css/style.css">
<link rel="stylesheet" type="text/css" href="libs/css/admin.css">
<link rel="stylesheet" type="text/css" href="libs/css/medias-queries.css">
<link rel="stylesheet" type="text/css" href="libs/ext/trumbowyg-master/dist/ui/trumbowyg.min.css">
<link rel="stylesheet" href="libs/ext/fancybox/dist/jquery.fancybox.min.css" />
<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> -->

<!-- scripts -->
<script type="text/javascript" src="libs/js/jquery-3.3.1.js"></script>
<script type="text/javascript" src="libs/js/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="libs/ext/trumbowyg-master/dist/trumbowyg.min.js"></script>
<script type="text/javascript" src="libs/js/jquery-mask/src/jquery.mask.js"></script>
<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js">
</script>
<script type="text/javascript" src="libs/js/scripts.js"></script>
<script type="text/javascript" src="libs/js/functions-js.js"></script>
<script src="libs/ext/fancybox/dist/jquery.fancybox.min.js"></script>

<meta property="og:url"           content="http://mypetplace.online" />
<meta property="og:type"          content="website" />
<meta property="og:title"         content="" />
<meta property="og:description"   content="Adquira o melhor serviço para o seu pet!" />
<meta property="og:image"         content="" />