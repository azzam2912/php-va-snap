<?php
if (isset($response['error'])) {
    echo 'Error: ' . $response['error'];
} else {
    if (isset($response['accessToken'])) {
        echo 'Access Token: ' . $response['accessToken'];
    } else {
        echo 'Invalid response format.';
    }
}