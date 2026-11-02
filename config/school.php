<?php

return [
    // Account-lockout policy (override in .env if you like)
    'login_max_failed_attempts' => (int) env('LOGIN_MAX_FAILED_ATTEMPTS', 3),
    'login_lockout_minutes'     => (int) env('LOGIN_LOCKOUT_MINUTES', 15),
];
