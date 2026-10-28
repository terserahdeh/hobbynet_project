<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/** Halaman publik "Model Bisnis": ringkasan monetisasi + simulator pendapatan. */
class BusinessController extends Controller
{
    public function __invoke(): View
    {
        return view('business.index');
    }
}
