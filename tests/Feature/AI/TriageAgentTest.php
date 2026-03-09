<?php

declare(strict_types=1);

use App\AI\Agents\TriageAgent;
use App\AI\DTOs\TriageInput;
use App\AI\DTOs\TriageResult;

beforeEach(function (): void {
    // TriageAgent::fake() is now available since we added Promptable trait
    TriageAgent::fake([
        json_encode([
            'category' => 'Technical',
            'priority' => 'high',
            'summary' => 'User cannot login.',
            'tags' => ['login', 'auth'],
            'escalation_flag' => false,
            'clarifying_question' => '',
        ]),
    ]);
});

it('returns TriageResult DTO with correct fields', function (): void {
    $agent = new TriageAgent();
    $input = new TriageInput(
        title: 'Cannot login',
        body: 'Getting 403 error',
        category: 'Technical',
    );

    $result = $agent->handle($input);

    expect($result)->toBeInstanceOf(TriageResult::class);
    expect($result->category)->toBe('Technical');
    expect($result->priority)->toBe('high');
    expect($result->tags)->toBeArray();
});

it('throws RuntimeException when AI returns invalid JSON', function (): void {
    TriageAgent::fake(['not valid json at all']);

    $agent = new TriageAgent();
    $input = new TriageInput(title: 'test', body: 'test', category: '');

    expect(fn (): TriageResult => $agent->handle($input))
        ->toThrow(RuntimeException::class);
});

it('strips markdown fences from AI response', function (): void {
    TriageAgent::fake([
        '```json'."\n".json_encode([
            'category' => 'Technical',
            'priority' => 'low',
            'summary' => 'test',
            'tags' => [],
            'escalation_flag' => false,
            'clarifying_question' => '',
        ])."\n```",
    ]);

    $agent = new TriageAgent();
    $input = new TriageInput(title: 'test', body: 'test', category: '');

    // Should NOT throw — markdown fences stripped successfully
    $result = $agent->handle($input);
    expect($result->category)->toBe('Technical');
});
