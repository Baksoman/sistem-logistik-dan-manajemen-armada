<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuoteRequestMail;

class QuoteRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'needs' => 'required|string|max:1000',
        ]);

        Mail::to('c14240085@john.petra.ac.id')->send(new QuoteRequestMail($validated));

        return response()->json(['success' => true, 'message' => 'Request berhasil dikirim!']);
    }
}
