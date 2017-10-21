<?php
/**
 * @file
 * Contains \Drupal\attendance\Plugin\Block\AttendanceBlock.
 */
namespace Drupal\attendance\Plugin\Block;

use Drupal\Core\Url;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a block.
 * @Block(
 *   id = "attendanceview_block",
 *   admin_label = @Translation("Attendance of last week"),
 *   category = @Translation("Custom attendance  view block ")
 * )
 */
class AttendanceViewBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\attendance\Form\AttendanceViewForm');
    return $form;
  }
}