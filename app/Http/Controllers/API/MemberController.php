<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeParentsRequest;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Rules\OppositeGender;
use App\Rules\RelatedTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index()
    {
        return MemberResource::collection(Member::withAllRelations()->orderBy('birth','ASC')->get());
    }

    public function show($member_id)
    {
        return new MemberResource(Member::withAllRelations()->where('id', '=', $member_id)->firstOrFail());
    }

    public function total_alive(){
        return Member::alive()->count();
    }

    public function total_registered(){
        return Member::count();
    }

    public function filter(Request $request)
    {
        return MemberResource::collection(Member::withAllRelations()->filterBy($request->all())->get());
    }

    public function store(StoreMemberRequest $request){
        try {
            DB::beginTransaction();
            $member = new Member;
            $member->firstname = $request->firstname;
            $member->middlename = $request->middlename;
            $member->lastname = $request->lastname;
            $member->givenname = $request->givenname;
            $member->gender = $request->gender;
            $member->birth = $request->birth;
            $member->death = $request->death;
            $member->address = $request->address;
            $member->save();
            if($request->father !== null && $request->mother !== null){
                $father = Member::find($request->father);
                $mother = Member::find($request->mother);
                $member->parents()->attach([
                    $father->id => ['relationship' => 'Father'],
                    $mother->id => ['relationship' => 'Mother']
                ]);
            }
            if($request->spouse !== null){
                $partner = Member::find($request->spouse);
                if($request->gender == "M"){
                    $member->spouses()->attach($partner->id, ['relationship' => 'Wife']);
                    $partner->spouses()->attach($member->id, ['relationship' => 'Husband']);
                }else{
                    $member->spouses()->attach($partner->id, ['relationship' => 'Husband']);
                    $partner->spouses()->attach($member->id, ['relationship' => 'Wife']);
                }
            }
            DB::commit();
            return $this->show($member->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function update(UpdateMemberRequest $request, $member_id){
        $member = Member::findOrFail($member_id);
        $member->firstname = $request->firstname;
        $member->middlename = $request->middlename;
        $member->lastname = $request->lastname;
        $member->givenname = $request->givenname;
        $member->gender = $request->gender;
        $member->birth = $request->birth;
        $member->death = $request->death;
        $member->address = $request->address;
        $member->save();
        return $this->show($member->id);
    }

    public function change_parents(ChangeParentsRequest $request, $member_id){
        $member = Member::findOrFail($member_id);
        try {
            DB::beginTransaction();
            DB::table('ascendants')->updateOrInsert(
                ['member_id' => $member->id, 'relationship' => 'Father'],
                ['ascendant_id' => $request->father]
            );
            DB::table('ascendants')->updateOrInsert(
                ['member_id' => $member->id, 'relationship' => 'Mother'],
                ['ascendant_id' => $request->mother]
            );
            DB::commit();
            return $this->show($member->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function add_spouse(Request $request, $member_id){
        $member = Member::withAllRelations()->where('id', '=', $member_id)->firstOrFail();
        $father = ($member->father->first()->id ?? null);
        $mother = ($member->mother->first()->id ?? null);
        $validated = $request->validate([
            'spouse' => [
                'bail','required','string','max:100','exists:App\Models\Member,id',
                new RelatedTo($father, $mother, true),
                new OppositeGender($member->gender)
            ]
        ]);
        try{
            DB::beginTransaction();
            if($member->gender == "M"){
                DB::table('spouses')->updateOrInsert(['member_id' => $member->id, 'partner_id' => $validated['spouse']], ['relationship' => 'Wife']);
                DB::table('spouses')->updateOrInsert(['member_id' => $validated['spouse'], 'partner_id' => $member->id], ['relationship' => 'Husband']);
            }else{
                DB::table('spouses')->updateOrInsert(['member_id' => $member->id, 'partner_id' => $validated['spouse']], ['relationship' => 'Husband']);
                DB::table('spouses')->updateOrInsert(['member_id' => $validated['spouse'], 'partner_id' => $member->id], ['relationship' => 'Wife']);
            }
            DB::commit();
            return $this->show($member->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function remove_spouse(Request $request, $member_id, $spouse_id){
        $member = Member::findOrFail($member_id);
        $spouse = Member::findOrFail($spouse_id);
        try{
            DB::beginTransaction();
            $member->spouses()->detach($spouse->id);
            $spouse->spouses()->detach($member->id);
            DB::commit();
            return $this->show($member->id);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response($exp->getMessage(), 400);
        }
    }

    public function destroy($member_id){
        $member = Member::findOrFail($member_id);
        return $member->delete();
    }
}
