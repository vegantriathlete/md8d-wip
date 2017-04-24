<?php

/******************************************************************************
 **                                                                          **
 ** Remember to include the domain as a query argument!                      **
 **                                                                          **
 ******************************************************************************/
$domain = $_GET['domain'];

/******************************************************************************
 **                                                                          **
 ** Note that we are appending the _format query argument. This tells the    **
 ** Serialization module how to serialize our response for us. We don't have **
 ** to worry about doing the serialization ourselves!                        **
 **                                                                          **
 ******************************************************************************/
$rest_uri = 'http://' . $domain . '/wea/actions?_format=json';

// Execute a cURL call
$curlExecutor = new curlExecutor($rest_uri);
$results = $curlExecutor->getRecords();
$decoded_results = json_decode($results);

/******************************************************************************
 **                                                                          **
 ** We are going to display the results of our cURL request as a simple      **
 ** HTML page.                                                               **
 **                                                                          **
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>"GET"ing WEA list from custom code</title>
  </head>
  <body>
    <h1>This is the data received.</h1>
<?php

/******************************************************************************
 **                                                                          **
 ** Note how intuitive our fields are. We get to pick the field names when   **
 ** we build our object that gets turned into the response.                  **
 **                                                                          **
 ** We haven't bothered to do any type of error checking. So, we may get     **
 ** various Notices, Warnings or Errors depending on what item we've         **
 ** requested.                                                               **
 **                                                                          **
 ******************************************************************************/
foreach ($decoded_results as $decoded_result) {
  echo "<ul>";
  echo "<li>ID: " . $decoded_result->id . "</li>";
  echo "<li>Title: " . $decoded_result->title . "</li>";
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
