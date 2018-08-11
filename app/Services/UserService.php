<?php

namespace App\Services;

use PDO;
use App\User;
use App\Session;

trait UserService
{
    /**
     * Get the first user.
     *
     * @param  string  $criteria
     * @return \App\User|null
     */
    public static function getFirstUser ($criteria = '*')
    {
        $sql = "SELECT {$criteria} FROM users ORDER BY id ASC LIMIT 1";

        $stmt = parent::query($sql);

        $data = $stmt->fetch();

        return $data ? new User($data) : null;
    }

    /**
     * Get the last user.
     *
     * @param  string  $criteria
     * @return \App\User|null
     */
    public static function getLastUser ($criteria = '*')
    {
        $sql = "SELECT {$criteria} FROM users ORDER BY id DESC LIMIT 1";

        $stmt = parent::query($sql);

        $data = $stmt->fetch();

        return $data ? new User($data) : null;
    }

    /**
     * Get user by its session token.
     *
     * @param  string  $token
     * @param  string  $criteria
     * @return \App\User|null
     */
    public static function findByToken ($token, $criteria = '*')
    {
        $session = Session::findBy('token', $token);

        if (!is_null($session))
        {
            if ($_SERVER['HTTP_USER_AGENT'] == $session->getField('user_agent'))
            {
                $sql = "SELECT {$criteria} FROM users WHERE id = :user_id LIMIT 1";

                $stmt = parent::prepare($sql);
                $stmt->bindValue(":user_id", $session->getField('user_id'), PDO::PARAM_STR);
                $stmt->execute();

                $data = $stmt->fetch();

                return $data ? new User($data) : null;
            }

            $sql = "DELETE FROM sessions WHERE id = :session_id";

            $stmt = parent::prepare($sql);
            $stmt->bindValue(":session_id", $session->getId(), PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
