<?php

namespace Drupal\personalization;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * An implementation of PersonalizationIPServiceInterface.
 */
class PersonalizationIpService implements PersonalizationIpServiceInterface {

  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a new Request.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RequestStack $request_stack) {
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function mapIpAddress($ip = NULL) {
    if (is_null($ip)) {
      $ip = $this->getIpAddress();
    }
    return $this->convertIpToCoordinates($ip);
  }

  /**
   * {@inheritdoc}
   */
  public function getIpAddress() {
    return $this->request->getClientIp();
  }

  /**
   * Convert an Ip Address to Lat / Long coordinates
   *
   * @param string $ip
   *   The Ip address to convert
   * @return string $coordinates
   *   The latitude / longitude coordinates for that Ip address
   */
  private function convertIpToCoordinates($ip) {
    return '39.7392° N, 104.9903° W';
  }

}
