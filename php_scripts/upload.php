<?PHP
  if(!empty($_FILES['uploaded_file']))
  {
    // Pfad für Uploads
    $path = "/home/rex/fi3/im16/s76042/public_html/uploads/"; // C:/xampp/htdocs/uploads/
    // Anhängen des Dateinamens
    $path = $path . basename( $_FILES['uploaded_file']['name']);
    // schiebt Datei in den festpgelegten Pfad und überprüft ob es funktioniert
    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
      echo "The file ".  basename( $_FILES['uploaded_file']['name']).
      " has been uploaded";
    } else{
        echo "There was an error uploading the file, please try again!";
    }
  }
?>
