<?php

namespace Drupal\user_location;

/**
 * Defines an interface for municipality provider service.
 */
interface MunicipalityProviderInterface {

  /**
   * Fetches municipalities.
   *
   * @return array
   *
   * @throws \Drupal\user_location\Exception\MunicipalityProviderException
   */
  public function fetch();

}
