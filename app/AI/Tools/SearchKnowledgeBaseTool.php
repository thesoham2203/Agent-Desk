<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: SearchKnowledgeBaseTool.php
 * LAYER: AI Tool
 * ============================================================
 *
 * WHAT IS THIS?
 * A plain PHP class that searches the local knowledge base (kb_articles table)
 * for relevant content based on a search query.
 *
 * WHY DOES IT EXIST?
 * To provide "grounding" for AI agents. By fetching real data from our DB,
 * we prevent the AI from hallucinating and ensure its answers are based on
 * our actual documentation.
 *
 * HOW IT FITS IN THE APP:
 * This tool is instantiated and called by AI Agents (like ReplyDraftAgent).
 * It returns a list of DTOs which are then injected into the LLM prompt.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This is a plain PHP class, not a built-in Laravel feature. We use it to
 * encapsulate search logic. It uses Eloquent (KbArticle::query()) to talk
 * to the database and Illuminate\Support\Str for text manipulation.
 * ============================================================
 */

namespace App\AI\Tools;

use App\AI\DTOs\KbSnippetDTO;
use App\Models\KbArticle;
use Illuminate\Support\Str;

class SearchKnowledgeBaseTool
{
    /**
     * Executes the search against the kb_articles table.
     *
     * @param  string  $query  The search term provided by the AI Agent
     * @param  int  $limit  The maximum number of results to return
     * @return KbSnippetDTO[] An array of typed snippet objects for the AI to process
     */
    public function execute(string $query, int $limit = 3): array
    {
        // 1. Search kb_articles using Laravel full-text-style LIKE logic
        // We search both the title and the body for the query string.
        $articles = KbArticle::query()->where('title', 'like', '%'.$query.'%')
            ->orWhere('body', 'like', '%'.$query.'%')
            ->limit($limit)
            ->get();

        // 2. Map the Eloquent results to our standardized KbSnippetDTO
        // This ensures the AI Agent receives a consistent structure.
        return $articles->map(fn (KbArticle $article): KbSnippetDTO => new KbSnippetDTO(
            articleId: (int) $article->id,
            title: (string) $article->title,
            excerpt: (string) Str::limit($article->body, 200),
            relevanceScore: 1.0 // Simple implementation: all matches weighted equally for now
        ))->all();
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/AI/Agents/TriageAgent.php]
 * WHY: This tool is the foundation of AI grounding; now we build the
 * first "Agent" that uses these concepts to process tickets.
 * ============================================================
 */
