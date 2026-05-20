<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormSubmissionController extends Controller
{
    public function __construct(
        protected FormSubmissionService $submissions
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $result = $this->submissions->process($request);

            return response()->json(array_merge(['success' => true], $result));
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function adminIndex(Request $request)
    {
        $stats = FormSubmissionService::aggregateStats();

        $query = FormSubmission::query()->latest();

        if ($request->filled('form_type')) {
            $query->where('form_type', $request->input('form_type'));
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        $submissions = $query->paginate(25)->withQueryString();

        return view('admin.form-submissions.index', [
            'submissions' => $submissions,
            'stats' => $stats,
            'formTypes' => FormSubmission::formTypeLabels(),
        ]);
    }
}
