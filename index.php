<?php

if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] == "off") {
	header("HTTP/1.1 301 Moved Permanently");

	// WWWs MAY cause an issue with SSL in the future (even though this is mostly irrelevant with HSTS)
	if (strpos($_SERVER["HTTP_HOST"], "www.") === 0) {
		header("Location: "."https://".substr($_SERVER["HTTP_HOST"], 4).$_SERVER["REQUEST_URI"]);
	} else {
		header("Location: "."https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	}
	
	die("Redirecting...");
}

$locations = [
	"404" => "https://catalystapp.co/Unimplemented/",

	// root
	"" => "https://catalystapp.co/",

	// artists
	"a" => "https://beta.catalystapp.co/Artist/{p1}/",
	"ar" => "https://beta.catalystapp.co/Artist/{p1}/",
	"art" => "https://beta.catalystapp.co/Artist/{p1}/",
	"artist" => "https://beta.catalystapp.co/Artist/{p1}/",
	"artists" => "https://beta.catalystapp.co/Artist/{p1}/",
	"cr" => "https://beta.catalystapp.co/Artist/{p1}/",
	"cre" => "https://beta.catalystapp.co/Artist/{p1}/",
	"create" => "https://beta.catalystapp.co/Artist/{p1}/",
	"creator" => "https://beta.catalystapp.co/Artist/{p1}/",
	"creators" => "https://beta.catalystapp.co/Artist/{p1}/",

	// characters
	"c" => "https://beta.catalystapp.co/Character/View/{p1}/",
	"ch" => "https://beta.catalystapp.co/Character/View/{p1}/",
	"char" => "https://beta.catalystapp.co/Character/View/{p1}/",
	"character" => "https://beta.catalystapp.co/Character/View/{p1}/",
	"characters" => "https://beta.catalystapp.co/Character/View/{p1}/",

	// commissions
	"co" => "https://beta.catalystapp.co/Commission/{p1}/",
	"comm" => "https://beta.catalystapp.co/Commission/{p1}/",
	"commission" => "https://beta.catalystapp.co/Commission/{p1}/",

	// commission types
	"commission-type" => "https://beta.catalystapp.co/Artist/View/{p1}/#ct-{p2}",
	"commissiontype" => "https://beta.catalystapp.co/Artist/View/{p1}/#ct-{p2}",
	"commtype" => "https://beta.catalystapp.co/Artist/View/{p1}/#ct-{p2}",
	"ct" => "https://beta.catalystapp.co/Artist/View/{p1}/#ct-{p2}",

	// users
	"u" => "https://beta.catalystapp.co/User/{p1}/",
	"us" => "https://beta.catalystapp.co/User/{p1}/",
	"user" => "https://beta.catalystapp.co/User/{p1}/",

	// staffs
	"staff" => "https://catalystapp.co/#our-staff",

	// fauxy boyo
	"faux.staff" => "https://beta.catalystapp.co/Character/View/fauxil/",
	"fauxil.staff" => "https://beta.catalystapp.co/Character/View/fauxil/",
	"f.staff" => "https://beta.catalystapp.co/Character/View/fauxil/",

	// cute wuffy
	"l.staff" => "https://beta.catalystapp.co/Character/View/lykai/",
	"lykai.staff" => "https://beta.catalystapp.co/Character/View/lykai/",

	// him
	"foxxo.staff" => "https://beta.catalystapp.co/Character/View/foxxo/",

	// egg
	"ace.staff" => "https://beta.catalystapp.co/Artist/acey/",
	"acey.staff" => "https://beta.catalystapp.co/Artist/acey/",
	"egg.staff" => "https://beta.catalystapp.co/Artist/acey/",
	"oneggiri.staff" => "https://beta.catalystapp.co/Artist/acey/",

	// social networks
	"d" => "https://discord.gg/EECUcnT",
	"dis" => "https://discord.gg/EECUcnT",
	"discord" => "https://discord.gg/EECUcnT",

	"t" => "https://telegram.dog/catalystapp",
	"te" => "https://telegram.dog/catalystapp",
	"tele" => "https://telegram.dog/catalystapp",
	"telegram" => "https://telegram.dog/catalystapp",

	"telegram-announcements" => "https://telegram.dog/catalystapp_announcements",

	"gh" => "https://github.com/catalyst-app/Catalyst",
	"git" => "https://github.com/catalyst-app/Catalyst",
	"github" => "https://github.com/catalyst-app/Catalyst",

	"fa" => "https://furaffinity.net/user/catalystapp/",

	"fn" => "https://beta.furrynetwork.com/catalyst/",

	"ig" => "https://instagram.com/catalyst.app/",
	"insta" => "https://instagram.com/catalyst.app/",

	"trello" => "https://trello.com/b/X37KEv4A/catalyst",

	"tw" => "https://twitter.com/catalystapp_co",
	"twit" => "https://twitter.com/catalystapp_co",
	"twitter" => "https://twitter.com/catalystapp_co",

	"steam" => "https://steamcommunity.com/groups/catalystapp",

	"da" => "https://catalystapp.deviantart.com/",
	"deviantart" => "https://catalystapp.deviantart.com/",

	"weasyl" => "https://weasyl.com/~catalystapp/",

	"r" => "https://reddit.com/r/catalystapp/",
	"redd" => "https://reddit.com/r/catalystapp/",
	"reddit" => "https://reddit.com/r/catalystapp/",

	"tos" => "https://catalystapp.co/Help/ToS",
	"pp" => "https://catalystapp.co/Help/Privacy",

	"p" => "https://patreon.com/catalyst",
	"patreon" => "https://patreon.com/catalyst",

	"kofi" => "https://ko-fi.com/catalystapp",
	"ko-fi" => "https://ko-fi.com/catalystapp",

	"tumblr" => "https://catalystapp-co.tumblr.com",

	"fb" => "https://facebook.com/catalystapp.co",
	"facebook" => "https://facebook.com/catalystapp.co",

	"stream" => "https://www.twitch.tv/catalystapp",
	"yt" => "https://www.youtube.com/channel/UCI8shN2z06O5cBJIfR2DIaA/live",

	// patron supporters!
	"keeri.patreon" => "https://beta.catalystapp.co/Character/View/w5nms1u/",

	"sinnerscout.patreon" => "https://beta.catalystapp.co/Artist/sinnerscout/",

	"mango.patreon" => "https://beta.catalystapp.co/Artist/Manguwu",
	"manguwu.patreon" => "https://beta.catalystapp.co/Artist/Manguwu",

	"toish.patreon" => "https://beta.catalystapp.co/Character/View/toish",

	// fren
	"k" => "https://kalka.io/",
];

if (strpos($_SERVER["HTTP_HOST"], "www.") === 0) {
	$host = substr($_SERVER["HTTP_HOST"], 4);
} else {
	$host = $_SERVER["HTTP_HOST"];
}

$host = substr($host, 0, -strlen("catl.st"));
$host = trim($host, ".");

$parameters = array_values(array_filter(explode("/", $_SERVER["REQUEST_URI"])));

$redirect = $locations["404"];

// if all empty redirect to main domain
if (empty($host) && empty($parameters)) {
	$redirect = $locations[""];
}

// if there was no host specified, use the first parameter (if one exists)
if (empty($host) && !empty($parameters)) {
	$host = array_shift($parameters);
	$parameters = array_values($parameters);
}

if (count($parameters) >= 1 && array_key_exists($parameters[0].".".$host, $locations)) {
	$host = array_shift($parameters).".".$host;
	$parameters = array_values($parameters);
}

if (array_key_exists($host, $locations)) {
	$redirect = $locations[$host];
}

if (count($parameters) >= 1) {
	$redirect = str_replace("{p1}", urlencode($parameters[0]), $redirect);
}
if (count($parameters) >= 2) {
	$redirect = str_replace("{p2}", urlencode($parameters[1]), $redirect);
}

if ($redirect == "https://catalystapp.co/Unimplemented/") {
	header("HTTP/1.1 302 Unimplemented");
} else {
	header("HTTP/1.1 301 Moved Permanently");
}
header("Location: ".$redirect);

?>
Redirecting...
