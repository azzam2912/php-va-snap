<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
} else {
    if (isset($response['responseCode']) && $response['responseCode'] === '2002400') {
        $virtualAccountData = $response['virtualAccountData'];
        echo 'Virtual Account Name: ' . $virtualAccountData['virtualAccountName'] . PHP_EOL;
    } else {
        echo 'Invalid response format.';
    }
}