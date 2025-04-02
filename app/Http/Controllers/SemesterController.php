<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'school_year');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Semester::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('school_year', 'like', "%{$filter}%")
                    ->orWhere('school_year', 'like', "%{$filter}%")
                    ->orWhere('start_date', 'like', "%{$filter}%")
                    ->orWhere('end_date', 'like', "%{$filter}%");
            });
        }

        $query->orderBy('start_date', 'asc');

        if (in_array($sortColumn, ['school_year', 'school_year', 'start_date', 'end_date'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }


        if ($pageSize) {
            $semesters = $query->paginate($pageSize);
        } else {
            $semesters = $query->get();
        }

        return $this->success($semesters);
    }

    /**
     * Store a newly created Semester.
     */
    public function store(SemesterRequest $request)
    {
        $data = $request->validated();

        Semester::where('is_active', 1)->update(['is_active' => 0]);

        $data['is_active'] = 1;
        $semester = Semester::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Semester created successfully.',
            'data' => $semester,
        ]);
    }

    /**
     * Display the specified Semester.
     */
    public function show(int $id)
    {
        $semester = Semester::with('user')->findOrFail($id);

        return $this->success([
            'status' => 'success',
            'data' => $semester,
        ]);
    }

    /**
     * Update the specified Semester.
     */
    public function update(SemesterRequest $request, int $id)
    {
        $semester = Semester::findOrFail($id);

        $data = $request->validated();

        Semester::where('is_active', 1)->update(['is_active' => 0]);

        $data['is_active'] = 1;
        $semester->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Semester updated successfully.',
            'data' => $semester,
        ]);
    }

    /**
     * Remove the specified Semester.
     */
    public function destroy(int $id)
    {
        $semester = Semester::findOrFail($id);

        $semester->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Semester deleted successfully.',
        ]);
    }
}
