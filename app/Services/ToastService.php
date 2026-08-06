<?php

namespace App\Services;

class ToastService
{
    public function success(
        string $message,
        ?string $title = null,
        ?string $icon = 'success'
    ): array {

        return [
            'type' => 'success',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
        ];
    }

    public function error(
        string $message,
        ?string $title = null
    ): array {

        return [
            'type' => 'error',
            'title' => $title,
            'message' => $message,
            'icon' => 'error',
        ];
    }

    public function warning(
        string $message
    ): array {

        return [
            'type' => 'warning',
            'message' => $message,
            'icon' => 'warning',
        ];
    }

    public function info(
        string $message
    ): array {

        return [
            'type' => 'info',
            'message' => $message,
            'icon' => 'info',
        ];
    }
}