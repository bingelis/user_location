<?php

namespace Drupal\user_location;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\user_location\Exception\MunicipalityProviderException;
use Drupal\user_location\Form\SettingsForm;
use function GuzzleHttp\Psr7\build_query;

/**
 * Defines a 'municipality provider' service.
 */
class MunicipalityProvider implements MunicipalityProviderInterface {

  /**
   * Cache ID for municipalities.
   */
  const CACHE_ID = 'user_location_municipalities';

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * MunicipalityProvider constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $clientFactory
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   * @param \Drupal\Component\Datetime\TimeInterface $time
   */
  public function __construct(
    ClientFactory $clientFactory,
    ConfigFactoryInterface $configFactory,
    CacheBackendInterface $cacheBackend,
    TimeInterface $time
  ) {
    $this->client = $clientFactory->fromOptions();
    $this->config = $configFactory->getEditable(SettingsForm::CONFIG_NAME);
    $this->cacheBackend = $cacheBackend;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public function fetch() {
    if ($cache = $this->cacheBackend->get(static::CACHE_ID)) {
      return $cache->data;
    }

    $config = $this->config->get('ws');

    if (empty($config['service_url'])) {
      throw new MunicipalityProviderException('No service URL.');
    }

    $query = [
      'key' => $config['api_key'] ?? '',
      'limit' => $config['limit'] ?? '',
      'group' => 'municipality',
      'municipality' => '',
    ];

    $data = [];

    do {
      $response = $this->client->get($config['service_url'], [
        'query' => $query,
      ]);

      $body = Json::decode($response->getBody());

      if (empty($body['status']) || $body['status'] != 'success') {
        throw new MunicipalityProviderException('API request failure');
      }

      $municipalities = array_column($body['data'], 'municipality');
      $data = array_merge($data, $municipalities);

      if (empty($body['page'])) {
        break;
      }

      $current = $body['page']['current'] ?? 0;
      $total = $body['page']['total'] ?? 0;
      $query['page'] = ++$current;

    } while ($total && $current <= $total);

    $expire = $this->time->getCurrentTime() + $this->config->get('cache.lifetime') * 3600;
    $this->cacheBackend->set(static::CACHE_ID, $data, $expire);

    return $data;
  }

}
