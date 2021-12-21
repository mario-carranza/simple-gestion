<?php

namespace App\Services\Transbank;

use Exception;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;
use Backpack\Settings\app\Models\Setting;

class WebpayPlusMallService {

    const PRODUCTION_ENDPOINT = 'https://webpay3g.transbank.cl';
    const INTEGRATION_ENDPOINT = 'https://webpay3gint.transbank.cl';

    private $enviromentEndpoint;
    private $apiKeySecret;
    private $commerceCode;

    const PAYMENT_CODE = 'tbkplusmall';

    public function __construct()
    {
        $paymentMethod = PaymentMethod::where('code', self::PAYMENT_CODE)->first();

        $enviromentSetting = Setting::get('payment_environment');

        $configuration = json_decode($paymentMethod->json_value);

        if ($enviromentSetting === 'INTEGRACION') {

            $this->enviromentEndpoint = self::INTEGRATION_ENDPOINT;
            $this->apiKeySecret = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
            $this->commerceCode = '597055555535';

        } else if ($enviromentSetting === 'PRODUCCION') {

            $this->enviromentEndpoint = self::PRODUCTION_ENDPOINT;
            $this->apiKeySecret = $configuration['api_key_secret'];
            $this->commerceCode = $configuration['commerce_code'];
            
        } else {
            throw new Exception('El valor de configuración establecido para el ambiente de WebpayPlus no es valido.');
        }
    }


    public function request($url, $method, array $data = [], array $headers = [])
    {
        $client = new \GuzzleHttp\Client();

        $defaultHeaders = [
            'Tbk-Api-Key-Id' => $this->commerceCode,
            'Tbk-Api-Key-Secret' => $this->apiKeySecret,
            'Content-Type' => 'application/json',
        ];

        $request = [
            'headers' => array_merge($defaultHeaders, $headers),
        ];

        if (!empty($data)) {
            $request = array_merge($request, $data);
        }

        try {
            $response = $client->request($method, $url, $request);
            return $response;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $error = $e->getResponse()->getBody()->getContents();
            Log::error('ClientException: ' . $error);
            return ['error_message' => $error];
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $error = $e->getResponse()->getBody()->getContents();
            Log::error('ServerException: ' . $error);
            return ['error_message' => $error];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $error = $e->getResponse()->getBody()->getContents();
            Log::error('RequestException: ' . $error);
            return ['error_message' => $error];
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Exception: ' . $error);
            return ['error_message' => $error];
        }
    }

    /**
     * Crea una nueva transaccion y retorna la URL y Token relacionado con ella
     * 
     * @param string $buyOrder
     * @param string $sessionId
     * @param array $details
     * @return array Array con  url y token
     * @throws Exception
     */
    public function createTransaction($buyOrder, $sessionId, $details) : array
    {
        $url = $this->enviromentEndpoint . '/rswebpaytransaction/api/webpay/v1.2/transactions';
        $method = 'POST';
        $data = [
            'json' => [
                'buy_order' => $buyOrder,
                'session_id' => $sessionId,
                'return_url' => route('transbank.webpayplus.mall.response'),
                'details' => $details,
            ],
        ];

        $response = $this->request($url, $method, $data);

        if (is_array($response) && array_key_exists('error_message', $response)) {
            throw new Exception($response['error_message']);
        }

        $dataResponse = $response->getBody()->getContents();

        return json_decode($dataResponse, true);
    }

    /**
     * Consulta el estado de una transacción utilizando su Token
     * 
     * @param string $token
     * @return array estado de la transacción
     * @throws Exception
     */
    public function getTokenResult(string $token) : array
    {
        $url = $this->enviromentEndpoint . '/rswebpaytransaction/api/webpay/v1.2/transactions/' . $token;
        
        $method = 'PUT';

        $response = $this->request($url, $method);

        if (is_array($response) && array_key_exists('error_message', $response)) {
            throw new Exception($response['error_message']);
        }

        $dataResponse = $response->getBody()->getContents();

        return json_decode($dataResponse, true);
    }
}