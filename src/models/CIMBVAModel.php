<?php

require_once('src/helpers/helpers.php');
class CIMBVAModel
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl; 
    }

    public function getTokenForMerchant($clientKey, $signature)
    {
        $url = $this->baseUrl . '/authorization/v1/access-token/b2b';
        $headers = [
            'X-CLIENT-KEY: ' . $clientKey,
            'X-TIMESTAMP: ' . getTimestamp(), // From helpers.php
            'X-SIGNATURE: ' . $signature,
        ];
        $body = json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function createVA($clientKey, $accessToken, $signature, $timestamp)
    {
        $externalId = getExternalId(); //'RID_C_20240404104404355';
        $url = $this->baseUrl . '/bi-snap-va/cimb/v1/transfer-va/create-va';
        $headers = array(
            "Content-Type: application/json",
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . $externalId, // From helpers.php
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'Authorization:Bearer ' . $accessToken,
            'CHANNEL-ID: VA009'
        );
        print_r($headers);
        echo "\n\n top is headers \n\n";
        $body = getCreateVAMGPCRequestBody(); // From helpers.php

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    function getAccessToken($url, $clientKey, $signature, $timestamp)
    {
        $url = $url . "/authorization/v1/access-token/b2b";
        $headers = array(
            "X-CLIENT-KEY: " . $clientKey,
            "X-TIMESTAMP: " . $timestamp,
            "X-SIGNATURE: " . $signature
        );

        $body = json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    // Add other methods for inquiry and payment

    private function makeRequest($method, $url, $headers, $body = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
        ]);

        //echo "[function CIMBVAModel makeRequest : $ curl before ] " . $curl . "\n\n";

        $response = curl_exec($curl);
        $error = curl_error($curl);

        echo "[function CIMBVAModel makeRequest : $ response ] " . $response . "\n\n";
        //echo "[function CIMBVAModel makeRequest : $ curlafter ] " . $curl . "\n\n";

        curl_close($curl);

        if ($error) {
            return ['error' => $error];

        }

        return json_decode($response, true);
    }

    public function getTokenForAcquirer($clientKey, $signature)
    {
        $url = $this->baseUrl . '/authorization/v1/access-token/b2b';
        $headers = [
            'X-CLIENT-KEY: ' . $clientKey,
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
        ];
        $body = json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function inquiry($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo)
    {
        $url = $this->baseUrl . '/va-bank-interface/cimb-snap/v1/transfer-va/inquiry';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateInit' => getTimestamp(),
            'inquiryRequestId' => getInquiryRequestId(),
            'channelCode' => 6011,
            'additionalInfo' => [
                'isPayment' => 'N',
            ],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function payment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName)
    {
        $url = $this->baseUrl . '/va-bank-interface/cimb-snap/v1/transfer-va/payment';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'paymentRequestId' => getInquiryRequestId(),
            'trxDateTime' => getTimestamp(),
            'channelCode' => 6011,
            'referenceNo' => getReferenceNo(),
            'paidAmount' => [
                'value' => '12500.00',
                'currency' => 'IDR',
            ],
            'totalAmount' => [
                'value' => '12500.00',
                'currency' => 'IDR',
            ],
            'additionalInfo' => [
                'info1' => 'Info 1',
                'info2' => 'Info 2',
            ],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function createVAMGPC($clientKey, $accessToken, $signature)
    {
        $url = $this->baseUrl . '/bi-snap-va/cimb/v1/transfer-va/create-va';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA009',
        ];
        $body = getCreateVAMGPCRequestBody();

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function inquiryDirect($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo)
    {
        $url = $this->baseUrl . '/va-bank-interface/cimb-snap/v1/transfer-va/inquiry';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'txnDateInit' => getTimestamp(),
            'inquiryRequestId' => getInquiryRequestId(),
            'channelCode' => 6011,
            'additionalInfo' => [
                'isPayment' => 'N',
            ],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function rejectPayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $rejectionReason)
    {
        $url = $this->baseUrl . '/va-bank-interface/cimb-snap/v1/transfer-va/reject-payment';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'paymentRequestId' => getInquiryRequestId(),
            'trxDateTime' => getTimestamp(),
            'channelCode' => 6011,
            'referenceNo' => getReferenceNo(),
            'paidAmount' => [
                'value' => '12500.00',
                'currency' => 'IDR',
            ],
            'totalAmount' => [
                'value' => '12500.00',
                'currency' => 'IDR',
            ],
            'rejectionReason' => $rejectionReason,
            'additionalInfo' => [
                'info1' => 'Info 1',
                'info2' => 'Info 2',
            ],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function createVABillVariable($clientKey, $accessToken, $signature)
    {
        $url = $this->baseUrl . '/bi-snap-va/cimb/v1/transfer-va/create-va';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = getCreateVABillVariableRequestBody();

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function createVAMultiBillVariable($clientKey, $accessToken, $signature)
    {
        $url = $this->baseUrl . '/bi-snap-va/cimb/v1/transfer-va/create-va';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = getCreateVAMultiBillVariableRequestBody();

        return $this->makeRequest('POST', $url, $headers, $body);
    }

    public function reversePayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $paymentRequestId)
    {
        $url = $this->baseUrl . '/va-bank-interface/cimb-snap/v1/transfer-va/reverse-payment';
        $headers = [
            'X-PARTNER-ID: ' . $clientKey,
            'X-EXTERNAL-ID: ' . getExternalId(),
            'X-TIMESTAMP: ' . getTimestamp(),
            'X-SIGNATURE: ' . $signature,
            'Authorization: Bearer ' . $accessToken,
            'CHANNEL-ID: VA011',
        ];
        $body = json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'paymentRequestId' => $paymentRequestId,
            'trxDateTime' => getTimestamp(),
            'channelCode' => 6011,
            'referenceNo' => getReferenceNo(),
            'additionalInfo' => [
                'info1' => 'Info 1',
                'info2' => 'Info 2',
            ],
        ]);

        return $this->makeRequest('POST', $url, $headers, $body);
    }
}