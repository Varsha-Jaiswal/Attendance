<?php
/**
 * @file
 * Contains \Drupal\attendance\Form\AttendanceForm.
 */
namespace Drupal\attendance\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AttendanceForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'attendance_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['submit_1'] = [
      '#type' => 'submit',
      '#value' => t('Check In'),
      '#submit' =>['::check_in_button'],
    ];
    $form['submit_2'] = [
      '#type' => 'submit',
      '#value' => t('Check Out'),
      '#submit' => ['::check_out_button'],
    ];
    return $form ;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}
  /**
   * {@inheritdoc}
   */
  function check_in_button() {
    global $user;
    global $value;
    $user=\Drupal::currentUser()->id();
    $timestamp=(int) $_SERVER['REQUEST_TIME'];
    $check_in=format_date($timestamp);
    $query = db_select('attendance')
      ->fields('attendance', ['event_id', 'userid','check_in', 'status'])
      ->condition('userid', $user)
      ->condition('event_id', 0, '>')
      ->orderBy('event_id', 'DESC')
      ->execute();
    $result=$query->fetchAll();
    $date_1=getdate($result[0]->check_in);
    $date_2=getdate($timestamp);
    if(empty($result[0]->status)) {
      db_insert('attendance')
        ->fields([
          'userid' => $user,
          'IP' => \Drupal::request()->getClientIp(),
          'check_in' => $timestamp,
          'check_out' => 0, 
          'status' => 'In',      
        ]
        )
        ->execute();
      drupal_set_message('Checked In');
    }
    else if($date_1['yday']==$date_2['yday']  ) {
      drupal_set_message('You have already checked in for the day');
    }
    else if($result[0]->status=='In') {  
      drupal_set_message('You are already Checked In');
    }
    else {
      db_insert('attendance')
      ->fields([
        'userid' =>$user,
        'IP' => \Drupal::request()->getClientIp(),
        'check_in' => $timestamp,
        'check_out' => 0, 
        'status' => 'In',      
      ]
      )
      ->execute();  
      drupal_set_message('Checked In');
    }
  }
  /**
   * {@inheritdoc}
   */
  function check_out_button($form, FormStateInterface $form_state) {
    global $user;
    $user=\Drupal::currentUser()->id();
    $timestamp=(int) $_SERVER['REQUEST_TIME'];
    $check_out=format_date($timestamp);
    $query = db_select('attendance')
      ->fields('attendance', ['event_id', 'userid', 'status'])
      ->condition('userid', $user) //Published.
      ->condition('event_id', 0, '>')
      ->orderBy('event_id', 'DESC') //Most recent first.
      ->execute();
    $result=$query->fetchAll();
    if($result[0]->status!='In') {
      drupal_set_message('Please Check In first');
    }
    else {
      db_update('attendance')
      ->fields(['check_out'=>$timestamp,'status'=>'Out'])
      ->condition('event_id', $result[0]->event_id)
      ->execute();
      drupal_set_message('Checked Out');
    }
  }
}