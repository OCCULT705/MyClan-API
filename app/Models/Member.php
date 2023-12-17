<?php

namespace App\Models;

use App\Filters\FilterBuilder;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Member extends Model
{
    use HasFactory, Uuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'givenname',
        'gender',
        'birth',
        'death',
        'address',
    ];

    /**
     * Get the clan member's full name.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullnameAttribute($value)
    {
        if($this->middlename !== "") return "{$this->firstname} {$this->middlename} {$this->lastname}";
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Get the clan member's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getFirstnameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the clan member's middle name.
     *
     * @param  string  $value
     * @return string
     */
    public function getMiddlenameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the clan member's last name.
     *
     * @param  string  $value
     * @return string
     */
    public function getLastnameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the clan member's given name.
     *
     * @param  string  $value
     * @return string
     */
    public function getGivennameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the clan member's gender.
     *
     * @param  string  $value
     * @return string
     */
    public function getGenderAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the clan member's age.
     *
     * @param  Date  $value
     * @return string|null
     */
    public function getAgeAttribute($value)
    {
        if($this->death == null){
            $age = Carbon::parse($this->birth)->diff(Carbon::now())->format('%y Years');
            if(intval($age) > 0){
                if(intval($age) == 1){
                    return Carbon::parse($this->birth)->diff(Carbon::now())->format('%y Year');
                }
                return $age;
            }else{
                $age = Carbon::parse($this->birth)->diff(Carbon::now())->format('%m Months');
                if(intval($age) > 0){
                    if(intval($age) == 1){
                        return Carbon::parse($this->birth)->diff(Carbon::now())->format('%m Month');
                    }
                    return $age;
                }else{
                    $age = Carbon::parse($this->birth)->diff(Carbon::now())->format('%d Days');
                    if(intval($age) == 1){
                        return Carbon::parse($this->birth)->diff(Carbon::now())->format('%d Day');
                    }
                    return $age;
                }
            }
        }
        return null;
    }

    /**
     * Scope a query to only include alive clan members.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAlive($query)
    {
        return $query->where('death', '=', null);
    }

    /**
     * Scope a query to only include deceased clan members.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeceased($query)
    {
        return $query->where('death', '<>', null);
    }

    /**
     * Filter clan members by given Parameters.
     *
     * @return mixed
     */
    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Filters\MemberFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);
        return $filter->apply();
    }

    /**
     * Scope a query to only include all relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllRelations($query)
    {
        return $query->with('credentials','pictures','father','mother','spouses','children');
    }

    /**
     * Get the credentials of the clan member.
     */
    public function credentials()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    /**
     * Get the pictures associated with the clan member.
     */
    public function pictures()
    {
        return $this->hasMany(Picture::class, 'member_id', 'id');
    }

    /**
     * The parents of the clan member.
     */
    public function parents()
    {
        return $this->belongsToMany(Member::class, 'ascendants', 'member_id', 'ascendant_id');
    }

    /**
     * The father of the clan member.
     */
    public function father()
    {
        return $this->belongsToMany(Member::class, 'ascendants', 'member_id', 'ascendant_id')
                    ->withPivot('relationship')
                    ->wherePivot('relationship', '=', 'Father');
    }

    /**
     * The mother of the clan member.
     */
    public function mother()
    {
        return $this->belongsToMany(Member::class, 'ascendants', 'member_id', 'ascendant_id')
                    ->withPivot('relationship')
                    ->wherePivot('relationship', '=', 'Mother');
    }

    /**
     * The spouses of the clan member.
     */
    public function spouses()
    {
        return $this->belongsToMany(Member::class, 'spouses', 'member_id', 'partner_id')
                    ->withPivot('relationship');
    }

    /**
     * The children of the clan member.
     */
    public function children()
    {
        return $this->belongsToMany(Member::class, 'ascendants', 'ascendant_id', 'member_id');
    }

    /**
     * Hook to the fired events in each member's lifecycle.
     */
    protected static function booted(){
        static::deleting(function($member){
            $member->pictures()->each(function($picture){
                $picture->delete();
            });
            $member->parents()->detach();
            $member->spouses()->detach();
            $member->children()->detach();
        });
    }
}
