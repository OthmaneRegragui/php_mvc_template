<?php

class User
{
    static public function getUser($id)
    {
        $crud = new CRUD();
        // Assuming 'users' is your table name
        $user = $crud->select('users', ['id' => $id], [], '', '1', true);
        return $user;
    }

    // Example of inserting a user
    static public function createUser($username, $email, $password)
    {
        $crud = new CRUD();
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT) // Always hash passwords!
        ];
        return $crud->insert('users', $data);
    }
}
