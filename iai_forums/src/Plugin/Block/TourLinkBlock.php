<?php

namespace Drupal\iai_forums\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a block with a link to start the tour.
 *
 * @Block(
 *   id = "iai_forums_tour_link",
 *   admin_label = @Translation("Link for the Forums tour"),
 *   category = @Translation("Links")
 * )
 */
class TourLinkBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $url = Url::fromUserInput('/forum?tour=1');
    $build['tour_link']= [
      '#type' => 'markup',
      '#markup' => Link::fromTextAndUrl(t('Take the tour!'), $url)->toString(),
    ];
    return $build;
  }

}
