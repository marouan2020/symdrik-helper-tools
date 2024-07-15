<?php
namespace  Drupal\symdrik_helper_tools;

use Drupal\user\Entity\Role;

class RolesHelper
{
    /**
     * @param string $roleName
     * @param string $roleLabel
     * @return Role|null
     */
    public function createRoleIfNotExists(string $roleName, string $roleLabel): ?Role
    {
        try {
            // Vérifie si le rôle existe déjà.
            $role = Role::load($roleName);
            if (null === $role) {
                // Le rôle n'existe pas, donc le crée.
                $role = Role::create(['id' => $roleName, 'label' => $roleLabel]);
                $role->save();
            }
            return $role;
        } catch (\Exception $e) {
            \Drupal::logger('symdrik_helper_tools')->error($e->getMessage());
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAllRoles(): array
    {
        $roles = [];

        // Charge tous les rôles.
        $roleEntities = Role::loadMultiple();
        foreach ($roleEntities as $role) {
            $roles[$role->id()] = $role->label();
        }

        return $roles;
    }
}
