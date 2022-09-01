<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title><?= (isset($_GET["suchbegriff"]) ? '"' . htmlspecialchars($_GET["suchbegriff"], ENT_HTML5, 'UTF-8') . '" -' : ''); ?> Small Search Engine</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      font-family: Verdana, Arial, Tahoma, Sans-Serif;
      font-size: 0.90rem;
    }

    a:link,
    a:visited {
      color: Royalblue;
    }

    /* Text snippet */
    samp {
      font-family: Tahoma, Arial, Sans-Serif;
      font-style: Oblique;
    }

    /* Highlight search term */
    mark {
      background-color: #D5FFAA;
    }

    /* File-Info */
    var {
      color: #00AF02;
      font-size: 0.70rem;
    }

    /* Search results (numbered list) */
    li {
      margin-top: 20px;
    }

    /* Meta-Tags */
    cite {
      font-family: Arial, Sans-Serif;
      font-size: 0.80rem;
      font-style: Normal;
    }

    /* Fields */
    input[type="search"],
    input[type="submit"] {
      font-family: Verdana, Arial, Sans-Serif;
      font-size: 1rem;
      border: Solid 1px #7A7A7A;
      transition: box-shadow 0.3s;
    }

    input[type="search"]:focus,
    input[type="submit"]:focus {
      border: 0;
      outline: 0;
      /* Chromium */
      border: Solid 1px #0078D7;
      box-shadow: 0px 0px 4px 0px #2284E6;
    }
  </style>

</head>

<body>

  <?php
  /* 
  Small Search Engine - Version at: 08/22/2021
  For an up-to-date version, see:
  https://werner-zenk.de/archiv/kleine-suchmaschine.php

  This search engine searches the specified directory (and its subdirectories)
  and the files it contains for a search term!
  Requirement: PHP 7.3 or higher.

 * The files to be searched must have valid HTML (<title>, <head>, <body>, ...).
 * To avoid problems with the display of characters, all files with character encoding UTF-8 (without BOM) be stored.
 * Only browse directories or files that do not contain sensitive data or unprotected scripts!
 */


  /* Enter the directorys to be searched,
      starting from the current directory where this file is located.
      Browse the current directory with  "./"   */
  $verzeichnisse = [
    "./Redmi-9-All-MIUI-RECOVERY-AND-FASTBOOT-ROM.html",
    "./REDMI-9-CUSTOM-ROM-ARROWOS.html",
    "./REDMI-9-CUSTOM-ROM-EVOLUTION-X.html",
    "./SleepingDogs.html",
  ];

  /* The file extensions of the files to be found.
      File extensions with uppercase letters (for example: . JPG) must be extra entered! */
  $dateiendungen = [
    ".gif",
    ".htm",
    ".html",
    ".jpg",
    ".mp3",
    ".pdf",
    ".php",
    ".png",
    ".txt",
    ".zip",
  ];

  /* The file extensions of the files from the upper list which are not 
      text files (only the file name will be displayed) */
  $_dateiendungen = [
    ".gif",
    ".jpg",
    ".mp3",
    ".pdf",
    ".png",
    ".zip",
  ];

  /* Signs for files that are not searched
      should, for example: _internally.htm,  _log.php,  _data.txt */
  $vorzeichen = "_"; // _ (1 Sign)

  /* Individual files that you do not want to search */
  $nicht_durchsuchen = [
    "private.htm",
    "login.php",
  ];

  /* Length of the minimum search term */
  $suchbegriff_min = 4; // 4

  /* Length of the maximum search term */
  $suchbegriff_max = 100; // 100

  /* Maximum display (limit) of results */
  $suchergebnisse_max = 50; // 50

  /* Exclude unwanted input from the search */
  $ausschluss_eingaben = [
    "Demotext",
    "Demotext2",
  ];

  /* Automatic forwarding if only one file
      has been found (yes/no) */
  $auto_weiterleitung = "no"; // no

  /* View HTML source code in search results (yes/no)
      Attention - This option also displays PHP source code! */
  $html_quellcode = "no"; // no

  /* Allow HTML tags as you type (yes/no) */
  $html_eingabe = "no"; // no

  /* Open target in new window/tab.
      $target = "_blank"; */
  $target = ""; // empty!

  /* Show tips when nothing is found (yes/no) */
  $tipps_anzeigen = "yes"; // yes

  /* Sort the results by the last file change (yes/no).
      If "no" will be sorted by the number of results found. */
  $sortierung_dateiaenderung = "no"; // no

  /* Save the result of the search (date, search queries and number of hits) (yes/no) */
  $sucheingabe_speichern = "no"; // no

  /* Name of the file in which the search entries are stored.
      This file is generated automatically on the first entry! */
  $suchdatei = "search_entries.txt"; // search_entries.txt

  /* Search entries that have already been entered as a selection in the 
      View form field (yes/no) */
  $datenliste_anzeigen = "no"; // no

  /* File Info - Show last change and file size (yes/no) */
  $datei_info = "yes"; // yes

  /* Path Info - Show file path (yes/no) */
  $pfad_info = "yes"; // yes

  /* Status Info - Number of directories searched
      and view files (yes/no) */
  $status_info = "yes"; // yes

  /* Length of the displayed text snippet */
  $textausschnitt = 150; // 150 characters

  /* The following characters, words, or HTML attributes
      Replace or remove from the text snippet. */
  $entfernung_zeichen = [
    'Privat' => '',
  ];

  /* Remove content that lies between the following HTML tags.
      This content will not appear in the search results!
      For example, if the <nav> tag contains additional HTML attributes, you must
      removed with the previous option ($entfernung_zeichen). */
  $entfernung_inhalt = [
    "head", // <head></head>
    "nav", // <nav></nav>
    "footer", // <footer></footer>
  ];

  /* Define a specific HTML area for the search.
      Examples: body, main, or article.
      Leave blank if you do not want to use this option. */
  $suche_in_bereich = "";

  /* META-Tags, if they exist, display (yes/no).
      Example the 'content'-Attribute:  <meta name="description" content="Description of the page."> */
  $metatags_anzeigen = "no"; // no

  /* Seaching in META-Tags (yes/no) */
  $metatags_durchsuchen = "no"; // no

  /* This META-Tags seaching */
  $metatags_attribute = [
    "description",
    "keywords",
    "author",
    // "date",
    // "title",
  ];

  /*  Set time zone (see: https://www.php.net/manual/de/timezones.america.php) */
  date_default_timezone_set("America/Chicago"); // An example!

  /* Display PHP Errors (0 / E_ALL) */
  error_reporting(0); // 0

  $datenliste = "";
  if (
    $sucheingabe_speichern == "yes" &&
    $datenliste_anzeigen == "yes" &&
    file_exists($suchdatei)
  ) {
    $datenliste = [];
    foreach (file($suchdatei, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES) as $element) {
      list($datum, $begriff, $anzahl) = explode('|', $element);
      if ($anzahl > 0) {
        $datenliste[] = $begriff;
      }
    }
    sort($datenliste);
    $datenliste = '<datalist id="liste"><option>' . implode('<option>', array_unique($datenliste)) . '</datalist>';
  }

  // View a form
  echo '<form method="get" action="#Form" id="Form">
 <input type="search" name="suchbegriff" placeholder="Enter search term" value="' .
    (isset($_GET["suchbegriff"]) ? htmlspecialchars($_GET["suchbegriff"], ENT_HTML5, 'UTF-8') : '') .
    '" size="35" minlength="' . $suchbegriff_min . '" maxlength="' . $suchbegriff_max . '" list="liste" required="required" spellcheck="true">
 ' . $datenliste . '
 <input type="submit" name="suche" value="search">
</form>
';

  $suchergebnisse = [];
  $gefunden = 0;
  $anzahl_dateien = 0;
  $hyperlink = "";
  $startzeit = microtime(true);
  $url = 'http' . (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ? 's' : '') . '://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]);
  array_push($nicht_durchsuchen, basename($_SERVER["SCRIPT_NAME"]));

  if (
    isset($_GET["suche"]) ||
    isset($_GET["suchbegriff"])
  ) {
    $_GET["suchbegriff"] = rawurldecode($_GET["suchbegriff"]);
    $_GET["suchbegriff"] = str_replace([',', '.', ':', ';', '!', '?', '"', "'", "\t"], '', preg_replace('/\s\s+/', ' ', trim($_GET["suchbegriff"])));
    $_GET["suchbegriff"] = str_ireplace($ausschluss_eingaben, "", $_GET["suchbegriff"]);
    $_GET["suchbegriff"] = $html_eingabe != "no" ? htmlspecialchars($_GET["suchbegriff"], ENT_HTML5, 'UTF-8') : strip_tags($_GET["suchbegriff"]);

    if (
      mb_strlen($_GET["suchbegriff"]) >= $suchbegriff_min &&
      mb_strlen($_GET["suchbegriff"]) <= $suchbegriff_max
    ) {

      foreach ($verzeichnisse as $verzeichnis) {

        foreach ($dateiendungen as $dateiendung) {

          foreach (glob($verzeichnis . "*" . $dateiendung) as $datei) {
            $dateiname = basename($datei);

            if (
              $dateiname[0] != $vorzeichen &&
              !in_array($dateiname, $nicht_durchsuchen)
            ) {
              $text = (!in_array($dateiendung, $_dateiendungen)) ? file_get_contents($datei) : mb_substr(strtoupper($dateiendung), 1) . '-File';
              preg_match('/<title>(.*?)<\/title>/is', $text, $treffer);
              $titel = isset($treffer[1]) ? $treffer[1] : $dateiname;
              unset($treffer[1]);
              $anzahl_dateien++;
              $meta = ($metatags_anzeigen == "yes" && count(get_meta_tags($datei)) > 0) ? '<br><cite>&#10151; ' . implode(' &#10151; ', get_meta_tags($datei)) . '</cite>' : '';

              if ($metatags_durchsuchen == "yes" && get_meta_tags($datei)) {
                $metatag_array = get_meta_tags($datei);

                foreach ($metatags_attribute as $attribut) {

                  if (isset($metatag_array[$attribut]))  $text .= ' [' . $attribut . ': ' . $metatag_array[$attribut] . ']';
                }
              }
              $text = strtr($text, $entfernung_zeichen);

              if (!empty($suche_in_bereich)) {
                preg_match('/<' . $suche_in_bereich . '>(.*?)<\/' . $suche_in_bereich . '>/Usi', $text, $bereich);
                $text = isset($bereich[1]) ? $bereich[1] : '';
              }

              foreach ($entfernung_inhalt as $htmltag) {
                $text = preg_replace('/\<' . $htmltag . '\>(.*)\<\/' . $htmltag . '\>/Usi', '', $text);
              }

              $text = $html_quellcode != "no" ? htmlspecialchars($text, ENT_HTML5, 'UTF-8') : strip_tags($text);
              $text = strtr($text, [
                "&auml;" => "ä", "&ouml;" => "ö", "&uuml;" => "ü", "&Auml;" => "Ä", "&Ouml;" => "Ö", "&Uuml;" => "Ü", "&szlig;" => "ß",
                "\r" => " ", "\n" => " ", "\t" => " ", "   " => " ", "  " => " "
              ]);
              $preg = explode(" ", $_GET["suchbegriff"]);

              if (strstr($_GET["suchbegriff"], "-")) $preg = explode("-", $_GET["suchbegriff"]);
              $pregCount = count($preg);
              $preg = implode(".*?|", $preg);

              if ((mb_strstr($text, $_GET["suchbegriff"]) or
                  mb_stristr($text, $_GET["suchbegriff"]) or
                  mb_stristr($text, mb_strtolower($_GET["suchbegriff"])) or
                  mb_stristr($dateiname, $_GET["suchbegriff"]) or
                  preg_match("/" . $preg . "/is", $text)) &&
                $gefunden <= $suchergebnisse_max
              ) {
                $gefunden++;
                $hyperlink = $datei;
                $start = mb_strpos(mb_strtolower($text), mb_strtolower($_GET["suchbegriff"])) - $textausschnitt;

                if ($start < 0) $start = 0;
                $ende = mb_strlen($_GET["suchbegriff"]) + $textausschnitt * 2;
                $textteil = mb_substr($text, $start, $ende);
                $textteil = (mb_strlen($textteil) < $textausschnitt ? mb_substr($text, mb_strpos($text, $_GET["suchbegriff"]), $textausschnitt) : $textteil);
                $treffer = mb_substr_count(mb_strtolower($text), mb_strtolower($_GET["suchbegriff"]));
                $worte = preg_split('/[\s]+/', $_GET["suchbegriff"]);

                foreach ($worte as $wort) {
                  $textteil = preg_replace('/(' . $wort . ')/i', "<mark>$1</mark>", $textteil);
                  $titel = preg_replace('/(' . $wort . ')/i', "<mark>$1</mark>", $titel);
                }

                $suchergebnisse[($sortierung_dateiaenderung == "yes" ? date("Y-m-d h:i:s", filemtime($datei)) : sprintf("%02s", $treffer) . sprintf("%02s", $gefunden))] =
                  '<li><a href="' . $datei . '" target="' . $target . '">' . $titel . '</a> <small>(' . ($treffer == 0 ? 1 : $treffer) . 'x)</small>' . $meta . '<br><samp>&hellip; ' . $textteil . ' &hellip;</samp>' .
                  ($datei_info == "yes" ? '<br><var>Erstellt: ' . date("d.m.Y h:i", filemtime($datei)) . ' - ' . number_format((filesize($datei) / 1024), 1, ",", ".") . ' KB' . ($pfad_info == "yes" ? ' - ' . $url . $datei : '') . '</var>' : '') .
                  '</li>';
              }
            }
          }
        }
      }

      // Sort results by number of hits or last file change
      krsort($suchergebnisse);
      $suchergebnisse = implode($suchergebnisse);

      // Save the result of the search
      if ($sucheingabe_speichern == "yes") {
        $fh = fopen($suchdatei, "a+");
        fputs($fh, date("d.m.Y H:i") . '|' . htmlspecialchars($_GET["suchbegriff"], ENT_HTML5, 'UTF-8') .  '|' . $gefunden . "\n");
        fclose($fh);
      }

      // No matches found
      if ($gefunden == 0) {
        echo '<p>No matches to the search term you entered were found.</p>';

        // View tips
        if ($tipps_anzeigen == "yes") {
          echo '<p>Tip: Observe the spelling';

          if (strtolower($_GET["suchbegriff"]) != $_GET["suchbegriff"]) {
            echo ', use only lowercase letters';
          }

          if (
            isset($pregCount) &&
            $pregCount > 1
          ) {
            echo ' or use a single search term';
          }
          echo '!</p>';
        }
      } else {

        // Automatic forwarding
        if (
          $auto_weiterleitung == "yes" &&
          $gefunden == 1
        ) {
          exit('<h4>Loading file ...</h4>
             <p><a href="' . $hyperlink . '">View found file</a></p>
            <script>window.location.href="' . $hyperlink . '";</script>
            <meta http-equiv="refresh" content="0; URL=' . $hyperlink . '">');
        }

        // Output of search results
        echo '<p>' . $gefunden . ' ' . ($gefunden == 1 ? 'file was' : 'files were') . ' found:</p>' .
          '<ol>' . $suchergebnisse . '</ol>';
      }

      // View file info
      if ($status_info == "yes") {
        printf('<p>Searched directories: ' . count($verzeichnisse) . ' - Files: ' . $anzahl_dateien . ' (in %.2f Seconds)</p>', microtime(true) - $startzeit);
      }
    }

    // Length of search term too short or too long
    else {
      echo '<p>' . (mb_strlen($_GET["suchbegriff"]) < $suchbegriff_min ?
        'At least ' . $suchbegriff_min . ' characters are required' :
        'A maximum of ' . $suchbegriff_max . ' characters are allowed') . '!</p>';
    }
  }
  ?>

</body>

</html>