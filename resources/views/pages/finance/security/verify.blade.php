@extends('layouts.sales.app')
@section('title', 'Verifikasi PIN Keamanan - Finance Reftech')

@push('after-style')
<style>
    .pin-auth-container {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pin-auth-card {
        width: 100%;
        max-width: 440px;
        border-radius: 16px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .pin-icon-wrapper {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
    }
    .pin-digit-group {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-bottom: 1.5rem;
    }
    .pin-box {
        width: 48px;
        height: 56px;
        text-align: center;
        font-size: 24px;
        font-weight: 700;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #1e293b;
    }
    .pin-box:focus {
        border-color: #4f46e5;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        outline: none;
    }
    .keypad-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        max-width: 280px;
        margin: 0 auto 1.5rem;
    }
    .keypad-btn {
        height: 52px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 18px;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .keypad-btn:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }
    .keypad-btn:active {
        background: #cbd5e1;
        transform: translateY(0);
    }
    .keypad-btn.action-btn {
        background: #f1f5f9;
        font-size: 15px;
        color: #64748b;
    }
</style>
@endpush

@section('content')
<div class="container-xxl pin-auth-container py-4">
    <div class="pin-auth-card p-4 p-sm-5 text-center">
        {{-- Icon --}}
        <div class="pin-icon-wrapper text-white">
            <i class="mdi mdi-shield-lock-outline fs-1"></i>
        </div>

        {{-- Heading --}}
        <h4 class="fw-bold text-dark mb-1">Verifikasi PIN Keamanan</h4>
        <p class="text-muted small mb-3">
            Akses menuju menu <strong>{{ $targetName }}</strong> diproteksi oleh PIN Otoritas Finance.
        </p>

        @if($isDeveloper)
            <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-center mb-3">
                <i class="mdi mdi-code-braces me-1"></i>
                <span>Role <strong>Developer</strong> aktif: Gunakan PIN <strong>121212</strong></span>
            </div>
        @endif

        {{-- Error Alert --}}
        @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small text-start mb-3" role="alert">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                {{ session('error') ?? $errors->first() }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- PIN Form --}}
        <form action="{{ route('finance.security.verify') }}" method="POST" id="pinVerifyForm">
            @csrf
            <input type="hidden" name="pin" id="fullPinInput">

            {{-- 6 Individual PIN Input Boxes --}}
            <div class="pin-digit-group">
                @for($i = 1; $i <= 6; $i++)
                    <input type="password" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                           class="pin-box" id="pinBox{{ $i }}" data-index="{{ $i }}"
                           autocomplete="off">
                @endfor
            </div>

            {{-- On-Screen Keypad --}}
            <div class="keypad-grid">
                @for($n = 1; $n <= 9; $n++)
                    <div class="keypad-btn" data-key="{{ $n }}">{{ $n }}</div>
                @endfor
                <div class="keypad-btn action-btn" data-action="clear">
                    <i class="mdi mdi-close"></i>
                </div>
                <div class="keypad-btn" data-key="0">0</div>
                <div class="keypad-btn action-btn" data-action="backspace">
                    <i class="mdi mdi-backspace-outline"></i>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnSubmitPin" disabled>
                    <i class="mdi mdi-key-variant me-1"></i> Buka Akses Menu
                </button>
                <a href="{{ url('/') }}" class="btn btn-label-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const boxes = [
            document.getElementById('pinBox1'),
            document.getElementById('pinBox2'),
            document.getElementById('pinBox3'),
            document.getElementById('pinBox4'),
            document.getElementById('pinBox5'),
            document.getElementById('pinBox6')
        ];
        const fullPinInput = document.getElementById('fullPinInput');
        const submitBtn = document.getElementById('btnSubmitPin');
        const form = document.getElementById('pinVerifyForm');

        function updateFullPin() {
            let pinVal = '';
            boxes.forEach(b => pinVal += b.value);
            fullPinInput.value = pinVal;

            if (pinVal.length === 6) {
                submitBtn.disabled = false;
                // Auto submit when 6 digits are entered
                setTimeout(() => {
                    form.submit();
                }, 200);
            } else {
                submitBtn.disabled = true;
            }
        }

        // Focus first box
        if (boxes[0]) {
            boxes[0].focus();
        }

        // Keyboard navigation and entry in boxes
        boxes.forEach((box, idx) => {
            box.addEventListener('input', function (e) {
                const val = this.value.replace(/\D/g, '');
                this.value = val ? val.charAt(val.length - 1) : '';

                if (this.value && idx < 5) {
                    boxes[idx + 1].focus();
                }
                updateFullPin();
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) {
                    boxes[idx - 1].focus();
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    boxes[idx - 1].focus();
                } else if (e.key === 'ArrowRight' && idx < 5) {
                    boxes[idx + 1].focus();
                }
            });

            // Paste handler for 6 digits
            box.addEventListener('paste', function (e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                if (pasteData.length > 0) {
                    for (let i = 0; i < 6; i++) {
                        boxes[i].value = pasteData.charAt(i) || '';
                    }
                    const nextFocus = Math.min(pasteData.length, 5);
                    boxes[nextFocus].focus();
                    updateFullPin();
                }
            });
        });

        // Keypad button clicks
        document.querySelectorAll('.keypad-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const key = this.getAttribute('data-key');
                const action = this.getAttribute('data-action');

                if (key !== null) {
                    // Find first empty box
                    for (let i = 0; i < 6; i++) {
                        if (!boxes[i].value) {
                            boxes[i].value = key;
                            if (i < 5) boxes[i + 1].focus();
                            break;
                        }
                    }
                    updateFullPin();
                } else if (action === 'backspace') {
                    // Backspace from last filled
                    for (let i = 5; i >= 0; i--) {
                        if (boxes[i].value) {
                            boxes[i].value = '';
                            boxes[i].focus();
                            break;
                        }
                    }
                    updateFullPin();
                } else if (action === 'clear') {
                    boxes.forEach(b => b.value = '');
                    boxes[0].focus();
                    updateFullPin();
                }
            });
        });
    });
</script>
@endpush
