<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
} else {
    if (isset($response['responseCode']) && $response['responseCode'] === '2002700') {
        $virtualAccountData = $response['virtualAccountData'];
        echo 'Partner Service ID: ' . $virtualAccountData['partnerServiceId'] . PHP_EOL;
        echo 'Customer No: ' . $virtualAccountData['customerNo'] . PHP_EOL;
        echo 'Virtual Account No: ' . $virtualAccountData['virtualAccountNo'] . PHP_EOL;
        echo 'Virtual Account Name: ' . $virtualAccountData['virtualAccountName'] . PHP_EOL;
        echo 'Virtual Account Email: ' . $virtualAccountData['virtualAccountEmail'] . PHP_EOL;
        echo 'Virtual Account Phone: ' . $virtualAccountData['virtualAccountPhone'] . PHP_EOL;
    } else {
        echo 'Invalid response format.';
    }
}