<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BirthdaySurprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BirthdaySurpriseTest extends TestCase
{
    use RefreshDatabase;

    protected $sender;
    protected $receiver;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->sender = User::factory()->create([
            'name' => 'Azvi',
            'email' => 'azvi@test.com'
        ]);
        
        $this->receiver = User::factory()->create([
            'name' => 'Khairun', 
            'email' => 'khairun@test.com'
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_birthday_surprises_index()
    {
        // Arrange: Login as sender
        $this->actingAs($this->sender);
        
        // Create some test surprises
        $receivedSurprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->receiver->id,
            'receiver_user_id' => $this->sender->id,
            'reveal_at' => now()->addDays(5)
        ]);
        
        $sentSurprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->addDays(10)
        ]);

        // Act: Visit index page
        $response = $this->get(route('birthday-surprises.index'));

        // Assert: Page loads successfully and shows surprises
        $response->assertStatus(200);
        $response->assertViewIs('birthday-surprises.index');
        $response->assertViewHas('receivedSurprises');
        $response->assertViewHas('sentSurprises');
    }

    /** @test */
    public function guest_cannot_access_birthday_surprises()
    {
        // Act: Try to access without login
        $response = $this->get(route('birthday-surprises.index'));

        // Assert: Redirected to login
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_create_form()
    {
        // Arrange: Login as sender
        $this->actingAs($this->sender);

        // Act: Visit create page
        $response = $this->get(route('birthday-surprises.create'));

        // Assert: Page loads with other user data
        $response->assertStatus(200);
        $response->assertViewIs('birthday-surprises.create');
        $response->assertViewHas('otherUser', $this->receiver);
    }

    /** @test */
    public function user_can_create_birthday_surprise_with_valid_data()
    {
        // Arrange: Login and prepare data
        $this->actingAs($this->sender);
        
        $surpriseData = [
            'receiver_user_id' => $this->receiver->id,
            'content_type' => 'message',
            'content_payload' => 'Happy Birthday! 🎉',
            'reveal_at' => now()->addDays(7)->format('Y-m-d H:i:s')
        ];

        // Act: Submit create form
        $response = $this->post(route('birthday-surprises.store'), $surpriseData);

        // Assert: Surprise created successfully
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('birthday_surprises', [
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'content_type' => 'message',
            'content_payload' => 'Happy Birthday! 🎉'
        ]);
    }

    /** @test */
    public function user_can_create_surprise_with_image_upload()
    {
        // Skip test jika GD tidak tersedia
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }
        
        // Arrange: Setup storage and login
        Storage::fake('public');
        $this->actingAs($this->sender);
        
        $image = UploadedFile::fake()->image('birthday.jpg', 800, 600);
        
        $surpriseData = [
            'receiver_user_id' => $this->receiver->id,
            'content_type' => 'image',
            'content_payload' => 'Special birthday image!',
            'reveal_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'image' => $image
        ];

        // Act: Submit with image
        $response = $this->post(route('birthday-surprises.store'), $surpriseData);

        // Assert: Success and file stored
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('success');
        
        $surprise = BirthdaySurprise::where('sender_user_id', $this->sender->id)->first();
        $this->assertNotNull($surprise->content);
        Storage::disk('public')->assertExists($surprise->content);
    }

    /** @test */
    public function create_surprise_validation_fails_with_invalid_data()
    {
        // Arrange: Login
        $this->actingAs($this->sender);
        
        $invalidData = [
            'receiver_user_id' => $this->receiver->id, // ✅ PERBAIKAN: Gunakan receiver yang valid
            'content_type' => 'invalid_type',
            'content_payload' => '', 
            'reveal_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ];
    
        // Act: Submit invalid data
        $response = $this->post(route('birthday-surprises.store'), $invalidData);
    
        // Assert: Validation errors (tanpa receiver_user_id karena sekarang valid)
        $response->assertSessionHasErrors([
            'content_type', 
            'content_payload', // Will fail 'min:1' validation
            'reveal_at'
        ]);
    }

    /** @test */
    public function only_sender_can_edit_surprise()
    {
        // Arrange: Create surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->addDays(5)
        ]);

        // Act & Assert: Sender can access edit
        $this->actingAs($this->sender);
        $response = $this->get(route('birthday-surprises.edit', $surprise));
        $response->assertStatus(200);
        
        // Act & Assert: Receiver cannot access edit
        $this->actingAs($this->receiver);
        $response = $this->get(route('birthday-surprises.edit', $surprise));
        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_edit_revealed_surprise()
    {
        // Arrange: Create revealed surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->subDays(1),
            'is_revealed' => true
        ]);

        // Act: Try to edit revealed surprise
        $this->actingAs($this->sender);
        $response = $this->get(route('birthday-surprises.edit', $surprise));

        // Assert: Redirected with error
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function sender_can_update_unrevealed_surprise()
    {
        // Arrange: Create surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'content_payload' => 'Original message',
            'reveal_at' => now()->addDays(5)
        ]);
        
        $updateData = [
            'content_type' => 'message',
            'content_payload' => 'Updated birthday message!',
            'reveal_at' => now()->addDays(10)->format('Y-m-d H:i:s')
        ];

        // Act: Update surprise
        $this->actingAs($this->sender);
        $response = $this->put(route('birthday-surprises.update', $surprise), $updateData);

        // Assert: Updated successfully
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('success');
        
        $surprise->refresh();
        $this->assertEquals('Updated birthday message!', $surprise->content_payload);
    }

    /** @test */
    public function receiver_can_view_surprise_when_time_comes()
    {
        // Arrange: Create surprise that can be revealed
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->subHours(1), // Past time - can be revealed
            'is_revealed' => false
        ]);

        // Act: Receiver views surprise
        $this->actingAs($this->receiver);
        $response = $this->get(route('birthday-surprises.show', $surprise));

        // Assert: Can view and status updated
        $response->assertStatus(200);
        $response->assertViewIs('birthday-surprises.show');
        
        $surprise->refresh();
        $this->assertTrue($surprise->is_revealed);
    }

    /** @test */
    public function receiver_cannot_view_surprise_before_time()
    {
        // Arrange: Create future surprise (in production)
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->addDays(5), // Future time
            'is_revealed' => false
        ]);
        
        // Mock production environment
        config(['app.env' => 'production']);

        // Act: Receiver tries to view early
        $this->actingAs($this->receiver);
        $response = $this->get(route('birthday-surprises.show', $surprise));

        // Assert: Redirected with error
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function sender_can_always_view_their_surprise()
    {
        // Arrange: Create future surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'reveal_at' => now()->addDays(5)
        ]);

        // Act: Sender views their own surprise
        $this->actingAs($this->sender);
        $response = $this->get(route('birthday-surprises.show', $surprise));

        // Assert: Can view anytime
        $response->assertStatus(200);
        $response->assertViewIs('birthday-surprises.show');
    }

    /** @test */
    public function unauthorized_user_can_not_view_surprise()
    {
        // Arrange: Create surprise between two users
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id
        ]);
        
        $thirdUser = User::factory()->create();

        // Act: Third user tries to view
        $this->actingAs($thirdUser);
        $response = $this->get(route('birthday-surprises.show', $surprise));

        // Assert: Access denied
        $response->assertStatus(403);
    }

    /** @test */
    public function only_sender_can_delete_surprise()
    {
        // Arrange: Create surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id
        ]);

        // Act & Assert: Receiver cannot delete
        $this->actingAs($this->receiver);
        $response = $this->delete(route('birthday-surprises.destroy', $surprise));
        $response->assertStatus(403);
        
        // Act & Assert: Sender can delete
        $this->actingAs($this->sender);
        $response = $this->delete(route('birthday-surprises.destroy', $surprise));
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('birthday_surprises', ['id' => $surprise->id]);
    }

    /** @test */
    public function cannot_delete_revealed_surprise()
    {
        // Arrange: Create revealed surprise
        $surprise = BirthdaySurprise::factory()->create([
            'sender_user_id' => $this->sender->id,
            'receiver_user_id' => $this->receiver->id,
            'is_revealed' => true
        ]);

        // Act: Try to delete revealed surprise
        $this->actingAs($this->sender);
        $response = $this->delete(route('birthday-surprises.destroy', $surprise));

        // Assert: Cannot delete
        $response->assertRedirect(route('birthday-surprises.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('birthday_surprises', ['id' => $surprise->id]);
    }
    
    /** @test */
    public function cannot_create_surprise_for_self()
    {
        // Arrange: Login
        $this->actingAs($this->sender);
        
        $invalidData = [
            'receiver_user_id' => $this->sender->id, // ❌ Sama dengan pengirim
            'content_type' => 'message',
            'content_payload' => 'Valid message', 
            'reveal_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ];
    
        // Act: Submit invalid data
        $response = $this->post(route('birthday-surprises.store'), $invalidData);
    
        // Assert: Validation error untuk receiver_user_id
        $response->assertSessionHasErrors(['receiver_user_id']);
    }
}