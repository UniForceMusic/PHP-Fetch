<?php
    include "fetch.php";

    // GET request
    fetch("https://www.google.com");

    // GET request with query string
    fetch("https://www.google.com", [
        "parameters" => [
            "key" => "value"
        ]
    ]);

    // POST request with form data body
    fetch("https://www.google.com", [
        "method"        => "POST",
        "body"          => [
            "key" => "value"
        ]
    ]);

    // POST request with json body
    fetch("https://www.google.com", [
        "method"        => "POST",
        "headers"       => [
            "Content-Type" => "application/json"
        ],
        "body"          => json_encode([
            "key" => "value"
        ])
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