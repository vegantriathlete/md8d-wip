<?php

// Execute a cURL call

$rest_uri = 'http://testmd8ddev/node/22?_format=json';
$curlExecutor = new curlExecutor($rest_uri);
$results = $curlExecutor->getRecords();
$decoded_results = json_decode($results);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"GET"ing data from Drupal core</title>
  </head>
  <body>
    <h1>This is the data received.</h1>
<?php
  echo "<ul>";
  echo "<li>Title: " . $decoded_results->title[0]->value . "</li>";
  echo "<li>Coordinates: " . $decoded_results->field_wea_coordinates[0]->value . "</li>";
  echo "<li>Description: " . $decoded_results->field_wea_description[0]->value . "</li>";
  echo "</ul>";
  echo "<p><pre>" . print_r($decoded_results, 1) . "</pre></p>";
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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
