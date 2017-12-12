<?php

namespace Drupal\overwatch_map\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Overwatch map entities.
 *
 * @ingroup overwatch_map
 */
interface OverwatchMapInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface {

  const ARENA = 1;

  const ASSAULT = 2;

  const ASSAULT_ESCORT = 3;

  const CAPTURE_THE_FLAG = 4;

  const CONTROL = 5;

  const DEATHMATCH = 6;

  const ESCORT = 7;

  const TEAM_DEATHMATCH = 8;

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Overwatch map name.
   *
   * @return string
   *   Name of the Overwatch map.
   */
  public function getName();

  /**
   * Sets the Overwatch map name.
   *
   * @param string $name
   *   The Overwatch map name.
   *
   * @return \Drupal\overwatch_map\Entity\OverwatchMapInterface
   *   The called Overwatch map entity.
   */
  public function setName($name);

  /**
   * Gets the Overwatch map creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Overwatch map.
   */
  public function getCreatedTime();

  /**
   * Sets the Overwatch map creation timestamp.
   *
   * @param int $timestamp
   *   The Overwatch map creation timestamp.
   *
   * @return \Drupal\overwatch_map\Entity\OverwatchMapInterface
   *   The called Overwatch map entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Overwatch map published status indicator.
   *
   * Unpublished Overwatch map are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Overwatch map is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Overwatch map.
   *
   * @param bool $published
   *   TRUE to set this Overwatch map to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\overwatch_map\Entity\OverwatchMapInterface
   *   The called Overwatch map entity.
   */
  public function setPublished($published);

  /**
   * Gets the Overwatch map revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Overwatch map revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\overwatch_map\Entity\OverwatchMapInterface
   *   The called Overwatch map entity.
   */
  public function setRevisionCreationTime($timestamp);

}
