<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
} else {
    if (isset($response['responseCode']) && $response['responseCode'] === '2002600') {
        echo 'Payment rejected successfully.';
    } else {
        echo 'Invalid response format.';
    }
}