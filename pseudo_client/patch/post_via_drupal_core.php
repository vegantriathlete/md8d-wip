<?php

// Execute a cURL call

// Just testing stuff with https://jsonplaceholder.typicode.com/
// @see: https://github.com/typicode/jsonplaceholder#creating-a-resource
$api_endpoint = 'http://jsonplaceholder.typicode.com/posts';
$post_fields = array(
  'data' => array(
    'title' => 'foo',
    'body' => 'bar',
    'userId' => 1,
  ),
);
$curlExecutor = new curlExecutor($api_endpoint, $post_fields);
$result = $curlExecutor->postFields();
var_dump($result);
$decoded_result = json_decode($result);
var_dump($decoded_result);

// Yippee! We successfully completed the script :)
exit(0);

/**
 * cURL Executor
 */
class curlExecutor {
  public $endPoint;
  public $postFields;

  public function __construct(string $api_endpoint, array $post_fields) {
    $this->endPoint = $api_endpoint;
    $this->postFields = $post_fields;
  }

  public function postFields() {
    // Setup the cURL request.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->endPoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
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
