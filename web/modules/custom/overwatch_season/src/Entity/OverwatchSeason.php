<?php

namespace Drupal\overwatch_season\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Overwatch season entity.
 *
 * @ingroup overwatch_season
 *
 * @ContentEntityType(
 *   id = "overwatch_season",
 *   label = @Translation("Overwatch season"),
 *   handlers = {
 *     "storage" = "Drupal\overwatch_season\OverwatchSeasonStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\overwatch_season\OverwatchSeasonListBuilder",
 *     "views_data" = "Drupal\overwatch_season\Entity\OverwatchSeasonViewsData",
 *     "translation" = "Drupal\overwatch_season\OverwatchSeasonTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\overwatch_season\Form\OverwatchSeasonForm",
 *       "add" = "Drupal\overwatch_season\Form\OverwatchSeasonForm",
 *       "edit" = "Drupal\overwatch_season\Form\OverwatchSeasonForm",
 *       "delete" = "Drupal\overwatch_season\Form\OverwatchSeasonDeleteForm",
 *     },
 *     "access" = "Drupal\overwatch_season\OverwatchSeasonAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\overwatch_season\OverwatchSeasonHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "overwatch_season",
 *   data_table = "overwatch_season_field_data",
 *   revision_table = "overwatch_season_revision",
 *   revision_data_table = "overwatch_season_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer overwatch season entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/overwatch-season/{overwatch_season}",
 *     "add-form" = "/overwatch-season/add",
 *     "edit-form" = "/overwatch-season/{overwatch_season}/edit",
 *     "delete-form" = "/overwatch-season/{overwatch_season}/delete",
 *     "version-history" = "/overwatch-season/{overwatch_season}/revisions",
 *     "revision" = "/overwatch-season/{overwatch_season}/revisions/{overwatch_season_revision}/view",
 *     "revision_revert" = "/overwatch-season/{overwatch_season}/revisions/{overwatch_season_revision}/revert",
 *     "revision_delete" = "/overwatch-season/{overwatch_season}/revisions/{overwatch_season_revision}/delete",
 *     "translation_revert" = "/overwatch-season/{overwatch_season}/revisions/{overwatch_season_revision}/revert/{langcode}",
 *     "collection" = "/admin/overwatch/season",
 *   },
 *   field_ui_base_route = "overwatch_season.settings"
 * )
 */
class OverwatchSeason extends RevisionableContentEntityBase implements OverwatchSeasonInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      // Default values.
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Overwatch season entity.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Overwatch season is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
