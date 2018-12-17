<?php
  // Liest alle Dateien im uploads Verzeichnis aus
  $files = scandir('../uploads');
  // erstellen eines Arrays was als Antwort gesendet wird
  $response = array();
    // Füllen des Arrays mit den Datei-Informationen
  foreach($files as $file) {
    // nicht gebrauchte Pfade ignorieren
    if ($file != "." && $file != ".." && stripos($file, ".db") === false) {
      // Datei-Informationen auslesen
      $tmpFile = pathinfo("../uploads/$file");
      
        // Datei inkl. Infos dem Array hinzufügen
      $response[] = array(
        'PATH' => "uploads/$file",
        'EXTENSION' => $tmpFile['extension'],
        'BASENAME' => $tmpFile['basename'],
        'FILENAME' => $tmpFile['filename'],
        'SIZE' => filesize("../uploads/$file"),
        'CTIME' => date("F d Y H:i:s.", filectime("../uploads/$file"))
      );

      if(isset($_GET['file'])) {
        if($tmpFile['basename'] == $_GET['file']) {
          $path = '../uploads/' . $tmpFile['basename'];
          unlink($path);
          array_pop($response);
        }
      }
    }
  }

  // senden des Arrays als Antwort
  echo json_encode($response);
?>
