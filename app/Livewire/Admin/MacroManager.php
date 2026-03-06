<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: MacroManager.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A Livewire component for managing agent macros.
 *
 * WHY DOES IT EXIST?
 * Macros (canned responses) improve efficiency by allowing agents
 * to quickly insert common answers into ticket replies.
 *
 * HOW IT FITS IN THE APP:
 * Accessed via /admin/macros. Updates the macros table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Computed properties in Livewire are cached during a single request,
 * making them efficient for data that is used multiple times in a view.
 * ============================================================
 */
use App\Models\Macro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class MacroManager extends Component
{
    /**
     * The title/name of the macro.
     */
    public string $title = '';

    /**
     * The body text content of the macro.
     */
    public string $body = '';

    /**
     * The ID of the macro being edited.
     */
    public ?int $editingId = null;

    /**
     * Whether the form is currently displayed.
     */
    public bool $showForm = false;

    /**
     * Computed property to get all macros ordered by title.
     */
    #[Computed]
    public function macros(): Collection
    {
        return Macro::query()->orderBy('title')->get();
    }

    /**
     * Persists a new macro to the database.
     */
    public function create(): void
    {
        $this->authorize('manage-macros');

        $this->validate([
            'title' => 'required|min:2|max:255',
            'body' => 'required|min:5',
        ]);

        Macro::query()->create([
            'title' => $this->title,
            'body' => $this->body,
        ]);

        $this->resetForm();
        session()->flash('success', 'Macro created successfully.');
    }

    /**
     * Prepares the form for editing an existing macro.
     */
    public function edit(int $id): void
    {
        $this->authorize('manage-macros');

        $macro = Macro::query()->findOrFail($id);
        $this->editingId = $id;
        $this->title = $macro->title;
        $this->body = $macro->body;
        $this->showForm = true;
    }

    /**
     * Updates an existing macro in the database.
     */
    public function update(): void
    {
        $this->authorize('manage-macros');

        $this->validate([
            'title' => 'required|min:2|max:255',
            'body' => 'required|min:5',
        ]);

        if ($this->editingId) {
            Macro::query()->where('id', $this->editingId)->update([
                'title' => $this->title,
                'body' => $this->body,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Macro updated successfully.');
    }

    /**
     * Deletes a macro from the database.
     */
    public function delete(int $id): void
    {
        $this->authorize('manage-macros');

        Macro::query()->findOrFail($id)->delete();
        session()->flash('success', 'Macro deleted successfully.');
    }

    /**
     * Resets internal form state.
     */
    public function resetForm(): void
    {
        $this->reset(['title', 'body', 'editingId', 'showForm']);
    }

    /**
     * Renders the component view.
     */
    public function render(): View
    {
        return view('livewire.admin.macro-manager', [
            'macroList' => $this->macros,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Admin/SlaConfigManager.php]
 * WHY: Moving from content management to system configuration.
 * ============================================================
 */
