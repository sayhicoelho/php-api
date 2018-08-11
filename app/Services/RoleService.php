<?php

namespace App\Services;

use PDO;
use App\Role;
use Core\Config;

trait RoleService
{
    /**
     * Add a role.
     *
     * @param  string  $name
     * @return boolean|int
     */
    public function addRole ($name)
    {
        $roles = Config::get('roles');

        if (array_key_exists($name, $roles))
        {
            if (!$this->hasRole($name))
            {
                $sql = "INSERT INTO roles (user_id, name) VALUES (:user_id, :name)";

                $stmt = parent::prepare($sql);
                $stmt->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->execute();

                return parent::getInsertedId();
            }
        }

        return false;
    }

    /**
     * Add a role.
     *
     * @param  string  $name
     * @return boolean
     */
    public function deleteRole ($name)
    {
        if ($this->hasRole($name))
        {
            $sql = "DELETE FROM roles WHERE user_id = :user_id AND name = :name";

            $stmt = parent::prepare($sql);
            $stmt->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);

            $stmt->execute();

            return true;
        }

        return false;
    }

    /**
     * Update a role.
     *
     * @param  string  $oldname
     * @param  string  $newname
     * @return boolean
     */
    public function updateRole ($oldname, $newname)
    {
        $roles = Config::get('roles');

        if ($this->hasRole($oldname) && !$this->hasRole($newname) && array_key_exists($newname, $roles))
        {
            $sql = "UPDATE roles SET name = :newname WHERE user_id = :user_id AND name = :oldname";

            $stmt = parent::prepare($sql);
            $stmt->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
            $stmt->bindValue(':oldname', $oldname, PDO::PARAM_STR);
            $stmt->bindValue(':newname', $newname, PDO::PARAM_STR);

            $stmt->execute();

            return true;
        }

        return false;
    }

    /**
     * Check if has a specified role.
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasRole ($name)
    {
        if (!$this->hasField('roles'))
        {
            $this->load(Role::class);
        }

        $roles = $this->getField('roles');

        foreach ($roles as $role)
        {
            if ($role->getField('name') == $name)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if has a any role.
     *
     * @param  array  $roles
     * @return boolean
     */
    public function hasAnyRole (array $roles)
    {
        foreach ($roles as $role)
        {
            if ($this->hasRole($role))
            {
                return true;
            }
        }

        return false;
    }
}
