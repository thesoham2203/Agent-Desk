<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: CategoryManager.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A Livewire component for managing ticket categories (CRUD).
 *
 * WHY DOES IT EXIST?
 * To allow administrators to organize tickets into logical groups
 * like "Hardware", "Software", or "Billing".
 *
 * HOW IT FITS IN THE APP:
 * Accessed via /admin/categories. Updates the categories table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Livewire components encapsulate both logic (PHP) and UI (Blade).
 * Public properties are automatically synchronized with the frontend.
 * ============================================================
 */
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read Collection<int, Category> $categories
 */
final class CategoryManager extends Component
{
    /**
     * The name of the category being created or edited.
     */
    public string $name = '';

    /**
     * The description of the category.
     */
    public string $description = '';

    /**
     * The ID of the category being edited, or null if creating.
     */
    public ?int $editingId = null;

    /**
     * Whether the creation/edition form is currently visible.
     */
    public bool $showForm = false;

    /**
     * Computed property to retrieve all categories ordered by name.
     *
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    /**
     * Toggles the form and resets properties for creation.
     */
    public function create(): void
    {
        $this->authorize('manage-categories');

        $this->validate([
            'name' => 'required|min:2|max:100',
            'description' => 'nullable|string',
        ]);

        Category::query()->create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetForm();
        session()->flash('success', 'Category created successfully.');
    }

    /**
     * Loads a category into the form for editing.
     */
    public function edit(int $id): void
    {
        $this->authorize('manage-categories');

        $category = Category::query()->findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->showForm = true;
    }

    /**
     * Updates an existing category.
     */
    public function update(): void
    {
        $this->authorize('manage-categories');

        $this->validate([
            'name' => 'required|min:2|max:100',
            'description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            Category::query()->where('id', $this->editingId)->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Category updated successfully.');
    }

    /**
     * Deletes a category from the database.
     */
    public function delete(int $id): void
    {
        $this->authorize('manage-categories');

        Category::query()->findOrFail($id)->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    /**
     * Resets the form properties to their defaults.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'description', 'editingId', 'showForm']);
    }

    /**
     * Renders the component view.
     */
    public function render(): View
    {
        return view('livewire.admin.category-manager', [
            'categoryList' => $this->categories,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Admin/MacroManager.php]
 * WHY: Both follow the same administrative CRUD pattern.
 * ============================================================
 */
