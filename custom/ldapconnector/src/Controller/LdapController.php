<?php

namespace Drupal\ldapconnector\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use GuzzleHttp\Exception\RequestException;

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
public function autocompleteEntite($string = '') {

// Obtenir la saisie utilisateur.
    $string = $request->query->get('q');

    // Récupérer toutes les entités via la fonction get_all_coll().
    $options = get_all_coll();

    // Filtrer les entités selon la saisie utilisateur.
    $matches = [];
    foreach ($options as $key => $label) {
        if (stripos($label, $string) !== FALSE) {
            $matches[] = ['value' => $label, 'label' => $label];
        }
    }

    // Retourner les résultats sous forme JSON.
    return new \Symfony\Component\HttpFoundation\JsonResponse($matches);
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
      if ($group['target_id'] == '103') {

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
/*
function get_connexion() {

    $api_url = 'https://pleiadetest.ecollectivites.fr/export/users/connexion';
    try {
      $client = \Drupal::httpClient();
      $response = $client->get($api_url, ['timeout' => 50]);
      if ($response->getStatusCode() == 200) {
        $data = json_decode($response->getBody(), TRUE);
        foreach ($data as $item) {
          $username_from_api = $item['name'];
          $timestamp = $item['access'];
          $user = user_load_by_name($username_from_api);

          if ($user) {
            if($timestamp){
                $user->set('access', $timestamp);
                $user->save();
            }
          }   
        }
      }
    }

    catch (RequestException $e) {
      \Drupal::messenger()->addError('Erreur lors de la synchronisation des utilisateurs: ' . $e->getMessage());
    }

    $url = Url::fromRoute('<front>')->toString();
    return new RedirectResponse($url);

}
*/

/**
 * Synchronise les données d'accès des utilisateurs depuis l'API.
 * 
 * @return \Symfony\Component\HttpFoundation\RedirectResponse
 */
function get_connexion() {
    $api_url = 'https://pleiadetest.ecollectivites.fr/export/users/connexion';
    
    try {
        $client = \Drupal::httpClient();
        $response = $client->get($api_url, [
            'timeout' => 50,
        ]);

        if ($response->getStatusCode() == 200) {
            // Récupérer le contenu brut
            $raw_content = $response->getBody()->getContents();
            \Drupal::logger('data_debug')->notice('Contenu brut reçu: @content', [
                '@content' => $raw_content
            ]);

            // Décoder le JSON
            $data = json_decode($raw_content, TRUE);

       // Vérifier les erreurs JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Drupal::logger('data_debug')->error('Erreur JSON: @error', [
                    '@error' => json_last_error_msg()
                ]);
                return;
            }

            // Déboguer chaque élément avant traitement
            foreach ($data as $index => $item) {
                \Drupal::logger('data_debug')->notice('Item @index brut: @item', [
                    '@index' => $index,
                    '@item' => print_r($item, TRUE)
                ]);

                if (!isset($item['access'])) {
                    \Drupal::logger('data_debug')->error('Access manquant pour item @index', [
                        '@index' => $index
                    ]);
                }

                $username_from_api = $item['name'];
                $timestamp = isset($item['access']) ? $item['access'] : 'manquant';

                // Vérifier si l'utilisateur existe
                $user = user_load_by_name($username_from_api);
                if ($user && isset($item['access'])) {
                    $ancien_timestamp = $user->get('access')->value;
                    $user->set('access', $item['access']);
                    $user->save();
                    
                    \Drupal::logger('data_debug')->notice(
                        'Mise à jour utilisateur @user: ancien=@old, nouveau=@new', 
                        [
                            '@user' => $username_from_api,
                            '@old' => $ancien_timestamp,
                            '@new' => $item['access']
                        ]
                    );
                }
            }
        }
    } catch (RequestException $e) {
        \Drupal::logger('data_debug')->error('Erreur de requête: @error', [
            '@error' => $e->getMessage()
        ]);
    }
    return new RedirectResponse(Url::fromRoute('<front>')->toString());
}

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
