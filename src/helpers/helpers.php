<?php

function getTimestamp()
{
    return gmdate('Y-m-d\TH:i:s\Z');
}

function getExternalId()
{
    return 'RID_C_' . uniqid();
}

function getCreateVARequestBody()
{
    return json_encode([
        'partnerServiceId' => getPartnerServiceId(),
        'trxId' => getInvoiceNumber(),
        'virtualAccountTrxType' => 1,
        'totalAmount' => [
            'value' => '12500.00',
            'currency' => 'IDR',
        ],
        'feeAmount' => [
            'value' => '1000.00',
            'currency' => 'IDR',
        ],
        'expiredDate' => getExpiredDate(),
        'virtualAccountName' => 'T_' . time(),
        'virtualAccountEmail' => 'test.cimb.' . time() . '@test.com',
        'virtualAccountPhone' => '628' . time(),
        'billDetails' => [
            [
                'billCode' => '01',
                'billNo' => '123456789012345678',
                'billName' => 'Bill A for Jan',
                'billShortName' => 'Bill A',
                'billDescription' => [
                    'english' => 'Maintenance',
                    'indonesia' => 'Pemeliharaan',
                ],
                'billSubCompany' => '00001',
                'billAmount' => [
                    'value' => '20000.00',
                    'currency' => 'IDR',
                ],
                'additionalInfo' => [],
            ],
        ],
        'freeTexts' => [
            [
                'english' => 'Free text ' . time(),
                'indonesia' => 'Tulisan bebas ' . time(),
            ],
        ],
        'additionalInfo' => [
            'virtualAccountConfig' => [
                'reusableStatus' => true,
            ],
            'deviceId' => '12345679237 ' . time(),
            'channel' => 'mobilephone ' . time(),
        ],
    ]);
}

function getInquiryRequestId()
{
    return 'R_' . substr(str_replace('.', '', microtime(true)), 0, 10);
}

function getReferenceNo()
{
    return substr(str_replace('.', '', microtime(true)), 0, 10);
}

function normalizeVaNumberSnapForAcq($acquirerBinLength)
{
    $partnerServiceId = getPartnerServiceId(); 
    $customerNo = getCustomerNo(); 
    $paycodeLength = 16;

    if (strlen($partnerServiceId) > $acquirerBinLength) {
        $partnerServiceId = substr($partnerServiceId, 0, $acquirerBinLength);
    } else {
        $partnerServiceId = str_pad($partnerServiceId, $acquirerBinLength, '0', STR_PAD_LEFT);
    }

    $customerNoLength = $paycodeLength - strlen($partnerServiceId);
    $customerNo = str_pad($customerNo, $customerNoLength, '0', STR_PAD_LEFT);

    $virtualAccountNo = $partnerServiceId . $customerNo;

    return [
        'partnerServiceId' => $partnerServiceId,
        'customerNo' => $customerNo,
        'virtualAccountNo' => $virtualAccountNo,
    ];
}

function generateSignatureForGetToken($clientKey, $privateKey)
{
    $data = [
        'clientKey' => $clientKey,
        'timestamp' => getTimestamp(),
        'body' => json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]),
    ];

    $signature = '';;

    return $signature;
}

function generateSignatureForCreateVA($clientKey, $clientSecret)
{
    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => getCreateVARequestBody(),
    ];

    $signature = '';

    return $signature;
}

function generateSignatureForInquiry($clientKey, $clientSecret)
{
    $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
    $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
    $customerNo = $normalizedVaNumber['customerNo'];
    $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => json_encode([
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateInit' => getTimestamp(),
            'inquiryRequestId' => getInquiryRequestId(),
            'channelCode' => 6011,
            'additionalInfo' => [
                'isPayment' => 'N',
            ],
        ]),
    ];

    $signature = '';

    return $signature;
}

function generateSignatureForPayment($clientKey, $clientSecret)
{
    $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
    $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
    $customerNo = $normalizedVaNumber['customerNo'];
    $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];
    $virtualAccountName = getVirtualAccountName(); 

    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => json_encode([
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
        ]),
    ];

    $signature = '';

    return $signature;
}

function generateSignature($clientId, $timestamp, $privateKey)
{
    $stringToSign = $clientId . ":" . $timestamp;
    $signature = "";

    // Calculate the signature using SHA256withRSA
    $success = openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    if ($success) {
        return base64_encode($signature);
    } else {
        return false;
    }
}

function getCreateVAMGPCRequestBody()
{
    $partnerServiceId = getPartnerServiceId(); 
    $customerNo = getCustomerNo(); 

    $acquirerBinLength = 4;
    $paycodeLength = 16;

    if (strlen($partnerServiceId) > $acquirerBinLength) {
        $partnerServiceId = substr($partnerServiceId, 0, $acquirerBinLength);
    } else {
        $partnerServiceId = str_pad($partnerServiceId, $acquirerBinLength, '0', STR_PAD_LEFT);
    }

    $customerNoLength = $paycodeLength - strlen($partnerServiceId);
    $customerNo = str_pad($customerNo, $customerNoLength, '0', STR_PAD_LEFT);

    $virtualAccountNo = $partnerServiceId . $customerNo;

    return json_encode([
        'partnerServiceId' => $partnerServiceId,
        'customerNo' => $customerNo,
        'virtualAccountNo' => $virtualAccountNo,
        'trxId' => getInvoiceNumber(),
        'virtualAccountTrxType' => 1,
        'totalAmount' => [
            'value' => '12500.00',
            'currency' => 'IDR',
        ],
        'feeAmount' => [
            'value' => '1000.00',
            'currency' => 'IDR',
        ],
        'expiredDate' => getExpiredDate(),
        'virtualAccountName' => 'T_' . time(),
        'virtualAccountEmail' => 'test.btn.' . time() . '@test.com',
        'virtualAccountPhone' => '628' . time(),
        'billDetails' => [
            [
                'billCode' => '01',
                'billNo' => '123456789012345678',
                'billName' => 'Bill A for Jan',
                'billShortName' => 'Bill A',
                'billDescription' => [
                    'english' => 'Maintenance',
                    'indonesia' => 'Pemeliharaan',
                ],
                'billSubCompany' => '00001',
                'billAmount' => [
                    'value' => '20000.00',
                    'currency' => 'IDR',
                ],
                'additionalInfo' => [],
            ],
        ],
        'freeTexts' => [
            [
                'english' => 'Free text',
                'indonesia' => 'Tulisan bebas',
            ],
        ],
        'additionalInfo' => [
            'deviceId' => '12345679237 ' . uniqid(),
            'channel' => 'mobilephone ' . uniqid(),
        ],
    ]);
}

function getExpiredDate()
{
    $expiredDays = 14; // Expired in 14 days for MGPC
    return date('Y-m-d\TH:i:s\Z', strtotime('+' . $expiredDays . ' days'));
}

function generateSignatureForCreateVAMGPC($clientKey, $clientSecret)
{
    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => getCreateVAMGPCRequestBody(),
    ];

    $signature = '';

    return $signature;
}

function generateSignatureForInquiryDirect($clientKey, $clientSecret)
{
    $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
    $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
    $customerNo = $normalizedVaNumber['customerNo'];
    $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];

    $data = [""
    ];

    #TODO
    return "";
}

function getCreateVABillVariableRequestBody()
{
    return json_encode([
        'partnerServiceId' => getPartnerServiceId(), 
        'trxId' => getInvoiceNumber(),
        'virtualAccountTrxType' => 8, 
        'totalAmount' => [
            'value' => '12500.00',
            'currency' => 'IDR',
        ],
        'feeAmount' => [
            'value' => '1000.00',
            'currency' => 'IDR',
        ],
        'expiredDate' => getExpiredDate(),
        'virtualAccountName' => 'T_' . time(),
        'virtualAccountEmail' => 'test.cimb.' . time() . '@test.com',
        'virtualAccountPhone' => '628' . time(),
        'billDetails' => [
            [
                'billCode' => '01',
                'billNo' => '123456789012345678',
                'billName' => 'Bill A for Jan',
                'billShortName' => 'Bill A',
                'billDescription' => [
                    'english' => 'Maintenance',
                    'indonesia' => 'Pemeliharaan',
                ],
                'billSubCompany' => '00001',
                'billAmount' => [
                    'value' => '20000.00',
                    'currency' => 'IDR',
                ],
                'additionalInfo' => [],
            ],
        ],
        'freeTexts' => [
            [
                'english' => 'Free text ' . time(),
                'indonesia' => 'Tulisan bebas ' . time(),
            ],
        ],
        'additionalInfo' => [
            'virtualAccountConfig' => [
                'reusableStatus' => true,
            ],
            'deviceId' => '12345679237 ' . time(),
            'channel' => 'mobilephone ' . time(),
        ],
    ]);
}

function getCreateVAMultiBillVariableRequestBody()
{
    return json_encode([
        'partnerServiceId' => getPartnerServiceId(), 
        'trxId' => getInvoiceNumber(),
        'virtualAccountTrxType' => 9, // Multi Bill Variable
        'totalAmount' => [
            'value' => '42500.00', // Sum of all billAmounts
            'currency' => 'IDR',
        ],
        'feeAmount' => [
            'value' => '1000.00',
            'currency' => 'IDR',
        ],
        'expiredDate' => getExpiredDate(),
        'virtualAccountName' => 'T_' . time(),
        'virtualAccountEmail' => 'test.cimb.' . time() . '@test.com',
        'virtualAccountPhone' => '628' . time(),
        'billDetails' => [
            [
                'billCode' => '01',
                'billNo' => '123456789012345678',
                'billName' => 'Bill A for Jan',
                'billShortName' => 'Bill A',
                'billDescription' => [
                    'english' => 'Maintenance',
                    'indonesia' => 'Pemeliharaan',
                ],
                'billSubCompany' => '00001',
                'billAmount' => [
                    'value' => '20000.00',
                    'currency' => 'IDR',
                ],
                'additionalInfo' => [],
            ],
            [
                'billCode' => '02',
                'billNo' => '987654321098765432',
                'billName' => 'Bill B for Feb',
                'billShortName' => 'Bill B',
                'billDescription' => [
                    'english' => 'Electricity',
                    'indonesia' => 'Listrik',
                ],
                'billSubCompany' => '00002',
                'billAmount' => [
                    'value' => '22500.00',
                    'currency' => 'IDR',
                ],
                'additionalInfo' => [],
            ],
        ],
        'freeTexts' => [
            [
                'english' => 'Free text ' . time(),
                'indonesia' => 'Tulisan bebas ' . time(),
            ],
        ],
        'additionalInfo' => [
            'virtualAccountConfig' => [
                'reusableStatus' => true,
            ],
            'deviceId' => '12345679237 ' . time(),
            'channel' => 'mobilephone ' . time(),
        ],
    ]);
}

function generateSignatureForCreateVABillVariable($clientKey, $clientSecret)
{
    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => getCreateVABillVariableRequestBody(),
    ];

    $signature = '';

    return $signature;
}

function generateSignatureForCreateVAMultiBillVariable($clientKey, $clientSecret)
{
    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => getCreateVAMultiBillVariableRequestBody(),
    ];

    $signature = '';

    return $signature;
}

function generateSignatureForReversePayment($clientKey, $clientSecret, $paymentRequestId)
{
    $normalizedVaNumber = normalizeVaNumberSnapForAcq(4); 
    $partnerServiceId = $normalizedVaNumber['partnerServiceId'];
    $customerNo = $normalizedVaNumber['customerNo'];
    $virtualAccountNo = $normalizedVaNumber['virtualAccountNo'];
    $virtualAccountName = getVirtualAccountName(); 

    $data = [
        'clientKey' => $clientKey,
        'clientSecret' => $clientSecret,
        'timestamp' => getTimestamp(),
        'body' => json_encode([
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
        ]),
    ];

    $signature = '';

    return $signature;
}
