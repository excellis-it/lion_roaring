<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatAttachmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function uploading_chat_attachment_sets_attachment_name()
    {
        Storage::fake('public');

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender, 'web');

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->post('/user/chats/send', [
            'sender_id' => $sender->id,
            'reciver_id' => $receiver->id,
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('chats', [
            'sender_id' => $sender->id,
            'reciver_id' => $receiver->id,
            'attachment_name' => 'photo.jpg',
        ]);
    }
}
