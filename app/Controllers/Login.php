<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function process()
    {
        helper(['form', 'url']);

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if ($this->validate($rules)) {
            $userModel = new UserModel();

            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');

            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password']) && $user['is_verified'] == 1) {
                // Set user session
                $userData = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'isLoggedIn' => true
                ];
                session()->set($userData);
                return redirect()->to('dashboard');
            } else {
                $data['validation'] = 'Invalid email or password.';
            }
        } else {
            $data['validation'] = $this->validator;
        }

        return view('login', $data);
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $data['user'] = session()->get();
        return view('dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
