<?php

declare(strict_types=1);

namespace Drupal\pf_notifications\Service;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Push notifications base interface constructor.
 */
interface BaseInterface {

  /**
   * Currently designed just for this DANSE module.
   *
   * @var string
   */
  public const DANSE_MODULE = 'content';

  /**
   * Id of subscription permission generated by REST api.
   *
   * @var string
   */
  public const REST_PERMISSION = 'restful post pf_notifications_subscription';

  /**
   * VAPID keys missing error line.
   *
   * @var string
   */
  public const VAPID_ERROR = 'The Notification service is not correctly set. VAPID <em>public key</em> and/or <em>private key</em> are not set at the config form.';

  /**
   * Subscription data container key.
   *
   * @var string
   */
  public const PROPERTY = 'pf_notifications';

  /**
   * Test notification id.
   *
   * @var string
   */
  public const TEST_ID = 'test-notification';

  /**
   * A list of views, such as page for "Manage subscriptions".
   *
   * Invalidate cache for these, upon subscription actions.
   *
   * @var array<string>
   */
  public const CACHED_VIEWS = [
    'views.view.push_subscriptions',
  ];

  /**
   * Route name of "Manage subscriptions" views page.
   *
   * @var string
   */
  public const REDIRECT_ROUTE = 'view.push_subscriptions.page_1';

  /**
   * Get current user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   Account proxy interface.
   */
  public function getCurrentUser(): AccountProxyInterface;

  /**
   * Get user.data service.
   *
   * @return \Drupal\user\UserDataInterface
   *   User data service.
   */
  public function getUserData(): UserDataInterface;

  /**
   * Get logger channel.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   This module's channel interface.
   */
  public function getLogger(): LoggerChannelInterface;

  /**
   * Get our config data.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Config object, pf_notifications.settings.
   */
  public function getConfig(): ImmutableConfig;

  /**
   * Get request stack.
   *
   * @return \Symfony\Component\HttpFoundation\RequestStack
   *   Request stack instance.
   */
  public function getRequest(): RequestStack;

  /**
   * Fetch VAPID keys from its table.
   *
   * @return array<string, string>
   *   Associative array with public and private key.
   */
  public function getKeys(): array;

  /**
   * Define all data related to entity, for the push subscription record.
   *
   * @param int $uid
   *   Subscription user id.
   * @param string $name
   *   Subscription user name.
   * @param \Drupal\Core\Entity\ContentEntityInterface<object> $entity
   *   Subscribed entity (e.q. node or comment).
   *
   * @return array<string, int|string>
   *   Push subscription entity related data.
   */
  public function getEntityData(int $uid, string $name, ContentEntityInterface $entity): array;

  /**
   * Define all data related to push subscription record.
   *
   * @param array<string, int|string> $entity_data
   *   Push subscription entity related data.
   * @param array $default_options
   *   Default options for a link.
   *
   * @return \Drupal\Core\Link
   *   Data related to push subscription record.
   */
  public function userLink(array $entity_data, array $default_options = []): Link;

  /**
   * Define all data related to push subscription record.
   *
   * @param array<string, int|string> $entity_data
   *   Push subscription entity related data.
   * @param string|null $endpoint
   *   Existing endpoint url for a subscription.
   *
   * @return array<string, \Drupal\Core\Link>
   *   Data related to push subscription record.
   */
  public function entityLinks(array $entity_data, string $endpoint = NULL): array;

  /**
   * Mark notification as seen upon licking on the notification.
   *
   * @see js/pf_notifications.service_worker.js
   */
  public function markSeen(): void;

  /**
   * Get any subscriptions, existing or default data from DANSE.
   *
   * @param int $uid
   *   Id of a user for whom subscriptions are fetched.
   * @param string|null $key
   *   DANSE unique key by default. Can be the other if some overriding.
   * @param string $module
   *   Either "danse" for default or some other if some overriding.
   *
   * @return int|array<string, array<string, mixed>>
   *   Either array with existing subscriptions or 0/1 default from DANSE.
   */
  public function getSubscriptions(int $uid, string $key = NULL, string $module = 'danse'): int|array;

  /**
   * Delete notification subscription from users_data.
   *
   * @param int $uid
   *   User id.
   * @param string $danse_key
   *   DANSE key, "name" in users_table.
   * @param string $token
   *   A unique subscription token per device/browser.
   * @param int $danse_active
   *   An original value of danse subscription, before updating with our data.
   * @param bool $test
   *   If it's a test notification found on config form.
   * @param bool $reset_cache
   *   If false, skip clearing cache for a bulk delete of all subscriptions.
   */
  public function deleteSubscription(int $uid, string $danse_key, string $token, int $danse_active, bool $test = FALSE, bool $reset_cache = TRUE): bool;

  /**
   * Delete all subscriptions data from DANSE array in users_data table.
   *
   * @todo Make this runs in a batch.
   */
  public function deleteAll(): void;

  /**
   * Clear cache upon some operations.
   *
   * @param string $type
   *   A type of entity or cache container to clear.
   */
  public function invalidateCacheTags(string $type = 'view'): void;

}