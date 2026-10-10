<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestActionController extends Controller
{
    /**
     * Resolve the authenticated admin's formatted ID.
     */
    protected function getAdminId(): string
    {
        return Admin::currentAdminId();
    }

    /**
     * Approve a user request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $result = UserRequest::approveRequest($id, $this->getAdminId());
        return response()->json($result, $result['status_code']);
    }

    /**
     * Disapprove a user request.
     */
    public function disapprove(Request $request, int $id): JsonResponse
    {
        $result = UserRequest::disapproveRequest($id, $this->getAdminId());
        return response()->json($result, $result['status_code']);
    }
}
