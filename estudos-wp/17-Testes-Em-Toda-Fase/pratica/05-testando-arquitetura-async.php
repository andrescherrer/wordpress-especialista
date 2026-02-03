<?php
/**
 * REFERÊNCIA RÁPIDA – Testando arquitetura e async jobs
 *
 * Repository: createMock(PostRepository::class); method('find')->willReturn($post); service usa mock; assert resultado.
 * Service: expects($this->once())->method('create'); expects($this->never())->method('create') quando validação falha.
 * DI Container: bind/make; assertInstanceOf; singleton: assertSame(instance1, instance2).
 * Action Scheduler: as_has_scheduled_action após enqueue; as_unschedule_action; assertFalse(has_scheduled).
 *
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md (Fase 13, Fase 16)
 */

use PHPUnit\Framework\TestCase;

// ========== Exemplo: Repository mockado ==========

interface PostRepositoryInterface
{
    public function find(int $id): ?object;
}

class PostService
{
    private PostRepositoryInterface $repository;

    public function __construct(PostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPost(int $id): ?object
    {
        return $this->repository->find($id);
    }
}

class RepositoryTest extends TestCase
{
    public function test_repository_can_be_mocked(): void
    {
        $mock_repo = $this->createMock(PostRepositoryInterface::class);
        $mock_post = (object) ['title' => 'Mocked Post'];
        $mock_repo->method('find')->willReturn($mock_post);

        $service = new PostService($mock_repo);
        $post    = $service->getPost(1);

        $this->assertNotNull($post);
        $this->assertEquals('Mocked Post', $post->title);
    }

    public function test_service_returns_null_when_repository_returns_null(): void
    {
        $mock_repo = $this->createMock(PostRepositoryInterface::class);
        $mock_repo->method('find')->willReturn(null);

        $service = new PostService($mock_repo);
        $this->assertNull($service->getPost(999));
    }
}

// ========== Exemplo: Action Scheduler (requer Action Scheduler ativo) ==========

class ActionSchedulerTest extends TestCase
{
    protected function tearDown(): void
    {
        if (function_exists('as_unschedule_action')) {
            as_unschedule_action('test_action');
        }
        parent::tearDown();
    }

    public function test_action_is_scheduled(): void
    {
        if (! function_exists('as_enqueue_async_action')) {
            $this->markTestSkipped('Action Scheduler não disponível');
        }
        as_enqueue_async_action('test_action', ['arg1' => 'value1']);
        $this->assertTrue(as_has_scheduled_action('test_action', ['arg1' => 'value1']));
    }

    public function test_action_can_be_cancelled(): void
    {
        if (! function_exists('as_enqueue_async_action')) {
            $this->markTestSkipped('Action Scheduler não disponível');
        }
        as_enqueue_async_action('test_action');
        $this->assertTrue(as_has_scheduled_action('test_action'));
        as_unschedule_action('test_action');
        $this->assertFalse(as_has_scheduled_action('test_action'));
    }
}
