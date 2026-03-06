<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AdminCrudTest.php
 * LAYER: Testing (Feature)
 * ============================================================
 *
 * WHAT IS THIS?
 * Functional tests for administrative CRUD operations.
 *
 * WHY DOES IT EXIST?
 * To verify that administrators can successfully manage categories,
 * macros, knowledge base articles, and SLA configurations.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Livewire::test() allows us to simulate user interaction with
 * components (setting properties, calling methods) and assert
 * changes in the database or rendered output.
 * ============================================================
 */
use App\Enums\UserRole;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\MacroManager;
use App\Livewire\Admin\SlaConfigManager;
use App\Models\Category;
use App\Models\Macro;
use App\Models\SlaConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
});

test('admin can create and list categories', function (): void {
    Livewire::actingAs($this->admin)
        ->test(CategoryManager::class)
        ->set('name', 'Networking')
        ->set('description', 'WiFi and Ethernet issues')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSee('Networking')
        ->assertSet('showForm', false);

    expect(Category::query()->where('name', 'Networking')->exists())->toBeTrue();
});

test('admin can edit and delete categories', function (): void {
    $category = Category::factory()->create(['name' => 'Old Name']);

    Livewire::actingAs($this->admin)
        ->test(CategoryManager::class)
        ->call('edit', $category->id)
        ->assertSet('name', 'Old Name')
        ->set('name', 'New Name')
        ->call('update')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('New Name');

    Livewire::actingAs($this->admin)
        ->test(CategoryManager::class)
        ->call('delete', $category->id);

    expect(Category::query()->find($category->id))->toBeNull();
});

test('admin can manage macros', function (): void {
    Livewire::actingAs($this->admin)
        ->test(MacroManager::class)
        ->set('title', 'Closing')
        ->set('body', 'Have a nice day!')
        ->call('create')
        ->assertSee('Closing');

    expect(Macro::query()->where('title', 'Closing')->exists())->toBeTrue();
});

test('admin can update SLA configuration', function (): void {
    // Ensure a seed config exists if migration didn't create it
    SlaConfig::query()->firstOrCreate([], [
        'first_response_hours' => 4,
        'resolution_hours' => 24,
    ]);

    Livewire::actingAs($this->admin)
        ->test(SlaConfigManager::class)
        ->set('firstResponseHours', 2)
        ->set('resolutionHours', 12)
        ->call('update')
        ->assertHasNoErrors()
        ->assertSee('updated successfully');

    $config = SlaConfig::query()->first();
    expect($config->first_response_hours)->toBe(2);
    expect($config->resolution_hours)->toBe(12);
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [tutor.md]
 * WHY: Closing out Day 10 and preparing for the final day!
 * ============================================================
 */
