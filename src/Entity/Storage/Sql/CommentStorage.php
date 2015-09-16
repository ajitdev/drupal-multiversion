<?php

/**
 * @file
 * Contains Drupal\multiversion\Entity\Storage\Sql\CommentStorage.
 */

namespace Drupal\multiversion\Entity\Storage\Sql;

use Drupal\multiversion\Entity\Storage\ContentEntityStorageInterface;
use Drupal\multiversion\Entity\Storage\ContentEntityStorageTrait;
use Drupal\comment\CommentStorage as CoreCommentStorage;

/**
 * Defines the controller class for comments.
 */
class CommentStorage extends CoreCommentStorage implements ContentEntityStorageInterface {

  use ContentEntityStorageTrait {
    // @todo Rename to doDelete for consistency with other storage handlers.
    delete as deleteEntities;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    // Delete received comments and all their children.
    if (!empty($entities)) {
      $child_cids = $this->getChildCids($entities);
      while (!empty($child_cids)) {
        $child_entities = $this->loadMultiple($child_cids);
        $entities = $entities + $child_entities;
        $child_cids = $this->getChildCids($child_entities);
      }
    }
    // Sort the array with entities descending to delete children before their
    // parents.
    krsort($entities);
    $this->deleteEntities($entities);
  }

}
