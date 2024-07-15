<?php
namespace Drupal\symdrik_helper_tools;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Class ParagraphHelper.
 *
 * @package Drupal\accor_import
 */
class ParagraphHelper {

  /**
   * Insert new paragraph.
   *
   * @param $machineName
   *   Machine name.
   * @param array $fields
   *   Fields and values.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph|null
   *   Result can be object paragraphs or null.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function insertParagraph($machineName, $fields = []) {
    $paragraph = Paragraph::create(['type' => $machineName]);
    $paragraph->isNew();
    $paragraph->save();
    return $this->updateParagraph($paragraph->id(), $fields);
  }

  /**
   * Update paragraph.
   *
   * @param $targetId
   *   Id of a paragraph
   * @param $fields
   *   Fields and values of paragraph.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph|null
   *   Result can be object paragraph or null.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateParagraph($targetId, $fields) {
    $paragraph = Paragraph::load($targetId);
    foreach ($fields as $fieldName => $value) {
      $paragraph->set($fieldName, $value);
    }
    $paragraph->save();
    return $paragraph;
  }

  /**
   * Retrieve a paragraph by fields.
   *
   * @param $type
   *   Machine name of paragraph.
   *
   * @param $fields
   *   Fields used to fetch a paragraph.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Resultat can be Object paragraph or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getParagraphByFields($type, $fields) {
    $query = \Drupal::entityQuery('paragraph')
      ->condition('type', $type);
    foreach ($fields as $fieldName => $fieldValue) {
      $query->condition($fieldName, $fieldValue);
    }
    $nids = $query->execute();
    $paragraph = \Drupal::entityTypeManager()
      ->getStorage('paragraph')
      ->loadMultiple($nids);
    $paragraph = reset($paragraph);
    return !empty($paragraph) ? $paragraph : NULL;
  }

  /**
   * Retrieve a paragraph by id.
   *
   * @param $targetId
   *   Id a paragraph.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph|null
   *  Result can be Entity paragraph or null.
   */
  public function getParagraphById($targetId) {
    $paragraph = Paragraph::load($targetId);
    return $paragraph;
  }
}