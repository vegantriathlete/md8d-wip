<?php

// Execute a cURL call

$rest_uri = 'http://testmd8ddev/es/wea/actions?_format=json';
$curlExecutor = new curlExecutor($rest_uri);
$results = $curlExecutor->getRecords();
$decoded_results = json_decode($results);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"GET"ing WEA list from custom code</title>
  </head>
  <body>
    <h1>Eso si que es!</h1>
<?php
foreach ($decoded_results as $decoded_result) {
  echo "<ul>";
  echo "<li>ID: " . $decoded_result->id . "</li>";
  echo "<li>Titulo: " . $decoded_result->title . "</li>";
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
