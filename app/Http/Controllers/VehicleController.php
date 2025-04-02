<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Vehicle::with(['user', 'vehicleRegistration']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('plate_number', 'like', "%{$filter}%")
                    ->orWhere('make', 'like', "%{$filter}%")
                    ->orWhere('model', 'like', "%{$filter}%")
                    ->orWhere('color', 'like', "%{$filter}%")
                    ->orWhere('type', 'like', "%{$filter}%");
            });
        }

        if (in_array($sortColumn, ['plate_number', 'make', 'model', 'year', 'type', 'created_at'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $vehicles = $query->paginate($pageSize);
        } else {
            $vehicles = $query->get();
        }

        return $this->success($vehicles);
    }

    /**
     * Store a newly created vehicle.
     */
    public function store(VehicleRequest $request)
    {
        $data = $request->validated();

        if (Vehicle::where('plate_number', $data['plate_number'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The plate number already exists.',
            ]);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5(uniqid() . now()) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/image', $imageName);

            $data['image'] = $imageName;
        }

        $vehicle = Vehicle::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle created successfully.',
            'data' => $vehicle,
        ]);
    }

    /**
     * Display the specified vehicle.
     */
    public function show(int $id)
    {
        $vehicle = Vehicle::with('user')->findOrFail($id);

        return $this->success([
            'status' => 'success',
            'data' => $vehicle,
        ]);
    }

    /**
     * Update the specified vehicle.
     */
    public function update(VehicleRequest $request, int $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $data = $request->validated();

        // Handle file upload for image
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle updated successfully.',
            'data' => $vehicle,
        ]);
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(int $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Delete associated image if exists
        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle deleted successfully.',
        ]);
    }

    public function getVehiclesByUserId($userId)
    {
        $vehicles = Vehicle::with(['user', 'vehicleRegistration'])->where('user_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $vehicles,
        ]);
    }
}
