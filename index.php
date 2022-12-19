<?php
/* This will give an error. Note the output
 * above, which is before the header() call */

// Määritellään tietokantayhteyden muodostamisessa
// tarvittavat tiedot.
$dsn = "mysql:host=localhost;" .
"dbname={$_SERVER['DB_DATABASE']};" .
"charset=utf8mb4";
$user = $_SERVER['DB_USERNAME'];
$pass = $_SERVER['DB_PASSWORD'];
$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
];

// Tarkistetaan, onko URL-osoitteessa annettu hash-parametri.
if (isset($_GET["hash"])) {

    // hash-parametrilla on arvo, poimitaan se muuttujaan.
    $hash = $_GET["hash"];

    try {
        // Avataan tietokantayhteys luomalla PDO-oliosta ilmentymä.
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Alustetaan hakukysely.
        $stmt = $pdo->prepare("SELECT url
        FROM osoite
        WHERE tunniste = ?");

        // Suoritetaan kysely ja haetaan tuloksen rivi.
        $stmt->execute([$hash]);
        $rivi = $stmt->fetch();

        if ($rivi) {

            // Edelleenohjataan riviltä löytyvään osoitteeseen.
            $url = $rivi['url'];
            header("Location: " . $url);
            exit;

        } else {

          // Taulusta ei löytynyt tunnistetta vastaavaa riviä,
          // tulostetaan virheilmoitus.
          echo "Väärä tunniste :(";

      }
      } catch (PDOException $e) {
        // Avaamisessa tapahtui virhe, tulostetaan virheilmoitus.
        echo $e->getMessage();
      }

    } else {

      // hash-parametrilla ei ole arvoa, tulostetaan käyttäjälle
      // esittelyteksti.
      echo "Tämä on osoitteiden lyhentäjä. Odota maltilla, " .
      "tänne tulee tulevaisuudessa lisää toiminnallisuutta.";
    }

?>