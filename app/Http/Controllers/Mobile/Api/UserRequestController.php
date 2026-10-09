<?php

namespace App\Http\Controllers\Mobile\Api;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRequestController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $requests = UserRequest::when($userId, fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data'   => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => ['required', 'string', 'max:100'],
            'notes'        => ['required', 'string'],
        ]);

        $userReq = UserRequest::create([
            'user_id'      => Auth::id(),
            'request_type' => $validated['request_type'],
            'notes'        => $validated['notes'],
            'status'       => 'Pending',
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $userReq,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $userReq = UserRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'in:Pending,Approved,Rejected,Completed'],
            'notes'  => ['sometimes', 'string'],
        ]);

        $userReq->update($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $userReq,
        ]);
    }
}
