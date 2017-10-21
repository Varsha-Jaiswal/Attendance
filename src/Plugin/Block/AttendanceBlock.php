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
 *   id = "attendance_block",
 *   admin_label = @Translation("Mark Attendance"),
 *   category = @Translation("Custom attendance  block ")
 * )
 */
class AttendanceBlock extends BlockBase{
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\attendance\Form\AttendanceForm');
    return $form;
  } 
}
