<?php require_once 'conn.php'; ?>
<?php require './libs/openboleto-master/autoloader.php'; ?>

<?php
 
use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Agente;

$dataAtual = date("Y/m/d"); 
$data  = date('Y-m-d', strtotime("+2 days",strtotime($dataAtual))); 
$valor = isset($_POST['valor-servico']) ? $_POST['valor-servico'] : 0 ;
$idComprador =  isset($_POST['id-comprador']) ? $_POST['id-comprador'] : 0 ;
$idPrestador =  isset($_POST['id-prestador']) ? $_POST['id-prestador'] : 0 ;
$idServico =  isset($_POST['id-servico']) ? $_POST['id-servico'] : 0 ;

if($idComprador)
{
    $selecionaComprador = $con->prepare('SELECT * from usuario where id=:id');
    $selecionaComprador->execute( array (':id' => $idComprador ) );
    $comprador = $selecionaComprador->fetch(PDO::FETCH_ASSOC);
    // $sacado = new Agente($comprador['nome'], $comprador['cpf'], 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');

    //pedido

    function geraVoucher($tamanho = 5, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
        $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) 
        {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }

    $voucher = 'V' . $idComprador . geraVoucher();

    $insert = $con->prepare("Insert into pedido (voucher, id_servico, id_comprador, id_prestador, status, valido, data, valor_servico) VALUES (:voucher, :servico, :comprador, :prestador, :status, :valido, :data, :valor)");
    $result = $insert->execute(  array (
        ':voucher' => $voucher,
        ':servico' => $idServico,
        ':comprador' => $idComprador,
        ':prestador' => $idPrestador,
        ':status' => 1,
        ':valido' => 0,
        ':data' => $dataAtual,
        ':valor' => $valor
        )
    );

    //fim pedido


    if($result)
    {
        $sacado = new Agente($comprador['nome'], $comprador['cpf']);
        $cedente = new Agente('PETPLACE LTDA', '02.123.123/0001-11', 'Comercial', '27524000', 'Resende', 'RJ');

        $boleto = new BancoDoBrasil(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($data),
            'valor' => $valor,
            'sequencial' => 1234567, // Para gerar o nosso número
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => 1724, // Até 4 dígitos
            'carteira' => 18,
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 1234, // 4, 6 ou 7 dígitos
        ));

        echo $boleto->getOutput();
        ?>

        <script type="text/javascript">
           window.print();
           window.close();
        </script>
    <?php
    }
}