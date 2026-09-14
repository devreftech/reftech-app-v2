{{-- Modal Verifikasi PIN Keamanan Finance --}}
<div class="modal fade" id="financePinModal" tabindex="-1" aria-labelledby="financePinModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4 p-sm-5 text-center position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                
                {{-- Icon Badge --}}
                <div class="d-inline-flex align-items-center justify-content-center mb-3 text-white" 
                     style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); box-shadow: 0 8px 24px rgba(79, 70, 229, 0.35);">
                    <i class="mdi mdi-shield-lock-outline" style="font-size: 34px;"></i>
                </div>

                {{-- Title & Target Info --}}
                <h5 class="fw-bold mb-1 text-dark" id="financePinModalLabel">Verifikasi PIN Keamanan</h5>
                <p class="text-muted small mb-2">
                    Akses modul <span id="pinModalTargetName" class="fw-semibold text-primary">Kas &amp; Bank</span> dilindungi oleh PIN keamanan.
                </p>

                {{-- Alert Error --}}
                <div id="pinModalAlert" class="alert alert-danger py-2 px-3 small text-start d-none mb-3" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> <span id="pinModalAlertText">Kode PIN tidak sesuai.</span>
                </div>

                {{-- PIN Input Form --}}
                <form id="financePinForm" method="POST" action="{{ route('finance.security.verify') }}" autocomplete="off">
                    @csrf
                    <input type="hidden" name="target_url" id="pinModalTargetUrl" value="">

                    {{-- 6-Box PIN Input --}}
                    <div class="d-flex justify-content-center gap-2 mb-3" id="pinBoxesContainer">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="password" 
                                   inputmode="numeric" 
                                   pattern="[0-9]*" 
                                   maxlength="1" 
                                   class="form-control text-center fw-bold pin-digit-input" 
                                   data-index="{{ $i }}"
                                   style="width: 44px; height: 52px; font-size: 22px; border-radius: 10px; border: 2px solid #cbd5e1; background: #f8fafc;"
                                   autocomplete="off">
                        @endfor
                    </div>
                    <input type="hidden" name="pin" id="fullPinHidden" value="">

                    {{-- On-Screen Numeric Keypad --}}
                    <div class="keypad-wrapper mb-3" style="max-width: 260px; margin: 0 auto;">
                        <div class="row g-2">
                            @for ($n = 1; $n <= 9; $n++)
                                <div class="col-4">
                                    <button type="button" class="btn btn-light w-100 fw-bold fs-5 py-2 pin-keypad-btn shadow-none border" data-val="{{ $n }}" style="border-radius: 10px;">
                                        {{ $n }}
                                    </button>
                                </div>
                            @endfor
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 pin-clear-btn shadow-none" style="border-radius: 10px; font-size: 13px;">
                                    Clear
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-light w-100 fw-bold fs-5 py-2 pin-keypad-btn shadow-none border" data-val="0" style="border-radius: 10px;">
                                    0
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 pin-backspace-btn shadow-none" style="border-radius: 10px;">
                                    <i class="mdi mdi-backspace-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm" id="btnSubmitPin" style="border-radius: 10px;">
                            <span id="btnPinSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                            <span id="btnPinText"><i class="mdi mdi-lock-open-variant-outline me-1"></i> Buka Akses</span>
                        </button>
                        <button type="button" class="btn btn-label-secondary py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
                            Batal
                        </button>
                    </div>
                </form>

                @if(Auth::check() && (Auth::user()->isDeveloper() || Auth::user()->role === 'Developer'))
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-code-braces text-warning"></i> Developer Mode Active
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .pin-digit-input:focus {
        border-color: #4f46e5 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
        outline: none;
    }
    .pin-digit-input.is-invalid {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
    }
    .pin-keypad-btn:hover {
        background: #e2e8f0 !important;
    }
    .pin-keypad-btn:active {
        background: #cbd5e1 !important;
        transform: scale(0.96);
    }
    @keyframes pinShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-8px); }
        40%, 80% { transform: translateX(8px); }
    }
    .pin-shake {
        animation: pinShake 0.4s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var isPinVerified = @json((bool) session('finance_pin_verified'));
        window.isFinancePinVerified = isPinVerified;
        var pinModalEl = document.getElementById('financePinModal');
        var pinModal = pinModalEl ? new bootstrap.Modal(pinModalEl) : null;
        
        var inputs = document.querySelectorAll('.pin-digit-input');
        var fullPinHidden = document.getElementById('fullPinHidden');
        var targetUrlInput = document.getElementById('pinModalTargetUrl');
        var targetNameEl = document.getElementById('pinModalTargetName');
        var alertEl = document.getElementById('pinModalAlert');
        var alertTextEl = document.getElementById('pinModalAlertText');
        var form = document.getElementById('financePinForm');
        var btnSubmit = document.getElementById('btnSubmitPin');
        var btnSpinner = document.getElementById('btnPinSpinner');
        var btnText = document.getElementById('btnPinText');
        var boxesContainer = document.getElementById('pinBoxesContainer');

        function updateFullPin() {
            var pin = '';
            inputs.forEach(function(input) {
                pin += input.value;
            });
            fullPinHidden.value = pin;
            return pin;
        }

        function clearPin() {
            inputs.forEach(function(input) {
                input.value = '';
                input.classList.remove('is-invalid');
            });
            fullPinHidden.value = '';
            if (inputs[0]) inputs[0].focus();
        }

        function showError(msg) {
            alertTextEl.textContent = msg || 'Kode PIN yang Anda masukkan tidak sesuai.';
            alertEl.classList.remove('d-none');
            inputs.forEach(function(input) {
                input.classList.add('is-invalid');
            });
            if (boxesContainer) {
                boxesContainer.classList.add('pin-shake');
                setTimeout(function() {
                    boxesContainer.classList.remove('pin-shake');
                }, 500);
            }
        }

        function hideError() {
            alertEl.classList.add('d-none');
            inputs.forEach(function(input) {
                input.classList.remove('is-invalid');
            });
        }

        window.openFinancePinModal = function(targetUrl, targetName) {
            targetUrlInput.value = targetUrl || '{{ route("bank.index") }}';
            if (targetNameEl) {
                targetNameEl.textContent = targetName || 'Kas & Bank';
            }
            hideError();
            clearPin();
            if (pinModal) {
                pinModal.show();
                setTimeout(function() {
                    if (inputs[0]) inputs[0].focus();
                }, 300);
            }
        };

        // Inputs keyboard events
        inputs.forEach(function(input, idx) {
            input.addEventListener('input', function(e) {
                hideError();
                var val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val.charAt(val.length - 1) : '';
                
                var currentPin = updateFullPin();
                
                if (e.target.value && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                } else if (currentPin.length === 6) {
                    submitPinAjax();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!e.target.value && idx > 0) {
                        inputs[idx - 1].focus();
                        inputs[idx - 1].value = '';
                        updateFullPin();
                    }
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    inputs[idx - 1].focus();
                } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                if (pasteData) {
                    for (var i = 0; i < Math.min(pasteData.length, inputs.length); i++) {
                        inputs[i].value = pasteData[i];
                    }
                    var p = updateFullPin();
                    if (p.length === 6) {
                        submitPinAjax();
                    } else if (inputs[p.length]) {
                        inputs[p.length].focus();
                    }
                }
            });
        });

        // Keypad buttons
        document.querySelectorAll('.pin-keypad-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                hideError();
                var val = this.getAttribute('data-val');
                for (var i = 0; i < inputs.length; i++) {
                    if (!inputs[i].value) {
                        inputs[i].value = val;
                        if (i < inputs.length - 1) {
                            inputs[i + 1].focus();
                        }
                        break;
                    }
                }
                var p = updateFullPin();
                if (p.length === 6) {
                    submitPinAjax();
                }
            });
        });

        // Clear button
        var clearBtn = document.querySelector('.pin-clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                hideError();
                clearPin();
            });
        }

        // Backspace button
        var backspaceBtn = document.querySelector('.pin-backspace-btn');
        if (backspaceBtn) {
            backspaceBtn.addEventListener('click', function() {
                hideError();
                for (var i = inputs.length - 1; i >= 0; i--) {
                    if (inputs[i].value) {
                        inputs[i].value = '';
                        inputs[i].focus();
                        break;
                    }
                }
                updateFullPin();
            });
        }

        // Form submit function (AJAX)
        function submitPinAjax() {
            var pin = updateFullPin();
            if (pin.length !== 6) {
                showError('Silakan lengkapi 6 digit PIN keamanan.');
                return;
            }

            btnSubmit.disabled = true;
            btnSpinner.classList.remove('d-none');
            btnText.innerHTML = 'Memverifikasi...';

            var target = targetUrlInput.value || '{{ route("bank.index") }}';

            fetch('{{ route("finance.security.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    pin: pin,
                    target_url: target
                })
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function(resObj) {
                btnSubmit.disabled = false;
                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="mdi mdi-lock-open-variant-outline me-1"></i> Buka Akses';

                if (resObj.ok && resObj.data.success) {
                    window.isFinancePinVerified = true;
                    btnSubmit.classList.remove('btn-primary');
                    btnSubmit.classList.add('btn-success');
                    btnText.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Terverifikasi!';

                    setTimeout(function() {
                        window.location.href = resObj.data.redirect_url || target;
                    }, 400);
                } else {
                    showError(resObj.data.message || 'Kode PIN tidak sesuai.');
                    clearPin();
                }
            })
            .catch(function(err) {
                btnSubmit.disabled = false;
                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="mdi mdi-lock-open-variant-outline me-1"></i> Buka Akses';
                showError('Terjadi kesalahan koneksi sistem. Silakan coba kembali.');
            });
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitPinAjax();
            });
        }

        // Intercept all clicks on protected finance links in sidebar or pages
        document.addEventListener('click', function(e) {
            var trigger = e.target.closest('.finance-pin-trigger') || 
                          e.target.closest('a[href*="/finance/bank"]') || 
                          e.target.closest('a[href*="/finance/petty-cash"]') || 
                          e.target.closest('a[href*="/finance/bank-reconciliation"]');
            
            if (!trigger) return;

            // Ignore if lock action, security manage, or javascript trigger
            var href = trigger.getAttribute('href') || trigger.href || '';
            if (!href || href.includes('/finance/security') || href.includes('javascript:void') || href === '#') {
                return;
            }

            // If already verified in this session, let browser navigate normally
            if (window.isFinancePinVerified) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            var menuName = trigger.getAttribute('data-target-name');
            if (!menuName) {
                if (href.includes('petty-cash')) {
                    menuName = 'Petty Cash (Kas Kecil)';
                } else if (href.includes('bank-reconciliation')) {
                    menuName = 'Rekonsiliasi Bank';
                } else if (href.includes('bank')) {
                    menuName = 'Daftar Rekening Bank';
                } else {
                    menuName = trigger.querySelector('[data-i18n]')?.textContent?.trim() || 
                               trigger.textContent?.trim() || 
                               'Kas & Bank';
                }
            }

            window.openFinancePinModal(href, menuName);
        }, true);

        // Auto open modal if session requested it
        @if (session('open_finance_pin_modal'))
            @php
                $intendedUrl = session('finance_pin_intended', route('bank.index'));
                $targetLabel = 'Kas & Bank';
                if (str_contains($intendedUrl, 'petty-cash')) {
                    $targetLabel = 'Petty Cash (Kas Kecil)';
                } elseif (str_contains($intendedUrl, 'bank-reconciliation')) {
                    $targetLabel = 'Rekonsiliasi Bank';
                } elseif (str_contains($intendedUrl, 'bank')) {
                    $targetLabel = 'Daftar Rekening Bank';
                }
            @endphp
            window.openFinancePinModal('{{ $intendedUrl }}', '{{ $targetLabel }}');
        @endif
    });
</script>
