<?php
/**
 * @file
 * Contains \Drupal\string_float\Form\StringFloatService.
 */

namespace Drupal\string_float\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\string_float\StringFloatService;

/**
 * StringFloat test form.
 */
class TestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'string_float_test_form';
  }

  /**
   * String to float converter class.
   *
   * @var \Drupal\string_float\StringFloatService
   */
  protected $stringFloat;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['test_string'] = array(
      '#type' => 'textfield',
      '#title' => t('Test input'),
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      $this->stringFloat = new StringFloatService($value);
      drupal_set_message($value . ': ' . $this->stringFloat->process());
    }
  }

}
