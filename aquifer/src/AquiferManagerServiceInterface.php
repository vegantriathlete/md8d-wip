<?php

namespace Drupal\aquifer;

/**
 * Defines an interface that provides functionality to manage aquifers.
 *
 * This is for managing the aquifer content type on the website. It is not
 * for managing the aquifer data that is contained in the (fictional) external
 * site that tracks aquifers world-wide. The only methods that this interface
 * will define are create, read and update. It is not intended that delete will
 * be available.
 */
interface AquiferManagerServiceInterface {

  /**
   * Create a new aquifer record
   *
   * @todo
   * It is not intended that it be possible to directly request to create an
   * aquifer. Instead, it is intended that the updateAquifer method should
   * always be called. If that method determines that the record does not exist
   * it will call this method. I'm going to have to remove this method from the
   * interface and put it in an abstract class that implements this interface.
   *
   * @param array $aquifer_data
   *   An associative array to define the aquifer record:
   *     name: The name of the aquifer
   *     coordinates: The longitude and latitude of the aquifer
   *     status: [ critical | low | adequate | full | overflowing ]
   *     volume: The current estimated volume in cubic liters
   *
   * @return string
   */
  public function createAquifer(array $aquifer_data);

  /**
   * Read (view) an aquifer
   *
   * @param string $aquifer_name
   *   The name of the aquifer to view
   *
   * @return array
   *   An associative array of the aquifer's properties:
   *     name: The name of the aquifer
   *     coordinates: The longitude and latitude of the aquifer
   *     status: [ critical | low | adequate | full | overflowing ]
   *     volume: The current estimated volume in cubic liters
   */
  public function readAquifer($aquifer_name);

  /**
   * Update an aquifer
   *
   * @param array $aquifer_data
   *   An associative array to define the aquifer record:
   *     name: The name of the aquifer
   *     coordinates: The longitude and latitude of the aquifer
   *     status: [ critical | low | adequate | full | overflowing ]
   *     volume: The current estimated volume in cubic liters
   *
   * @return array
   *   The names of aquifers
   */
  public function updateAquifer($aquifer_data);

}
