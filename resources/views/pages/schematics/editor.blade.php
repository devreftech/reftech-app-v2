@extends('layouts.sales.app')
@section('title', 'Schematic Diagram Editor - ' . ($schematic->title ?? 'Baru'))

@push('before-style')
<style>
    /* Workspace container sizing */
    .schematic-editor-container {
        height: calc(100vh - 145px) !important;
        min-height: 620px !important;
        display: flex !important;
        flex-direction: column !important;
        background: #ffffff;
        transition: all 0.25s ease;
    }
    .schematic-editor-container.is-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 99999 !important;
        border-radius: 0 !important;
        margin: 0 !important;
    }
    .schematic-editor-container > .card-body {
        display: flex !important;
        flex-direction: row !important;
        flex: 1 1 0% !important;
        height: 100% !important;
        min-height: 0 !important;
        overflow: hidden !important;
        position: relative !important;
    }
    .schematic-palette-sidebar {
        width: 230px;
        min-width: 230px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        transition: margin-left 0.2s ease, opacity 0.2s ease;
    }
    .schematic-palette-sidebar.is-collapsed {
        margin-left: -230px;
        opacity: 0;
        pointer-events: none;
    }
    .schematic-properties-sidebar {
        width: 250px;
        min-width: 250px;
        background: #ffffff;
        border-left: 1px solid #e2e8f0;
        transition: margin-right 0.2s ease, opacity 0.2s ease;
    }
    .schematic-properties-sidebar.is-collapsed {
        margin-right: -250px;
        opacity: 0;
        pointer-events: none;
    }
    .schematic-center-workspace {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        min-height: 0 !important;
        height: 100% !important;
        overflow: hidden !important;
        position: relative !important;
    }
    .schematic-canvas-area {
        background-color: #f8fafc !important;
        background-image: radial-gradient(#94a3b8 1.2px, transparent 1.2px) !important;
        background-size: 20px 20px !important;
        position: relative !important;
        overflow: hidden !important;
        user-select: none !important;
        flex: 1 1 0% !important;
        width: 100% !important;
        height: 100% !important;
        min-height: 0 !important;
    }
    #schematic-svg {
        width: 100% !important;
        height: 100% !important;
        display: block !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }
    .palette-item:hover {
        border-color: #3b82f6 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    .schematic-node:hover .node-graphic rect,
    .schematic-node:hover .node-graphic circle,
    .schematic-node:hover .node-graphic path {
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    }
    @keyframes pipeFlowPulse {
        from { stroke-dashoffset: 24; }
        to { stroke-dashoffset: 0; }
    }
    .pipe-flow-pulse {
        stroke-dasharray: 8, 6;
        animation: pipeFlowPulse 0.9s linear infinite;
        pointer-events: none;
    }
    .floating-line-toolbar {
        position: absolute;
        display: none;
        z-index: 10000;
        background: #1e293b;
        color: #ffffff;
        border-radius: 8px;
        padding: 4px 6px;
        box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.35), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        transform: translate(-50%, -100%);
        margin-top: -14px;
        transition: opacity 0.15s ease;
        align-items: center;
        gap: 2px;
    }
    .floating-line-toolbar .btn-whimsical-tool {
        background: transparent;
        border: none;
        color: #cbd5e1;
        padding: 5px 7px;
        border-radius: 4px;
        font-size: 13px;
        line-height: 1;
        cursor: pointer;
        transition: all 0.1s ease;
    }
    .floating-line-toolbar .btn-whimsical-tool:hover {
        background: #334155;
        color: #ffffff;
    }
    .floating-line-toolbar .btn-whimsical-tool.active {
        background: #3b82f6;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
    <!-- Top Metadata & Action Bar -->
    <div class="card border-0 shadow-sm mb-2" id="schematic-top-bar">
        <div class="card-body p-2 px-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <!-- Left: Title & Info -->
                <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 500px;">
                    <a href="{{ route('schematics.index') }}" class="btn btn-sm btn-outline-secondary px-2" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <div class="flex-grow-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted font-monospace fw-bold" style="font-size: 11px;">
                                {{ $schematic->schematic_number }}
                            </span>
                            <input type="text" id="schematic-title-input" class="form-control fw-bold" value="{{ $schematic->title }}" placeholder="Nama Skematik / Proyek">
                        </div>
                    </div>
                </div>

                <!-- Center: Project & Client Meta -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div style="min-width: 170px;">
                        <select id="schematic-client-select" class="form-select form-select-sm">
                            <option value="">-- Pilih Klien --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ $schematic->client_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 140px;">
                        <input type="text" id="schematic-project-input" class="form-control form-control-sm" value="{{ $schematic->project_name }}" placeholder="Nama Proyek / Ruangan">
                    </div>

                    <div style="min-width: 150px;">
                        <select id="schematic-type-select" class="form-select form-select-sm">
                            <option value="Refrigeration System" {{ $schematic->diagram_type === 'Refrigeration System' ? 'selected' : '' }}>Refrigeration System</option>
                            <option value="Compressed Air System" {{ $schematic->diagram_type === 'Compressed Air System' ? 'selected' : '' }}>Compressed Air System</option>
                            <option value="Piping & Instrumentation (P&ID)" {{ $schematic->diagram_type === 'Piping & Instrumentation (P&ID)' ? 'selected' : '' }}>Piping & Instrumentation (P&ID)</option>
                            <option value="Other" {{ $schematic->diagram_type === 'Other' ? 'selected' : '' }}>Lainnya / General</option>
                        </select>
                    </div>

                    <div style="min-width: 100px;">
                        <select id="schematic-status-select" class="form-select form-select-sm">
                            <option value="Draft" {{ $schematic->status === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Final" {{ $schematic->status === 'Final' ? 'selected' : '' }}>Final</option>
                            <option value="Review" {{ $schematic->status === 'Review' ? 'selected' : '' }}>Review</option>
                        </select>
                    </div>
                </div>

                <!-- Right: Save & Export Actions -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btn-export-png" class="btn btn-sm btn-outline-info">
                        <i class="mdi mdi-download me-1"></i>Export PNG
                    </button>
                    <button type="button" id="btn-save-schematic" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-content-save me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="card border-0 shadow-sm overflow-hidden schematic-editor-container" id="schematic-main-workspace">
        <div class="card-body p-0 position-relative">
            
            <!-- Left Sidebar: Component Palette -->
            <div class="schematic-palette-sidebar d-flex flex-column h-100" id="palette-sidebar">
                <div class="p-2 px-3 border-bottom bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary" style="font-size: 13px;"><i class="mdi mdi-shape-plus me-1"></i>Library</h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-light p-1" id="btn-collapse-palette" title="Sembunyikan Panel Library">
                        <i class="mdi mdi-chevron-left"></i>
                    </button>
                </div>
                <!-- Custom Image Upload Action -->
                <div class="p-2 border-bottom bg-white">
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm" id="btn-trigger-upload-image" title="Upload foto/gambar mesin (PNG/JPG/SVG)">
                        <i class="mdi mdi-cloud-upload-outline"></i>
                        <span class="fw-bold" style="font-size: 11.5px;">+ Upload Gambar</span>
                    </button>
                    <input type="file" id="input-custom-image-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" style="display: none;">
                </div>
                <div class="p-2 flex-grow-1 overflow-auto" id="palette-container" style="max-height: calc(100% - 90px);">
                    <!-- Palette items rendered by JS -->
                </div>
            </div>

            <!-- Center Workspace: Canvas + Floating Toolbar -->
            <div class="schematic-center-workspace position-relative">
                
                <!-- Canvas Control Toolbar -->
                <div class="p-2 border-bottom bg-white d-flex align-items-center justify-content-between z-1 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Toggle Palette Button if collapsed -->
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-show-palette" title="Buka Panel Library Komponen">
                            <i class="mdi mdi-shape-plus me-1"></i>Komponen
                        </button>

                        <div class="vr mx-1"></div>

                        <!-- Pipe Type Selector -->
                        <div class="d-flex align-items-center gap-1 bg-light p-1 rounded border">
                            <span class="text-muted small px-1 fw-bold"><i class="mdi mdi-pipe me-1"></i>Pipa:</span>
                            <select id="pipe-type-selector" class="form-select form-select-sm border-0 bg-white" style="width: 190px; font-size: 11px;">
                                <option value="discharge" selected>🔴 Discharge (HP Gas)</option>
                                <option value="suction">🔵 Suction (LP Vapor)</option>
                                <option value="liquid">🟢 Liquid Line</option>
                                <option value="air">🌐 Compressed Air</option>
                                <option value="hotgas">🟠 Hot Gas / Bypass</option>
                                <option value="oil">🟡 Oil Return</option>
                                <option value="water">⚪ Water / Drain</option>
                                <option value="general">⚫ Standard</option>
                            </select>
                        </div>

                        <div class="vr mx-1"></div>

                        <!-- Undo / Redo -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="btn-undo" title="Undo (Ctrl+Z)" disabled>
                                <i class="mdi mdi-undo"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-redo" title="Redo (Ctrl+Y)" disabled>
                                <i class="mdi mdi-redo"></i>
                            </button>
                        </div>

                        <div class="vr mx-1"></div>

                        <!-- Zoom Controls -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="btn-zoom-out" title="Zoom Out">
                                <i class="mdi mdi-minus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-zoom-reset" title="Reset Zoom">
                                <span id="zoom-percentage">100%</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-zoom-in" title="Zoom In">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>

                        <div class="vr mx-1"></div>

                        <!-- Snap to Grid -->
                        <div class="form-check form-switch mb-0 small">
                            <input class="form-check-input" type="checkbox" id="snap-to-grid-toggle" checked>
                            <label class="form-check-label small fw-semibold" for="snap-to-grid-toggle">Snap</label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <!-- Fullscreen Toggle -->
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-fullscreen" title="Layar Penuh (Fullscreen)">
                            <i class="mdi mdi-fullscreen" id="fullscreen-icon"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-clear-canvas" title="Kosongkan Canvas">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>

                        <!-- Toggle Properties Button if collapsed -->
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-show-properties" title="Buka/Tutup Properties">
                            <i class="mdi mdi-tune"></i>
                        </button>
                    </div>
                </div>

                <!-- Canvas Area (Fully Expanded) -->
                <div class="schematic-canvas-area" id="canvas-container">
                    <svg id="schematic-svg" style="width: 100%; height: 100%; display: block; position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                        <defs>
                            <pattern id="grid-pattern" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1.2" fill="#94a3b8" opacity="0.4"/>
                            </pattern>
                        </defs>
                        <rect id="svg-grid-rect" width="100%" height="100%" fill="url(#grid-pattern)" />
                        
                        <g id="svg-viewport">
                            <!-- Lines / Pipes layer -->
                            <g id="svg-lines-layer"></g>
                            <!-- Nodes / Equipment layer -->
                            <g id="svg-nodes-layer"></g>
                        </g>
                    </svg>

                    <!-- Whimsical Floating Micro-Toolbar on Line Selection -->
                    <div id="line-floating-toolbar" class="floating-line-toolbar">
                        <!-- Route Flip & Customizer -->
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn-whimsical-tool" id="btn-float-flip-route" title="Tukar Arah Belokan (Ke Atas Dulu / Ke Samping Dulu)">
                                <i class="mdi mdi-swap-vertical-bold"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool" id="btn-float-add-waypoint" title="Tambah Titik Belok Bebas">
                                <i class="mdi mdi-plus-circle-outline"></i>
                            </button>
                        </div>
                        <div class="vr bg-secondary mx-1" style="height: 18px; opacity: 0.5;"></div>
                        <!-- Shape: Elbow, Curved, Straight -->
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn-whimsical-tool btn-line-mode" data-mode="elbow" title="Siku Halus (Elbow)">
                                <i class="mdi mdi-vector-polyline"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-line-mode" data-mode="curved" title="Kurva (Curved)">
                                <i class="mdi mdi-vector-curve"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-line-mode" data-mode="straight" title="Lurus (Straight)">
                                <i class="mdi mdi-vector-line"></i>
                            </button>
                        </div>
                        <div class="vr bg-secondary mx-1" style="height: 18px; opacity: 0.5;"></div>
                        <!-- Pattern: Solid, Dashed, Dotted -->
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn-whimsical-tool btn-line-pattern" data-pattern="solid" title="Garis Solid">
                                <i class="mdi mdi-minus"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-line-pattern" data-pattern="dashed" title="Garis Putus-putus">
                                <i class="mdi mdi-ray-start-end"></i>
                            </button>
                        </div>
                        <div class="vr bg-secondary mx-1" style="height: 18px; opacity: 0.5;"></div>
                        <!-- Arrow & Flow Mode -->
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn-whimsical-tool btn-arrow-mode" data-arrow="end" title="Panah Satu Arah">
                                <i class="mdi mdi-arrow-right"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-arrow-mode" data-arrow="both" title="Panah Dua Arah">
                                <i class="mdi mdi-arrow-left-right"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-arrow-mode" data-arrow="flow" title="Animasi Aliran Fluida (Flow Pulse)">
                                <i class="mdi mdi-motion-play-outline"></i>
                            </button>
                            <button type="button" class="btn-whimsical-tool btn-arrow-mode" data-arrow="none" title="Tanpa Panah">
                                <i class="mdi mdi-ray-start"></i>
                            </button>
                        </div>
                        <div class="vr bg-secondary mx-1" style="height: 18px; opacity: 0.5;"></div>
                        <!-- Delete line -->
                        <button type="button" class="btn-whimsical-tool text-danger" id="btn-float-delete-line" title="Hapus Pipa">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>

                    <!-- Bottom Canvas Tips -->
                    <div class="position-absolute bottom-0 start-0 m-3 px-3 py-1 bg-white bg-opacity-90 rounded shadow-sm border small text-muted" style="pointer-events: none; font-size: 11px;">
                        <i class="mdi mdi-information-outline me-1 text-info"></i>
                        <strong>Tips Customize Jalur:</strong> Klik pipa, lalu tarik <strong>titik biru (waypoint)</strong> di canvas untuk membelokkan jalur (misal: ke atas dulu lewat atas mesin). Atau klik tombol <kbd>↕️</kbd> di toolbar cepat untuk membalik arah belokan.
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Properties Panel -->
            <div class="schematic-properties-sidebar d-flex flex-column h-100" id="properties-sidebar">
                <div class="p-2 px-3 border-bottom bg-light d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-primary" style="font-size: 13px;"><i class="mdi mdi-tune me-1"></i>Inspector</h6>
                    <button type="button" class="btn btn-sm btn-light p-1" id="btn-collapse-properties" title="Sembunyikan Inspector">
                        <i class="mdi mdi-chevron-right"></i>
                    </button>
                </div>
                <div class="p-3 flex-grow-1 overflow-auto" id="properties-panel-content">
                    <div class="text-center text-muted py-5">
                        <i class="mdi mdi-cursor-default-outline fs-1 opacity-50 mb-2"></i>
                        <p class="mb-0 fw-semibold">Pilih Komponen / Pipa</p>
                        <small>Klik komponen atau garis pipa untuk mengubah properti & ukurannya.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
<script>
    window.initialCanvasData = {!! json_encode($schematic->canvas_data ? json_decode($schematic->canvas_data) : null) !!};
</script>
<script src="{{ asset('assets/js/schematic-builder.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isCreate = {{ $schematic->id ? 'false' : 'true' }};
        const saveUrl = isCreate 
            ? '{{ route('schematics.store') }}' 
            : '{{ route('schematics.update', $schematic->id ?? 0) }}';

        window.builder = new SchematicBuilder({
            containerId: 'canvas-container',
            svgId: 'schematic-svg',
            viewportId: 'svg-viewport',
            linesLayerId: 'svg-lines-layer',
            nodesLayerId: 'svg-nodes-layer',
            saveUrl: saveUrl,
            isCreate: isCreate,
            csrfToken: '{{ csrf_token() }}'
        });

        // Sidebar Toggle Behaviors
        const paletteSidebar = document.getElementById('palette-sidebar');
        const propertiesSidebar = document.getElementById('properties-sidebar');
        const mainWorkspace = document.getElementById('schematic-main-workspace');

        document.getElementById('btn-collapse-palette')?.addEventListener('click', () => {
            paletteSidebar.classList.add('is-collapsed');
        });
        document.getElementById('btn-show-palette')?.addEventListener('click', () => {
            paletteSidebar.classList.toggle('is-collapsed');
        });

        document.getElementById('btn-collapse-properties')?.addEventListener('click', () => {
            propertiesSidebar.classList.add('is-collapsed');
        });
        document.getElementById('btn-show-properties')?.addEventListener('click', () => {
            propertiesSidebar.classList.toggle('is-collapsed');
        });

        // Fullscreen Toggle
        const btnFullscreen = document.getElementById('btn-toggle-fullscreen');
        const iconFullscreen = document.getElementById('fullscreen-icon');
        btnFullscreen?.addEventListener('click', () => {
            mainWorkspace.classList.toggle('is-fullscreen');
            if (mainWorkspace.classList.contains('is-fullscreen')) {
                iconFullscreen.classList.replace('mdi-fullscreen', 'mdi-fullscreen-exit');
            } else {
                iconFullscreen.classList.replace('mdi-fullscreen-exit', 'mdi-fullscreen');
            }
        });

        // Upload Custom Image File Handler
        const btnTriggerUpload = document.getElementById('btn-trigger-upload-image');
        const inputCustomImage = document.getElementById('input-custom-image-file');

        btnTriggerUpload?.addEventListener('click', () => {
            inputCustomImage?.click();
        });

        inputCustomImage?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                const dataUrl = event.target.result;
                const cleanName = file.name.replace(/\.[^/.]+$/, "");
                window.builder.addCustomImage(dataUrl, cleanName);
                inputCustomImage.value = '';
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
