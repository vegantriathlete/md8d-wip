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
    // You can learn more about Symfony's Request object by looking at
    // vendor/symfony/http-foundation/Request.php
    // relative to your Drupal root.
    // @see: http://api.symfony.com/3.1/Symfony/Component/HttpFoundation/Request.html
    // Also @see: http://symfony.com/blog/new-in-symfony-2-4-the-request-stack
    // Also @see: http://symfony.com/doc/current/components/http_foundation.html
    // Also @see: http://symfony.com/doc/current/introduction/http_fundamentals.html
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
    // Obviously, this method is hard-coded to return the same thing every
    // time. This is why we have chosen to make it a private method. In a
    // true application we would likely have a method of a service that would
    // be responsible for completing the conversion. Perhaps it would be a
    // method of this service (in other words, this very method) and perhaps it
    // would part of a separate service. In any case, we would most likely make
    // the method public. The method might do all the work on its own or the
    // method might rely on some other service, which could potentially be an
    // external one.
    return '39.7392° N, 104.9903° W';
  }

}
