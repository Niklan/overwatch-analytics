<?php

namespace Drupal\overwatch_map\Entity;

use Consolidation\OutputFormatters\Exception\UnknownFieldException;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Overwatch map entity.
 *
 * @ingroup overwatch_map
 *
 * @ContentEntityType(
 *   id = "overwatch_map",
 *   label = @Translation("Overwatch map"),
 *   handlers = {
 *     "storage" = "Drupal\overwatch_map\OverwatchMapStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\overwatch_map\OverwatchMapListBuilder",
 *     "views_data" = "Drupal\overwatch_map\Entity\OverwatchMapViewsData",
 *     "translation" = "Drupal\overwatch_map\OverwatchMapTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\overwatch_map\Form\OverwatchMapForm",
 *       "add" = "Drupal\overwatch_map\Form\OverwatchMapForm",
 *       "edit" = "Drupal\overwatch_map\Form\OverwatchMapForm",
 *       "delete" = "Drupal\overwatch_map\Form\OverwatchMapDeleteForm",
 *     },
 *     "access" = "Drupal\overwatch_map\OverwatchMapAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\overwatch_map\OverwatchMapHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "overwatch_map",
 *   data_table = "overwatch_map_field_data",
 *   revision_table = "overwatch_map_revision",
 *   revision_data_table = "overwatch_map_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer overwatch map entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/overwatch-map/{overwatch_map}",
 *     "add-form" = "/overwatch-map/add",
 *     "edit-form" = "/overwatch-map/{overwatch_map}/edit",
 *     "delete-form" = "/overwatch-map/{overwatch_map}/delete",
 *     "version-history" = "/overwatch-map/{overwatch_map}/revisions",
 *     "revision" =
 *   "/overwatch-map/{overwatch_map}/revisions/{overwatch_map_revision}/view",
 *     "revision_revert" =
 *   "/overwatch-map/{overwatch_map}/revisions/{overwatch_map_revision}/revert",
 *     "revision_delete" =
 *   "/overwatch-map/{overwatch_map}/revisions/{overwatch_map_revision}/delete",
 *     "translation_revert" =
 *   "/overwatch-map/{overwatch_map}/revisions/{overwatch_map_revision}/revert/{langcode}",
 *     "collection" = "/admin/overwatch/map",
 *   },
 *   field_ui_base_route = "overwatch_map.settings"
 * )
 */
class OverwatchMap extends RevisionableContentEntityBase implements OverwatchMapInterface {

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
      ->setDescription(t('The name of the Overwatch map entity.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
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
      ->setDescription(t('A boolean indicating whether the Overwatch map is published.'))
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

  /**
   * Check is this map competitive or not.
   *
   * @throws \Consolidation\OutputFormatters\Exception\UnknownFieldException
   *   If field with map type is missing, we can't handle it.
   *
   * @return bool
   *   TRUE is competitive, FALSE otherwise.
   */
  public function isCompetitive() {
    if (!$this->hasField('field_map_types')) {
      throw new UnknownFieldException('field_map_types');
    }

    if ($this->field_map_types->isEmpty()) {
      return FALSE;
    }

    $competitive_map_types = [
      OverwatchMapInterface::ASSAULT,
      OverwatchMapInterface::ASSAULT_ESCORT,
      OverwatchMapInterface::ESCORT,
      OverwatchMapInterface::CONTROL,
    ];

    foreach ($this->field_map_types->getValue() as $value) {
      if (in_array($value['value'], $competitive_map_types)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
