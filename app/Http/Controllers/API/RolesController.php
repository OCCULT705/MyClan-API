<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        return RoleResource::collection(config('roles.models.role')::with(['users' => function($query){
            $query->with('details');
        }, 'permissions'])->get());
    }

    public function show($role_id)
    {
        return new RoleResource(config('roles.models.role')::with(['users' => function($query){
            $query->with('details');
        }, 'permissions'])->where('id', '=', $role_id)->firstOrFail());
    }

    public function store(StoreRoleRequest $request)
    {
        try{
            DB::beginTransaction();
            $newRole = config('roles.models.role')::create([
                'name'          => $request['name'],
                'slug'          => str_slug($request['name'], '.'),
                'description'   => $request['description'],
                'level'         => $request['level'],
            ]);
            foreach ($request->permissions as $permission_id) {
                $permission = config('roles.models.permission')::where('id', '=', $permission_id)->first();
                $newRole->attachPermission($permission);
            }
            DB::commit();
            return $this->show($newRole->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function update(UpdateRoleRequest $request, $role_id)
    {
        $role = config('roles.models.role')::find($role_id);
        $role->name = $request->name;
        $role->slug = str_slug($request->name, '.');
        $role->description = $request->description;
        $role->level = $request->level;
        $role->save();
        return $this->show($role->id);
    }

    public function add_permissions(Request $request, $role_id){
        $validated = $request->validate([
            'permissions' => 'required|array',
        ]);
        $role = config('roles.models.role')::find($role_id);
        try{
            DB::beginTransaction();
            foreach ($request->permissions as $permission_id) {
                $permission = config('roles.models.permission')::where('id', '=', $permission_id)->first();
                $role->attachPermission($permission);
            }
            DB::commit();
            return $this->show($role->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function remove_permissions(Request $request, $role_id){
        $validated = $request->validate([
            'permissions' => 'required|array',
        ]);
        $role = config('roles.models.role')::find($role_id);
        try{
            DB::beginTransaction();
            foreach ($request->permissions as $permission_id) {
                $permission = config('roles.models.permission')::where('id', '=', $permission_id)->first();
                $role->detachPermission($permission);
            }
            DB::commit();
            return $this->show($role->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function remove_all_permissions($role_id)
    {
        $role = config('roles.models.role')::find($role_id);
        $role->detachAllPermissions();
        return $this->show($role->id);
    }

    public function destroy($role_id){
        $role = config('roles.models.role')::find($role_id);
        $role->detachAllPermissions();
        return $role->delete();
    }
}
