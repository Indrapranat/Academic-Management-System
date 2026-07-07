<?php
// Includes/session_bootstrap.php

function bootRoleSession(string $role): void
{
    $role = strtolower(trim($role));
    if ($role === '') $role = 'guest';

    // KUNCI: cookie session name harus beda per role
    // Jadi 1 browser bisa login 3 role sekaligus di tab berbeda
    $sessionName = 'AMSSESS_' . strtoupper($role);

    // Jangan sampai session sudah aktif dengan name lain
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Kalau sudah aktif tapi namanya beda, tutup dulu biar tidak tabrakan
        if (session_name() !== $sessionName) {
            session_write_close();
        } else {
            return; // sudah benar & aktif
        }
    }

    // Set nama session
    session_name($sessionName);

    // Cookie params (path "/" aman, karena pembeda utamanya nama cookie)
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,     // true kalau https
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
