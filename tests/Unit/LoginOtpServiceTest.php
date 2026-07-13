<?php

namespace Tests\Unit;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\VerifyOTP;
use App\Services\LoginOtpService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class LoginOtpServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        DB::purge('sqlite');

        Schema::create('verify_o_t_p_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email');
            $table->string('otp');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('verify_o_t_p_s');
        Schema::dropIfExists('users');
        DB::purge('sqlite');

        parent::tearDown();
    }

    public function test_rapid_duplicate_issue_reuses_code_and_sends_one_email(): void
    {
        Mail::fake();
        $user = $this->user();
        $service = new LoginOtpService();

        $firstCode = $service->issue($user);
        $secondCode = $service->issue($user);

        $this->assertSame($firstCode, $secondCode);
        $this->assertSame(1, VerifyOTP::where('user_id', $user->id)->count());
        Mail::assertSent(OtpMail::class, 1);
    }

    public function test_issue_after_cooldown_creates_and_emails_a_new_code(): void
    {
        Mail::fake();
        $user = $this->user();
        $oldOtp = new VerifyOTP();
        $oldOtp->user_id = $user->id;
        $oldOtp->email = $user->email;
        $oldOtp->otp = 1234;
        $oldOtp->save();
        VerifyOTP::whereKey($oldOtp->id)->update([
            'created_at' => now()->subSeconds(61),
        ]);

        (new LoginOtpService())->issue($user);

        $this->assertSame(2, VerifyOTP::where('user_id', $user->id)->count());
        Mail::assertSent(OtpMail::class, 1);
    }

    public function test_mail_failure_removes_the_new_otp(): void
    {
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('mail failed'));

        try {
            (new LoginOtpService())->issue($this->user());
            $this->fail('Expected mail delivery to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('mail failed', $exception->getMessage());
        }

        $this->assertSame(0, VerifyOTP::withTrashed()->count());
    }

    private function user(): User
    {
        DB::table('users')->insert([
            'id' => 42,
            'email' => 'member@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail(42);
    }
}
