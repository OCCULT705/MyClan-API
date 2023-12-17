<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventsController extends Controller
{
    public function index(){
        return [
            'birthday' => [
                'tomorrow' => $this->birth('tomorrow'),
                'today' => $this->birth('today'),
                'yesterday' => $this->birth('yesterday')
            ],
            'commemoration' => [
                'tomorrow' => $this->death('tomorrow'),
                'today' => $this->death('today'),
                'yesterday' => $this->death('yesterday')
            ]
        ];
    }

    public function calendar(Request $request){
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = 'Y-m-d';
        $start = (Carbon::canBeCreatedFromFormat($startDate, $format)) ? Carbon::createFromFormat($format, $startDate) : now()->subDays(14);
        $end = (Carbon::canBeCreatedFromFormat($endDate, $format)) ? Carbon::createFromFormat($format, $endDate) : now()->addDays(14);
        return [
            [
                'event' => 'birthday',
                'members' => MemberResource::collection(Member::withAllRelations()->where(function($query) use($start, $end){
                            $query->whereMonth("birth", ">=", $start->month)->WhereDay("birth", ">=", $start->day)
                                ->whereMonth("birth", "<=", $end->month)->WhereDay("birth", "<=", $end->day);
                        })->get())
            ],
            [
                'event' => 'commemoration',
                'members' => MemberResource::collection(Member::withAllRelations()->where(function($query) use($start, $end){
                            $query->whereMonth("death", ">=", $start->month)->WhereDay("death", ">=", $start->day)
                                ->whereMonth("death", "<=", $end->month)->WhereDay("death", "<=", $end->day);
                        })->get())
            ]
        ];
    }

    public function birth($day){
        $eventday = now();
        switch ($day) {
            case 'tomorrow':
                $eventday = now()->addDay();
                break;

            case 'today':
                $eventday = now();
                break;

            case 'yesterday':
                $eventday = now()->subDay();
                break;

            default:
                # code...
                break;
        }
        return MemberResource::collection(Member::whereMonth("birth", $eventday->month)->WhereDay("birth", $eventday->day)->get());
    }

    public function death($day){
        $eventday = now();
        switch ($day) {
            case 'tomorrow':
                $eventday = now()->addDay();
                break;

            case 'today':
                $eventday = now();
                break;

            case 'yesterday':
                $eventday = now()->subDay();
                break;

            default:
                # code...
                break;
        }
        return MemberResource::collection(Member::whereMonth("death", $eventday->month)->WhereDay("death", $eventday->day)->get());
    }
}
