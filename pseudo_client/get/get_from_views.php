<?php

// Execute a cURL call

$rest_uri = 'http://testmd8ddev/views/wea';
$curlExecutor = new curlExecutor($rest_uri);
$results = $curlExecutor->getRecords();
$decoded_results = json_decode($results);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"GET"ing data from Views</title>
  </head>
  <body>
    <h1>This is the data received.</h1>
<?php
foreach ($decoded_results as $decoded_result) {
  echo "<ul>";
  echo "<li>Title: " . $decoded_result->wea_title . "</li>";
  echo "<li>Coordinates: " . $decoded_result->wea_coordinates . "</li>";
  echo "<li>Description: " . $decoded_result->wea_description . "</li>";
  echo "</ul>";
}
?>
  </body>
</html>
<?php
// Yippee! We successfully completed the script :)
exit(0);

/**
 * cURL Executor
 */
class curlExecutor {
  public $restURI;

  public function __construct(string $rest_uri) {
    $this->restURI = $rest_uri;
  }

  public function getRecords() {
    // Setup the cURL request.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->restURI);
    //curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    //$headers = array(
    //  'Accept: application/xml'
    //);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    // Report any errors
    if ($error = curl_error($ch)) {
      $error_message = "cURL error at $this->endPoint: $error";
      throw new Exception($error_message);
    }
    curl_close($ch);
    return $result;
  }

}
