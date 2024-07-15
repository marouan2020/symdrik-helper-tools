<?php

namespace Drupal\symdrik_helper_tools;

use Drupal\taxonomy\Entity\Term;

/**
 * Class TaxonomyTermHelper
 *
 * @package Drupal\symdrik_helper_tools
 */
class TaxonomyTermHelper {

  /**
   * Get term by taxonomy term name.
   *
   * @param null $name
   *   Label name of term.
   * @param null $vid
   *   Machine name of taxonomy.
   *
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null
   *   Result can be Entity taxonomy term or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTermyName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term : NULL;
  }

  /**
   * Insert new term.
   *
   * @param array $properties
   *   Properties of taxonomy (type, title, status)
   * @param array $fields
   *   Fields of taxonomy term.
   *
   * @return \Drupal\taxonomy\Entity\Term $term
   *   Term object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\taxonomy\Entity\Term
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function insertTaxonomyTerm($properties, $fields = []) {
    $term = Term::create($properties);
    foreach ($fields as $fieldName => $fieldValue) {
      $term->set($fieldName, $fieldValue);
    }
    $term->save();
    return $term;
  }

  /**
   * Update existing term.
   *
   * @param \Drupal\taxonomy\Entity\Term $term
   *   Term object.
   * @param array $properties
   *   Properties of taxonomy (type, title, status)
   * @param array $fields
   *   Fields of taxonomy term.
   *
   * @return \Drupal\taxonomy\Entity\Term $term
   *   Term object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\taxonomy\Entity\Term
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateTaxonomyTerm(Term $term, $properties, $fields = []) {
    foreach ($properties as $propertyName => $propertyValue) {
      $term->set($propertyName, $propertyValue);
    }
    foreach ($fields as $fieldName => $fieldValue) {
      $term->set($fieldName, $fieldValue);
    }
    $term->save();
    return $term;
  }

  /**
   * Getting terms by specific fields.
   *
   * @param string $vid
   *   Taxonomy machine name.
   * @param array $fields
   *   Fields of taxonomy term.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|null
   *   Result can be term object or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTaxonomyTermsByFields($vid, $fields = []) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', $vid);
    foreach ($fields as $fieldName => $fieldValue) {
      $query->condition($fieldName, $fieldValue);
    }
    $tids = $query->execute();
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadMultiple($tids);
    return !empty($terms) ? $terms : NULL;
  }

  /**
   * Getting term by specific fields.
   *
   * @param string $vid
   *   Taxonomy machine name.
   * @param array $fields
   *   Fields of taxonomy term.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|null
   *   Result can be term object or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTaxonomyTermByFields($vid, $fields = []) {
    $terms = $this->getTaxonomyTermsByFields($vid, $fields);
    if ($terms != NULL) {
      $term = reset($terms);
    }
    return !empty($term) ? $term : NULL;
  }
}
