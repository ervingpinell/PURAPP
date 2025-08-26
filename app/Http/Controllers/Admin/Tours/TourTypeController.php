<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use App\Models\TourType;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\TourType\StoreTourTypeRequest;
use App\Http\Requests\Tour\TourType\UpdateTourTypeRequest;
use App\Http\Requests\Tour\TourType\ToggleTourTypeRequest;

class TourTypeController extends Controller
{
    protected string $controller = 'TourTypeController';

    public function index()
    {
        $tourTypes = TourType::orderByDesc('created_at')->get();
        return view('admin.tourtypes.index', compact('tourTypes'));
    }

    public function store(StoreTourTypeRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tourType = TourType::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'duration'    => $data['duration'] ?? null,
                'is_active'   => true,
            ]);

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'tourtypes.created_success');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_type', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'tourtypes.unexpected_error')
                ->withInput();
        }
    }

    public function update(UpdateTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tourType->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'duration'    => $data['duration'] ?? null,
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'tourtypes.updated_success');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'tourtypes.unexpected_error')
                ->withInput()
                ->with('edit_modal', $tourType->getKey());
        }
    }

    public function toggle(ToggleTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $tourType->is_active = ! $tourType->is_active;
            $tourType->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $tourType->getKey(), [
                'is_active' => $tourType->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $key = $tourType->is_active ? 'tourtypes.activated_success' : 'tourtypes.deactivated_success';

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', $key);

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'tourtypes.unexpected_error');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $tourType = TourType::findOrFail($id);

        try {
            $tourType->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_type', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'tourtypes.deleted_success');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_type', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'tourtypes.in_use_error');
        }
    }
}
