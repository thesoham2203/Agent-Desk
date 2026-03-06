<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;

final class VerifyEmailController
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
