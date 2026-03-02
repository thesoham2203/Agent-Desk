<?php

declare(strict_types=1);

namespace App\AI\DTOs;

/**
 * ============================================================
 * FILE: KbSnippetDTO.php
 * LAYER: DTO
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a concise snippet from the knowledge base (KB)
 * relevant to a specific ticket.
 *
 * WHY DOES IT EXIST?
 * To allow the SearchKnowledgeBaseTool to return only the
 * essential parts of an article for grounding AI responses.
 *
 * HOW IT FITS IN THE APP:
 * The SearchKnowledgeBaseTool yields a list of these objects,
 * which the ReplyDraftAgent then uses to formulate its reply.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Readonly classes are excellent for results coming out of
 * tools and functions, preventing any accidental modification.
 * ============================================================
 */
final readonly class KbSnippetDTO
{
    /**
     * Initializes the immutable snippet from the Knowledge Base.
     *
     * @param  int  $articleId  The unique ID of the source KB article.
     * @param  string  $title  The display title of the KB article.
     * @param  string  $excerpt  A shortened version of the article's body.
     * @param  float  $relevanceScore  A score representing the closeness of the snippet to the query.
     */
    public function __construct(
        public int $articleId,
        public string $title,
        public string $excerpt,
        public float $relevanceScore,
    ) {}
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [KbSnippetDTO.php], the next logical file
 * to read is:
 *
 * → [database/seeders/DatabaseSeeder.php]
 *
 * WHY: After defining how we pass data around, we define how
 *       we populate the system with realistic data for testing.
 * ============================================================
 */
