<form action="{{ route('expense-account.update', 1) }}" id="editForm" method="POST">
    @csrf
    @method('PATCH')
    <div class="modal fade" id="editAccount" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning py-3 text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="mdi mdi-pencil-circle-outline me-2"></i> Edit Akun Chart of Accounts (COA)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="edit_code_input" class="form-control edit_code" name="code" placeholder="Misal: 6101" required>
                                <label for="edit_code_input">Kode Akun <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select edit_category" id="edit_category_select" name="category" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Cash/Bank">Cash / Bank</option>
                                    <option value="Expense">Expense (Beban Operasional)</option>
                                    <option value="Cost Of Good Sold">Cost of Goods Sold (HPP)</option>
                                    <option value="Other Expense">Other Expense (Beban Lain-lain)</option>
                                    <option value="Revenue">Revenue (Pendapatan Usaha)</option>
                                    <option value="Other Income">Other Income (Pendapatan Lain-lain)</option>
                                    <option value="Fixed Asset">Fixed Asset (Aset Tetap)</option>
                                    <option value="Other Current Asset">Other Current Asset (Aset Lancar Lainnya)</option>
                                    <option value="Account Receivable">Account Receivable (Piutang Usaha)</option>
                                    <option value="Account Payable">Account Payable (Hutang Usaha)</option>
                                    <option value="Other Current Liabilities">Other Current Liabilities (Kewajiban Lancar)</option>
                                    <option value="Long Term Liabilities">Long Term Liabilities (Kewajiban Jk Panjang)</option>
                                    <option value="Equity">Equity (Ekuitas / Modal)</option>
                                    <option value="Accumulated Depreciation">Accumulated Depreciation (Akumulasi Penyusutan)</option>
                                </select>
                                <label for="edit_category_select">Kategori Akun <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="edit_name_input" class="form-control edit_name" name="name" placeholder="Nama Akun Pembukuan" required>
                                <label for="edit_name_input">Nama Akun (COA) <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select edit_currency" id="edit_currency_select" name="currency">
                                    <option value="IDR" selected>IDR (Rupiah)</option>
                                    <option value="USD">USD (US Dollar)</option>
                                    <option value="EUR">EUR (Euro)</option>
                                    <option value="SGD">SGD (Singapore Dollar)</option>
                                </select>
                                <label for="edit_currency_select">Mata Uang (Currency)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select edit_saldo" id="edit_saldo_select" name="saldo">
                                    <option value="Debit">Debit (D)</option>
                                    <option value="Kredit">Kredit (K)</option>
                                </select>
                                <label for="edit_saldo_select">Posisi Saldo Normal</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select edit_parent" name="parent" id="parent">
                                    <option value="">-- Tanpa Induk (Jadikan Header Akun Level 1) --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}">
                                            {{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})
                                        </option>
                                    @endforeach
                                </select>
                                <label for="parent">Akun Induk (Parent COA)</label>
                            </div>
                            <small class="text-muted mt-1 d-block">Pilih akun induk jika akun ini merupakan sub-akun transaksi (Level 2).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 text-white">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Perbarui Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
