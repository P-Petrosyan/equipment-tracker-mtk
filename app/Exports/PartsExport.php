<?php

namespace App\Exports;

use App\Models\Part;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartsExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        $parts = Part::all();
        return view('exports.parts', compact('parts'));
    }
}