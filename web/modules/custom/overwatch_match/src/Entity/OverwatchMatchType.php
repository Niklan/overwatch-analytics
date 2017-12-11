<?php

namespace Drupal\overwatch_match\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Overwatch match type entity.
 *
 * @ConfigEntityType(
 *   id = "overwatch_match_type",
 *   label = @Translation("Overwatch match type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\overwatch_match\OverwatchMatchTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\overwatch_match\Form\OverwatchMatchTypeForm",
 *       "edit" = "Drupal\overwatch_match\Form\OverwatchMatchTypeForm",
 *       "delete" = "Drupal\overwatch_match\Form\OverwatchMatchTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\overwatch_match\OverwatchMatchTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "overwatch_match_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "overwatch_match",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/overwatch_match_type/{overwatch_match_type}",
 *     "add-form" = "/admin/structure/overwatch_match_type/add",
 *     "edit-form" = "/admin/structure/overwatch_match_type/{overwatch_match_type}/edit",
 *     "delete-form" = "/admin/structure/overwatch_match_type/{overwatch_match_type}/delete",
 *     "collection" = "/admin/structure/overwatch_match_type"
 *   }
 * )
 */
class OverwatchMatchType extends ConfigEntityBundleBase implements OverwatchMatchTypeInterface {

  /**
   * The Overwatch match type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Overwatch match type label.
   *
   * @var string
   */
  protected $label;

}
