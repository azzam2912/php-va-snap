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
        $clientKey = getClientKey(); 
        $privateKey = getPrivateKey(); 
        $signature = generateSignatureForGetToken($clientKey, $privateKey);

        $response = $this->model->getTokenForMerchant($clientKey, $signature);

        require 'views/responses/tokenResponse.php';
    }

    public function createVA()
    {
        $clientKey = getClientKey(); 
        $clientSecret = getClientSecret(); 
        $accessToken = getAccessToken(); 
        $signature = generateSignatureForCreateVA($clientKey, $clientSecret);

        $response = $this->model->createVA($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function getTokenForAcquirer()
    {
        $clientKey = getAcquirerClientKey(); 
        $privateKey = getAcquirerPrivateKey(); 
        $signature = generateSignatureForGetToken($clientKey, $privateKey);

        $response = $this->model->getTokenForAcquirer($clientKey, $signature);

        require 'views/responses/tokenResponse.php';
    }

    public function inquiry()
    {
        $clientKey = getAcquirerClientKey(); 
        $clientSecret = getAcquirerClientSecret(); 
        $accessToken = getAcquirerAccessToken(); 
        $signature = generateSignatureForInquiry($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $response = $this->model->inquiry($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo);

        require 'views/responses/inquiryResponse.php';
    }

    public function payment()
    {
        $clientKey = getAcquirerClientKey(); 
        $clientSecret = getAcquirerClientSecret(); 
        $accessToken = getAcquirerAccessToken(); 
        $signature = generateSignatureForPayment($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); 

        $response = $this->model->payment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName);

        require 'views/responses/paymentResponse.php';
    }

    public function createVAMGPC()
    {
        $clientKey = getClientKey(); 
        $clientSecret = getClientSecret(); 
        $accessToken = getAccessToken(); 
        $signature = generateSignatureForCreateVAMGPC($clientKey, $clientSecret);

        $response = $this->model->createVAMGPC($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function inquiryDirect()
    {
        $clientKey = getAcquirerClientKey(); 
        $clientSecret = getAcquirerClientSecret(); 
        $accessToken = getAcquirerAccessToken(); 
        $signature = generateSignatureForInquiryDirect($clientKey, $clientSecret);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $response = $this->model->inquiryDirect($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo);

        require 'views/responses/inquiryResponse.php';
    }

    public function rejectPayment($rejectionReason)
    {
        $clientKey = getAcquirerClientKey(); 
        $clientSecret = getAcquirerClientSecret(); 
        $accessToken = getAcquirerAccessToken(); 
        $signature = generateSignatureForRejectPayment($clientKey, $clientSecret, $rejectionReason);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); 

        $response = $this->model->rejectPayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $rejectionReason);

        require 'views/responses/rejectPaymentResponse.php';
    }

    public function createVABillVariable()
    {
        $clientKey = getClientKey(); 
        $clientSecret = getClientSecret(); 
        $accessToken = getAccessToken(); 
        $signature = generateSignatureForCreateVABillVariable($clientKey, $clientSecret);

        $response = $this->model->createVABillVariable($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }

    public function createVAMultiBillVariable()
    {
        $clientKey = getClientKey(); 
        $clientSecret = getClientSecret(); 
        $accessToken = getAccessToken(); 
        $signature = generateSignatureForCreateVAMultiBillVariable($clientKey, $clientSecret);

        $response = $this->model->createVAMultiBillVariable($clientKey, $accessToken, $signature);

        require 'views/responses/createVAResponse.php';
    }
    public function reversePayment($paymentRequestId)
    {
        $clientKey = getAcquirerClientKey(); 
        $clientSecret = getAcquirerClientSecret(); 
        $accessToken = getAcquirerAccessToken(); 
        $signature = generateSignatureForReversePayment($clientKey, $clientSecret, $paymentRequestId);

        $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
        $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
        $customerNo = $normalizedVaNumber['customerNo'];
        $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

        $virtualAccountName = getVirtualAccountName(); 

        $response = $this->model->reversePayment($clientKey, $accessToken, $signature, $partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $paymentRequestId);

        require 'views/responses/reversePaymentResponse.php';
    }
}