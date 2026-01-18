<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data)
            $data = $_POST;

        $user = User::verify($data['email'], $data['password']);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
        }
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data)
            $data = $_POST;

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Email y contraseña requeridos']);
            return;
        }

        try {
            User::create($data['email'], $data['password']);
            $user = User::findByEmail($data['email']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'El usuario ya existe o error en el servidor']);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
