<?php

/**
 * @file
 * Contains \Drupal\multiversion\Tests\WorkspaceSwitcherTest.
 */

namespace Drupal\multiversion\Tests;

use Drupal\multiversion\Entity\Workspace;

/**
 * Tests workspace switching functionality.
 *
 * @group multiversion
 */
class WorkspaceSwitcherTest extends MultiversionWebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['multiversion'];

  /**
   * Test that the block displays and switches workspaces.
   * Then test the admin page displays workspaces and allows switching.
   */
  public function testSwitchingWorkspaces() {
    // Login as a user who can administer workspaces.
    $user = $this->drupalCreateUser(['administer workspaces']);
    $this->drupalLogin($user);

    // Create a new workspace to switch to.
    $new_workspace = Workspace::create(['machine_name' => 'new_workspace', 'label' => 'New Workspace', 'type' => 'basic']);
    $new_workspace->save();

    // Add the block to the sidebar.
    $this->drupalPlaceBlock('multiversion_workspace_block', [
      'id' => 'workspaceswitcher',
      'region' => 'sidebar_first',
      'label' => 'Workspace switcher',
    ]);

    // Confirm the block shows on the front page.
    $this->drupalGet('<front>');
    $this->assertText('Workspace switcher', 'Block successfully being displayed on the page.');

    // Click the "Default" workspace to switch to it.
    $current_path = \Drupal::service('path.current')->getPath();
    $this->drupalPostForm($current_path, [], t('New Workspace'));

    // Ensure switching a workspace is successful.
    $this->assertText('Now viewing workspace New Workspace', 'Form button to switch workspaces completes successfully.');

    // Ensure both workspaces are listed on the collection list.
    $this->drupalGet('admin/structure/workspaces');
    $this->assertText('Default (default)', 'Default workspace found.');
    $this->assertText('New Workspace (new_workspace)', 'New Workspace found.');

    // Load the activate form and check the confirmation message.
    $this->drupalGet('admin/structure/workspaces/1/activate');
    $this->assertText('Would you like to activate the Default workspace?');

    // Submit the activate form and ensure switching a workspace is successful.
    $this->drupalPostForm('admin/structure/workspaces/1/activate', [], t('Activate'));
    $this->assertText('Now viewing workspace Default', 'Form button to switch workspaces completes successfully.');

  }
}
