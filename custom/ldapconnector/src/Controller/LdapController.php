<?php

namespace Drupal\ldapconnector\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;

class LdapController extends ControllerBase
{

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  public function __construct(Renderer $renderer)
  {
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('renderer')
    );
  }

  public function listEntries()
  {
    $current_user_id = \Drupal::currentUser()->id();
    $current_user = \Drupal\user\Entity\User::load($current_user_id);
    if ($current_user_id) {
      // Charger l'entité utilisateur.
      $user = User::load($current_user_id);

      // Vous pouvez également vérifier si le champ existe avant d'accéder à sa valeur.
      if ($user->hasField('field_collectivite')) {
        $field_collectivite_value = $user->get('field_collectivite')->getValue();
        if ($field_collectivite_value !== null) {
          $group_name = $field_collectivite_value[0]["target_id"];
        }
      }
    }

    $current_user_grp_applis = $current_user->get('field_groupes_applicatifs')->getValue();
    foreach ($current_user_grp_applis as $group) {
      if ($group['target_id'] == '154') {

        $users = $this->loadAllUsers();

        $header = [
          'name' => t('Nom d\'utilisateur'),
          'email' => t('Mail'),
          'last_login' => t('Dernière connexion'),
          'created' => t('Date de création'),
          'status' => t('Etat'),
          'supp' => t('Supprimer'),

        ];

        $rows = [];
        foreach ($users as $user) {
          if ($user->id() == 1) {
            // Exclure l'utilisateur avec l'ID 1.
            continue;
          }

          if ($user->hasField('field_collectivite')) {

            if ($user->get('field_collectivite') != null) {
              $collectivite = $user->get('field_collectivite')->getValue()[0]["target_id"];
            }
            // Vérifier si l'utilisateur a la même collectivité que $group_name.
            if ($collectivite == $group_name) {
              $cancel_url = Url::fromUri('internal:/user/' . $user->id() . '/cancel');
              $cancel_link = Link::fromTextAndUrl('Supprimer', $cancel_url)->toRenderable();

              // Get the date of last login.
              $last_login_timestamp = $user->getLastLoginTime();
              $last_login_date = date('d/m/Y H:i', $last_login_timestamp);

              // Get the date of account creation.
              $created_timestamp = $user->getCreatedTime();
              $created_date = date('d/m/Y H:i', $created_timestamp);

              // Add the row with the additional information.
              $rows[] = [
                  'name' => $user->getDisplayName(),
                  'email' => $user->getEmail(),
                  'last_login' => $last_login_date,
                  'created' => $created_date,
                  'status' => $user->isActive() ? t('Active') : t('Blocked'),
                  'supp' => $this->renderer->render($cancel_link),
              ];
            }
          }
        }

        $table = [
          '#theme' => 'table',
          '#header' => $header,
          '#rows' => $rows,
        ];
        $content['create_link'] = [
          '#markup' => '<a href="/admin/people/create">Créer un utilisateur</a>',
        ];
        $content['table'] = [
          '#markup' => $this->renderer->render($table),
        ];
        
        

        return $content;
        
      }
    }

    return AccessResult::allowed();
  }


  /**
   * Load all user entities.
   *
   * @return \Drupal\user\Entity\User[]
   */
  private function loadAllUsers()
  {
    $query = \Drupal::entityQuery('user')
      ->condition('status', 1); // Active users

    // Explicitly set access checking to FALSE.
    $query->accessCheck(TRUE);

    $uids = $query->execute();
    return User::loadMultiple($uids);
  }


}
