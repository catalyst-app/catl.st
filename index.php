<?php

if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] == "off") {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: "."https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    
    die("Redirecting...");
}

echo "You've reached https://".htmlspecialchars($_SERVER["HTTP_HOST"]).htmlspecialchars($_SERVER["REQUEST_URI"]);
