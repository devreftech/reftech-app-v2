<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\KanbanTask;
use App\Models\ProjectReport;
use App\Models\ProjectReportEquipment;
use App\Models\ProjectReportManpower;
use App\Models\ProjectReportMaterial;
use App\Models\ProjectReportPhoto;
use App\Models\ProjectReportTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data();
        }
        return redirect('/service-reports?tab=project');
    }

    /**
     * Data feed for DataTables
     */
    public function data()
    {
        $reports = ProjectReport::with(['creator', 'client', 'kanbanTask.board'])->orderBy('id', 'desc')->get();

        $data = $reports->map(function ($row) {
            $showUrl = route('project-reports.show', $row->id);
            $editUrl = route('project-reports.edit', $row->id);
            $printUrl = route('project-reports.print', $row->id);

            $dayStr = $row->day_name ? e($row->day_name) . ', ' : '';
            $dayKe = $row->day_number ? ' <span class="badge bg-label-info">Hari ke-' . e($row->day_number) . '</span>' : '';

            $statusBadge = '<span class="badge bg-label-secondary">Draft</span>';
            if ($row->status == 'approved') {
                $statusBadge = '<span class="badge bg-label-success">Approved</span>';
            } elseif ($row->status == 'completed') {
                $statusBadge = '<span class="badge bg-label-primary">Completed</span>';
            }

            $kanbanBadge = '';
            if ($row->kanbanTask) {
                $kanbanUrl = url('/kanban/board/' . $row->kanbanTask->board_id . '?task=' . $row->kanbanTask->id);
                $kanbanBadge = '<br><a href="' . $kanbanUrl . '" class="badge bg-label-secondary text-primary mt-1" target="_blank" title="Buka di Kanban Board"><i class="mdi mdi-view-dashboard-outline me-1"></i>' . e(Str::limit($row->kanbanTask->title, 26)) . '</a>';
            }

            return [
                'id' => $row->id,
                'job_name' => $row->job_name,
                'contract_no' => $row->contract_no ?: '-',
                'job_info' => '<div><strong class="text-primary">' . e($row->job_name) . '</strong>' .
                    ($row->contract_no ? '<br><small class="text-muted"><i class="mdi mdi-file-document-outline me-1"></i>' . e($row->contract_no) . '</small>' : '') .
                    $kanbanBadge .
                    '</div>',
                'date_formatted' => '<div>' . $dayStr . ($row->report_date ? Carbon::parse($row->report_date)->format('d M Y') : '-') . $dayKe . '</div>',
                'report_date' => $row->report_date ? Carbon::parse($row->report_date)->format('Y-m-d') : '',
                'creator_name' => $row->creator ? e($row->creator->name) : '-',
                'status_badge' => $statusBadge,
                'action' => '
                    <div class="d-inline-block text-nowrap">
                        <a href="' . $showUrl . '" class="btn btn-sm btn-icon btn-label-info me-1" title="Detail"><i class="mdi mdi-eye-outline fs-5"></i></a>
                        <a href="' . $printUrl . '" target="_blank" class="btn btn-sm btn-icon btn-label-secondary me-1" title="Print PDF"><i class="mdi mdi-printer fs-5"></i></a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-icon btn-label-primary me-1" title="Edit"><i class="mdi mdi-pencil-outline fs-5"></i></a>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger delete-project-report" data-id="' . $row->id . '" title="Delete"><i class="mdi mdi-delete-outline fs-5"></i></button>
                    </div>
                '
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $today = Carbon::today();
        $daysIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $defaultDayName = $daysIndo[$today->format('l')] ?? $today->format('l');

        // Auto generate report number format PR-YYYYMM-XXXX
        $yearMonth = $today->format('Ym');
        $countThisMonth = ProjectReport::whereYear('report_date', $today->year)
            ->whereMonth('report_date', $today->month)
            ->count();
        $suggestedReportNo = 'PR-' . $yearMonth . '-' . str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);

        $clients = Client::select('id', 'company')->orderBy('company', 'asc')->get();
        $kanbanTasks = KanbanTask::with(['board', 'column'])
            ->whereHas('board', function ($q) {
                $q->where('title', 'like', '%HVAC%');
            })
            ->orderBy('id', 'desc')
            ->get();

        $selectedKanbanTaskId = $request->query('kanban_task_id');
        $selectedKanbanTask = null;
        $suggestedDayNumber = null;
        $prefilledJobName = null;
        $prefilledContractNo = null;
        $prefilledClientId = null;

        if ($selectedKanbanTaskId) {
            $selectedKanbanTask = KanbanTask::with([
                'pendingPo.quote.pic.client',
                'pendingPo.unitQuotation.client',
                'unitQuotation.client',
                'projectReports'
            ])->find($selectedKanbanTaskId);

            if ($selectedKanbanTask) {
                $prefilledJobName = $selectedKanbanTask->title;
                $existingReportsCount = $selectedKanbanTask->projectReports->count();
                $suggestedDayNumber = $existingReportsCount + 1;

                if ($selectedKanbanTask->pendingPo) {
                    $po = $selectedKanbanTask->pendingPo;
                    $isUnit = (bool) $po->id_unit_quotation;
                    $quoteRef = $isUnit ? $po->unitQuotation : $po->quote;
                    $client = $isUnit ? ($quoteRef->client ?? null) : (($quoteRef && $quoteRef->pic) ? $quoteRef->pic->client : null);
                    if ($client) {
                        $prefilledClientId = $client->id;
                    }
                    if (!empty($po->no_po)) {
                        $prefilledContractNo = $po->no_po;
                    }
                } elseif ($selectedKanbanTask->unitQuotation) {
                    $uq = $selectedKanbanTask->unitQuotation;
                    if ($uq->client) {
                        $prefilledClientId = $uq->client->id;
                    }
                    if (!empty($uq->po_number)) {
                        $prefilledContractNo = $uq->po_number;
                    }
                }
            }
        }

        return view('pages.technician.project-reports.form', compact(
            'today',
            'defaultDayName',
            'suggestedReportNo',
            'clients',
            'kanbanTasks',
            'selectedKanbanTaskId',
            'selectedKanbanTask',
            'suggestedDayNumber',
            'prefilledJobName',
            'prefilledContractNo',
            'prefilledClientId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_name' => 'required|string|max:255',
            'report_date' => 'required|date',
            'contractor_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $report = new ProjectReport();
            $report->report_number = $request->report_number;
            $report->job_name = $request->job_name;
            $report->contract_no = $request->contract_no;
            $report->report_date = $request->report_date;
            $report->contractor_name = $request->contractor_name ?: 'PT. REFTECH JAYA OPTIMA';
            $report->day_number = $request->day_number;
            $report->day_name = $request->day_name;
            $report->days_remaining = $request->days_remaining;
            $report->client_id = $request->client_id;
            $report->kanban_task_id = $request->kanban_task_id ?: null;
            $report->created_by = Auth::id();

            // Cuaca
            $report->weather_cerah = $request->has('weather_cerah') ? 1 : 0;
            $report->weather_cerah_time = $request->weather_cerah_time;
            $report->weather_hujan = $request->has('weather_hujan') ? 1 : 0;
            $report->weather_hujan_time = $request->weather_hujan_time;
            $report->weather_mendung = $request->has('weather_mendung') ? 1 : 0;
            $report->weather_mendung_time = $request->weather_mendung_time;
            $report->weather_dll = $request->has('weather_dll') ? 1 : 0;
            $report->weather_dll_time = $request->weather_dll_time;

            // Notes
            $report->planning_today = $request->planning_today;
            $report->achievement_today = $request->achievement_today;
            $report->issues_constraints = $request->issues_constraints;
            $report->next_plan = $request->next_plan;

            $report->client_pic_name = $request->client_pic_name;
            $report->contractor_pic_name = $request->contractor_pic_name;
            $report->status = $request->status ?: 'completed';

            // Signatures
            if ($request->hasFile('client_sign')) {
                $path = $request->file('client_sign')->store('project-reports/signs', 'public');
                $report->client_sign = $path;
            } elseif ($request->client_sign_base64) {
                $report->client_sign = $this->saveBase64Image($request->client_sign_base64, 'project-reports/signs');
            }

            if ($request->hasFile('contractor_sign')) {
                $path = $request->file('contractor_sign')->store('project-reports/signs', 'public');
                $report->contractor_sign = $path;
            } elseif ($request->contractor_sign_base64) {
                $report->contractor_sign = $this->saveBase64Image($request->contractor_sign_base64, 'project-reports/signs');
            }

            $report->save();

            // Save Dynamic Tasks (Pekerjaan)
            if ($request->has('tasks') && is_array($request->tasks)) {
                foreach ($request->tasks as $idx => $t) {
                    if (!empty($t['task_name'])) {
                        ProjectReportTask::create([
                            'id_project_report' => $report->id,
                            'task_name' => $t['task_name'],
                            'location' => $t['location'] ?? null,
                            'notes' => $t['notes'] ?? null,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Dynamic Materials (Bahan / Material)
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $idx => $m) {
                    if (!empty($m['material_name'])) {
                        ProjectReportMaterial::create([
                            'id_project_report' => $report->id,
                            'material_name' => $m['material_name'],
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Dynamic Equipments (Peralatan)
            if ($request->has('equipments') && is_array($request->equipments)) {
                foreach ($request->equipments as $idx => $e) {
                    if (!empty($e['equipment_name'])) {
                        ProjectReportEquipment::create([
                            'id_project_report' => $report->id,
                            'equipment_name' => $e['equipment_name'],
                            'qty' => $e['qty'] ?? null,
                            'unit' => $e['unit'] ?? null,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Dynamic Manpowers (Tenaga Kerja)
            if ($request->has('manpowers') && is_array($request->manpowers)) {
                foreach ($request->manpowers as $idx => $mp) {
                    if (!empty($mp['position'])) {
                        ProjectReportManpower::create([
                            'id_project_report' => $report->id,
                            'position' => $mp['position'],
                            'manpower_count' => $mp['manpower_count'] ?? 1,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Documentation Photos (Foto Dokumentasi)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $idx => $photoFile) {
                    $path = $photoFile->store('project-reports/photos', 'public');
                    $caption = $request->photo_captions[$idx] ?? null;
                    ProjectReportPhoto::create([
                        'id_project_report' => $report->id,
                        'photo_path' => $path,
                        'caption' => $caption,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('project-reports.show', $report->id)->with('success', 'Daily Project Report berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan report: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = ProjectReport::with(['tasks', 'materials', 'equipments', 'manpowers', 'photos', 'creator', 'client'])->findOrFail($id);
        return view('pages.technician.project-reports.detail', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = ProjectReport::with(['tasks', 'materials', 'equipments', 'manpowers', 'photos', 'kanbanTask'])->findOrFail($id);
        $clients = Client::select('id', 'company')->orderBy('company', 'asc')->get();
        $kanbanTasks = KanbanTask::with(['board', 'column'])
            ->where(function ($query) use ($report) {
                $query->whereHas('board', function ($q) {
                    $q->where('title', 'like', '%HVAC%');
                });
                if ($report->kanban_task_id) {
                    $query->orWhere('id', $report->kanban_task_id);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
        return view('pages.technician.project-reports.form', compact('report', 'clients', 'kanbanTasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = ProjectReport::findOrFail($id);

        $request->validate([
            'job_name' => 'required|string|max:255',
            'report_date' => 'required|date',
            'contractor_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $report->report_number = $request->report_number;
            $report->job_name = $request->job_name;
            $report->contract_no = $request->contract_no;
            $report->report_date = $request->report_date;
            $report->contractor_name = $request->contractor_name ?: 'PT. REFTECH JAYA OPTIMA';
            $report->day_number = $request->day_number;
            $report->day_name = $request->day_name;
            $report->days_remaining = $request->days_remaining;
            $report->client_id = $request->client_id;
            $report->kanban_task_id = $request->kanban_task_id ?: null;

            // Cuaca
            $report->weather_cerah = $request->has('weather_cerah') ? 1 : 0;
            $report->weather_cerah_time = $request->weather_cerah_time;
            $report->weather_hujan = $request->has('weather_hujan') ? 1 : 0;
            $report->weather_hujan_time = $request->weather_hujan_time;
            $report->weather_mendung = $request->has('weather_mendung') ? 1 : 0;
            $report->weather_mendung_time = $request->weather_mendung_time;
            $report->weather_dll = $request->has('weather_dll') ? 1 : 0;
            $report->weather_dll_time = $request->weather_dll_time;

            // Notes
            $report->planning_today = $request->planning_today;
            $report->achievement_today = $request->achievement_today;
            $report->issues_constraints = $request->issues_constraints;
            $report->next_plan = $request->next_plan;

            if ($request->filled('client_pic_name')) {
                $report->client_pic_name = $request->client_pic_name;
            }
            if ($request->filled('contractor_pic_name')) {
                $report->contractor_pic_name = $request->contractor_pic_name;
            }
            if ($request->filled('status')) {
                $report->status = $request->status;
            }

            // Signatures
            if ($request->hasFile('client_sign')) {
                $path = $request->file('client_sign')->store('project-reports/signs', 'public');
                $report->client_sign = $path;
            } elseif ($request->client_sign_base64) {
                $report->client_sign = $this->saveBase64Image($request->client_sign_base64, 'project-reports/signs');
            }

            if ($request->hasFile('contractor_sign')) {
                $path = $request->file('contractor_sign')->store('project-reports/signs', 'public');
                $report->contractor_sign = $path;
            } elseif ($request->contractor_sign_base64) {
                $report->contractor_sign = $this->saveBase64Image($request->contractor_sign_base64, 'project-reports/signs');
            }

            $report->save();

            // Re-sync Tasks
            ProjectReportTask::where('id_project_report', $report->id)->delete();
            if ($request->has('tasks') && is_array($request->tasks)) {
                foreach ($request->tasks as $idx => $t) {
                    if (!empty($t['task_name'])) {
                        ProjectReportTask::create([
                            'id_project_report' => $report->id,
                            'task_name' => $t['task_name'],
                            'location' => $t['location'] ?? null,
                            'notes' => $t['notes'] ?? null,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Re-sync Materials
            ProjectReportMaterial::where('id_project_report', $report->id)->delete();
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $idx => $m) {
                    if (!empty($m['material_name'])) {
                        ProjectReportMaterial::create([
                            'id_project_report' => $report->id,
                            'material_name' => $m['material_name'],
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Re-sync Equipments
            ProjectReportEquipment::where('id_project_report', $report->id)->delete();
            if ($request->has('equipments') && is_array($request->equipments)) {
                foreach ($request->equipments as $idx => $e) {
                    if (!empty($e['equipment_name'])) {
                        ProjectReportEquipment::create([
                            'id_project_report' => $report->id,
                            'equipment_name' => $e['equipment_name'],
                            'qty' => $e['qty'] ?? null,
                            'unit' => $e['unit'] ?? null,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Re-sync Manpowers
            ProjectReportManpower::where('id_project_report', $report->id)->delete();
            if ($request->has('manpowers') && is_array($request->manpowers)) {
                foreach ($request->manpowers as $idx => $mp) {
                    if (!empty($mp['position'])) {
                        ProjectReportManpower::create([
                            'id_project_report' => $report->id,
                            'position' => $mp['position'],
                            'manpower_count' => $mp['manpower_count'] ?? 1,
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Additional Photos
            if ($request->hasFile('photos')) {
                $maxSort = ProjectReportPhoto::where('id_project_report', $report->id)->max('sort_order') ?? 0;
                foreach ($request->file('photos') as $idx => $photoFile) {
                    $path = $photoFile->store('project-reports/photos', 'public');
                    $caption = $request->photo_captions[$idx] ?? null;
                    ProjectReportPhoto::create([
                        'id_project_report' => $report->id,
                        'photo_path' => $path,
                        'caption' => $caption,
                        'sort_order' => $maxSort + $idx + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('project-reports.show', $report->id)->with('success', 'Daily Project Report berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui report: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = ProjectReport::findOrFail($id);
        $photos = ProjectReportPhoto::where('id_project_report', $id)->get();
        foreach ($photos as $p) {
            if ($p->photo_path && Storage::disk('public')->exists($p->photo_path)) {
                Storage::disk('public')->delete($p->photo_path);
            }
        }
        if ($report->client_sign && Storage::disk('public')->exists($report->client_sign)) {
            Storage::disk('public')->delete($report->client_sign);
        }
        if ($report->contractor_sign && Storage::disk('public')->exists($report->contractor_sign)) {
            Storage::disk('public')->delete($report->contractor_sign);
        }

        $report->delete();
        return response()->json(['success' => true, 'message' => 'Project report berhasil dihapus.']);
    }

    /**
     * Print View matching PDF 1:1 layout
     */
    public function print($id)
    {
        $report = ProjectReport::with(['tasks', 'materials', 'equipments', 'manpowers', 'photos', 'client'])->findOrFail($id);
        return view('pages.technician.project-reports.print', compact('report'));
    }

    /**
     * Save base64 signature string
     */
    public function saveSignature(Request $request, $id)
    {
        $report = ProjectReport::findOrFail($id);

        if ($request->has('client_sign_base64') && $request->client_sign_base64) {
            $report->client_sign = $this->saveBase64Image($request->client_sign_base64, 'project-reports/signs');
            if ($request->filled('client_pic_name')) {
                $report->client_pic_name = $request->client_pic_name;
            }
        }

        if ($request->has('contractor_sign_base64') && $request->contractor_sign_base64) {
            $report->contractor_sign = $this->saveBase64Image($request->contractor_sign_base64, 'project-reports/signs');
            if ($request->filled('contractor_pic_name')) {
                $report->contractor_pic_name = $request->contractor_pic_name;
            }
        }

        $report->save();
        return response()->json(['success' => true, 'message' => 'Tanda tangan berhasil disimpan.']);
    }

    /**
     * Upload single photo (AJAX)
     */
    public function uploadPhoto(Request $request, $id)
    {
        $report = ProjectReport::findOrFail($id);
        $request->validate([
            'photo' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('photo')->store('project-reports/photos', 'public');
        $maxSort = ProjectReportPhoto::where('id_project_report', $report->id)->max('sort_order') ?? 0;

        $photo = ProjectReportPhoto::create([
            'id_project_report' => $report->id,
            'photo_path' => $path,
            'caption' => $request->caption,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json(['success' => true, 'photo' => $photo, 'url' => $photo->url]);
    }

    /**
     * Delete photo item
     */
    public function deletePhoto($photo_id)
    {
        $photo = ProjectReportPhoto::findOrFail($photo_id);
        if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();
        return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus.']);
    }

    /**
     * Update photo caption
     */
    public function updatePhotoCaption(Request $request, $photo_id)
    {
        $photo = ProjectReportPhoto::findOrFail($photo_id);
        $photo->caption = $request->caption;
        $photo->save();
        return response()->json(['success' => true, 'message' => 'Keterangan foto diperbarui.']);
    }

    /**
     * Helper to save base64 data to public storage
     */
    private function saveBase64Image($base64Data, $folder)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);
            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }
            $filename = Str::random(20) . '.' . $type;
            $path = $folder . '/' . $filename;
            Storage::disk('public')->put($path, $data);
            return $path;
        }
        return null;
    }
}
