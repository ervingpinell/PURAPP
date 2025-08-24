<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Tour;
use App\Models\TourAvailability;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\TourAvailability\StoreTourAvailabilityRequest;
use App\Http\Requests\Tour\TourAvailability\UpdateTourAvailabilityRequest;

class TourAvailabilityController extends Controller
{
    protected string $controller = 'TourAvailabilityController';

    public function index()
    {
        $availabilityPage = TourAvailability::with('tour')->orderByDesc('date')->paginate(10);

        return view('admin.tours.availabilities.index', [
            'availabilities' => $availabilityPage,
        ]);
    }

    public function create()
    {
        $tourList = Tour::orderBy('name')->get();

        return view('admin.tours.availabilities.create', [
            'tourList' => $tourList,
        ]);
    }

    public function store(StoreTourAvailabilityRequest $request)
    {
        try {
            $data = $request->validated();

            $payload = [
                'tour_id'      => $data['tour_id'],
                'date'         => $data['date'],
                'start_time'   => $data['start_time'] ?? null,
                'end_time'     => $data['end_time'] ?? null,
                'is_available' => $request->boolean('available', true),
                'is_active'    => $request->boolean('is_active', true),
            ];

            $availability = TourAvailability::create($payload);

            LoggerHelper::mutated($this->controller, 'store', 'tour_availability', $availability->getKey(), [
                'tour_id' => $availability->tour_id,
                'date'    => $availability->date,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.availabilities.index')
                ->with('success', 'Availability created successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'There was a problem creating the availability.');
        }
    }

    public function edit(TourAvailability $availability)
    {
        $tourList = Tour::orderBy('name')->get();

        return view('admin.tours.availabilities.edit', [
            'availability' => $availability,
            'tourList'     => $tourList,
        ]);
    }

    public function update(UpdateTourAvailabilityRequest $request, TourAvailability $availability)
    {
        try {
            $data = $request->validated();

            $payload = [
                'tour_id'      => $data['tour_id'],
                'date'         => $data['date'],
                'start_time'   => $data['start_time'] ?? null,
                'end_time'     => $data['end_time'] ?? null,
                'is_available' => $request->has('available')
                    ? $request->boolean('available')
                    : $availability->is_available,
                'is_active'    => $request->has('is_active')
                    ? $request->boolean('is_active')
                    : $availability->is_active,
            ];

            $availability->update($payload);

            LoggerHelper::mutated($this->controller, 'update', 'tour_availability', $availability->getKey(), [
                'tour_id' => $availability->tour_id,
                'date'    => $availability->date,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.availabilities.index')
                ->with('success', 'Availability updated successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'There was a problem updating the availability.');
        }
    }

    public function destroy(TourAvailability $availability)
    {
        try {
            $availability->update(['is_active' => false]);

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_availability', $availability->getKey(), [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.availabilities.index')
                ->with('success', 'Availability deactivated successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'There was a problem deactivating the availability.');
        }
    }
}
