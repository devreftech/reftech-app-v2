<div class="modal-onboarding modal modal-lg fade animate__animated" id="detailPayment" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body"> Detail Payment of {{ $quote->no_quote }}</h4>
                    <div class="onboarding-info mb-3">
                        {{ $quote->pic->client->company }}
                    </div>
                    <div class="row mb-4 text-nowrap">
                        <table class="table table-bordered">
                            @php
                                $totalAmount = 0;
                                $remaining = $quote->harga_total;
                            @endphp
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        {{-- <a href="{{ route('download-payment.quotation', $payment->id) }}"
                                            class="btn btn-sm btn-primary d-grid w-100 waves-effect">Pay
                                            Image</a> --}}
                                        @if ($payment->file != null)
                                            <a href="{{ url($payment->file) }}"
                                                class="btn btn-sm btn-primary d-grid w-100 waves-effect"
                                                target="_blank">Pay
                                                Image</a>
                                        @else
                                            <input class="form-control" type="file" id="upload-photo" name="file"
                                                accept=".jpg,.jpeg,.png" data-id="{{ $payment->id }}">
                                        @endif
                                    </td>
                                    <td>
                                        <p>Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $payment->note }}</p>
                                    </td>
                                    @if (Auth::user()->role == 'Sales')
                                        <td>
                                            @if ($payment->level == 0)
                                                <p>Not Confirmed</p>
                                            @else
                                                <p>CONFIRMED!!</p>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" data-id="{{ $payment->id }}"
                                                data-quote="{{ $quote->id }}"
                                                class="btn btn-sm btn-label-danger delete-payments waves-effect">
                                                <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                            </a>
                                            @if ($payment->file != null)
                                                <button type="button" class="btn btn-sm btn-label-primary copy-link"
                                                    data-link="{{ route('payment_detail.payment', $payment->id) }}">
                                                    Copy Link
                                                </button>
                                            @endif
                                        </td>
                                    @elseif(Auth::user()->role == 'Admin')
                                        <td>
                                            @if ($payment->level == 0)
                                                <a href="#" data-id="{{ $payment->id }}"
                                                    {{-- data-invoice="{{ @$invoice->id }}" --}}
                                                    data-quote="{{ $quote->id }}"
                                                    class="btn btn-sm btn-label-success confirm-payments waves-effect">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-14px mdi-check-outline m-0"></i>
                                                </a>
                                            @else
                                                <p>CONFIRMED!!</p>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @php
                                    $totalAmount += $payment->amount;
                                    $remaining = $quote->harga_total - $totalAmount;
                                @endphp
                            @endforeach
                        </table>
                        {{-- <div class="col-4">
                                    <a href="#"
                                        class="btn btn-primary d-grid w-100 waves-effect">Download Payment</a>
                                </div>
                                <div class="col-4">
                                </div>
                                <div class="col-4">
                                </div> --}}
                        <hr>
                        <div class="col-6">
                            <h5 class="text-start"> Pay Remaining</h5>
                        </div>
                        <div class="col-6">
                            <h5>: Rp {{ number_format($remaining, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $('#upload-photo').on('change', function() {
            let id = $(this).data('id');
            let file = this.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append("file", file);
            formData.append("_token", "{{ csrf_token() }}");

            $.ajax({
                url: "/quotation/" + id + "/proof_payment",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        // langsung refresh halaman
                        location.reload();
                    } else {
                        alert("Upload gagal: " + res.message);
                    }
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan: " + xhr.responseText);
                }
            });
        });
    </script>
@endpush
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.copy-link').forEach(button => {
                button.addEventListener('click', function() {
                    const link = this.getAttribute('data-link');

                    navigator.clipboard.writeText(link)
                        .then(() => {
                            // Bootstrap alert kecil dan auto hilang
                            const alert = document.createElement('div');
                            alert.className =
                                'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                            alert.role = 'alert';
                            alert.innerHTML = `
                        Link berhasil disalin ke clipboard!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                            document.body.appendChild(alert);

                            setTimeout(() => {
                                alert.classList.remove('show');
                                alert.remove();
                            }, 2000);
                        })
                        .catch(err => {
                            alert('Gagal menyalin link: ' + err);
                        });
                });
            });
        });
    </script>
@endpush
