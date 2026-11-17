<?php

namespace App\View\Composers;

use App\Services\DiscoveryService;
use Illuminate\View\View;

/** Menyiapkan $moreItems untuk partial "partials.other-hobbies". Isinya dari DiscoveryService. */
class OtherHobbiesComposer
{
    public function __construct(private DiscoveryService $discovery)
    {
    }

    public function compose(View $view): void
    {
        $user = auth()->user();

        $view->with('moreItems', $user ? $this->discovery->others($user) : collect());
    }
}
