<?php
/**
 * @file
 * Contains \Drupal\attendance\Form\AttendanceViewForm.
 */

namespace Drupal\attendance\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides route responses for the attendance module.
 */
class AttendanceViewForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'attendanceview_form';
  }
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $user;
    $user=\Drupal::currentUser()->id();
    $header = [
      t('Date'),
      t('Check In'),
      t('Check Out'),
      t('Working Hour'),
    ];
    $query = db_select('attendance')
    ->fields('attendance', ['event_id', 'userid','check_in','check_out', 'status'])
    ->condition('userid', $user)
    ->condition('event_id', 0, '>')
    ->orderBy('check_in', 'DESC')
    ->execute();
    $results = $query->fetchAll();
    foreach ($results as $record) {
      $check_in_time=$record->check_in;
      $check_out_time=$record->check_out;
      $date_1=getdate($check_in_time);
      $date_2=getdate($check_out_time);
  
      if($date_1['yday']==$date_2['yday']) {
        $seconds_differ=$date_2['hours']*60*60+$date_2['minutes']*60+$date_2['seconds']-$date_1['hours']*60*60-$date_1['minutes']*60-$date_1['seconds'];
        $hours=(int)($seconds_differ/3600);
        $minutes=(int)(($seconds_differ-$hours*60*60)/60);
        $seconds=$seconds_differ-$hours*60*60-$minutes*60;
        $date=$date_1['weekday'].','.' '.$date_1['mday'].' '.$date_1['month'].','.' '.$date_1['year'];
        $in_time=$date_1['hours'].':'.$date_1['minutes'].':'.$date_1['seconds'];
        if($check_out_time!=0) {
          $out_time= $date_2['hours'].':'.$date_2['minutes'].':'.$date_2['seconds'];  
        }
        else {
          $out_time= 'Not Checked Out';
        }
        if($record->status!='In') {
          $working_hour=$hours.' hours '.$minutes.' minutes '.$seconds.' seconds';
        }
        else {
          $working_hour='NA';
        }
      }
      else if($date_1['yday']!=$date_2['yday']){
        $date=$date_1['weekday'].','.' '.$date_1['mday'].' '.$date_1['month'].','.' '.$date_1['year'];
        $in_time=$date_1['hours'].':'.$date_1['minutes'].':'.$date_1['seconds'];
        if($check_out_time!=0) {
          $out_time= '00'.':'.'00'  .':'.'00';  
        }
        else {
          $out_time= 'Not Checked Out';
        }
        if($record->status!='In') {
          $working_hour=(23-$date_1['hours']).' hours '.(59-$date_1['minutes']).' minutes '.(59-$date_1['seconds']).' seconds';
        }
        else {
          $working_hour='NA';
        }
      }
      $rows[] =[
        'data'=>[
          t($date),
          t($in_time),
          t($out_time),
          t($working_hour),
        ],
      ];
    }

    $form['attendance_table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#empty' => $this->t('No users found.'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}