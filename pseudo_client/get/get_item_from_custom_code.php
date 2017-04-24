<?php

/******************************************************************************
 **                                                                          **
 ** Remember to include the domain as a query argument!                      **
 ** Remember to include the item number as a query argument!                 **
 **                                                                          **
 ******************************************************************************/
$domain = $_GET['domain'];
$item = $_GET['item'];

/******************************************************************************
 **                                                                          **
 ** Note that we are appending the _format query argument. This tells the    **
 ** Serialization module how to serialize our response for us. We don't have **
 ** to worry about doing the serialization ourselves!                        **
 **                                                                          **
 ** The URI we use is determined by the Annotation in our resource,          **
 ** specifically the canonical entry.                                        **
 **  uri_paths = {                                                           **
 **    "canonical" = "/wea/actions/{id}",                                    **
 **    "https://www.drupal.org/link-relations/create" = "/wea/actions"       **
 **  }                                                                       **
 **                                                                          **
 ******************************************************************************/
$rest_uri = 'http://' . $domain . '/wea/actions/' . $item . '?_format=json';

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
    <title>"GET"ing data from WEA module</title>
  </head>
  <body>
    <h1>This is the data received.</h1>
<?php

/******************************************************************************
 **                                                                          **
 ** Note how much more intuitive our fields are. There is no nesting and the **
 ** fields names are what people would guess them to be.                     **
 **                                                                          **
 ** We haven't bothered to do any type of error checking. So, we may get     **
 ** various Notices, Warnings or Errors depending on what item we've         **
 ** requested.                                                               **
 **                                                                          **
 ******************************************************************************/
  echo "<ul>";
  echo "<li>Title: " . $decoded_results->title . "</li>";
  echo "<li>Coordinates: " . $decoded_results->coordinates . "</li>";
  echo "<li>Description: " . $decoded_results->description . "</li>";
  echo "</ul>";
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
