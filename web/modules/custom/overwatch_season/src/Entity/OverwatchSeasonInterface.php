<?php

namespace Drupal\overwatch_season\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Overwatch season entities.
 *
 * @ingroup overwatch_season
 */
interface OverwatchSeasonInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Overwatch season name.
   *
   * @return string
   *   Name of the Overwatch season.
   */
  public function getName();

  /**
   * Sets the Overwatch season name.
   *
   * @param string $name
   *   The Overwatch season name.
   *
   * @return \Drupal\overwatch_season\Entity\OverwatchSeasonInterface
   *   The called Overwatch season entity.
   */
  public function setName($name);

  /**
   * Gets the Overwatch season creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Overwatch season.
   */
  public function getCreatedTime();

  /**
   * Sets the Overwatch season creation timestamp.
   *
   * @param int $timestamp
   *   The Overwatch season creation timestamp.
   *
   * @return \Drupal\overwatch_season\Entity\OverwatchSeasonInterface
   *   The called Overwatch season entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Overwatch season published status indicator.
   *
   * Unpublished Overwatch season are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Overwatch season is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Overwatch season.
   *
   * @param bool $published
   *   TRUE to set this Overwatch season to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\overwatch_season\Entity\OverwatchSeasonInterface
   *   The called Overwatch season entity.
   */
  public function setPublished($published);

  /**
   * Gets the Overwatch season revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Overwatch season revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\overwatch_season\Entity\OverwatchSeasonInterface
   *   The called Overwatch season entity.
   */
  public function setRevisionCreationTime($timestamp);

}
