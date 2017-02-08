<?php

// Execute a cURL call

// Just testing stuff with https://jsonplaceholder.typicode.com/
// @see: https://github.com/typicode/jsonplaceholder#creating-a-resource
$rest_uri = 'http://jsonplaceholder.typicode.com/posts/1';
$curlExecutor = new curlExecutor($rest_uri);
$result = $curlExecutor->getFields();
$decoded_result = json_decode($result);
echo "User ID: " . $decoded_result->userId . "<br>";
echo "Post ID: " . $decoded_result->id . "<br>";
echo "Post title: " . $decoded_result->title . "<br>";
echo "Post body: " . $decoded_result->body . "<br>";

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

  public function getFields() {
    // Setup the cURL request.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->restURI);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
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
