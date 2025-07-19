<?php

namespace Tests\Feature;

use App\Models\Memory;
use App\Models\User;
use App\Services\StorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $storageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->storageService = $this->createMock(StorageService::class);
        $this->app->instance(StorageService::class, $this->storageService);
    }

    /** @test */
    public function user_can_view_memories_index()
    {
        // Arrange
        Memory::factory()->count(3)->create(['user_id' => $this->user->id]);
        Memory::factory()->count(2)->create(); // Other user's memories

        // Act
        $response = $this->actingAs($this->user)->get(route('memories.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('memories.index');
        $response->assertViewHas('memories');
        
        $memories = $response->viewData('memories');
        $this->assertCount(3, $memories);
        $this->assertTrue($memories->every(fn($memory) => $memory->user_id === $this->user->id));
    }

    /** @test */
    public function user_can_view_create_memory_form()
    {
        $response = $this->actingAs($this->user)->get(route('memories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('memories.create');
    }

    /** @test */
    public function user_can_create_memory_without_image()
    {
        // Arrange
        $memoryData = [
            'memory_date' => '2024-01-15',
            'description' => 'A beautiful memory',
            'spotify_track_id' => 'spotify:track:123456'
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->post(route('memories.store'), $memoryData);

        // Assert
        $response->assertRedirect(route('memories.index'));
        $response->assertSessionHas('success', 'Memory created successfully! 💝');
        
        $this->assertDatabaseHas('memories', [
            'user_id' => $this->user->id,
            'memory_date' => '2024-01-15',
            'description' => 'A beautiful memory',
            'spotify_track_id' => 'spotify:track:123456',
            'image_path' => null
        ]);
    }

    /** @test */
    public function user_can_create_memory_with_image()
    {
        // Arrange
        Storage::fake('public');
        $file = UploadedFile::fake()->image('memory.jpg', 800, 600);
        $this->storageService->expects($this->once())
            ->method('uploadImage')
            ->with($file, 'memories', $this->stringContains('memory_'))
            ->willReturn('memories/memory_123.jpg');

        $memoryData = [
            'memory_date' => '2024-01-15',
            'description' => 'A beautiful memory with image',
            'image' => $file
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->post(route('memories.store'), $memoryData);

        // Assert
        $response->assertRedirect(route('memories.index'));
        $this->assertDatabaseHas('memories', [
            'user_id' => $this->user->id,
            'image_path' => 'memories/memory_123.jpg'
        ]);
    }

    /** @test */
    public function create_memory_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('memories.store'), []);

        $response->assertSessionHasErrors(['memory_date', 'description']);
    }

    /** @test */
    public function create_memory_validates_image_format()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $response = $this->actingAs($this->user)
            ->post(route('memories.store'), [
                'memory_date' => '2024-01-15',
                'description' => 'Test memory',
                'image' => $file
            ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function create_memory_validates_image_size()
    {
        $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB
        
        $response = $this->actingAs($this->user)
            ->post(route('memories.store'), [
                'memory_date' => '2024-01-15',
                'description' => 'Test memory',
                'image' => $file
            ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function user_can_view_memory_details()
    {
        // Arrange
        $memory = Memory::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)
            ->get(route('memories.show', $memory));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('memories.show');
        $response->assertViewHas('memory', $memory);
    }

    /** @test */
    public function user_can_view_edit_memory_form()
    {
        // Arrange
        $memory = Memory::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user)
            ->get(route('memories.edit', $memory));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('memories.edit');
        $response->assertViewHas('memory', $memory);
    }

    /** @test */
    public function user_can_update_memory_without_changing_image()
    {
        // Arrange
        $memory = Memory::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'Original description',
            'image_path' => 'memories/original.jpg'
        ]);

        $updateData = [
            'memory_date' => '2024-02-20',
            'description' => 'Updated description',
            'spotify_track_id' => 'spotify:track:updated'
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->put(route('memories.update', $memory), $updateData);

        // Assert
        $response->assertRedirect(route('memories.show', $memory));
        $response->assertSessionHas('success', 'Memory updated successfully! ✨');
        
        $memory->refresh();
        $this->assertEquals('Updated description', $memory->description);
        $this->assertEquals('memories/original.jpg', $memory->image_path); // Image unchanged
    }

    /** @test */
    public function user_can_update_memory_with_new_image()
    {
        // Arrange
        $memory = Memory::factory()->create([
            'user_id' => $this->user->id,
            'image_path' => 'memories/old.jpg'
        ]);

        $newFile = UploadedFile::fake()->image('new.jpg');
        
        $this->storageService->expects($this->once())
            ->method('deleteFile')
            ->with('memories/old.jpg');
            
        $this->storageService->expects($this->once())
            ->method('uploadImage')
            ->willReturn('memories/new.jpg');

        $updateData = [
            'memory_date' => $memory->memory_date->format('Y-m-d'),
            'description' => $memory->description,
            'image' => $newFile
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->put(route('memories.update', $memory), $updateData);

        // Assert
        $response->assertRedirect(route('memories.show', $memory));
        $memory->refresh();
        $this->assertEquals('memories/new.jpg', $memory->image_path);
    }

    /** @test */
    public function user_cannot_update_other_users_memory()
    {
        // Arrange
        $otherUser = User::factory()->create();
        $memory = Memory::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($this->user)
            ->put(route('memories.update', $memory), [
                'memory_date' => '2024-01-15',
                'description' => 'Hacked description'
            ]);

        // Assert
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_memory()
    {
        // Arrange
        $memory = Memory::factory()->create([
            'user_id' => $this->user->id,
            'image_path' => 'memories/test.jpg'
        ]);

        $this->storageService->expects($this->once())
            ->method('deleteFile')
            ->with('memories/test.jpg');

        // Act
        $response = $this->actingAs($this->user)
            ->delete(route('memories.destroy', $memory));

        // Assert
        $response->assertRedirect(route('memories.index'));
        $response->assertSessionHas('success', 'Memory deleted successfully.');
        $this->assertDatabaseMissing('memories', ['id' => $memory->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_memory()
    {
        // Arrange
        $otherUser = User::factory()->create();
        $memory = Memory::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($this->user)
            ->delete(route('memories.destroy', $memory));

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('memories', ['id' => $memory->id]);
    }

    /** @test */
    public function guest_cannot_access_memory_routes()
    {
        $memory = Memory::factory()->create();

        $this->get(route('memories.index'))->assertRedirect(route('login'));
        $this->get(route('memories.create'))->assertRedirect(route('login'));
        $this->post(route('memories.store'))->assertRedirect(route('login'));
        $this->get(route('memories.show', $memory))->assertRedirect(route('login'));
        $this->get(route('memories.edit', $memory))->assertRedirect(route('login'));
        $this->put(route('memories.update', $memory))->assertRedirect(route('login'));
        $this->delete(route('memories.destroy', $memory))->assertRedirect(route('login'));
    }
}