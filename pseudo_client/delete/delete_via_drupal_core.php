<?php

// Execute a cURL call

$rest_uri = 'http://testmd8ddev/node/40?_format=hal_json';
$tokenRetriever = new tokenRetriever();
$token = $tokenRetriever->getToken();
$curlExecutor = new curlExecutor($rest_uri, $token);
$result = $curlExecutor->deleteContent();
$decoded_result = json_decode($result);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"DELETE"ing data via Drupal core</title>
  </head>
  <body>
    <h1>Here is some lovely information for you!</h1>
    <h2>This is the data we received.</h2>
<?php
echo "<pre>" . print_r($decoded_result, 1) . "</pre>";
?>
  </body>
</html>
<?php
// Yippee! We successfully completed the script :)
exit(0);

/**
 * cURL Token Retriever
 */
class tokenRetriever {
  public function getToken() {
    // Setup the cURL request.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://testmd8ddev/session/token');
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

/**
 * cURL Executor
 */
class curlExecutor {
  public $restURI;
  public $token;
  public function __construct(string $rest_uri, string $token) {
    $this->restURI = $rest_uri;
    $this->token = $token;
  }

  public function deleteContent() {
    // Setup the cURL request.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->restURI);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'rest_user:rest_user');
    // We need to set the header at the end because PHP cURL sets it to
    // 'Content-type: application/x-www-form-urlencoded'
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'X-CSRF-Token: ' . $this->token
    ));
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
