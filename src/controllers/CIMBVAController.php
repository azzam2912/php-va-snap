<?php

require_once 'models/CIMBVAModel.php';
require_once 'helpers/helpers.php';

class CIMBVAController
{
    private $model;

    public function __construct()
    {
        $this->model = new CIMBVAModel();
    }

    public function getTokenForMerchant()
    {
        $clientKey = getClientKey(); // From config.php
        $privateKey = getPrivateKey(); // From config.php
        $signature = generateSignatureForGetToken($clientKey, $privateKey);

        $response = $this->model->getTokenForMerchant($clientKey, $signature);

        require 'views/responses/tokenResponse.php';
    }

    public function createVA()
    {
        $clientKey = getClientKey(); // From config.php
        $clientSecret = getClientSecret(); // From config.php
        $accessToken = getAccessToken(); // From previous response
        $signature = generateSignatureForCreateVA($clientKey, $clientSecret);

        $response = $this->model->createVA($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function getTokenForAcquirer()
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $privateKey = getAcquirerPrivateKey(); // From config.php
        $signature = generateSignatureForGetToken($clientKey, $privateKey);

        $response = $this->model->getTokenForAcquirer($clientKey, $signature);

        require 'views/responses/tokenResponse.php';
    }

    public function inquiry()
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $clientSecret = getAcquirerClientSecret(); // From config.php
        $accessToken = getAcquirerAccessToken(); // From previous response
        $signature = generateSignatureForInquiry($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); // Acquirer bin length is 4
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $response = $this->model->inquiry($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo);

        require 'views/responses/inquiryResponse.php';
    }

    public function payment()
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $clientSecret = getAcquirerClientSecret(); // From config.php
        $accessToken = getAcquirerAccessToken(); // From previous response
        $signature = generateSignatureForPayment($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); // Acquirer bin length is 4
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); // From inquiry response

        $response = $this->model->payment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName);

        require 'views/responses/paymentResponse.php';
    }

    public function createVAMGPC()
    {
        $clientKey = getClientKey(); // From config.php
        $clientSecret = getClientSecret(); // From config.php
        $accessToken = getAccessToken(); // From previous response
        $signature = generateSignatureForCreateVAMGPC($clientKey, $clientSecret);

        $response = $this->model->createVAMGPC($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function inquiryDirect()
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $clientSecret = getAcquirerClientSecret(); // From config.php
        $accessToken = getAcquirerAccessToken(); // From previous response
        $signature = generateSignatureForInquiryDirect($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); // Acquirer bin length is 4
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $response = $this->model->inquiryDirect($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo);

        require 'views/responses/inquiryResponse.php';
    }

    public function rejectPayment($rejectionReason)
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $clientSecret = getAcquirerClientSecret(); // From config.php
        $accessToken = getAcquirerAccessToken(); // From previous response
        $signature = generateSignatureForRejectPayment($clientKey, $clientSecret, $rejectionReason);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); // Acquirer bin length is 4
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); // From inquiry response

        $response = $this->model->rejectPayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $rejectionReason);

        require 'views/responses/rejectPaymentResponse.php';
    }

    public function createVABillVariable()
    {
        $clientKey = getClientKey(); // From config.php
        $clientSecret = getClientSecret(); // From config.php
        $accessToken = getAccessToken(); // From previous response
        $signature = generateSignatureForCreateVABillVariable($clientKey, $clientSecret);

        $response = $this->model->createVABillVariable($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function createVAMultiBillVariable()
    {
        $clientKey = getClientKey(); // From config.php
        $clientSecret = getClientSecret(); // From config.php
        $accessToken = getAccessToken(); // From previous response
        $signature = generateSignatureForCreateVAMultiBillVariable($clientKey, $clientSecret);

        $response = $this->model->createVAMultiBillVariable($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }
    public function reversePayment($paymentRequestId)
    {
        $clientKey = getAcquirerClientKey(); // From config.php
        $clientSecret = getAcquirerClientSecret(); // From config.php
        $accessToken = getAcquirerAccessToken(); // From previous response
        $signature = generateSignatureForReversePayment($clientKey, $clientSecret, $paymentRequestId);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); // Acquirer bin length is 4
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); // From inquiry response

        $response = $this->model->reversePayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $paymentRequestId);

        require 'views/responses/reversePaymentResponse.php';
    }
}