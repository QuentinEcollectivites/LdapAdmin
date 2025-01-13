<?php

namespace Drupal\ldapimporter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
class LDAPImporterSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ldapimporter_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ldapimporter.settings'];
  }

  // Add form elements and submit handlers as needed.
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
  
    $form['test_get_users'] = [
      '#type' => 'submit',
      '#value' => $this->t('Tester la récupération des users'),
    ];
    $form['test_get_groups'] = [
        '#type' => 'submit',
        '#value' => $this->t('Tester la récupération des groupes/applicatifs'),
      ];
    $form['synchro_users'] = [
        '#type' => 'submit',
        '#value' => $this->t('Lancer la récupération des users'),
    ];
    $form['synchro_groups'] = [
        '#type' => 'submit',
        '#value' => $this->t('Lancer la récupération des groups'),
    ];
    $form['import_pastell_entite'] = [
      '#type' => 'submit',
      '#value' => $this->t('Importer les entités pastell'),
    ];
    $form['import_role_user_pastell'] = [
      '#type' => 'submit',
      '#value' => $this->t('Importer les roles pastell'),
    ];
    $form['csv_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Importer des utilisateurs'),
      '#description' => $this->t('Choisissez un fichier CSV à importer.'),
    ];
    
  $collectivites = $this->getAllCollectivites();
  $form['collectivite'] = [
    '#type' => 'select',
    '#title' => $this->t('Collectivité'),
    '#options' => $collectivites,
    '#empty_option' => $this->t('- Sélectionner une collectivité -'),
    '#description' => $this->t('Si la collectivité n\'existe pas, la créé avant d\'importer les utilisateurs.'),
    
  ];
    // Autres éléments de formulaire ici.
  
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Vérifiez quel bouton a été cliqué en utilisant $form_state.
    $triggering_button = $form_state->getTriggeringElement()['#id'];
    
    if ($triggering_button == 'edit-test-get-users') {
        test_get_user_from_ldap();
    } 
    elseif ($triggering_button == 'edit-import-pastell-entite') {
      import_entite_from_pastell();
    }
    elseif ($triggering_button == 'edit-import-role-user-pastell') {
      import_role_user_pastell();
    }
    elseif ($triggering_button == 'edit-synchro-users') {   
        synchro_user_from_ldap();
    }
    elseif ($triggering_button == 'edit-test-get-groups') {
        test_get_groups_from_ldap();
    } 
    elseif ($triggering_button == 'edit-synchro-groups') {
        synchro_groups_from_ldap();
    }
    // Traitement du fichier CSV uploadé.
    $validators = [
      'file_validate_extensions' => ['csv'],
    ];
    $file = file_save_upload('csv_upload', $validators, FALSE, 0);
    
    if ($file) {
      // Le fichier a été téléchargé avec succès, maintenant nous pouvons le traiter.
      $file->setPermanent();
      $file->save();

      $csv_file_path = $file->getFileUri();
      // Récupérer le contenu du fichier CSV.
      ImportUserFromLDAP($csv_file_path, $form_state->getValue('collectivite') );

    }

  }
  private function getAllCollectivites() {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('collectivite');
    $options = [];
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }
    return $options;
  }
}