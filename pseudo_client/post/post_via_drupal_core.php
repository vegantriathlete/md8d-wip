<?php

// Execute a cURL call

// @see: https://www.drupal.org/docs/8/core/modules/rest/3-post-for-creating-content-entities
//       As of Drupal 8.3.0 you may use /node instead of /entity/node.
$rest_uri = 'http://testmd8ddev/entity/node?_format=hal_json';
$timestamp = date('F j, Y g:i a');
// Drupal requires us to supply the '_links' key. The easiest way to figure out
// how to build the links key is to first to a "GET" on the node type you wish
// to build and examine the response. Doing this also helps you figure out how
// to build the rest of the data.
$post_fields = array(
  '_links' => array(
    'type' => array(
      'href' => 'http://testmd8ddev/rest/type/node/water_eco_action',
    ),
  ),
  'title' => array(0 => array('value' => 'My POSTed WEA - ' . $timestamp)),
  'type' => array(0 => array('target_id' => 'water_eco_action')),
  'langcode' => array(0 => array('value' => 'en')),
  'field_wea_description' => array(0 => array('value' => 'I successfully created this with a POST operation at ' . $timestamp . '!')),
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
    <title>"POST"ing data via Drupal core</title>
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
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'rest_user:rest_user');
    // We need to set the header at the end because PHP cURL sets it to
    // 'Content-type: application/x-www-form-urlencoded'
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-type: application/hal+json',
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
