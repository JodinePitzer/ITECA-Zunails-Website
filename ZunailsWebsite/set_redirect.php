<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['redirectUrl'])) {
        $_SESSION['redirect_url'] = $data['redirectUrl'];
    }
}
