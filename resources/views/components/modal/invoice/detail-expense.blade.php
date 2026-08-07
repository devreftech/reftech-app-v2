    <div class="modal modal-lg animate__animated animate__fadeIn" id="detailExpense" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Detail Expense {{ $invoice->no_invoice }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            $nomor = 1;
                        @endphp
                        <tbody>
                            @foreach ($expense as $expenses)
                                <tr>
                                    <td>
                                        {{ $nomor }}
                                    </td>
                                    <td>
                                        {{ $expenses->desc }}
                                    </td>
                                    <td>
                                        <p>{{ $expenses->qty }}</p>
                                    </td>
                                    <td>
                                        <p>Rp {{ number_format($expenses->price, 0, ',', '.') }}</p>
                                    </td>
                                    <td>
                                        <a href="#" data-id="{{ $expenses->id }}"
                                            data-invoice="{{ $invoice->id }}"
                                            class="btn btn-sm btn-label-danger delete-expense waves-effect">
                                            <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $nomor++;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="3">Total Expense</td>
                                <td colspan="2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Total Harga</td>
                                <td colspan="2">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Total After Expense</td>
                                <td colspan="2">Rp {{ number_format($hargaAfterExpanse, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
