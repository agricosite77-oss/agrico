<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $faqs = FAQ::where('status', 'Active')->orderBy('order')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $faqs,
        ]);
    }
}
