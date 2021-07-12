<?php
    include "fetch.php";

    // GET request
    $response = fetch("https://www.google.com");

    // GET request with query string
    $response = fetch("https://www.google.com", [
        "parameters" => [
            "key" => "value"
        ]
    ]);

    // POST request with form data body
    $response = fetch("https://www.google.com", [
        "method"        => "POST",
        "body"          => [
            "key" => "value"
        ]
    ]);

    // POST request with json body
    $response = fetch("https://www.google.com", [
        "method"        => "POST",
        "headers"       => [
            "Content-Type" => "application/json"
        ],
        "body"          => json_encode([
            "key" => "value"
        ])
    ]);

    // POST request with json body without a secure connection
    $response = fetch("https://www.google.com", [
        "method"        => "POST",
        "headers"       => [
            "Content-Type" => "application/json"
        ],
        "body"          => json_encode([
            "key" => "value"
        ]),
        "secure"        => false
    ]);

    // Response object that fetch returns
    $response->$url;
    $response->code;
    $response->http;
    $response->headers;
    $response->body;
    $response->error;
    $response->json();
    $response->xml();
?>