<?php
/* This will give an error. Note the output
 * above, which is before the header() call */

//Luodaan osoitteet-taulukko.
$osoitteet = array("NG5TG" => "https://www.w3schools.com/php/",
"R7E7L" =>	"https://www.php.net/manual/en/index.php",
"S44E8" =>	"https://thevalleyofcode.com/php/",
"UDCJ9" =>	"https://phpapprentice.com/",
"ZZU1M" =>	"https://phptherightway.com/");

// Tarkistetaan, onko URL-osoitteessa annettu hash-parametri.
if (isset($_GET["hash"])) {

    // hash-parametrilla on arvo, poimitaan se muuttujaan.
    $hash = $_GET["hash"];

    // Tarkistetaan, onko taulukossa arvoa hash-muuttujan arvolla.
    if (isset($osoitteet[$hash])) {

    // Taulukossa on hash-muuttujaa vastaava avain, haetaan osoite.
    $url = $osoitteet[$hash];

    // Edelleenohjataan taulukosta löytyvään osoitteeseen.
    header("Location: " . $url);
    exit;

    } else {
    // Taulukosta ei löytynyt hash-muuttujaa vastaavaa avainta,
    // tulostetaan virheilmoitus.
    echo "Väärä tunniste :(";
    }

  } else {
    // hash-parametrilla ei ole arvoa, tulostetaan käyttäjälle
    // esittelyteksti.
    echo "Tämä on osoitteiden lyhentäjä. Odota maltilla, " .
    "tänne tulee tulevaisuudessa lisää toiminnallisuutta.";
    }
    
?>