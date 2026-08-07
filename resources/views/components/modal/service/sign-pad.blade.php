<form action="{{ route('service-reports.sign', $service->id) }}" method="post" enctype="multipart/form-data"
    id="signPadForm-{{ $service->id }}">
    @csrf
    <input type="file" name="sign_client" id="signPadFileInput-{{ $service->id }}" accept="image/png" hidden>
    <div class="modal animate__animated animate__fadeIn" id="inputSignPad-{{ $service->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabelSignPad">Tanda Tangan Online</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="text-muted mb-2">Gambar tanda tangan di kotak bawah ini pakai jari (HP/tablet) atau
                        mouse.</p>
                    <div class="border rounded" style="touch-action: none;">
                        <canvas id="signPadCanvas-{{ $service->id }}" width="760" height="300"
                            style="width: 100%; height: 300px; display: block; cursor: crosshair;"></canvas>
                    </div>
                    <div class="mt-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            id="signPadClear-{{ $service->id }}">Bersihkan</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="signPadSave-{{ $service->id }}">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    (function() {
        var canvas = document.getElementById('signPadCanvas-{{ $service->id }}');
        var ctx = canvas.getContext('2d');
        var hasDrawn = false;
        var drawing = false;
        var lastX = 0;
        var lastY = 0;

        function clearCanvas() {
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
        }

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
            var clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function start(e) {
            drawing = true;
            hasDrawn = true;
            var pos = getPos(e);
            lastX = pos.x;
            lastY = pos.y;
            e.preventDefault();
        }

        function move(e) {
            if (!drawing) return;
            var pos = getPos(e);
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            lastX = pos.x;
            lastY = pos.y;
            e.preventDefault();
        }

        function end() {
            drawing = false;
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start, {
            passive: false
        });
        canvas.addEventListener('touchmove', move, {
            passive: false
        });
        canvas.addEventListener('touchend', end);

        document.getElementById('inputSignPad-{{ $service->id }}').addEventListener('shown.bs.modal', clearCanvas);

        document.getElementById('signPadClear-{{ $service->id }}').addEventListener('click', clearCanvas);

        document.getElementById('signPadSave-{{ $service->id }}').addEventListener('click', function() {
            if (!hasDrawn) {
                alert('Silakan gambar tanda tangan terlebih dahulu.');
                return;
            }
            if (typeof DataTransfer === 'undefined') {
                alert('Browser ini belum mendukung Tanda Tangan Online, silakan pakai fitur Upload Foto TTD.');
                return;
            }
            canvas.toBlob(function(blob) {
                var file = new File([blob], 'sign-' + Date.now() + '.png', {
                    type: 'image/png'
                });
                var dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('signPadFileInput-{{ $service->id }}').files = dt.files;
                document.getElementById('signPadForm-{{ $service->id }}').submit();
            }, 'image/png');
        });

        clearCanvas();
    })();
</script>
