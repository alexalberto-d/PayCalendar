<?php

namespace App\Controllers;

use App\Models\Subscription;

class ApiController
{
    public function index()
    {
        $subscriptions = Subscription::all();
        header('Content-Type: application/json');
        echo json_encode($subscriptions);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data)
            $data = $_POST;

        try {
            $id = Subscription::create($data);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $id = intval($id);
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            Subscription::update($id, $data);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $id = intval($id);
        try {
            Subscription::delete($id);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
