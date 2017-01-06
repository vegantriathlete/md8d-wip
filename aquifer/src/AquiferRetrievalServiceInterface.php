<?php

namespace Drupal\aquifer;

/**
 * Defines an interface that provides information about aquifers.
 *
 * This interface defines methods to retrieve information about aquifers.
 * It is possible to find out about all known aquifers as well as to find out
 * details about a specific aquifer.
 */
interface AquiferRetrievalServiceInterface {

  /**
   * Get the name of the REST endpoint
   *
   * We wouldn't really need a method like this in an actual site. We are
   * including it here just so that we can have a way to show the argument that
   * was passed into the constructor.
   *
   * @return string
   */
  public function getRESTEndpoint();

  /**
   * Get the total number of aquifers
   *
   * @param string $region
   *   Region to which to limit the search
   *
   * @return integer
   *   The number of tracked aquifers
   */
  public function getTotalAquifers($region = 'ALL');

  /**
   * Get the names of aquifers
   *
   * We are pretending that aquifers have names that uniquely identify them.
   *
   * @param string $region
   *   Region to which to limit the search
   * @param integer $limit
   *   The number of results to return
   * @param integer $offset
   *   The amount of results to skip passed
   *
   * @return array
   *   The names of aquifers
   */
  public function getAquiferNames($region = 'ALL', $limit = -1, $offset = 0);

  /**
   * Retrieve the current data for a given aquifer
   *
   * @param string $aquifer
   *   The name of the acquifer for which the data is being retrieved
   *
   * @return array
   *   An associative array that contains the properties and their
   *   corresponding values for an aquifer. The properties are:
   *     coordinates: The latitude / longitude of the aquifer
   *     status: [ empty | critical | low | adequate | full | overflowing ]
   *     volume: measured in cubic liters
   */
  public function getAquiferData($name);
}
