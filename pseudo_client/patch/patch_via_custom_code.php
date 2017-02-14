<?php

// Execute a cURL call

$rest_uri = 'http://testmd8ddev/wea/actions/42?_format=json';
$timestamp = date('F j, Y g:i a');
$post_fields = array(
  'title' => 'My custom PATCHed WEA - ' . $timestamp,
  'description' => 'I successfully updated this with a PATCH operation at ' . $timestamp . '! And I did it with my custom REST URI, to boot.',
);
$tokenRetriever = new tokenRetriever();
$token = $tokenRetriever->getToken();
$curlExecutor = new curlExecutor($rest_uri, $token, $post_fields);
$result = $curlExecutor->postFields();
$decoded_result = json_decode($result);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"PATCH"ing data via custom code</title>
  </head>
  <body>
    <h1>Here is some lovely information for you!</h1>
    <h2>This is the data we sent.</h2>
<?php
echo "<pre>" . print_r($post_fields, 1) . "</pre>";
?>
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
  public $postFields;
  public function __construct(string $rest_uri, string $token, array $post_fields) {
    $this->restURI = $rest_uri;
    $this->token = $token;
    $this->postFields = $post_fields;
  }

  public function postFields() {
    // Setup the cURL request.
    $data = json_encode($this->postFields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->restURI);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'rest_user:rest_user');
    // We need to set the header at the end because PHP cURL sets it to
    // 'Content-type: application/x-www-form-urlencoded'
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-type: application/json',
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
