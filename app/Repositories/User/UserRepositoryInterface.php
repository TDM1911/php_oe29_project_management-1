<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getDeletedUser();

    public function restoreUser($id);

    public function forceDeleteUser($id);

    public function updateUser($user, $data, $roles);

    public function import($request);
}
