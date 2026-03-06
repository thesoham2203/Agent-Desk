<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: KbArticleManager.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A management interface for the system's Knowledge Base articles.
 *
 * WHY DOES IT EXIST?
 * To populate the knowledge base used by both human agents
 * and the AI triage/reply-draft agents.
 *
 * HOW IT FITS IN THE APP:
 * CRUD for kb_articles table. Accessed via /admin/kb-articles.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Form validation in Livewire prevents processing invalid data,
 * automatically returning error messages to the Blade view.
 * ============================================================
 */
use App\Models\KbArticle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class KbArticleManager extends Component
{
    /**
     * The title of the article.
     */
    public string $title = '';

    /**
     * The main content body of the article.
     */
    public string $body = '';

    /**
     * The ID of the article being edited.
     */
    public ?int $editingId = null;

    /**
     * Controls the visibility of the entry form.
     */
    public bool $showForm = false;

    /**
     * Retrieves all KB articles ordered by title.
     */
    #[Computed]
    public function articles(): Collection
    {
        return KbArticle::query()->orderBy('title')->get();
    }

    /**
     * Creates a new KB article.
     */
    public function create(): void
    {
        $this->authorize('manage-kb-articles');

        $this->validate([
            'title' => 'required|min:5|max:255',
            'body' => 'required|min:10',
        ]);

        KbArticle::query()->create([
            'title' => $this->title,
            'body' => $this->body,
        ]);

        $this->resetForm();
        session()->flash('success', 'Article created successfully.');
    }

    /**
     * Prepares an article for editing.
     */
    public function edit(int $id): void
    {
        $this->authorize('manage-kb-articles');

        $article = KbArticle::query()->findOrFail($id);
        $this->editingId = $id;
        $this->title = $article->title;
        $this->body = $article->body;
        $this->showForm = true;
    }

    /**
     * Updates an existing KB article.
     */
    public function update(): void
    {
        $this->authorize('manage-kb-articles');

        $this->validate([
            'title' => 'required|min:5|max:255',
            'body' => 'required|min:10',
        ]);

        if ($this->editingId) {
            KbArticle::query()->where('id', $this->editingId)->update([
                'title' => $this->title,
                'body' => $this->body,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Article updated successfully.');
    }

    /**
     * Removes an article from the database.
     */
    public function delete(int $id): void
    {
        $this->authorize('manage-kb-articles');

        KbArticle::query()->findOrFail($id)->delete();
        session()->flash('success', 'Article deleted successfully.');
    }

    /**
     * Clears the form state.
     */
    public function resetForm(): void
    {
        $this->reset(['title', 'body', 'editingId', 'showForm']);
    }

    /**
     * Renders the article management view.
     */
    public function render(): View
    {
        return view('livewire.admin.kb-article-manager', [
            'articleList' => $this->articles,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Admin/AuditLogViewer.php]
 * WHY: Moving from active management to passive system monitoring.
 * ============================================================
 */
