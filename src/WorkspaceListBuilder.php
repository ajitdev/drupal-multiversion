<?php

/**
 * @file
 * Contains \Drupal\multiversion\WorkspaceListBuilder.
 */

namespace Drupal\multiversion;

use Drupal\Core\Entity\EntityInterface;
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
    $header['uid'] = t('Owner');
    $header['type'] = t('Type');
    $header['status'] = t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label() . ' (' . $entity->getMachineName() . ')';
    $row['owner'] = $entity->getOwner()->getDisplayname();
    $type = $entity->get('type')->first()->entity;
    $row['type'] = $type ? $type->label() : '';
    $active_workspace = $entity->getActiveWorkspaceId();
    $row['status'] = $active_workspace && $active_workspace[0] == $entity->id() ? 'Active' : 'Inactive';
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['edit'])) {
      $operations['edit']['query']['destination'] = $entity->url('collection');
    }

    $active_workspace = $entity->getActiveWorkspaceId();
    if (!$active_workspace || $entity->id() != $active_workspace[0]) {
      $operations['activate'] = array(
        'title' => $this->t('Set Active'),
        'weight' => 20,
        'url' => $entity->urlInfo('activate-form', ['query' => ['destination' => $entity->url('collection')]]),
      );
    }

    return $operations;
  }

}
