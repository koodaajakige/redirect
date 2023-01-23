<?php
// Tuodaan funktiot.
require_once('utils.php');

// Alustetaan pagestatus-muuttuja:
// 0 = etusivu
// -1 = virheellinen tunniste
// -2 = tietokantavirhe
// 1 = lisätyn osoitteen tietosivu
$pagestatus = 0;

// Palvelun osoite
$baseurl = "https://neutroni.hayo.fi/~knykanen/redirect/";

//require = tiedosto tarvitaan (esim.funktio), jotta ohjelma jatkuu
//include = tiedostoa ei välttämättä tarvita, hyvä lisäke 

require_once('funktio.php');

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

// Tarkistetaan onko lomakkeen nappia painettu.
if (isset($_POST["shorten"])) {
  // Nappia on painettu, noudetaan URL-osoite lomakkeelta.
  $url = $_POST["url"];
  try {
  // Avataan tietokantayhteys luomalla PDO-oliosta ilmentymä.
  $pdo = new PDO($dsn, $user, $pass, $options);

  // Alustetaan lyhytosoitteen tarkistuskysely.
$stmt = $pdo->prepare("SELECT 1
                        FROM osoite
                        WHERE tunniste = ?");

// Muuttuja valitulle lyhytosoitteelle.
$hash = "";

// Toistetaan, kunnes sopiva lyhyosoite on
// löytynyt.
while ($hash == "") {

  // Muodostetaan lyhytosoite-ehdokas.
  $generated = generateHash(5);

  // Tarkistetaan, ettei generoitu lyhytosoite
  // ole jo käytetty. Kysely ei tuota tulosta,
  // jos ehdokasta ei löydy taulusta.
  $stmt->execute([$generated]);
  $result = $stmt->fetchColumn();
  if (!$result) {
  // Ehdokasta ei ole käytetty, valitaan
  // se käytettäväksi lyhytosoitteeksi.
  $hash = $generated;
  }
}

// Haetaan käyttäjän ip-osoite.
$ip = $_SERVER['REMOTE_ADDR'];

// Alustetaan lisäyslause.
$stmt2 = $pdo->prepare("INSERT INTO osoite
                        (tunniste, url, ip)
                        VALUES
                        (?, ?, ?)");
// Lisätään osoite tietokantaan.
$stmt2->execute([$hash, $url, $ip]);

// TODO tulossivu käyttäjälle

// Osoite on lisätty tietokantaan, muodostetaan
// käyttäjälle tietosivu.
$pagestatus = 1;
$shorturl = $baseurl . $hash;

  } catch (PDOException $e) {
  // Avaamisessa tapahtui virhe, tulostetaan virheilmoitus.
  $pagestatus = -2;
  $error = $e->getMessage();
  }
  } 

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

    // Tarkistetaan, onko taulukossa arvoa hash-muuttujan arvolla.
    //if (isset($osoitteet[$hash])) {

    // Taulukossa on hash-muuttujaa vastaava avain, haetaan osoite.
    //$url = $osoitteet[$hash];
    
    if ($rivi) {
    // Edelleenohjataan riviltä löytyvään osoitteeseen.
    $url = $rivi['url'];
    header("Location: " . $url);
    exit;

    } else {
      // Taulukosta ei löytynyt hash-muuttujaa vastaavaa riviä,
      // tulostetaan virheilmoitus.
      $pagestatus = -1;
    }

  } catch (PDOException $e) {
    // Avaamisessa tapahtui virhe, tulostetaan virheilmoitus.
    $pagestatus = -2;
    $error = $e->getMessage();
  }
    

//} else {

  //Tähän tulee lomake.
  //include 'lomake.html';
  // hash-parametrilla ei ole arvoa, tulostetaan käyttäjälle
  // esittelyteksti.
  //echo "Tämä on osoitteiden lyhentäjä. Odota maltilla, " .
  //"tänne tulee tulevaisuudessa lisää toiminnallisuutta.";
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Lyhentäjä</title>
  <meta charset='UTF-8'>
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0">
  <link href='styles.css' rel='stylesheet'>
</head>
<body>
  <div class='page'>
    <header>
      <h1>Lyhentäjä</h1>
      <div>ällistyttävä osoitelyhentäjä</div>
    </header>
    <main>

<?php
  if ($pagestatus == 0) {
?>
      <div class='form'>
        <p>Tällä palvelulla voit lyhentää pitkän osoitteen
          lyhyeksi. Syötä alla olevaan kenttään pitkä osoite
          ja paina nappia, saat käyttöösi lyhytosoitteen,
          jota voit jakaa eteenpäin.</p>
        <form action='' method='POST'>
          <label for='url'>Syötä lyhennettävä osoite</label>
          <div class='url'>
            <input type='text' name='url'
                placeholder='tosi pitkä osoite'>
            <input type='submit' name='shorten' value='lyhennä'>
          </div>
        </form>
      </div>
<?php
}

  if ($pagestatus == -1) {
?>
        <div class='error'>
          <h2>HUPSISTA!</h2>
          <p>Näyttää siltä, että lyhytosoitetta ei löytynyt.
            Ole hyvä ja tarkista antamasi osoite.</p>
          <p>Voit tehdä <a href="<?=$baseurl?>">tällä
            palvelulla</a> oman lyhytosoitteen.</p>
        </div>
<?php
  }

  if ($pagestatus == -2) {
?>
      <div class='error'>
        <h2>NYT KÄVI HASSUSTI!</h2>
        <p>Nostamme käden ylös virheen merkiksi,
          palvelimellamme on pientä hässäkkää.
          Ole hyvä ja kokeile myöhemmin uudelleen.</p>
        <p>(virheilmoitus: <?=$error?>)</p>
      </div>
<?php
  }

  if ($pagestatus == 1) {
    ?>
    <div class='finish'>
    <h2>JIPPII!</h2>
    <p>Loit itsellesi uuden lyhytosoitteen,
    aivan mahtava juttu! Jatkossa voit käyttää
    seuraavaa osoitetta:
    <div class='code'><?=$shorturl?></div></p>
    <p>Voit tehdä uuden lyhytosoitteen
    <a href="<?=$baseurl?>">täällä</a>.</p>
    </div>
    <?php
    }
    

?>

    </main>
    <footer>
      <hr>
      &copy; Kurpitsa Solutions
    </footer>
  </div>
  </body>
</html>
