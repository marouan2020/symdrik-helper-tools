<?php
namespace  Drupal\symdrik_helper_tools;

use Drupal\node\Entity\Node;

/**
 * Class NodeHelper
 *
 * @package Drupal\symdrik_helper_tools
 */
class NodeHelper {

  /**
   * Insert a node.
   *
   * @param array $properties
   *   Exclusive fields title, status, type.
   * @param array $fields
   *   Fields content type to add it.
   *
   * @return \Drupal\node\Entity\Node $node
   *   Result is a Entity node.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function insertNode($properties, $fields = []) {
    $node = Node::create($properties);
    foreach($fields as $fieldName => $fieldValue) {
      $node->set($fieldName, $fieldValue);
    }
    //$node->enforceIsNew();
    $node->save();
    return $node;
  }

  /**
   * Retrive a node using fields.
   *
   * @param string $type
   *   Machine name of type content.
   * @param array $fields
   *   Fields and values to fetch.
   *
   * @return \Drupal\node\Entity\Node|null
   *   Result can be Entity node or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNodeByFields($type, $fields=[]) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', $type);
    foreach($fields as $fieldName => $fieldValue) {
      $query->condition($fieldName, $fieldValue);
    }
    $nids = $query->execute();
    if (empty($fields)) {
      return $nids;
    }
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    $node = reset($nodes);
    return !empty($node) ? $node : null;
  }

  /**
   * Update node.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Entity node.
   *
   * @param $properties
   *   Exclusive fields title, status, type.
   * @param array $fields
   *   Fields and values for updating.
   *
   * @return \Drupal\node\Entity\Node
   *   Result its a entity node.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateNode(Node $node, $properties, $fields = []) {
    foreach($properties as $propertyName => $propertyValue) {
      $node->set($propertyName, $propertyValue);
    }
    foreach($fields as $fieldName => $fieldValue) {
      $node->set($fieldName, $fieldValue);
    }
    $node->save();
    return $node;
  }

  /**
   * Get list nodes by hir fields.
   *
   * @param $type
   * @param array $fields
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]|int
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNodesByFields($type, array $fields = []) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', $type);
    foreach($fields as $fieldName => $fieldValue) {
      $query->condition($fieldName, $fieldValue);
    }
    $nids = $query->execute();
    if (empty($nids)) {
      return;
    }
    return Node::loadMultiple($nids);
  }

    /**
   * Get list nodes by hir fields.
   *
   * @param $type
   * @param array $fields
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]|int
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNodesByNids($type, array $nids = []) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', $type)
      ->condition("field_registration_number", $nids, "IN");
    $nids = $query->execute();
    if (empty($nids)) {
      return;
    }
    return Node::loadMultiple($nids);
  }

  /**
   * Retrieve country by isocode.
   *
   * @param $isocode
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getCountryByIsoCode($isocode) {
    $countries = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_country_iso_code' => $isocode, 'vid' => 'country']);
    return !empty($countries) ? reset($countries) : null;
  }
}
