<?php

require_once('src/config/config.php');

function getTimestamp($offset = true)
{
    if($offset) return generateTimestampWithOffset();
    return generateTimestampWithOffset('+00:00');
}

function generateTimestampWithOffset($offset = '+07:00') {
  $timestamp = new DateTime('now');
  $timestamp->setTimezone(new DateTimeZone($offset));
  return $timestamp->format('Y-m-d\TH:i:s'.$offset);
}

function getExternalId()
{
    return 'RID_C_' . '20240404111958408';
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

function normalizeVaNumberSnapForAcq($acquirerBinLength, $paycodeLength)
{
    $invoiceNumber = "INV_CIMB_" . date('YmdHis');
    $partnerServiceIdGenerated = getPartnerServiceId(); 
    $customerNo = getCustomerNo(); 

    // if (strlen($partnerServiceIdGenerated) > $acquirerBinLength) {
    //     $partnerServiceIdGenerated = substr($partnerServiceIdGenerated, 0, $acquirerBinLength);
    // } else {
    //     $partnerServiceIdGenerated = str_pad($partnerServiceIdGenerated, $acquirerBinLength, '0', STR_PAD_LEFT);
    // }

    $customerNoLength = $paycodeLength - strlen($partnerServiceIdGenerated);
    $customerNoGenerated = substr(str_pad(time(), 10, '0', STR_PAD_LEFT), 0, $customerNoLength);
    $virtualAccountNoGenerated = $partnerServiceIdGenerated . $customerNoGenerated;

    return [
        'partnerServiceId' => $partnerServiceIdGenerated,
        'customerNo' => $customerNoGenerated,
        'virtualAccountNo' => $virtualAccountNoGenerated,
        'invoiceNumber' => $invoiceNumber
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
    $stringToSign = $clientId . "|" . $timestamp;
    echo "[helpers.php generateSignature $ stringToSign] " . $stringToSign . "\n\n";
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

    $acquirerBinLength = 4;
    $paycodeLength = 16;
    $variables = normalizeVaNumberSnapForAcq($acquirerBinLength, $paycodeLength);
    $partnerServiceId = $variables['partnerServiceId'];
    $customerNo = $variables['customerNo'];
    $virtualAccountNo = $variables['virtualAccountNo'];
    $invoiceNumber = $variables['invoiceNumber'];
    $uniqueId = '20240404085435';
    $expiredDate = getExpiredDate();

    $encodedJSON = json_encode([
        'partnerServiceId' => $partnerServiceId,
        'customerNo' => $customerNo,
        'virtualAccountNo' => $virtualAccountNo,
        'trxId' => $invoiceNumber,
        'virtualAccountTrxType' => 1,
        'totalAmount' => [
            'value' => '12500.00',
            'currency' => 'IDR',
        ],
        'feeAmount' => [
            'value' => '1000.00',
            'currency' => 'IDR',
        ],
        'expiredDate' => $expiredDate,
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
                'additionalInfo' => new stdClass(),
            ],
        ],
        'freeTexts' => [
            [
                'english' => 'Free text',
                'indonesia' => 'Tulisan bebas',
            ],
        ],
        'additionalInfo' => [
            'deviceId' => '12345679237 ' . $uniqueId,
            'channel' => 'mobilephone ' . $uniqueId,
        ],
    ]);
    echo "Encoded JSON: " . $encodedJSON . "\n\n";
    return $encodedJSON;
}

function getExpiredDate($offset = '+07:00', $expiredDays = '14')
{
    $timestamp = new DateTime('now');
    $timestamp->setTimezone(new DateTimeZone($offset));
    $timestamp->add(new DateInterval('P'.$expiredDays.'D'));
    return $timestamp->format('Y-m-d\TH:i:s'.$offset);
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
    $normalizedVaNumber = normalizeVaNumberSnapForAcq(4, 16); 
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
