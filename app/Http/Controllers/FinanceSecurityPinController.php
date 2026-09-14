<?php

namespace App\Http\Controllers;

use App\Models\FinanceSecurityPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceSecurityPinController extends Controller
{
    /**
     * Show PIN Verification Form.
     */
    public function verifyForm(Request $request)
    {
        // If session already verified, proceed directly to intended URL or bank index
        if ($request->session()->get('finance_pin_verified') === true) {
            $intended = $request->session()->pull('finance_pin_intended', route('bank.index'));
            return redirect($intended);
        }

        $intendedUrl = $request->session()->get('finance_pin_intended', route('bank.index'));
        
        $targetName = 'Kas & Bank / Petty Cash';
        if (str_contains($intendedUrl, 'petty-cash')) {
            $targetName = 'Petty Cash (Kas Kecil)';
        } elseif (str_contains($intendedUrl, 'bank')) {
            $targetName = 'Kas & Bank';
        }

        $isDeveloper = Auth::user() && (Auth::user()->isDeveloper() || Auth::user()->getRawOriginal('role') === 'Developer');

        return view('pages.finance.security.verify', compact('targetName', 'intendedUrl', 'isDeveloper'));
    }

    /**
     * Process PIN Verification submission.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ], [
            'pin.required' => 'PIN keamanan wajib diisi.',
            'pin.size'     => 'PIN keamanan harus terdiri dari 6 digit angka.',
            'pin.regex'    => 'PIN keamanan hanya boleh berupa angka.',
        ]);

        $pin = $request->input('pin');
        $user = Auth::user();

        if (FinanceSecurityPin::verify($pin, $user)) {
            $request->session()->put('finance_pin_verified', true);
            $request->session()->put('finance_pin_verified_at', now()->timestamp);

            $intended = $request->input('target_url') ?: $request->session()->pull('finance_pin_intended', route('bank.index'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akses keamanan berhasil diverifikasi.',
                    'redirect_url' => $intended,
                ]);
            }

            return redirect($intended)->with('success', 'Akses keamanan berhasil diverifikasi.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode PIN yang Anda masukkan tidak sesuai. Silakan coba kembali.',
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', 'Kode PIN yang Anda masukkan tidak sesuai. Silakan coba kembali.');
    }

    /**
     * Lock current session (revoke PIN verification).
     */
    public function lock(Request $request)
    {
        $request->session()->forget(['finance_pin_verified', 'finance_pin_verified_at', 'finance_pin_intended']);

        return redirect('/')->with('info', 'Sesi menu Kas & Bank dan Petty Cash telah berhasil dikunci kembali.');
    }

    /**
     * Manage PIN settings (Accessible by Finance Manager and Developer).
     */
    public function manage(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->getRawOriginal('role') ?? $user->role;

        $allowedRoles = ['Finance Manager', 'Finance', 'Developer'];
        if (!in_array($userRole, $allowedRoles) && !$user->isDeveloper()) {
            abort(403, 'Hanya Finance Manager atau Developer yang dapat mengatur PIN Keamanan.');
        }

        $vault = FinanceSecurityPin::getVaultPin();
        $vault->load('updater');

        $isDeveloper = $user->isDeveloper() || $userRole === 'Developer';

        return view('pages.finance.security.manage', compact('vault', 'isDeveloper'));
    }

    /**
     * Update/Change the security PIN.
     */
    public function updatePin(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->getRawOriginal('role') ?? $user->role;

        $allowedRoles = ['Finance Manager', 'Finance', 'Developer'];
        if (!in_array($userRole, $allowedRoles) && !$user->isDeveloper()) {
            abort(403, 'Hanya Finance Manager atau Developer yang dapat mengubah PIN Keamanan.');
        }

        $request->validate([
            'current_pin'          => 'required|string|size:6',
            'new_pin'              => 'required|string|size:6|regex:/^[0-9]+$/',
            'new_pin_confirmation'=> 'required|same:new_pin',
        ], [
            'current_pin.required'          => 'PIN saat ini wajib diisi.',
            'new_pin.required'              => 'PIN baru wajib diisi.',
            'new_pin.size'                  => 'PIN baru harus tepat 6 digit angka.',
            'new_pin.regex'                 => 'PIN baru hanya boleh berisi angka.',
            'new_pin_confirmation.required' => 'Konfirmasi PIN baru wajib diisi.',
            'new_pin_confirmation.same'     => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
        ]);

        // Verify current PIN before allowing change
        if (!FinanceSecurityPin::verify($request->current_pin, $user)) {
            return back()
                ->withErrors(['current_pin' => 'PIN saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        FinanceSecurityPin::updatePin($request->new_pin, Auth::id());

        // Keep current user session verified with the new PIN
        $request->session()->put('finance_pin_verified', true);
        $request->session()->put('finance_pin_verified_at', now()->timestamp);

        return redirect()->route('finance.security.manage')
            ->with('success', 'PIN Keamanan Finance berhasil diperbarui. Seluruh akses Kas & Bank dan Petty Cash kini menggunakan PIN baru.');
    }
}
