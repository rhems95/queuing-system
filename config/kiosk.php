<?php

return [
    'walkin_unlock_minutes' => (int) env('KIOSK_WALKIN_UNLOCK_MINUTES', 5),

    'walkin_issuer_email' => (string) env('KIOSK_WALKIN_ISSUER_EMAIL', 'guard@gmail.com'),
];
