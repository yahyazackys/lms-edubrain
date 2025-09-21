<?php

namespace App\Http\Controllers;

use App\Models\Kalender;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class KalenderAkademikController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'tanggal_mulai' => $request->get('tanggal_mulai'),
            'tanggal_akhir' => $request->get('tanggal_akhir'),
        ];

        return view('kalender-akademik', compact('filters'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validationRules = [
            'judul' => 'required|string|max:255|min:3',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_all_day' => 'boolean',
        ];

        $validationMessages = [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'judul.min' => 'Judul minimal 3 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
        ];

        try {
            $validated = $request->validate($validationRules, $validationMessages);

            DB::beginTransaction();

            $event = KalenderAkademik::create([
                'judul' => $validated['judul'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'is_all_day' => $validated['is_all_day'] ?? true,
            ]);

            DB::commit();

            return $this->handleResponse(
                $request,
                true,
                'Kalender akademik berhasil ditambahkan.',
                $event,
                201
            );
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->handleValidationResponse($request, $e);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleResponse(
                $request,
                false,
                'Terjadi kesalahan saat menyimpan data.',
                null,
                500
            );
        }
    }

    public function update(Request $request, KalenderAkademik $kalenderAkademik): JsonResponse|RedirectResponse
    {
        $validationRules = [
            'judul' => 'required|string|max:255|min:3',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_all_day' => 'boolean',
        ];

        $validationMessages = [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'judul.min' => 'Judul minimal 3 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
        ];

        try {
            $validated = $request->validate($validationRules, $validationMessages);

            DB::beginTransaction();

            $kalenderAkademik->update([
                'judul' => $validated['judul'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'is_all_day' => $validated['is_all_day'] ?? true,
            ]);

            DB::commit();

            return $this->handleResponse(
                $request,
                true,
                'Kalender akademik berhasil diperbarui.',
                $kalenderAkademik->fresh()
            );
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->handleValidationResponse($request, $e);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleResponse(
                $request,
                false,
                'Terjadi kesalahan saat memperbarui data.',
                null,
                500
            );
        }
    }

    public function destroy(Request $request, KalenderAkademik $kalenderAkademik): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $title = $kalenderAkademik->judul;
            $kalenderAkademik->delete();

            DB::commit();

            return $this->handleResponse(
                $request,
                true,
                "Kalender akademik '{$title}' berhasil dihapus."
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleResponse(
                $request,
                false,
                'Terjadi kesalahan saat menghapus data.',
                null,
                500
            );
        }
    }

    /**
     * Get calendar events for FullCalendar
     */
    public function getEvents(Request $request): JsonResponse
    {
        try {
            $start = $request->get('start');
            $end = $request->get('end');
            $search = $request->get('search');

            $query = KalenderAkademik::query();

            // Apply date range filter if provided
            if ($start && $end) {
                $startDate = Carbon::parse($start);
                $endDate = Carbon::parse($end);

                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                        ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                        ->orWhere(function ($q2) use ($startDate, $endDate) {
                            $q2->where('tanggal_mulai', '<=', $startDate)
                                ->where('tanggal_selesai', '>=', $endDate);
                        });
                });
            }

            // Apply search filter
            if ($search) {
                $query->where('judul', 'like', "%{$search}%");
            }

            $events = $query->orderBy('tanggal_mulai')
                ->get()
                ->map(function ($item) {
                    $event = [
                        'id' => $item->id,
                        'title' => $item->judul,
                        'start' => $item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : null,
                        'allDay' => (bool) $item->is_all_day,
                        'backgroundColor' => '#3788d8',
                        'borderColor' => '#3788d8',
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'duration_text' => $this->getDurationText($item),
                            'is_multi_day' => $item->tanggal_selesai && $item->tanggal_mulai &&
                                $item->tanggal_mulai->format('Y-m-d') !== $item->tanggal_selesai->format('Y-m-d'),
                            'original_start' => $item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : null,
                            'original_end' => $item->tanggal_selesai ? $item->tanggal_selesai->format('Y-m-d') : null,
                        ]
                    ];

                    // Add end date if exists
                    if ($item->tanggal_selesai) {
                        if ($item->is_all_day) {
                            $endDate = $item->tanggal_selesai->copy()->addDay();
                            $event['end'] = $endDate->format('Y-m-d');
                        } else {
                            $event['end'] = $item->tanggal_selesai->format('Y-m-d');
                        }
                    }

                    return $event;
                });

            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load calendar events',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle response based on request type (JSON or redirect)
     */
    private function handleResponse(
        Request $request,
        bool $success,
        string $message,
        $data = null,
        int $statusCode = 200
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            $response = [
                'success' => $success,
                'message' => $message
            ];

            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response, $success ? $statusCode : ($statusCode >= 400 ? $statusCode : 400));
        }

        $flashType = $success ? 'success' : 'error';
        $redirect = $success ? redirect()->route('admin.kalender-akademik.index') : redirect()->back();

        if (!$success) {
            $redirect = $redirect->withInput();
        }

        return $redirect->with($flashType, $message);
    }

    /**
     * Handle validation error response
     */
    private function handleValidationResponse(Request $request, ValidationException $e): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($e->errors());
    }

    /**
     * Get duration text for an event
     */
    private function getDurationText($item): string
    {
        if (!$item->tanggal_mulai) {
            return 'Tanggal tidak valid';
        }

        if (!$item->tanggal_selesai) {
            return $item->tanggal_mulai->locale('id')->isoFormat('dddd, D MMMM Y');
        }

        if ($item->tanggal_mulai->format('Y-m-d') === $item->tanggal_selesai->format('Y-m-d')) {
            return $item->tanggal_mulai->locale('id')->isoFormat('dddd, D MMMM Y');
        }

        return $item->tanggal_mulai->locale('id')->isoFormat('D MMMM Y') .
            ' - ' .
            $item->tanggal_selesai->locale('id')->isoFormat('D MMMM Y');
    }
}