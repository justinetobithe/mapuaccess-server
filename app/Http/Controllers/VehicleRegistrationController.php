<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRegistrationRequest;
use App\Models\Semester;
use App\Models\Vehicle;
use App\Models\VehicleRegistration;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class VehicleRegistrationController extends Controller
{
    use ApiResponse;

    public function store(VehicleRegistrationRequest $request)
    {
        $data = $request->validated();

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        $semester = Semester::where('is_active', true)->first();
        if (!$semester) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active semester found.',
            ], 400);
        }

        $existingRegistration = VehicleRegistration::where('vehicle_id', $vehicle->id)
            ->where('semester_id', $semester->id)
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'status' => 'error',
                'message' => 'This vehicle is already registered for the current semester.',
            ], 400);
        }

        $code = $this->generateCode();

        $registration = VehicleRegistration::create([
            'vehicle_id' => $vehicle->id,
            'semester_id' => $semester->id,
            'code' => $code,
            'valid_until' => $semester->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle registration created successfully.',
            'data' => $registration,
        ]);
    }

    protected function generateCode(): string
    {
        return strtoupper(Str::random(6));
    }
}
