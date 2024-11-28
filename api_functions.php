<?php
// api_functions.php

// API base URL and authorization token
define('API_BASE_URL', 'https://admin.simhas.klads.co.in');
define('TOKEN', 'eVWpHfuEu9TzlRyxFSzNXQRQM-Io--ul');

// Asset URL constant
define('ASSET_URL', API_BASE_URL . '/assets/');

// Fetch API function using cURL
function fetchAPI($endpoint, $method = 'GET', $body = null) {
    $url = API_BASE_URL . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'Authorization: ' . 'Bearer ' . TOKEN,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("Curl failed: " . $error);
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode >= 400) {
        die("HTTP error code: " . $httpcode . " Response: " . $response);
    }

    return json_decode($response, true);
}

// Get project by ID
function getProjectById($id) {
    return fetchAPI("/items/projects/" . intval($id));
}



// Get blogs
function getblogs($filter = "", $limit = "") {
    $queryString = "?sort=-date_created";

    if ($filter) {
        $queryString .= "&filter[slug_name][_eq]=" . urlencode($filter);
    }

    if ($limit) {
        $queryString .= "&limit=" . intval($limit);
    }

    return fetchAPI("/items/blogs" . $queryString);
}

// Get blog by ID
function getBlogById($id) {
    return fetchAPI("/items/blogs/" . intval($id));
}

// Additional shared functions can be added here
?>
