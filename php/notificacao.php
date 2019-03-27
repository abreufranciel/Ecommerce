<?php 
header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");

require "../conn.php";

$notificationCode = 'CA1FC519E8EFE8EF802224962F8237025DAE';


$data['email'] = 'gislanefabiano@gmail.com';
$data['token'] = '101A7C68C433488E8EEA277A7BB2EE30';


$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications
/' . $notificationCode . '?' . http_build_query($data); 

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);

$xml = curl_exec($curl);

//curl_close($curl);

$xml = simplexml_load_string($xml);

var_dump($xml);

//$reference = $xml->reference;
//$status = $xml->status;

// if($reference && $status)
// {
// 	$select = $con->prepare('Select * from pedido where id = :reference');
// 	$select->execute( array (':reference' => $reference ) );
// 	$result = $select->fetch(PDO::FETCH_ASSOC);

// 	if($result)
// 	{
// 		$update = $con->prepare('Update pedido set status = :status where id = :reference');
// 		$resultUpdate = $update->execute( array (
// 			':reference' => $reference,
// 			':status' => $status
// 			)
// 		);
// 	}
//}