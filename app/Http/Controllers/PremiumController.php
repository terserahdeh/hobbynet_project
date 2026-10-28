<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Services\BadgeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * HobbyNet Plus. Pembayaran SENGAJA disimulasikan untuk demo kuliah:
 * di produksi, aksi subscribe() diganti webhook payment gateway (Midtrans / Xendit)
 * yang mengisi plus_until setelah pembayaran berhasil.
 */
class PremiumController extends Controller
{
    public const PLANS = [
        'monthly' => ['label' => 'Bulanan', 'price' => 29000, 'months' => 1],
        'yearly'  => ['label' => 'Tahunan', 'price' => 249000, 'months' => 12],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('premium.index', [
            'plans' => self::PLANS,
            'user' => $user,
            'isPlus' => $user?->isPlus() ?? false,
            'yearlySaving' => (int) round((1 - self::PLANS['yearly']['price'] / (self::PLANS['monthly']['price'] * 12)) * 100),
        ]);
    }

    public function subscribe(Request $request, BadgeService $badges): RedirectResponse
    {
        $data = $request->validate(['plan' => ['required', 'in:monthly,yearly']]);
        $plan = self::PLANS[$data['plan']];
        $user = $request->user();

        // Perpanjang dari masa aktif yang tersisa, bukan dari hari ini.
        $start = $user->isPlus() ? $user->plus_until : now();

        $user->forceFill([
            'plan' => 'plus',
            'plus_until' => $start->copy()->addMonths($plan['months']),
        ])->save();

        // Lencana apresiasi untuk pelanggan Plus (muncul di profil).
        $badge = Badge::firstOrCreate(
            ['slug' => 'plus-member'],
            ['name' => 'Plus Member', 'description' => 'Mendukung HobbyNet dengan paket Plus.', 'icon' => 'star', 'color' => 'pink']
        );
        $badges->award($user, $badge);

        return redirect()->route('plus')->with('status', 'HobbyNet Plus aktif. Selamat berkarya tanpa batas! (pembayaran demo)');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->user()->forceFill(['plan' => 'free', 'plus_until' => null])->save();

        return redirect()->route('plus')->with('status', 'Langgananmu dihentikan. Kamu kembali ke paket gratis.');
    }
}
