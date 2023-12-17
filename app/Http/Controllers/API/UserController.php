<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::withAllRelations()->get());
    }

    public function show($user_id)
    {
        return new UserResource(User::withAllRelations()->where('id', '=', $user_id)->firstOrFail());
    }

    public function total(){
        return User::count();
    }

    public function total_with_details(){
        return User::has('details')->count();
    }

    public function store(Request $request){
        $validated = $request->validate([
            'member_id' => ['required', 'string', 'max:100', 'exists:App\Models\Member,id'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:App\Models\User,email']
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $clanMemberRole = config('roles.models.role')::where('slug', '=', 'clan.member')->firstOrFail();
        try{
            DB::beginTransaction();
            $new_user = new User;
            $new_user->email = $validated['email'];
            $new_user->password = Hash::make('12345678');
            $new_user->save();

            $member->credentials()->associate($new_user);
            $member->save();

            $new_user->attachRole($clanMemberRole);

            DB::commit();
            return $new_user;
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function assign_roles(Request $request, $user_id){
        $validated = $request->validate([
            'roles' => 'required|array',
        ]);
        try{
            DB::beginTransaction();
            $user = config('roles.models.defaultUser')::find($user_id);
            foreach ($request->roles as $role_id) {
                $role = config('roles.models.role')::where('id', '=', $role_id)->first();
                $user->attachRole($role);
            }
            DB::commit();
            return $user;
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function remove_roles(Request $request, $user_id){
        $validated = $request->validate([
            'roles' => 'required|array',
        ]);
        try{
            DB::beginTransaction();
            $user = config('roles.models.defaultUser')::find($user_id);
            foreach ($request->roles as $role_id) {
                $role = config('roles.models.role')::where('id', '=', $role_id)->first();
                $user->detachRole($role);
            }
            DB::commit();
            return $user;
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function change_password(Request $request, $user_id){
        $user = User::findOrFail($user_id);
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|string|min:8|confirmed|different:old_password',
        ]);
        if(Hash::check($validated['old_password'], $user->password)){
            return $user->fill(['password' => Hash::make($validated['password'])])->save();
        }else{
            return response('Invalid account password!', 400);
        }
    }

    public function destroy($user_id){
        $user = User::findOrFail($user_id);
        return $user->delete();
    }

    public function clean_up(){
        return User::doesntHave('details')->delete();
    }
}
