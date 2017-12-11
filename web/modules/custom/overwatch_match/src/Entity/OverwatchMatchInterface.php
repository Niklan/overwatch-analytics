<?php

namespace Drupal\overwatch_match\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Overwatch match entities.
 *
 * @ingroup overwatch_match
 */
interface OverwatchMatchInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Overwatch match name.
   *
   * @return string
   *   Name of the Overwatch match.
   */
  public function getName();

  /**
   * Sets the Overwatch match name.
   *
   * @param string $name
   *   The Overwatch match name.
   *
   * @return \Drupal\overwatch_match\Entity\OverwatchMatchInterface
   *   The called Overwatch match entity.
   */
  public function setName($name);

  /**
   * Gets the Overwatch match creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Overwatch match.
   */
  public function getCreatedTime();

  /**
   * Sets the Overwatch match creation timestamp.
   *
   * @param int $timestamp
   *   The Overwatch match creation timestamp.
   *
   * @return \Drupal\overwatch_match\Entity\OverwatchMatchInterface
   *   The called Overwatch match entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Overwatch match published status indicator.
   *
   * Unpublished Overwatch match are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Overwatch match is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Overwatch match.
   *
   * @param bool $published
   *   TRUE to set this Overwatch match to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\overwatch_match\Entity\OverwatchMatchInterface
   *   The called Overwatch match entity.
   */
  public function setPublished($published);

  /**
   * Gets the Overwatch match revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Overwatch match revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\overwatch_match\Entity\OverwatchMatchInterface
   *   The called Overwatch match entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Overwatch match revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Overwatch match revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\overwatch_match\Entity\OverwatchMatchInterface
   *   The called Overwatch match entity.
   */
  public function setRevisionUserId($uid);

}
