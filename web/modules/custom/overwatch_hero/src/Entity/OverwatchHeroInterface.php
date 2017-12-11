<?php

namespace Drupal\overwatch_hero\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Overwatch hero entities.
 *
 * @ingroup overwatch_hero
 */
interface OverwatchHeroInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Overwatch hero name.
   *
   * @return string
   *   Name of the Overwatch hero.
   */
  public function getName();

  /**
   * Sets the Overwatch hero name.
   *
   * @param string $name
   *   The Overwatch hero name.
   *
   * @return \Drupal\overwatch_hero\Entity\OverwatchHeroInterface
   *   The called Overwatch hero entity.
   */
  public function setName($name);

  /**
   * Gets the Overwatch hero creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Overwatch hero.
   */
  public function getCreatedTime();

  /**
   * Sets the Overwatch hero creation timestamp.
   *
   * @param int $timestamp
   *   The Overwatch hero creation timestamp.
   *
   * @return \Drupal\overwatch_hero\Entity\OverwatchHeroInterface
   *   The called Overwatch hero entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Overwatch hero published status indicator.
   *
   * Unpublished Overwatch hero are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Overwatch hero is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Overwatch hero.
   *
   * @param bool $published
   *   TRUE to set this Overwatch hero to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\overwatch_hero\Entity\OverwatchHeroInterface
   *   The called Overwatch hero entity.
   */
  public function setPublished($published);

  /**
   * Gets the Overwatch hero revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Overwatch hero revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\overwatch_hero\Entity\OverwatchHeroInterface
   *   The called Overwatch hero entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Overwatch hero revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Overwatch hero revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\overwatch_hero\Entity\OverwatchHeroInterface
   *   The called Overwatch hero entity.
   */
  public function setRevisionUserId($uid);

}
