<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index()
    {
        return PermissionResource::collection(config('roles.models.permission')::with('users', 'roles')->get());
    }

    public function show($permission_id)
    {
        return new PermissionResource(config('roles.models.permission')::with('users', 'roles')->where('id', '=', $permission_id)->firstOrFail());
    }
}
