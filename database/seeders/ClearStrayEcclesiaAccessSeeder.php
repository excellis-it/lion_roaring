<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Client testing 2026-08, item US #18: members without an ECCLESIA role were carrying a
 * House of Ecclesia *access* value (users.manage_ecclesia). That made them visible to
 * ECCLESIA admins of houses they do not belong to — e.g. irenesubowo@gmail.com, whose house
 * is Lion Roaring PMA, appearing in the Grace International admin's All Members list.
 *
 * Their own house (users.ecclesia_id) is left untouched.
 */
class ClearStrayEcclesiaAccessSeeder extends Seeder
{
    public function run(): void
    {
        $strays = User::whereNotNull('manage_ecclesia')
            ->where('manage_ecclesia', '!=', '')
            ->where(function ($q) {
                $q->whereNull('is_ecclesia_admin')->orWhere('is_ecclesia_admin', '!=', 1);
            })
            ->get();

        foreach ($strays as $user) {
            $this->command?->warn(sprintf(
                'Clearing access "%s" from %s (role %s, house %s)',
                $user->manage_ecclesia,
                $user->email,
                optional($user->userRole)->name ?? '-',
                optional($user->ecclesia)->name ?? '(none)'
            ));

            $user->manage_ecclesia = null;
            $user->save();
        }

        $this->command?->info('Cleared stray House of Ecclesia access from ' . $strays->count() . ' member(s).');
    }
}
