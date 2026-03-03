<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class);

it('has welcome page', function (): void {
    $this->markTestSkipped('Requires Dusk browser setup.');
});
