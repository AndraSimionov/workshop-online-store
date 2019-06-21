<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SendService
{
	const BANK_RESPONSE_CODE_OK = 'Ok';

	function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('ApiClient');
    }

    /**
     * @param string $token
     * @param string $email
     * @param int $price
     * @param string $currency
     * @param string $orderReference
     *
     * @return string
     *
     * @throws Exception
     */
    function payOrder($token, $email, $price, $currency, $orderReference)
    {
        //TODO 5: build request (don't forget about headers)

		//TODO 5: initialize an HTTP client using the above request
		//
		//TODO 5: make the request & handle the response
    }

	/**
	 * @param string $bankResponse
	 * @return array
	 */
    public function interpretPayApiResponse($bankResponse)
    {
    	$storeResponse = [
			'success' => false,
			'message' => '',
			'orderReference' => 0
		];

		$decodedBankResponse = $this->parseApiResponse($bankResponse);
		if (null === $decodedBankResponse) {
			//output bank communication error
			$storeResponse['message'] = 'A communication error with the bank occurred! :(';

			return $storeResponse;
		}

		if (!isset($decodedBankResponse['meta']['status'])) {
			//output bank error bankResponse
			$storeResponse['message'] = 'Malformed bank response! :(';

			return $storeResponse;
		}

		if (self::BANK_RESPONSE_CODE_OK !== $decodedBankResponse['meta']['status']) {
			$storeResponse['message'] = 'Transaction refused by bank!';

			if (isset($decodedBankResponse['meta']['message'])) {
				$storeResponse['message'] = $decodedBankResponse['meta']['message'];
			}

			return $storeResponse;
		}

		$storeResponse['success'] = true;
		$storeResponse['orderReference'] = $decodedBankResponse['orderData']['reference'];
		$storeResponse['message'] = sprintf('Order %s successfully processed & paid!', $decodedBankResponse['orderData']['reference']);

		$this->updateOrderStatus($storeResponse);

		return $storeResponse;
    }

    private function updateOrderStatus($storeResponse)
	{
		if ($storeResponse['success']) {
			$this->CI->OrderModel->updateOrderStatus($storeResponse['orderReference'], Order::ORDER_STATUS_PAID);
		} else {
			$this->CI->OrderModel->updateOrderStatus($storeResponse['orderReference'], Order::ORDER_STATUS_FAILED);
		}
	}

    /**
     * @param $response
     * @return mixed
     */
    private function parseApiResponse($response)
    {
        return $responseParameters = json_decode($response, true);
    }

    //TODO 9: create a method that sends a refund request to bank
}
