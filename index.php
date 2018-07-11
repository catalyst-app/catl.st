<?php

if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] == "off") {
	header("HTTP/1.1 301 Moved Permanently");

	// WWWs MAY cause an issue with SSL in the future
	if (strpos($_SERVER["HTTP_HOST"], "www.") === 0) {
		header("Location: "."https://".substr($_SERVER["HTTP_HOST"], 4).$_SERVER["REQUEST_URI"]);
	} else {
		header("Location: "."https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	}
	
	die("Redirecting...");
}

// WWWs are annoying and icky
if (strpos($_SERVER["HTTP_HOST"], "www.") === 0) {
	header("Location: "."https://".substr($_SERVER["HTTP_HOST"], 4).$_SERVER["REQUEST_URI"]);
} else {
	header("Location: "."https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
}

echo "You've reached https://".htmlspecialchars($_SERVER["HTTP_HOST"]).htmlspecialchars($_SERVER["REQUEST_URI"]);
