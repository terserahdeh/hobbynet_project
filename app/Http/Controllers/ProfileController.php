<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Relasi hobi milik User. Kalau namanya bukan "hobbies", sesuaikan di sini.
        $hobbies = method_exists($user, 'hobbies') ? $user->hobbies : collect();

        return view('profile.show', [
            'user'       => $user,
            'initial'    => mb_strtoupper(mb_substr(trim((string) $user->name), 0, 1)) ?: '?',
            'hobbies'    => $hobbies,
            'since'      => $user->created_at ? $user->created_at->locale('id')->translatedFormat('F Y') : null,
            'savedCount' => DB::table('starter_pack_bookmarks')->where('user_id', $user->id)->count(),
            // Lencana akan terbuka setelah fitur proyek terpandu selesai.
            'badges'     => [
                ['name' => 'Langkah Pertama',     'desc' => 'Selesaikan proyek pertamamu.', 'icon' => 'sprout',  'tone' => 'sage', 'earned' => false],
                ['name' => 'Penjelajah Penasaran', 'desc' => 'Coba 2 hobi yang berbeda.',    'icon' => 'compass', 'tone' => 'blue', 'earned' => false],
            ],
        ]);
    }
}
