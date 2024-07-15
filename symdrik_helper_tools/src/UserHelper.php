<?php
namespace  Drupal\symdrik_helper_tools;

use Drupal\user\Entity\User;

/**
 * Class UsersHelper
 *
 * @package Drupal\symdrik_helper_tools
 */
class UserHelper {

  /**
   * @var $email
   */
  protected $email;

  /**
   * UsersHelper constructor.
   *
   * @param $email
   */
  public function __construct($email) {
    $this->email = $email;
  }

  /**
   * Create new user.
   *
   * @param array $data
   *   Fields user.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\user\Entity\User|null
   *    Result can be user object or null.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createUser(array $data) {
    if (empty($data['name'])) {
      return NULL;
    }
    if($this->isUsedUsername($data[2])) {
      $data['name'] = $this->getEmail();
    }
    $user = User::create([
      'name' => $data['name'],
      'mail' => $this->getEmail(),
      'status' => TRUE,
    ]);
    $user->enforceIsNew();
    $user->save();
    $this->updateUser($user,$data);
    return $user;
  }

  /**
   * Update User.
   *
   * @param \Drupal\user\Entity\User $user
   *   User object.
   * @param array $data
   *   Fields user to update.
   *
   * @return \Drupal\user\Entity\User
   *   Result is user object.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateUser(User $user, array $data) {
    $user->setEmail($data['email']);
    unset($data['email']);
    foreach ($data as $fieldName => $value) {
      if (!$user->hasField($fieldName)) {
        continue;
      }
      $user->set($fieldName,$value);
    }
    $user->save();
    return $user;
  }

  /**
   * Fetches a user object by email address.
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface|mixed
   *   Result can be user object or False.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getUserByEmail() {
    if (empty($this->getEmail())) {
      return FALSE;
    }
    $user = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'mail' => $this->getEmail(),
      ]);
    return $user ? reset($user) : FALSE;
  }

  /**
   * Check if email is valid and return it.
   *
   * @return string|null
   *   Result can be a valid email or null.
   */
  public function getEmail() {
    $isValidEmail = \Drupal::service('email.validator')
      ->isValid($this->email);
    if ($isValidEmail) {
      return $this->email;
    }
    return NULL;
  }

  /**
   * Check reused username.
   *
   * @param $fieldValue
   *   Value of username.
   *
   * @return bool
   *   Result true or false.
   */
  public function isUsedUsername($fieldValue) {
    $query = \Drupal::entityQuery('user');
    $query->condition('name', base64_encode($fieldValue));
    $idUser = $query->execute();
    if (!empty($idUser)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get all users or use uids for load specific users.
   *
   * @param array|NULL $uids
   *   Ids of users
   * @param bool $reset
   *   Refresh storage.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|\Drupal\user\Entity\User[]
   *    Result can be array entities users.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function usersLoad(array $uids = NULL, $reset = FALSE) {
    if ($reset) {
      \Drupal::entityTypeManager()
        ->getStorage('user')
        ->resetCache($uids);
    }
    return User::loadMultiple($uids);
  }

  /**
   * Get user using field
   *
   * @param $fields
   *   Field of user.
   *
   * @return Drupal\user\Entity\User|null
   *   Can be user or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getUserByField($fields) {
    if (empty($fields)) {
      return NULL;
    }
    $user = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties($fields);
    return $user ? reset($user) : FALSE;
  }
}
