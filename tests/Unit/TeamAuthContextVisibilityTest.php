<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TeamAuthContextVisibilityTest extends TestCase
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
                'foreign_key_constraints' => false,
            ],
        ]);
        \Illuminate\Support\Facades\DB::purge('sqlite');

        Schema::create('countries', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('domain')->nullable();
            $table->boolean('is_global')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_types', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('user_type')->nullable();
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teams', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('group_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('team_members', function ($table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Country::create([
            'name' => 'Global',
            'code' => 'GL',
            'domain' => 'https://example.test/global',
            'is_global' => true,
            'status' => true,
        ]);

        Country::create([
            'name' => 'United States',
            'code' => 'US',
            'domain' => 'https://example.test/us',
            'is_global' => false,
            'status' => true,
        ]);

        Country::clearDomainCache();
    }

    public function test_regional_member_group_hidden_on_global_site_for_g_r_user(): void
    {
        $this->setRequestUrl('https://example.test/global/team');

        $grUser = $this->makeUser([
            'user_name' => 'gr_user',
            'email' => 'gr@example.test',
            'user_type' => 'G_R',
            'country' => 2,
        ]);

        $regionalUser = $this->makeUser([
            'user_name' => 'regional_user',
            'email' => 'regional@example.test',
            'user_type' => 'Regional',
            'country' => 2,
        ]);

        $team = Team::create([
            'name' => 'Regional Group',
            'description' => 'Regional only',
        ]);

        TeamMember::create(['team_id' => $team->id, 'user_id' => $grUser->id, 'is_admin' => true]);
        TeamMember::create(['team_id' => $team->id, 'user_id' => $regionalUser->id]);

        Auth::login($grUser);

        $visibleIds = Team::visibleInAuthContext()
            ->whereActiveMember($grUser->id)
            ->pluck('id')
            ->all();

        $this->assertNotContains($team->id, $visibleIds);
    }

    public function test_global_member_group_visible_on_global_site_for_g_r_user(): void
    {
        $this->setRequestUrl('https://example.test/global/team');

        $grUser = $this->makeUser([
            'user_name' => 'gr_user2',
            'email' => 'gr2@example.test',
            'user_type' => 'G_R',
            'country' => 2,
        ]);

        $globalUser = $this->makeUser([
            'user_name' => 'global_user',
            'email' => 'global@example.test',
            'user_type' => 'Global',
            'country' => 1,
        ]);

        $team = Team::create([
            'name' => 'Global Group',
            'description' => 'Global members',
        ]);

        TeamMember::create(['team_id' => $team->id, 'user_id' => $grUser->id, 'is_admin' => true]);
        TeamMember::create(['team_id' => $team->id, 'user_id' => $globalUser->id]);

        Auth::login($grUser);

        $visibleIds = Team::visibleInAuthContext()
            ->whereActiveMember($grUser->id)
            ->pluck('id')
            ->all();

        $this->assertContains($team->id, $visibleIds);
    }

    private function makeUser(array $attributes): User
    {
        $user = User::create([
            'user_name' => $attributes['user_name'],
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $attributes['email'],
            'password' => bcrypt('secret'),
            'country' => $attributes['country'] ?? null,
            'status' => 1,
        ]);

        $user->forceFill([
            'user_type' => $attributes['user_type'],
        ])->save();

        return $user->fresh();
    }

    private function setRequestUrl(string $url): void
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $server = [
            'HTTP_HOST' => $parsed['host'] ?? 'localhost',
            'SERVER_NAME' => $parsed['host'] ?? 'localhost',
            'REQUEST_URI' => $path,
            'REQUEST_METHOD' => 'GET',
            'HTTPS' => 'on',
        ];

        $request = Request::create($url, 'GET', [], [], [], $server);
        $this->app->instance('request', $request);
        Country::clearDomainCache();
    }
}
