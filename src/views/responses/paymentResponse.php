<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
}
if (isset($response['responseCode']) && $response['responseCode'] === '2002500') {
    $virtualAccountData = $response['virtualAccountData'];
    echo 'Virtual Account Name: ' . $virtualAccountData['virtualAccountName'] . PHP_EOL;
} else {
    echo 'Invalid response format.';
}