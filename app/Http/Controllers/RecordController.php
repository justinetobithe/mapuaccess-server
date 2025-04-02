<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordRequest;
use App\Models\Record;
use App\Models\Vehicle;
use App\Models\VehicleRegistration;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'type');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Record::with(['vehicleRegistration.vehicle.user']);

        // if ($filter) {
        //     $query->where(function ($q) use ($filter) {
        //         $q->where('vehicle', 'like', "%{$filter}%")
        //             ->orWhere('owner', 'like', "%{$filter}%")
        //             ->orWhere('recorded_at', 'like', "%{$filter}%");
        //     });
        // }

        $query->orderBy('type', 'asc');

        if (in_array($sortColumn, ['vehicle', 'owner', 'type', 'recorded_at'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }


        if ($pageSize) {
            $records = $query->paginate($pageSize);
        } else {
            $records = $query->get();
        }

        return $this->success($records);
    }

    public function scanQRCode(RecordRequest $request)
    {
        $data = $request->validated();
        $qrCode = $data['qr_code'];
        $type = $data['type'];
        $vehicleRegistration = VehicleRegistration::where('code', $qrCode)->first();

        if (!$vehicleRegistration) {
            return $this->error('Vehicle registration not found.');
        }

        $today = Carbon::today();

        if ($vehicleRegistration->valid_until < $today) {
            return $this->error('Vehicle registration has expired.');
        }

        if ($type === 'entry') {
            $latestRecord = Record::where('vehicle_registration_id', $vehicleRegistration->id)
                ->whereNull('deleted_at')
                ->latest()
                ->first();

            if ($latestRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle has already entered.',
                ]);
            }

            $record = Record::create([
                'vehicle_registration_id' => $vehicleRegistration->id,
                'qr_code' => $qrCode,
                'recorded_at' => Carbon::now(),
                'type' => 'entry',
            ]);

            $record->load('vehicleRegistration.vehicle.user');

            return response()->json([
                'status' => 'success',
                'message' => 'Vehicle has entered.',
                'data' => $record,
            ]);
        } elseif ($type === 'exit') {
            $record = Record::create([
                'vehicle_registration_id' => $vehicleRegistration->id,
                'qr_code' => $qrCode,
                'recorded_at' => Carbon::now(),
                'type' => 'exit',
            ]);

            // Eager load the necessary relationships
            $record->load('vehicleRegistration.vehicle.user');

            return response()->json([
                'status' => 'success',
                'message' => 'Vehicle has exited.',
                'data' => $record,
            ]);
        } else {
            return $this->error('Invalid type provided. Type must be either "entry" or "exit".');
        }
    }

    public function showRecordsWithVehicleId($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return $this->error('Vehicle not found.');
        }

        $vehicleRegistrationIds = VehicleRegistration::where('vehicle_id', $vehicle->id)->pluck('id');

        $records = Record::with(['vehicleRegistration.vehicle.user'])
            ->whereIn('vehicle_registration_id', $vehicleRegistrationIds)
            ->orderBy('recorded_at', 'desc')
            ->get();

        return $this->success($records);
    }
}
