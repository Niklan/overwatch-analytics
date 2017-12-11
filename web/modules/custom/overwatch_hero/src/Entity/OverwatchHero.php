<?php

namespace Drupal\overwatch_hero\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Overwatch hero entity.
 *
 * @ingroup overwatch_hero
 *
 * @ContentEntityType(
 *   id = "overwatch_hero",
 *   label = @Translation("Overwatch hero"),
 *   handlers = {
 *     "storage" = "Drupal\overwatch_hero\OverwatchHeroStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\overwatch_hero\OverwatchHeroListBuilder",
 *     "views_data" = "Drupal\overwatch_hero\Entity\OverwatchHeroViewsData",
 *     "translation" = "Drupal\overwatch_hero\OverwatchHeroTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\overwatch_hero\Form\OverwatchHeroForm",
 *       "add" = "Drupal\overwatch_hero\Form\OverwatchHeroForm",
 *       "edit" = "Drupal\overwatch_hero\Form\OverwatchHeroForm",
 *       "delete" = "Drupal\overwatch_hero\Form\OverwatchHeroDeleteForm",
 *     },
 *     "access" = "Drupal\overwatch_hero\OverwatchHeroAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\overwatch_hero\OverwatchHeroHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "overwatch_hero",
 *   data_table = "overwatch_hero_field_data",
 *   revision_table = "overwatch_hero_revision",
 *   revision_data_table = "overwatch_hero_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer overwatch hero entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/overwatch-hero/{overwatch_hero}",
 *     "add-form" = "/overwatch-hero/add",
 *     "edit-form" = "/overwatch-hero/{overwatch_hero}/edit",
 *     "delete-form" = "/overwatch-hero/{overwatch_hero}/delete",
 *     "version-history" = "/overwatch-hero/{overwatch_hero}/revisions",
 *     "revision" = "/overwatch-hero/{overwatch_hero}/revisions/{overwatch_hero_revision}/view",
 *     "revision_revert" = "/overwatch-hero/{overwatch_hero}/revisions/{overwatch_hero_revision}/revert",
 *     "revision_delete" = "/overwatch-hero/{overwatch_hero}/revisions/{overwatch_hero_revision}/delete",
 *     "translation_revert" = "/overwatch-hero/{overwatch_hero}/revisions/{overwatch_hero_revision}/revert/{langcode}",
 *     "collection" = "/admin/overwatch/hero",
 *   },
 *   field_ui_base_route = "overwatch_hero.settings"
 * )
 */
class OverwatchHero extends RevisionableContentEntityBase implements OverwatchHeroInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      // Default values for fields.
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // Make the anonymous user the owner.
      //$translation->setOwnerId(0);
    }

    // Make the anonymous user the owner.
    //$this->setRevisionUserId(0);
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
      ->setDescription(t('The name of the Overwatch hero.'))
      ->setRevisionable(TRUE)
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
      ->setDescription(t('A boolean indicating whether the Overwatch hero is published.'))
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
