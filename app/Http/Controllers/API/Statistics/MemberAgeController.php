<?php

namespace App\Http\Controllers\API\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberAgeController extends Controller
{
    /**
     * Count alive clan members born between a given range of years ago.
     *
     * @param  string  $from
     * @param  string|null  $to
     * @return integer
     */
    private function countByAgeRange($from, $to = null){
        if($to == null){
            $a = date_format(date_sub(now(), date_interval_create_from_date_string($from)), 'Y-m-d');
            return Member::alive()->whereDate('birth', '<=', $a)->count();
        }
        return Member::alive()->where(function($query) use($from, $to){
            $a = date_format(date_sub(now(), date_interval_create_from_date_string($from)), 'Y-m-d');
            $b = date_format(date_sub(now(), date_interval_create_from_date_string($to)), 'Y-m-d');
            $query->whereDate('birth', '>=', $b)->whereDate('birth', '<=', $a);
        })->count();
    }

    public function age(){
        return [
            'children' => $this->countByAgeRange("0 years", "17 years"),// 0-17 years
            'youths' => $this->countByAgeRange("18 years", "44 years"),// 18-44 years
            'adults' => $this->countByAgeRange("45 years"),// 45+ years
        ];
    }

    public function children(){
        return [
            'neonates' => $this->countByAgeRange("0 months", "2 months"),// 0-2 months
            'infants' => $this->countByAgeRange("3 months", "11 months"),// 3-11 months
            'toddlers' => $this->countByAgeRange("1 years", "3 years"),// 1-3 years
            'preschoolers' => $this->countByAgeRange("4 years", "5 years"),// 4-5 years
            'kids' => $this->countByAgeRange("6 years", "11 years"),// 6-11 years
            'young teens' => $this->countByAgeRange("12 years", "14 years"),// 12-14 years
            'teenagers' => $this->countByAgeRange("15 years", "17 years"),// 15-17 years
        ];
    }

    public function youths(){
        return [
            'early' => $this->countByAgeRange("18 years", "29 years"),// 18-29 years
            'middle' => $this->countByAgeRange("30 years", "39 years"),// 30-39 years
            'late' => $this->countByAgeRange("40 years", "44 years"),// 40-44 years
        ];
    }

    public function adults(){
        return [
            'early' => $this->countByAgeRange("45 years", "49 years"),// 45-49 years
            'middle' => $this->countByAgeRange("50 years", "59 years"),// 50-59 years
            'senior' => $this->countByAgeRange("60 years"),// 60+ years
        ];
    }
}
