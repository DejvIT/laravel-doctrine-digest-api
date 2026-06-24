<?php

namespace Tests\Feature;

use DateTime;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDomainData();
    }

    public function test_blogger_can_create_article_in_assigned_category(): void
    {
        $token = $this->loginToken();

        $response = $this->postJson('/articles', [
            'title'         => 'New article',
            'content'       => 'Article body',
            'category_uuid' => $this->categoryA->getUuid(),
        ], $this->authHeaders($token));

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'New article');
    }

    public function test_blogger_cannot_create_article_in_unassigned_category(): void
    {
        $token = $this->loginToken();

        $response = $this->postJson('/articles', [
            'title'         => 'Forbidden article',
            'content'       => 'Article body',
            'category_uuid' => $this->categoryB->getUuid(),
        ], $this->authHeaders($token));

        $response->assertForbidden();
    }

    public function test_blogger_can_update_undistributed_article(): void
    {
        $article = $this->createArticle($this->blogger1, $this->categoryA);
        $token = $this->loginToken();

        $response = $this->putJson('/articles/'.$article->getUuid(), [
            'title' => 'Updated title',
        ], $this->authHeaders($token));

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Updated title');
    }

    public function test_blogger_cannot_update_distributed_article(): void
    {
        $article = $this->createArticle($this->blogger1, $this->categoryA, new DateTime('-1 day'));
        $token = $this->loginToken();

        $response = $this->putJson('/articles/'.$article->getUuid(), [
            'title' => 'Should fail',
        ], $this->authHeaders($token));

        $response->assertForbidden();
    }

    public function test_blogger_cannot_delete_distributed_article(): void
    {
        $article = $this->createArticle($this->blogger1, $this->categoryA, new DateTime('-1 day'));
        $token = $this->loginToken();

        $response = $this->deleteJson('/articles/'.$article->getUuid(), [], $this->authHeaders($token));

        $response->assertForbidden();
    }

    public function test_blogger_can_list_articles_with_distributed_query_string(): void
    {
        $this->createArticle($this->blogger1, $this->categoryA);
        $this->createArticle($this->blogger1, $this->categoryA, new DateTime('-1 day'));
        $token = $this->loginToken();

        $this->getJson('/articles?distributed=false', $this->authHeaders($token))
            ->assertOk();

        $this->getJson('/articles?distributed=true', $this->authHeaders($token))
            ->assertOk();
    }

    public function test_blogger_cannot_view_other_blogger_article(): void
    {
        $article = $this->createArticle($this->blogger2, $this->categoryB);
        $token = $this->loginToken();

        $response = $this->getJson('/articles/'.$article->getUuid(), $this->authHeaders($token));

        $response->assertForbidden();
    }
}
