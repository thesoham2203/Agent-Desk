<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;

arch()->preset()->php();

arch('preset → strict')
    ->preset()
    ->strict()
    ->ignoring('App\Http\Controllers')
    ->ignoring('App\Livewire\Forms')
    ->ignoring('App\Livewire\Actions')
    ->ignoring('App\AI\Agents')
    ->ignoring('App\AI\Tools');

arch()->preset()->security();

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toUse(Controller::class)
    ->ignoring('App\Http\Controllers\Auth');
