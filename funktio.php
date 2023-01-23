<?php

  function lyhytosoite ($pituus =5) {
    $merkit = "2346789BCDFGHJKMPQRTVWXYabcdefghijklmnopqrstvxyz";
    $merkkeja = strlen($merkit);

    $tulos = "";
    for ($i = 1; $i <= $pituus; $i++) {
      $paikka = rand(1, $merkkeja - 1);
      $merkki = $merkit[$paikka];
      $tulos = $tulos . $merkki;
      }

      return $tulos;
    }

?>