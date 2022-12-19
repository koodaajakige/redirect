<?php
/* This will give an error. Note the output
 * above, which is before the header() call */

//Luodaan osoitteet-taulukko.
$osoitteet = array("NG5TG" => "https://www.w3schools.com/php/",
"R7E7L" =>	"https://www.php.net/manual/en/index.php",
"S44E8" =>	"https://thevalleyofcode.com/php/",
"UDCJ9" =>	"https://phpapprentice.com/",
"ZZU1M" =>	"https://phptherightway.com/");

// Poimitaan URL-osoitteesta hash-parametrin arvo.
$hash = $_GET["hash"];

// Haetaan $hash-muuttujan arvoa vastaava osoite.
$url = $osoitteet[$hash];

// Edelleenohjataan hash-tunnusta vastaavaan osoitteeseen.
header('Location: '. $url);    
exit;

?>