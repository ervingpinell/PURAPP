<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Tour;

class TourDataController extends Controller
{
    public function schedules(Tour $tour)
    {
        $data = $tour->schedules()
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->orderBy('start_time')
            ->get(['schedules.schedule_id','schedules.start_time','schedules.end_time'])
            ->map(fn($s)=>[
                'schedule_id' => (int)$s->schedule_id,
                'start_time'  => (string)$s->start_time,
                'end_time'    => (string)$s->end_time,
            ]);

        return response()->json($data);
    }

    public function languages(Tour $tour)
    {
        $data = $tour->languages()
            ->orderBy('name')
            ->get(['tour_languages.tour_language_id','tour_languages.name'])
            ->map(fn($l)=>[
                'tour_language_id' => (int)$l->tour_language_id,
                'name'             => (string)$l->name,
            ]);

        return response()->json($data);
    }

    public function categories(Tour $tour)
    {
        $data = $tour->prices()
            ->where('is_active', true)
            ->with('category:category_id,name')
            ->orderBy('category_id')
            ->get(['category_id','price','min_quantity','max_quantity','is_active'])
            ->map(fn($p)=>[
                'id'        => (int)$p->category_id,
                'name'      => (string)optional($p->category)->name,
                'price'     => (float)$p->price,
                'min'       => (int)$p->min_quantity,
                'max'       => (int)$p->max_quantity,
                'is_active' => (bool)$p->is_active,
            ]);

        return response()->json($data);
    }
}
