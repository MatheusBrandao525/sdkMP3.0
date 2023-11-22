<?php

require_once 'vendor/autoload.php';
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Resources\Preference\Item;
use MercadoPago\Exceptions\MPApiException;

MercadoPagoConfig::setAccessToken("ACCESS_TOKEN");

$client = new PreferenceClient();

try{
    $total = 150.00 ;
    $request = [
        "external_reference" => "4567",
        "items" => array(
            array(
                "id" => "4567",
                "title" => "Productos Informaticos",
                "description" => "Pc gamer",
                "quantity" => 1,
                "unit_price" => $total
            )

            ),
        'payer' => array(
          'name' => 'Matheus',
          'surname' => 'Brandao',
          'email' => 'mafe123silva@gmail.com',
          'phone' => array(
            'area_code' => '69',
            'number' => '9993203891',
          ),
          'identification' => array(
            'type' => 'CPF',
            'number' => '123456789'
          ),
          'address' => array(
            'street_name' => 'Street',
            'street_number' => 123,
            'zip_code' => '06233200',
          ),
          "payment_methods" => array(
            "installments" => 1,
            "default_payment_method_id" => null,
            "default_installments" => null,
            "excluded_payment_methods" => array(), // Mantém os métodos existentes
            "excluded_payment_types" => array(), // Mantém os tipos de pagamento existentes
            "pix" => array(
                "enabled" => true,
                "type" => "standard"
                // Configurações específicas do PIX, se necessário
            ),
        ),
          'notification_url' => 'https://localhost/pastateste/index.php',
          'statement_descriptor' => 'LIZZIIMPORTS'
        )
    ];
    $preference = $client->create($request);
    $preference->back_urls = array(
        "success" => "http://localhost/RC/Tienda/pagoConfirmado.php",
        "failure" => "http://localhost/RC/Tienda/pagoErroneo.php",
    );
    $preference->auto_return = "approved";
    $preference->binary_mode = true;
}catch (MPApiException $e) {
    echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
    var_dump($e->getApiResponse()->getContent());
} catch (\Exception $e) {
    echo $e->getMessage();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirmação de Pagamento</title>
  <script src="https://sdk.mercadopago.com/js/v2">
    </script>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #f1b5b8;">
  <div class="container mt-5" style="background-color: #fff; padding:2rem;border-radius:20px;">
  <table class="table table-bordered">
      <tbody>
        <tr class="bg-dark">
          <td colspan="2">
            <h2>Dados do Usuário</h2>
          </td>
          <td colspan="2">
            <h2>Detalhes do Pedido</h2>
          </td>
        </tr>
        <tr>
          <td><strong>Nome:</strong></td>
          <td>Matheus Brandao</td>
          <td><strong>Produto:</strong></td>
          <td>PC Gamer</td>
        </tr>
        <tr>
          <td><strong>Email:</strong></td>
          <td>mafe123silva@gmail.com</td>
          <td><strong>Quantidade:</strong></td>
          <td>1</td>
        </tr>
        <tr>
          <td><strong>CPF:</strong></td>
          <td>123.456.789-00</td>
          <td><strong>Total Produtos:</strong></td>
          <td>R$ 150,00</td>
        </tr>
        <tr>
          <td><strong>Endereço:</strong></td>
          <td>Rua Exemplo, 123</td>
          <td><strong>Frete:</strong></td>
          <td>R$ 30,00</td>
        </tr>
        <tr>
          <td><strong>CEP:</strong></td>
          <td>06233-200</td>
          <td><strong>Total Pedido:</strong></td>
          <td>R$ 180,00</td>
        </tr>
        <tr>
          <td><strong>Telefone:</strong></td>
          <td>(69) 99932-0389</td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>
    <div id="wallet_container">
  </div>
  
  <script>
      const mp = new MercadoPago('PUBLIC_KEY', {
        locale: 'pt-BR'
      });

      mp.bricks().create("wallet", "wallet_container", {
        initialization: {
            preferenceId: "<?= $preference->id ?>",
        },
      });
  </script>
</body>
</html>
