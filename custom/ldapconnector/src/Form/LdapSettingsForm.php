<?php

namespace Drupal\ldapconnector\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure LDAP settings for this site.
 */
class LdapSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ldapconnector_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ldapconnector.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ldapconnector.settings');
  
    $form['ldap_server'] = [
      '#type' => 'textfield',
      '#title' => $this->t('LDAP Server'),
      '#default_value' => $config->get('ldap_server'),
    ];
  
    $form['ldap_port'] = [
      '#type' => 'number',
      '#title' => $this->t('LDAP Port'),
      '#default_value' => $config->get('ldap_port', 389), // 389 is the default LDAP port
    ];
  
    $form['ldap_bind_dn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bind DN'),
      '#default_value' => $config->get('ldap_bind_dn'),
    ];
  
    $form['ldap_bind_pass'] = [
      '#type' => 'password',
      '#title' => $this->t('Bind Password'),
      '#default_value' => $config->get('ldap_bind_pass'),
    ];
  
    $form['ldap_base_dn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base DN for searches'),
      '#default_value' => $config->get('ldap_base_dn'),
    ];
  
    $form['ou_for_group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ou Pour les groupes'),
      '#default_value' => $config->get('ou_for_group', FALSE),
    ];
  
    $form['ou_for_people'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ou Pour les utilisateurs'),
      '#default_value' => $config->get('ou_for_people', FALSE),
    ];
    $form['ou_for_apps'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ou Pour les applicatifs'),
      '#default_value' => $config->get('ou_for_apps', FALSE),
    ];
    $form['pastell_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Url Pastell pour appels api'),
      '#default_value' => $config->get('pastell_url', FALSE),
    ];
    $form['pastell_user'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Id user api pastell'),
      '#default_value' => $config->get('pastell_user', FALSE),
    ];
    $form['pastell_password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password user api Pastell'),
      '#default_value' => $config->get('pastell_password', FALSE),
    ];
    return parent::buildForm($form, $form_state);
  }
  
  // ...
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ldapconnector.settings')
      ->set('ldap_server', $form_state->getValue('ldap_server'))
      ->set('ldap_port', $form_state->getValue('ldap_port'))
      ->set('ldap_bind_dn', $form_state->getValue('ldap_bind_dn'))
      ->set('ldap_bind_pass', $form_state->getValue('ldap_bind_pass')) // This is not ideal for security. See note below.
      ->set('ldap_base_dn', $form_state->getValue('ldap_base_dn'))
      ->set('ou_for_group', $form_state->getValue('ou_for_group'))
      ->set('ou_for_people', $form_state->getValue('ou_for_people'))
      ->set('ou_for_apps', $form_state->getValue('ou_for_apps'))
      ->set('pastell_url', $form_state->getValue('pastell_url'))
	->set('pastell_user', $form_state->getValue('pastell_user'))
	->set('pastell_password', $form_state->getValue('pastell_password'))
      ->save();
  
    parent::submitForm($form, $form_state);
  }

}
