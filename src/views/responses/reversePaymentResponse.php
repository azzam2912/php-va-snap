<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
} else {
    if (isset($response['responseCode']) && $response['responseCode'] === '2002800') {
        echo 'Payment reversed successfully.';
    } else {
        echo 'Invalid response format.';
    }
}