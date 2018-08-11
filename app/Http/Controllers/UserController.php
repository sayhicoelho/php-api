<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Core\Hash;
use Core\Request;

class UserController extends Controller
{
    /**
     * Get user by id.
     *
     * @param  \Core\Request  $request
     * @param  int  $id
     * @return \Core\Response
     */
    public function getUser (Request $request, $id)
    {
        $user = User::findById($id);

        if (!is_null($user))
        {
            $user->load(Role::class);

            return response ([
                'user' => $user->toArray()
            ], 200);
        }

        return response (['error' => 'User not found.'], 404);
    }

    /**
     * Get all the user roles.
     *
     * @param  \Core\Request  $request
     * @param  int  $id
     * @return \Core\Response
     */
    public function getRoles (Request $request, $id)
    {
        $user = User::findById($id);

        if (!is_null($user))
        {
            $user->load(Role::class);

            return response (['roles' => $user->toArray()['roles']], 200);
        }

        return response (['error' => 'User not found.'], 404);
    }

    /**
     * Add role to user.
     *
     * @param  \Core\Request  $request
     * @param  int  $id
     * @return \Core\Response
     */
    public function addRole (Request $request, $id)
    {
        $user = User::findById($id);

        if (!is_null($user))
        {
            $role = $request->input('role');

            if ($user->addRole($role))
            {
                return response (['success' => "The role \"{$role}\" was added to user \"{$user->getField('name')}\"."], 200);
            }

            return response (['error' => 'Role not found or already exists.'], 404);
        }

        return response (['error' => 'User not found.'], 404);
    }

    /**
     * Delete role from user.
     *
     * @param  \Core\Request  $request
     * @param  int  $id
     * @return \Core\Response
     */
    public function deleteRole (Request $request, $id)
    {
        $user = User::findById($id);

        if (!is_null($user))
        {
            $role = $request->input('role');

            if ($user->deleteRole($role))
            {
                return response (['success' => "The role \"{$role}\" was removed."], 200);
            }

            return response (['error' => "User {$user->getField('name')} does not have \"{$role}\" role."], 404);
        }

        return response (['error' => 'User not found.'], 404);
    }

    /**
     * Update a specified role from user.
     *
     * @param  \Core\Request  $request
     * @param  int  $id
     * @return \Core\Response
     */
    public function updateRole (Request $request, $id)
    {
        $user = User::findById($id);

        if (!is_null($user))
        {
            $oldname = $request->input('oldname');
            $newname = $request->input('newname');

            if ($user->updateRole($oldname, $newname))
            {
                return response (['success' => "Role \"{$oldname}\" was changed to \"{$newname}\"."], 200);
            }

            return response (['error' => 'Role not found.'], 404);
        }

        return response (['error' => 'User not found.'], 404);
    }

    /**
     * Update the authenticated user password.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function updatePassword (Request $request)
    {
        Auth::user()->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response(['success' => true], 200);
    }
}
