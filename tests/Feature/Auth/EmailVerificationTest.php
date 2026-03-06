<?php

declare(strict_types=1);

test('email verification screen can be rendered', function (): void {
    $this->markTestSkipped('Email verification disabled.');
});

test('email can be verified', function (): void {
    $this->markTestSkipped('Email verification disabled.');
});

test('email is not verified with invalid hash', function (): void {
    $this->markTestSkipped('Email verification disabled.');
});
