<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\HrEmployeeAsset;
use App\Models\HrLeaveBalance;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use App\Models\HrPayrollItem;
use App\Models\HrReimbursement;
use App\Models\Hr\HrOfficeWifi;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.sales.user.create-acc");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $isClientVendor = ($request->role === 'Client Vendor');

        if ($isClientVendor) {
            $rule = [
                'name'     => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ];
            $customMessages = [
                'name.required'     => 'Field Nama Wajib Diisi!',
                'email.required'    => 'Field Email Wajib Diisi!',
                'email.unique'      => 'Email sudah terdaftar.',
                'password.required' => 'Field Password Wajib Diisi!',
                'password.min'      => 'Password minimal 6 karakter.',
            ];
        } else {
            $rule = [
                'name'  => 'required',
                'email' => 'required|email|unique:users,email',
                'area'  => 'required',
                'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
                'phone' => 'required',
            ];
            $customMessages = [
                'name.required'  => 'Field Nama Wajib Diisi!',
                'email.required' => 'Field Email Wajib Diisi',
                'email.unique'   => 'Email sudah terdaftar.',
                'image.required' => 'Field Foto Wajib Diisi',
                'area.required'  => 'Field Area Wajib Diisi',
                'phone.required' => 'Field Phone Wajib Diisi!',
            ];
        }

        $this->validate($request, $rule, $customMessages);
        $users = new User;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->area = $request->area ?: '-';
        $users->code = $request->code;
        $users->active = $request->active ?? '1';
        $users->role = $request->role;
        $users->phone = $request->filled('phone') ? ('+62' . preg_replace('/\D/', '', $request->phone)) : null;
        $users->password = Hash::make($request->password);

        if (!$isClientVendor && $request->hasFile('image')) {
            if ($users->image != 'asset/profile/profile.jpg') {
                File::delete($users->image);
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $request->file('image')->move($upload_path, $imagename);

            $users['image'] = $imagename;
        } else {
            $users->image = 'asset/profile/profile.jpg';
        }
        $status = $users->save();
        if ($status) {
            return redirect('/profile' . '/' . Auth::user()->id)->with('success', 'User akun berhasil dibuat!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($profile)
    {
        $user = ($profile instanceof User && $profile->exists) ? $profile : User::find($profile);
        if (!$user) {
            $user = User::find(Auth::id());
        }

        if ($user && $user->role === 'Client Vendor') {
            return redirect()->route('profile.edit', $user->id);
        }

        $employee = $user->employee;
        if ($employee) {
            $employee->loadMissing(['department', 'position', 'salary']);
        }

        $isOwnProfile = (Auth::id() == $user->id);
        $isAdminOrHr = in_array(Auth::user()->role, ['Admin', 'HRD', 'Super Admin', 'Director']);

        // Data Absensi & HR jika akun terkait karyawan
        $todayAttendance = null;
        $monthAttendances = collect();
        $leaveBalance = null;
        $myLeaves = collect();
        $leaveTypes = collect();
        $myPayslips = collect();
        $myReimbursements = collect();
        $myAssets = collect();

        if ($employee) {
            $today = Carbon::today('Asia/Jakarta')->toDateString();
            $currentMonth = Carbon::now('Asia/Jakarta')->month;
            $currentYear = Carbon::now('Asia/Jakarta')->year;

            // Selected Month & Year (Default to current running month)
            $selectedMonth = (int) request('att_month', request('month', $currentMonth));
            $selectedYear  = (int) request('att_year', request('year', $currentYear));

            if ($selectedMonth < 1 || $selectedMonth > 12) {
                $selectedMonth = $currentMonth;
            }
            if ($selectedYear < 2020 || $selectedYear > 2035) {
                $selectedYear = $currentYear;
            }

            // Evaluasi Auto Clock-Out otomatis (Asia/Jakarta GMT+7)
            HrAttendance::processAutoClockOutIfDue();

            // Today's attendance
            $todayAttendance = HrAttendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            // Monthly attendance records for selected period
            $monthAttendances = HrAttendance::where('employee_id', $employee->id)
                ->whereMonth('date', $selectedMonth)
                ->whereYear('date', $selectedYear)
                ->orderByDesc('date')
                ->get();

            // Calculate penalty amount for records
            $totalLatePenalty = 0;
            foreach ($monthAttendances as $att) {
                $pInfo = ($att->late_minutes > 0 || $att->status === 'Alpa')
                    ? HrAttendance::calculatePenaltyInfo($employee->id, $att->date, (int) $att->late_minutes, $employee)
                    : null;
                $att->penalty_info = $pInfo;

                if ($att->penalty_amount !== null && (float) $att->penalty_amount > 0) {
                    $penaltyVal = (float) $att->penalty_amount;
                } elseif ($att->late_minutes > 0 && ($att->status ?? 'Hadir') === 'Hadir') {
                    $penaltyVal = (float) ($pInfo['penalty'] ?? 0);
                    $att->calculated_penalty = $penaltyVal;
                } else {
                    $penaltyVal = 0;
                }
                $att->effective_penalty = $penaltyVal;
                $totalLatePenalty += $penaltyVal;
            }

            // Monthly attendance summary statistics
            $attStats = [
                'totalRecords'      => $monthAttendances->count(),
                'totalHadir'        => $monthAttendances->where('status', 'Hadir')->count(),
                'totalOnTime'       => $monthAttendances->where('status', 'Hadir')->where('late_minutes', '<=', 0)->count(),
                'totalLate'         => $monthAttendances->where('late_minutes', '>', 0)->count(),
                'totalLateMins'     => (int) $monthAttendances->sum('late_minutes'),
                'totalLatePenalty'  => (float) $totalLatePenalty,
                'totalOvertimeMins' => (int) $monthAttendances->sum('overtime_minutes'),
                'totalIzin'         => $monthAttendances->whereIn('status', ['Izin', 'Sakit', 'Cuti', 'Dinas Luar'])->count(),
            ];

            // List of available months / years from employee attendance records
            $availableAttendancePeriods = HrAttendance::where('employee_id', $employee->id)
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, count(*) as total_days')
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get();

            // Period Navigation Helpers
            $selectedPeriod = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $prevPeriod = $selectedPeriod->copy()->subMonth();
            $nextPeriod = $selectedPeriod->copy()->addMonth();
            $isCurrentRunningMonth = ($selectedMonth == $currentMonth && $selectedYear == $currentYear);

            // Leave balances & requests
            $leaveBalance = HrLeaveBalance::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => $currentYear],
                ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12, 'is_active' => true]
            );
            $myLeaves = HrLeaveRequest::with('leaveType')
                ->where('employee_id', $employee->id)
                ->orderByDesc('created_at')
                ->get();
            $leaveTypes = HrLeaveType::where('is_active', true)->get();

            // Payslips
            if ($isOwnProfile || $isAdminOrHr) {
                $myPayslips = HrPayrollItem::with('payroll')
                    ->where('employee_id', $employee->id)
                    ->orderByDesc('id')
                    ->get();
            }

            // Reimbursements
            $myReimbursements = HrReimbursement::where('employee_id', $employee->id)
                ->orderByDesc('created_at')
                ->get();

            // Assets yang sedang dipegang
            $myAssets = HrEmployeeAsset::with('fixedAsset')
                ->where('employee_id', $employee->id)
                ->where('status', 'Digunakan')
                ->get();
        }

        // Anti-fraud & Security settings untuk Clock In widget
        $wifiSetting = DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $isWifiRestrictionEnabled = $wifiSetting && $wifiSetting->value === '1';
        $deviceLockSetting = DB::table('hr_attendance_settings')->where('key', 'is_device_lock_enabled')->first();
        $isDeviceLockEnabled = !$deviceLockSetting || $deviceLockSetting->value === '1';
        $selfieSetting = DB::table('hr_attendance_settings')->where('key', 'is_selfie_required')->first();
        $isSelfieRequired = $selfieSetting && $selfieSetting->value === '1';

        $activeWifis = HrOfficeWifi::where('is_active', true)->get();
        $allowedIps = $activeWifis->pluck('ip_address')->toArray();
        $clientIp = request()->ip();

        $isWifiVerified = !$isWifiRestrictionEnabled
            || in_array($clientIp, $allowedIps)
            || (app()->isLocal() && in_array($clientIp, ['127.0.0.1', '::1']));

        // Kinerja Sales (jika role Sales)
        $isSales = ($user->role === 'Sales');
        $salesMetrics = [];
        $paymentTemplates = collect();
        $salesClients = collect();

        if ($isSales) {
            $sqHot   = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'hot_prospect')->count();
            $sqNego  = \App\Models\UnitQuotation::where('id_sales', $user->id)->whereIn('status', ['negotiation', 'revision'])->count();
            $sqPo    = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'po_received')->count();
            $sqDraft = \App\Models\UnitQuotation::where('id_sales', $user->id)->whereIn('status', ['draft', 'sent'])->count();
            $sqLoss  = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'loss')->count();
            $totalSmartQuotes = \App\Models\UnitQuotation::where('id_sales', $user->id)->count();

            $lqHot   = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [70, 75, 80, 90])->count();
            $lqNego  = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [30, 40, 50, 60])->count();
            $lqPo    = \App\Models\Quotation::where('id_sales', $user->id)->where('status', 100)->count();
            $lqDraft = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [0, 10, 20])->count();
            $lqLoss  = \App\Models\Quotation::where('id_sales', $user->id)->where('status', '<', 0)->count();
            $totalLegacyQuotes = \App\Models\Quotation::where('id_sales', $user->id)->count();

            $salesMetrics = [
                'countHotProspect' => $sqHot + $lqHot,
                'countNegotiation' => $sqNego + $lqNego,
                'countPoReceived'  => $sqPo + $lqPo,
                'countDraft'       => $sqDraft + $lqDraft,
                'countLoss'        => $sqLoss + $lqLoss,
                'totalQuotations'  => $totalSmartQuotes + $totalLegacyQuotes,
                'totalClients'     => \App\Models\Client::where('id_sales', $user->id)->count(),
                'totalCustomers'   => \App\Models\Client::where('id_sales', $user->id)->whereIn('role', ['Customers', 'Customer'])->count(),
                'typeUnit'         => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Unit')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-U/%')->count(),
                'typeParts'        => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Parts')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-P/%')->count(),
                'typeService'      => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Service')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-S/%')->count(),
                'typeRental'       => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Rental')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-R/%')->count(),
                'typeProject'      => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Project')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-PR/%')->count(),
                'typePiping'       => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Piping')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-PIP/%')->count(),
                'typeAirAudit'     => \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Air Audit')->count() + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-AA/%')->count(),
            ];

            $paymentTemplates = \App\Models\SalesPaymentTemplate::with('client')->where('id_sales', $user->id)->orderBy('is_default', 'desc')->orderBy('name')->get();
            $salesClients = \App\Models\Client::where('id_sales', $user->id)->orderBy('company')->get();
        }

        return view('pages.sales.user.profile', compact(
            'user',
            'employee',
            'isOwnProfile',
            'isAdminOrHr',
            'todayAttendance',
            'monthAttendances',
            'selectedMonth',
            'selectedYear',
            'selectedPeriod',
            'prevPeriod',
            'nextPeriod',
            'isCurrentRunningMonth',
            'attStats',
            'availableAttendancePeriods',
            'leaveBalance',
            'myLeaves',
            'leaveTypes',
            'myPayslips',
            'myReimbursements',
            'myAssets',
            'isWifiRestrictionEnabled',
            'isWifiVerified',
            'isDeviceLockEnabled',
            'isSelfieRequired',
            'clientIp',
            'activeWifis',
            'isSales',
            'salesMetrics',
            'paymentTemplates',
            'salesClients'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($profile)
    {
        $user = ($profile instanceof User && $profile->exists) ? $profile : User::find($profile);
        if (!$user) {
            $user = User::find(Auth::id());
        }
        return view('pages.sales.user.setting', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $users = User::findOrFail($id);

        if ($users->role === 'Client Vendor') {
            $rule = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
            ];
            $customMessages = [
                'name.required'  => 'Field Nama Wajib Diisi!',
                'email.required' => 'Field EMail Wajib Diisi!',
                'email.unique'   => 'Email sudah digunakan oleh akun lain.',
                'password.min'   => 'Password minimal 6 karakter.',
            ];

            $this->validate($request, $rule, $customMessages);
            $users->name = $request->name;
            $users->email = $request->email;
            if ($request->filled('password')) {
                $users->password = Hash::make($request->password);
            }
            $users->save();

            return redirect()->route('profile.edit', $id)->with('success', 'Profil Client Vendor berhasil diperbarui!');
        }

        $rule = [
            'name'   => 'required',
            'email'  => 'required|email|unique:users,email,' . $id,
            'image'  => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'banner' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'phone'  => 'required',
        ];
        $customMessages = [
            'name.required' => 'Field Nama Wajib Diisi!',
            'email.required' => 'Field EMail Wajib Diisi',
            'image.required' => 'Field Foto Wajib Diisi',
            'phone.required' => 'Field phone Wajib Diisi!',
        ];

        $this->validate($request, $rule, $customMessages);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->birthday = $request->birthday;
        if ($request->filled('password')) {
            $users->password = Hash::make($request->password);
        }
        $users->address = $request->address;
        $users->phone = '+62' . $request->phone;

        if ($request->hasFile('image')) {
            if ($users->image && $users->image != 'asset/profile/profile.jpg' && File::exists(public_path($users->image))) {
                File::delete(public_path($users->image));
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $foto->move(public_path($upload_path), $foto_name . '.' . $foto_ext);

            $users->image = $imagename;
        }

        if ($request->hasFile('banner')) {
            if ($users->banner && File::exists(public_path($users->banner))) {
                File::delete(public_path($users->banner));
            }

            $banner = $request->file('banner');
            $banner_ext = $banner->getClientOriginalExtension();
            $banner_name = 'banner_' . Str::random(10);

            $upload_path = 'asset/profile/banners';
            if (!File::exists(public_path($upload_path))) {
                File::makeDirectory(public_path($upload_path), 0777, true, true);
            }
            $bannerPath = $upload_path . '/' . $banner_name . '.' . $banner_ext;
            $banner->move(public_path($upload_path), $banner_name . '.' . $banner_ext);

            $users->banner = $bannerPath;
        }

        $status = $users->save();
        if ($status) {
            return redirect('/profile/' . $id)->with('success', 'Data Has been updated');
        }
    }

    /**
     * Update user profile banner photo.
     */
    public function updateBanner(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'banner.required' => 'Silakan pilih foto banner!',
            'banner.image' => 'File harus berupa gambar.',
            'banner.mimes' => 'Format gambar: jpeg, png, jpg, gif, webp.',
            'banner.max' => 'Ukuran maksimal banner 5MB.',
        ]);

        $user = User::findOrFail($id);

        if ($request->hasFile('banner')) {
            if ($user->banner && File::exists(public_path($user->banner))) {
                File::delete(public_path($user->banner));
            }

            $foto = $request->file('banner');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = 'banner_' . Str::random(10);

            $upload_path = 'asset/profile/banners';
            if (!File::exists(public_path($upload_path))) {
                File::makeDirectory(public_path($upload_path), 0777, true, true);
            }
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $foto->move(public_path($upload_path), $foto_name . '.' . $foto_ext);

            $user->banner = $imagename;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto Banner profil berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        abort(404);
    }

    /**
     * Update customizable quick actions for the authenticated user (max 4).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuickActions(Request $request)
    {
        $request->validate([
            'quick_actions' => 'required|array|min:1|max:4',
        ], [
            'quick_actions.required' => 'Pilih atau buat minimal 1 menu Quick Action.',
            'quick_actions.array'    => 'Format data Quick Action tidak valid.',
            'quick_actions.min'      => 'Pilih minimal 1 menu Quick Action.',
            'quick_actions.max'      => 'Maksimal 4 menu Quick Action yang dapat dipilih.',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $catalog = \App\Services\QuickActionService::getMasterCatalog();
        $inputItems = array_slice($request->input('quick_actions', []), 0, \App\Services\QuickActionService::MAX_ITEMS);
        $cleanActions = [];

        foreach ($inputItems as $item) {
            if (is_string($item)) {
                if (isset($catalog[$item])) {
                    $cleanActions[] = $item;
                }
            } elseif (is_array($item)) {
                $type = $item['type'] ?? '';
                if ($type === 'custom' || !empty($item['is_custom'])) {
                    $title = trim($item['title'] ?? '');
                    $url   = trim($item['url'] ?? '');
                    if (!empty($title) && !empty($url)) {
                        $cleanActions[] = [
                            'type'     => 'custom',
                            'title'    => mb_substr($title, 0, 40),
                            'subtitle' => mb_substr(trim($item['subtitle'] ?? 'Direct Link'), 0, 40),
                            'url'      => $url,
                            'icon'     => !empty($item['icon']) ? trim($item['icon']) : 'mdi mdi-link-variant',
                            'icon_bg'  => !empty($item['icon_bg']) ? trim($item['icon_bg']) : 'bg-label-primary',
                        ];
                    }
                }
            }
        }

        if (empty($cleanActions)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih menu atau masukkan link custom yang valid.'
            ], 422);
        }

        $user->quick_actions = $cleanActions;
        $user->save();

        return response()->json([
            'success'       => true,
            'message'       => 'Quick Action berhasil diperbarui!',
            'quick_actions' => \App\Services\QuickActionService::getUserQuickActions($user),
        ]);
    }
}
