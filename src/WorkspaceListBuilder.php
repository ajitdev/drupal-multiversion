<?php

/**
 * @file
 * Contains \Drupal\multiversion\WorkspaceListBuilder.
 */

namespace Drupal\multiversion;

use Drupal\multiversion\Entity\WorkspaceInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of workspace entities.
 *
 * @see \Drupal\multiversion\Entity\Workspace
 */
class WorkspaceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Workspace');
    $header['type'] = t('Type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(WorkspaceInterface $entity) {
    $row['label'] = $entity->label() . ' (' . $entity->getMachineName() . ')';
    $type = $entity->get('type')->first()->entity;
    $row['type'] = $type ? $type->label() : '';
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(WorkspaceInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['edit'])) {
      $operations['edit']['query']['destination'] = $entity->url('collection');
    }
    return $operations;
  }

}
