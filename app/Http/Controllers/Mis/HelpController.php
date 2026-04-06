<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('mis.help.index');
    }
}
