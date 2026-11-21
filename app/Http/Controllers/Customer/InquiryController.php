<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower($request->user()->email)])
            ->latest()
            ->paginate(10);

        return view('customer.inquiries.index', [
            'inquiries' => $inquiries,
        ]);
    }
}
