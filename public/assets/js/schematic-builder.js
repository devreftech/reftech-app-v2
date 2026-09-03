/**
 * Reftech Online Schematic Diagram Builder Engine - Whimsical Style Piping & Custom Routing
 * Features:
 * - Rounded Elbows (90° fillet smooth arcs), Smooth Bezier Curves, and Straight connections.
 * - Custom Route Adjustment: Vertical-First (Ke Atas/Bawah Dulu), Horizontal-First, and Interactive Draggable Waypoint Handles.
 * - Arrowheads: End, Both, None, and Animated Flow Pulses.
 * - Line Patterns: Solid, Dashed, Dotted.
 * - Floating micro-toolbar on line selection for instant styling.
 * - Standard HVAC/P&ID symbols & Custom uploaded image components.
 */
(function (window) {
    'use strict';

    // Component Definitions / Symbols
    const COMPONENT_DEFINITIONS = {
        // --- COMPRESSORS & POWER ---
        'screw_compressor': {
            name: 'Screw Compressor',
            category: 'Compressors',
            width: 140,
            height: 90,
            color: '#1e40af',
            ports: [
                { id: 'suc', name: 'Suction', x: 0, y: 45, dir: 'left', type: 'suction' },
                { id: 'dis', name: 'Discharge', x: 140, y: 30, dir: 'right', type: 'discharge' },
                { id: 'oil', name: 'Oil Return', x: 70, y: 90, dir: 'bottom', type: 'oil' },
                { id: 'eco', name: 'Economizer', x: 70, y: 0, dir: 'top', type: 'liquid' }
            ],
            svg: `
                <rect x="5" y="10" width="130" height="70" rx="10" fill="#f8fafc" stroke="#1e40af" stroke-width="2.5"/>
                <circle cx="50" cy="45" r="24" fill="#dbeafe" stroke="#1e40af" stroke-width="1.5"/>
                <circle cx="85" cy="45" r="20" fill="#bfdbfe" stroke="#1e40af" stroke-width="1.5"/>
                <path d="M35 45 C45 30, 55 60, 65 45" stroke="#1e40af" stroke-width="2" fill="none"/>
                <path d="M70 45 C80 32, 90 58, 100 45" stroke="#1e40af" stroke-width="2" fill="none"/>
                <text x="70" y="83" font-size="10" font-weight="bold" fill="#1e3a8a" text-anchor="middle">SCREW COMP</text>
            `
        },
        'recip_compressor': {
            name: 'Reciprocating Compressor',
            category: 'Compressors',
            width: 120,
            height: 90,
            color: '#1e40af',
            ports: [
                { id: 'suc', name: 'Suction', x: 0, y: 45, dir: 'left', type: 'suction' },
                { id: 'dis', name: 'Discharge', x: 120, y: 30, dir: 'right', type: 'discharge' },
                { id: 'oil', name: 'Oil Drain', x: 60, y: 90, dir: 'bottom', type: 'oil' }
            ],
            svg: `
                <circle cx="60" cy="45" r="38" fill="#f8fafc" stroke="#1e40af" stroke-width="2.5"/>
                <circle cx="60" cy="45" r="28" fill="#dbeafe" stroke="#1e40af" stroke-width="1.5"/>
                <polygon points="50,30 70,30 60,60" fill="#1e40af"/>
                <rect x="52" y="60" width="16" height="6" fill="#1e40af"/>
                <text x="60" y="78" font-size="9" font-weight="bold" fill="#1e3a8a" text-anchor="middle">RECIP</text>
            `
        },
        'scroll_compressor': {
            name: 'Scroll Compressor',
            category: 'Compressors',
            width: 100,
            height: 110,
            color: '#1e40af',
            ports: [
                { id: 'suc', name: 'Suction', x: 0, y: 70, dir: 'left', type: 'suction' },
                { id: 'dis', name: 'Discharge', x: 50, y: 0, dir: 'top', type: 'discharge' },
                { id: 'oil', name: 'Sight Glass', x: 50, y: 110, dir: 'bottom', type: 'oil' }
            ],
            svg: `
                <rect x="15" y="10" width="70" height="90" rx="20" fill="#f8fafc" stroke="#1e40af" stroke-width="2.5"/>
                <path d="M50 35 A15 15 0 0 1 65 50 A15 15 0 0 1 50 65 A10 10 0 0 1 40 50 A5 5 0 0 1 50 45" fill="none" stroke="#1e40af" stroke-width="2"/>
                <text x="50" y="90" font-size="9" font-weight="bold" fill="#1e3a8a" text-anchor="middle">SCROLL</text>
            `
        },
        'chiller_package': {
            name: 'Chiller Package Unit',
            category: 'Compressors',
            width: 160,
            height: 100,
            color: '#0369a1',
            ports: [
                { id: 'chw_in', name: 'CHW In', x: 0, y: 35, dir: 'left', type: 'water' },
                { id: 'chw_out', name: 'CHW Out', x: 0, y: 65, dir: 'left', type: 'water' },
                { id: 'cdw_in', name: 'CDW In', x: 160, y: 35, dir: 'right', type: 'water' },
                { id: 'cdw_out', name: 'CDW Out', x: 160, y: 65, dir: 'right', type: 'water' }
            ],
            svg: `
                <rect x="5" y="5" width="150" height="90" rx="8" fill="#f0f9ff" stroke="#0369a1" stroke-width="2.5"/>
                <rect x="15" y="15" width="40" height="70" rx="4" fill="#bae6fd" stroke="#0369a1" stroke-width="1.5"/>
                <circle cx="95" cy="50" r="22" fill="#e0f2fe" stroke="#0369a1" stroke-width="1.5"/>
                <circle cx="130" cy="50" r="14" fill="#bae6fd" stroke="#0369a1" stroke-width="1"/>
                <text x="35" y="55" font-size="9" font-weight="bold" fill="#0369a1" text-anchor="middle">EVAP</text>
                <text x="95" y="54" font-size="8" font-weight="bold" fill="#0369a1" text-anchor="middle">COMP</text>
                <text x="95" y="85" font-size="10" font-weight="bold" fill="#0c4a6e" text-anchor="middle">CHILLER UNIT</text>
            `
        },

        // --- VESSELS & TANKS ---
        'air_receiver_tank': {
            name: 'Air Receiver Tank',
            category: 'Tanks & Vessels',
            width: 110,
            height: 140,
            color: '#0d9488',
            ports: [
                { id: 'in', name: 'Air Inlet', x: 0, y: 50, dir: 'left', type: 'air' },
                { id: 'out', name: 'Air Outlet', x: 110, y: 50, dir: 'right', type: 'air' },
                { id: 'prv', name: 'Safety Relief', x: 55, y: 0, dir: 'top', type: 'air' },
                { id: 'drain', name: 'Auto Drain', x: 55, y: 140, dir: 'bottom', type: 'water' }
            ],
            svg: `
                <path d="M20 30 C20 12, 90 12, 90 30 L90 110 C90 128, 20 128, 20 110 Z" fill="#f0fdfa" stroke="#0d9488" stroke-width="2.5"/>
                <line x1="20" y1="30" x2="90" y2="30" stroke="#0d9488" stroke-width="1" stroke-dasharray="3,3"/>
                <line x1="20" y1="110" x2="90" y2="110" stroke="#0d9488" stroke-width="1" stroke-dasharray="3,3"/>
                <circle cx="55" cy="55" r="14" fill="#ccfbf1" stroke="#0d9488" stroke-width="1.5"/>
                <line x1="55" y1="55" x2="62" y2="48" stroke="#0f766e" stroke-width="1.5"/>
                <text x="55" y="85" font-size="9" font-weight="bold" fill="#115e59" text-anchor="middle">RECEIVER</text>
                <text x="55" y="98" font-size="8" fill="#115e59" text-anchor="middle">TANK</text>
            `
        },
        'liquid_receiver': {
            name: 'Liquid Receiver (Horizontal)',
            category: 'Tanks & Vessels',
            width: 140,
            height: 80,
            color: '#0d9488',
            ports: [
                { id: 'in', name: 'Liquid In', x: 30, y: 0, dir: 'top', type: 'liquid' },
                { id: 'out', name: 'Liquid Out', x: 110, y: 80, dir: 'bottom', type: 'liquid' },
                { id: 'prv', name: 'PRV Port', x: 80, y: 0, dir: 'top', type: 'discharge' }
            ],
            svg: `
                <path d="M25 15 C10 15, 10 65, 25 65 L115 65 C130 65, 130 15, 115 15 Z" fill="#f0fdfa" stroke="#0d9488" stroke-width="2.5"/>
                <line x1="25" y1="15" x2="25" y2="65" stroke="#0d9488" stroke-width="1" stroke-dasharray="3,3"/>
                <line x1="115" y1="15" x2="115" y2="65" stroke="#0d9488" stroke-width="1" stroke-dasharray="3,3"/>
                <rect x="50" y="32" width="40" height="14" rx="7" fill="#99f6e4" stroke="#0d9488" stroke-width="1.5"/>
                <text x="70" y="58" font-size="9" font-weight="bold" fill="#115e59" text-anchor="middle">LIQUID RECEIVER</text>
            `
        },
        'oil_separator': {
            name: 'Oil Separator',
            category: 'Tanks & Vessels',
            width: 80,
            height: 120,
            color: '#ca8a04',
            ports: [
                { id: 'in', name: 'Discharge In', x: 0, y: 35, dir: 'left', type: 'discharge' },
                { id: 'out', name: 'Gas Out', x: 40, y: 0, dir: 'top', type: 'discharge' },
                { id: 'oil_out', name: 'Oil Return', x: 40, y: 120, dir: 'bottom', type: 'oil' }
            ],
            svg: `
                <rect x="15" y="15" width="50" height="90" rx="16" fill="#fefce8" stroke="#ca8a04" stroke-width="2.5"/>
                <path d="M25 45 L55 45 L40 70 Z" fill="#fef08a" stroke="#ca8a04" stroke-width="1.5"/>
                <circle cx="40" cy="85" r="6" fill="#eab308"/>
                <text x="40" y="100" font-size="8" font-weight="bold" fill="#854d0e" text-anchor="middle">OIL SEP</text>
            `
        },
        'suction_accumulator': {
            name: 'Suction Accumulator',
            category: 'Tanks & Vessels',
            width: 80,
            height: 120,
            color: '#2563eb',
            ports: [
                { id: 'in', name: 'Vapor In', x: 20, y: 0, dir: 'top', type: 'suction' },
                { id: 'out', name: 'Vapor Out', x: 60, y: 0, dir: 'top', type: 'suction' },
                { id: 'drain', name: 'Oil Orifice', x: 40, y: 120, dir: 'bottom', type: 'oil' }
            ],
            svg: `
                <rect x="15" y="15" width="50" height="90" rx="16" fill="#eff6ff" stroke="#2563eb" stroke-width="2.5"/>
                <path d="M55 20 L55 80 A15 15 0 0 1 25 80 L25 50" fill="none" stroke="#2563eb" stroke-width="2"/>
                <text x="40" y="98" font-size="8" font-weight="bold" fill="#1e40af" text-anchor="middle">ACCUMULATOR</text>
            `
        },

        // --- HEAT EXCHANGERS ---
        'condenser_air': {
            name: 'Air-Cooled Condenser',
            category: 'Heat Exchangers',
            width: 140,
            height: 90,
            color: '#dc2626',
            ports: [
                { id: 'in', name: 'Discharge In', x: 0, y: 25, dir: 'left', type: 'discharge' },
                { id: 'out', name: 'Liquid Out', x: 140, y: 65, dir: 'right', type: 'liquid' }
            ],
            svg: `
                <rect x="10" y="10" width="120" height="70" rx="6" fill="#fef2f2" stroke="#dc2626" stroke-width="2"/>
                <path d="M20 25 L100 25 C110 25, 110 45, 100 45 L30 45 C20 45, 20 65, 30 65 L120 65" fill="none" stroke="#dc2626" stroke-width="2.5"/>
                <circle cx="65" cy="45" r="18" fill="none" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="4,2"/>
                <line x1="65" y1="30" x2="65" y2="60" stroke="#dc2626" stroke-width="1.5"/>
                <line x1="50" y1="45" x2="80" y2="45" stroke="#dc2626" stroke-width="1.5"/>
                <text x="65" y="78" font-size="9" font-weight="bold" fill="#991b1b" text-anchor="middle">CONDENSER</text>
            `
        },
        'evaporator_unit': {
            name: 'Evaporator / Cooling Unit',
            category: 'Heat Exchangers',
            width: 140,
            height: 90,
            color: '#2563eb',
            ports: [
                { id: 'in', name: 'Liquid / TXV In', x: 0, y: 65, dir: 'left', type: 'liquid' },
                { id: 'out', name: 'Suction Out', x: 140, y: 25, dir: 'right', type: 'suction' },
                { id: 'drain', name: 'Drain Pan', x: 70, y: 90, dir: 'bottom', type: 'water' }
            ],
            svg: `
                <rect x="10" y="10" width="120" height="70" rx="6" fill="#eff6ff" stroke="#2563eb" stroke-width="2"/>
                <path d="M20 65 L100 65 C110 65, 110 45, 100 45 L30 45 C20 45, 20 25, 30 25 L120 25" fill="none" stroke="#2563eb" stroke-width="2.5"/>
                <circle cx="65" cy="45" r="18" fill="none" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4,2"/>
                <polygon points="65,35 72,45 65,55 58,45" fill="#93c5fd"/>
                <text x="65" y="78" font-size="9" font-weight="bold" fill="#1e40af" text-anchor="middle">EVAPORATOR</text>
            `
        },
        'plate_heat_exchanger': {
            name: 'Plate Heat Exchanger (PHE)',
            category: 'Heat Exchangers',
            width: 100,
            height: 110,
            color: '#4f46e5',
            ports: [
                { id: 'r_in', name: 'Refrig In', x: 0, y: 25, dir: 'left', type: 'discharge' },
                { id: 'r_out', name: 'Refrig Out', x: 0, y: 85, dir: 'left', type: 'liquid' },
                { id: 'w_in', name: 'Fluid In', x: 100, y: 85, dir: 'right', type: 'water' },
                { id: 'w_out', name: 'Fluid Out', x: 100, y: 25, dir: 'right', type: 'water' }
            ],
            svg: `
                <rect x="15" y="10" width="70" height="90" rx="4" fill="#eef2ff" stroke="#4f46e5" stroke-width="2"/>
                <line x1="30" y1="20" x2="30" y2="90" stroke="#6366f1" stroke-width="2"/>
                <line x1="45" y1="20" x2="45" y2="90" stroke="#6366f1" stroke-width="2"/>
                <line x1="60" y1="20" x2="60" y2="90" stroke="#6366f1" stroke-width="2"/>
                <line x1="70" y1="20" x2="70" y2="90" stroke="#6366f1" stroke-width="2"/>
                <text x="50" y="60" font-size="9" font-weight="bold" fill="#3730a3" text-anchor="middle">PHE</text>
            `
        },

        // --- AIR TREATMENT & ACCESSORIES ---
        'air_dryer': {
            name: 'Refrigerated Air Dryer',
            category: 'Treatment & Filters',
            width: 120,
            height: 90,
            color: '#0284c7',
            ports: [
                { id: 'in', name: 'Wet Air In', x: 0, y: 45, dir: 'left', type: 'air' },
                { id: 'out', name: 'Dry Air Out', x: 120, y: 45, dir: 'right', type: 'air' },
                { id: 'drain', name: 'Condensate Drain', x: 60, y: 90, dir: 'bottom', type: 'water' }
            ],
            svg: `
                <rect x="10" y="10" width="100" height="70" rx="8" fill="#f0f9ff" stroke="#0284c7" stroke-width="2"/>
                <rect x="25" y="25" width="30" height="40" rx="4" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                <circle cx="75" cy="45" r="14" fill="#e0f2fe" stroke="#0284c7" stroke-width="1"/>
                <text x="40" y="50" font-size="8" fill="#0369a1" text-anchor="middle">HX</text>
                <text x="75" y="49" font-size="7" fill="#0369a1" text-anchor="middle">REF</text>
                <text x="60" y="76" font-size="8" font-weight="bold" fill="#075985" text-anchor="middle">AIR DRYER</text>
            `
        },
        'filter_drier': {
            name: 'Filter Drier',
            category: 'Treatment & Filters',
            width: 80,
            height: 50,
            color: '#059669',
            ports: [
                { id: 'in', name: 'Inlet', x: 0, y: 25, dir: 'left', type: 'liquid' },
                { id: 'out', name: 'Outlet', x: 80, y: 25, dir: 'right', type: 'liquid' }
            ],
            svg: `
                <rect x="15" y="10" width="50" height="30" rx="15" fill="#ecfdf5" stroke="#059669" stroke-width="2"/>
                <line x1="32" y1="12" x2="32" y2="38" stroke="#10b981" stroke-width="1.5" stroke-dasharray="2,2"/>
                <line x1="48" y1="12" x2="48" y2="38" stroke="#10b981" stroke-width="1.5" stroke-dasharray="2,2"/>
                <polygon points="36,25 44,20 44,30" fill="#059669"/>
                <text x="40" y="47" font-size="7" font-weight="bold" fill="#065f46" text-anchor="middle">DRIER</text>
            `
        },
        'sight_glass': {
            name: 'Sight Glass / Indicator',
            category: 'Treatment & Filters',
            width: 60,
            height: 50,
            color: '#059669',
            ports: [
                { id: 'in', name: 'Inlet', x: 0, y: 25, dir: 'left', type: 'liquid' },
                { id: 'out', name: 'Outlet', x: 60, y: 25, dir: 'right', type: 'liquid' }
            ],
            svg: `
                <line x1="0" y1="25" x2="60" y2="25" stroke="#059669" stroke-width="2"/>
                <circle cx="30" cy="25" r="14" fill="#ecfdf5" stroke="#059669" stroke-width="2"/>
                <circle cx="30" cy="25" r="7" fill="#a7f3d0" stroke="#059669" stroke-width="1"/>
                <circle cx="30" cy="25" r="3" fill="#10b981"/>
            `
        },

        // --- VALVES & CONTROLS ---
        'ball_valve': {
            name: 'Ball Valve',
            category: 'Valves',
            width: 60,
            height: 50,
            color: '#475569',
            ports: [
                { id: 'in', name: 'Port A', x: 0, y: 25, dir: 'left', type: 'general' },
                { id: 'out', name: 'Port B', x: 60, y: 25, dir: 'right', type: 'general' }
            ],
            svg: `
                <polygon points="12,12 48,38 48,12 12,38" fill="#f1f5f9" stroke="#334155" stroke-width="2"/>
                <circle cx="30" cy="25" r="4" fill="#334155"/>
                <line x1="30" y1="25" x2="30" y2="5" stroke="#334155" stroke-width="2"/>
                <line x1="22" y1="5" x2="38" y2="5" stroke="#334155" stroke-width="2"/>
            `
        },
        'check_valve': {
            name: 'Check / Non-Return Valve',
            category: 'Valves',
            width: 60,
            height: 50,
            color: '#475569',
            ports: [
                { id: 'in', name: 'Inlet', x: 0, y: 25, dir: 'left', type: 'general' },
                { id: 'out', name: 'Outlet', x: 60, y: 25, dir: 'right', type: 'general' }
            ],
            svg: `
                <polygon points="12,12 48,38 48,12 12,38" fill="#f1f5f9" stroke="#334155" stroke-width="2"/>
                <line x1="32" y1="12" x2="32" y2="38" stroke="#334155" stroke-width="2"/>
                <circle cx="32" cy="25" r="4" fill="#ef4444"/>
            `
        },
        'solenoid_valve': {
            name: 'Solenoid Valve',
            category: 'Valves',
            width: 60,
            height: 60,
            color: '#9333ea',
            ports: [
                { id: 'in', name: 'Inlet', x: 0, y: 35, dir: 'left', type: 'general' },
                { id: 'out', name: 'Outlet', x: 60, y: 35, dir: 'right', type: 'general' }
            ],
            svg: `
                <polygon points="12,22 48,48 48,22 12,48" fill="#faf5ff" stroke="#9333ea" stroke-width="2"/>
                <line x1="30" y1="35" x2="30" y2="18" stroke="#9333ea" stroke-width="2"/>
                <rect x="22" y="6" width="16" height="12" fill="#d8b4fe" stroke="#9333ea" stroke-width="1.5"/>
                <text x="30" y="15" font-size="8" font-weight="bold" fill="#581c87" text-anchor="middle">S</text>
            `
        },
        'expansion_valve': {
            name: 'Expansion Valve (TXV / EXV)',
            category: 'Valves',
            width: 70,
            height: 65,
            color: '#2563eb',
            ports: [
                { id: 'in', name: 'Liquid In', x: 0, y: 40, dir: 'left', type: 'liquid' },
                { id: 'out', name: 'Low Press Out', x: 70, y: 40, dir: 'right', type: 'liquid' },
                { id: 'bulb', name: 'Sensing Bulb', x: 35, y: 0, dir: 'top', type: 'sensor' }
            ],
            svg: `
                <polygon points="15,25 55,55 55,25 15,55" fill="#eff6ff" stroke="#2563eb" stroke-width="2"/>
                <path d="M22 20 C22 8, 48 8, 48 20 Z" fill="#bfdbfe" stroke="#2563eb" stroke-width="1.5"/>
                <line x1="35" y1="20" x2="35" y2="40" stroke="#2563eb" stroke-width="2"/>
                <text x="35" y="62" font-size="7" font-weight="bold" fill="#1e40af" text-anchor="middle">TXV</text>
            `
        },
        'prv_valve': {
            name: 'Safety Relief Valve (PRV)',
            category: 'Valves',
            width: 50,
            height: 60,
            color: '#dc2626',
            ports: [
                { id: 'in', name: 'Inlet', x: 25, y: 60, dir: 'bottom', type: 'general' },
                { id: 'out', name: 'Vented Outlet', x: 50, y: 30, dir: 'right', type: 'general' }
            ],
            svg: `
                <polygon points="10,45 40,15 40,45 10,15" fill="#fef2f2" stroke="#dc2626" stroke-width="1.5" transform="rotate(45 25 30)"/>
                <line x1="25" y1="30" x2="25" y2="10" stroke="#dc2626" stroke-width="2"/>
                <polygon points="20,10 30,10 25,4" fill="#dc2626"/>
                <text x="25" y="55" font-size="7" font-weight="bold" fill="#991b1b" text-anchor="middle">PRV</text>
            `
        },

        // --- INSTRUMENTS & GAUGES ---
        'pressure_gauge': {
            name: 'Pressure Gauge',
            category: 'Instruments',
            width: 50,
            height: 60,
            color: '#0284c7',
            ports: [
                { id: 'in', name: 'Tap', x: 25, y: 60, dir: 'bottom', type: 'sensor' }
            ],
            svg: `
                <line x1="25" y1="60" x2="25" y2="40" stroke="#0284c7" stroke-width="2"/>
                <circle cx="25" cy="22" r="18" fill="#f0f9ff" stroke="#0284c7" stroke-width="2"/>
                <text x="25" y="26" font-size="11" font-weight="bold" fill="#0369a1" text-anchor="middle">PI</text>
            `
        },
        'temp_gauge': {
            name: 'Temperature Gauge / Sensor',
            category: 'Instruments',
            width: 50,
            height: 60,
            color: '#ea580c',
            ports: [
                { id: 'in', name: 'Tap', x: 25, y: 60, dir: 'bottom', type: 'sensor' }
            ],
            svg: `
                <line x1="25" y1="60" x2="25" y2="40" stroke="#ea580c" stroke-width="2"/>
                <circle cx="25" cy="22" r="18" fill="#fff7ed" stroke="#ea580c" stroke-width="2"/>
                <text x="25" y="26" font-size="11" font-weight="bold" fill="#c2410c" text-anchor="middle">TI</text>
            `
        },
        'flow_meter': {
            name: 'Flow Meter',
            category: 'Instruments',
            width: 60,
            height: 60,
            color: '#0d9488',
            ports: [
                { id: 'in', name: 'Inlet', x: 0, y: 30, dir: 'left', type: 'general' },
                { id: 'out', name: 'Outlet', x: 60, y: 30, dir: 'right', type: 'general' }
            ],
            svg: `
                <line x1="0" y1="30" x2="60" y2="30" stroke="#0d9488" stroke-width="2"/>
                <circle cx="30" cy="30" r="16" fill="#f0fdfa" stroke="#0d9488" stroke-width="2"/>
                <text x="30" y="34" font-size="11" font-weight="bold" fill="#0f766e" text-anchor="middle">FI</text>
            `
        },
        'pressure_switch': {
            name: 'Dual Pressure Switch (HP/LP)',
            category: 'Instruments',
            width: 70,
            height: 60,
            color: '#d97706',
            ports: [
                { id: 'hp', name: 'HP Tap', x: 15, y: 60, dir: 'bottom', type: 'discharge' },
                { id: 'lp', name: 'LP Tap', x: 55, y: 60, dir: 'bottom', type: 'suction' }
            ],
            svg: `
                <rect x="8" y="10" width="54" height="38" rx="6" fill="#fffbeb" stroke="#d97706" stroke-width="2"/>
                <text x="35" y="28" font-size="9" font-weight="bold" fill="#b45309" text-anchor="middle">DPS</text>
                <text x="35" y="40" font-size="7" fill="#92400e" text-anchor="middle">HP / LP</text>
            `
        },

        // --- ANNOTATIONS ---
        'text_box': {
            name: 'Text Annotation / Tag',
            category: 'Annotations',
            width: 120,
            height: 40,
            color: '#64748b',
            ports: [],
            svg: `
                <rect x="2" y="2" width="116" height="36" rx="4" fill="#ffffff" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="3,3"/>
                <text x="60" y="24" font-size="10" font-weight="bold" fill="#334155" text-anchor="middle">Label / Notes</text>
            `
        },
        'zone_box': {
            name: 'Zone / Boundary Area',
            category: 'Annotations',
            width: 220,
            height: 160,
            color: '#94a3b8',
            ports: [],
            svg: `
                <rect x="4" y="4" width="212" height="152" rx="8" fill="#f8fafc" fill-opacity="0.5" stroke="#94a3b8" stroke-width="2" stroke-dasharray="6,4"/>
                <rect x="10" y="10" width="90" height="20" rx="4" fill="#e2e8f0"/>
                <text x="55" y="24" font-size="9" font-weight="bold" fill="#475569" text-anchor="middle">ZONE AREA</text>
            `
        },

        // --- CUSTOM IMAGE COMPONENT ---
        'custom_image': {
            name: 'Custom Image Component',
            category: 'Custom Uploads',
            width: 130,
            height: 90,
            color: '#6366f1',
            ports: [
                { id: 'left', name: 'Left Port', x: 0, y: 45, dir: 'left', type: 'general' },
                { id: 'right', name: 'Right Port', x: 130, y: 45, dir: 'right', type: 'general' },
                { id: 'top', name: 'Top Port', x: 65, y: 0, dir: 'top', type: 'general' },
                { id: 'bottom', name: 'Bottom Port', x: 65, y: 90, dir: 'bottom', type: 'general' }
            ],
            svg: `
                <rect x="4" y="4" width="122" height="82" rx="8" fill="#f8fafc" stroke="#6366f1" stroke-width="2" stroke-dasharray="4,2"/>
                <circle cx="65" cy="38" r="14" fill="#e0e7ff"/>
                <text x="65" y="42" font-size="12" fill="#4f46e5" text-anchor="middle">🖼️</text>
                <text x="65" y="68" font-size="9" font-weight="bold" fill="#4338ca" text-anchor="middle">GAMBAR</text>
            `
        }
    };

    // Piping Line Styles
    const PIPING_TYPES = {
        'discharge': { name: 'Discharge Gas (HP)', color: '#ef4444', width: 3.5, dash: '' },
        'suction':   { name: 'Suction Vapor (LP)', color: '#3b82f6', width: 3.5, dash: '' },
        'liquid':    { name: 'Liquid Line',        color: '#10b981', width: 3.5, dash: '' },
        'air':       { name: 'Compressed Air',    color: '#06b6d4', width: 3.5, dash: '' },
        'hotgas':    { name: 'Hot Gas / Bypass',   color: '#f97316', width: 3.0, dash: '6,3' },
        'oil':       { name: 'Oil Line',           color: '#eab308', width: 3.0, dash: '4,2' },
        'water':     { name: 'Water / Condensate', color: '#64748b', width: 3.0, dash: '' },
        'general':   { name: 'Standard Line',      color: '#334155', width: 3.0, dash: '' }
    };

    class SchematicBuilder {
        constructor(options) {
            this.container = document.getElementById(options.containerId);
            this.svg = document.getElementById(options.svgId);
            this.viewport = document.getElementById(options.viewportId || 'svg-viewport');
            this.linesLayer = document.getElementById(options.linesLayerId || 'svg-lines-layer');
            this.nodesLayer = document.getElementById(options.nodesLayerId || 'svg-nodes-layer');
            this.floatingToolbar = document.getElementById('line-floating-toolbar');
            
            this.saveUrl = options.saveUrl;
            this.isCreate = options.isCreate || false;
            this.csrfToken = options.csrfToken;

            this.nodes = [];
            this.connections = [];
            this.customImages = [];
            this.selectedItem = null;

            // Viewport transform
            this.pan = { x: 40, y: 40 };
            this.zoom = 1;
            this.gridSize = 20;
            this.snapToGrid = true;
            this.activePipeType = 'discharge';
            this.activeLineMode = 'elbow'; // 'elbow', 'curved', 'straight'
            this.activeArrowMode = 'end';  // 'end', 'both', 'none', 'flow'

            // Interaction states
            this.isPanning = false;
            this.panStart = { x: 0, y: 0 };
            this.isDraggingNode = false;
            this.draggedNode = null;
            this.dragOffset = { x: 0, y: 0 };
            this.isConnecting = false;
            this.connectingSource = null;

            // Waypoint dragging state
            this.isDraggingWaypoint = false;
            this.draggedConn = null;
            this.draggedWaypointIndex = 0;

            // History
            this.history = [];
            this.historyIndex = -1;

            this.init();
        }

        init() {
            this.renderPalette();
            this.setupEventListeners();
            this.setupFloatingToolbarEvents();
            this.updateTransform();

            if (window.initialCanvasData) {
                try {
                    const data = typeof window.initialCanvasData === 'string' ? JSON.parse(window.initialCanvasData) : window.initialCanvasData;
                    this.loadJSON(data, false);
                } catch (e) {
                    console.error('Failed to parse initial canvas data:', e);
                }
            } else {
                this.pushHistory();
            }

            // Post-layout render guarantee
            setTimeout(() => {
                this.updateTransform();
                this.renderAll();
            }, 60);
        }

        renderPalette() {
            const categories = {};
            for (const [key, def] of Object.entries(COMPONENT_DEFINITIONS)) {
                if (!categories[def.category]) {
                    categories[def.category] = [];
                }
                categories[def.category].push({ key, ...def });
            }

            const paletteEl = document.getElementById('palette-container');
            if (!paletteEl) return;

            let html = '';

            if (this.customImages.length > 0) {
                html += `
                    <div class="palette-category mb-3">
                        <div class="palette-category-title text-uppercase fw-bold text-primary mb-2 px-1 d-flex align-items-center justify-content-between" style="font-size: 11px; letter-spacing: 0.5px;">
                            <span><i class="mdi mdi-image-multiple me-1"></i>Gambar Custom</span>
                            <span class="badge bg-label-primary">${this.customImages.length}</span>
                        </div>
                        <div class="row g-2">
                `;

                this.customImages.forEach((imgItem, idx) => {
                    html += `
                        <div class="col-6">
                            <div class="palette-item p-2 text-center rounded border bg-white shadow-sm"
                                 draggable="true"
                                 data-custom-idx="${idx}"
                                 style="cursor: grab; transition: all 0.15s ease;"
                                 title="${imgItem.name}">
                                <div class="palette-preview mb-1 d-flex align-items-center justify-content-center" style="height: 52px; overflow: hidden;">
                                    <img src="${imgItem.url}" style="max-height: 48px; max-width: 100%; object-fit: contain;">
                                </div>
                                <div class="palette-label text-truncate text-dark fw-semibold" style="font-size: 11px;">
                                    ${imgItem.name}
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            }

            for (const [catName, items] of Object.entries(categories)) {
                if (catName === 'Custom Uploads' && this.customImages.length === 0) continue;

                html += `
                    <div class="palette-category mb-3">
                        <div class="palette-category-title text-uppercase fw-bold text-muted mb-2 px-1" style="font-size: 11px; letter-spacing: 0.5px;">
                            ${catName}
                        </div>
                        <div class="row g-2">
                `;

                items.forEach(item => {
                    html += `
                        <div class="col-6">
                            <div class="palette-item p-2 text-center rounded border bg-white shadow-sm"
                                 draggable="true"
                                 data-type="${item.key}"
                                 style="cursor: grab; transition: all 0.15s ease;"
                                 title="${item.name}">
                                <div class="palette-preview mb-1 d-flex align-items-center justify-content-center" style="height: 52px; overflow: hidden;">
                                    <svg viewBox="0 0 ${item.width} ${item.height}" style="max-height: 48px; max-width: 100%;">
                                        ${item.svg}
                                    </svg>
                                </div>
                                <div class="palette-label text-truncate text-dark fw-semibold" style="font-size: 11px;">
                                    ${item.name}
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            }

            paletteEl.innerHTML = html;

            paletteEl.querySelectorAll('.palette-item').forEach(el => {
                el.addEventListener('dragstart', (e) => {
                    const customIdx = el.getAttribute('data-custom-idx');
                    if (customIdx !== null) {
                        e.dataTransfer.setData('text/custom-idx', customIdx);
                    } else {
                        e.dataTransfer.setData('text/plain', el.getAttribute('data-type'));
                    }
                    el.style.opacity = '0.5';
                });

                el.addEventListener('dragend', () => {
                    el.style.opacity = '1';
                });

                el.addEventListener('click', () => {
                    const customIdx = el.getAttribute('data-custom-idx');
                    const rect = this.container.getBoundingClientRect();
                    const centerPt = this.screenToCanvas(rect.width / 2, rect.height / 2);

                    if (customIdx !== null) {
                        const imgItem = this.customImages[parseInt(customIdx)];
                        if (imgItem) {
                            this.addCustomImageNode(imgItem.url, imgItem.name, centerPt.x - (imgItem.width || 130) / 2, centerPt.y - (imgItem.height || 90) / 2, imgItem.width, imgItem.height, {
                                aspectRatio: imgItem.aspectRatio
                            });
                        }
                    } else {
                        const type = el.getAttribute('data-type');
                        if (type && COMPONENT_DEFINITIONS[type]) {
                            const def = COMPONENT_DEFINITIONS[type];
                            let x = centerPt.x - def.width / 2;
                            let y = centerPt.y - def.height / 2;
                            if (this.snapToGrid) {
                                x = Math.round(x / this.gridSize) * this.gridSize;
                                y = Math.round(y / this.gridSize) * this.gridSize;
                            }
                            this.addNode(type, x, y);
                        }
                    }
                });
            });
        }

        setupEventListeners() {
            this.container.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
            });

            this.container.addEventListener('drop', (e) => {
                e.preventDefault();
                const rect = this.container.getBoundingClientRect();
                const clientX = e.clientX - rect.left;
                const clientY = e.clientY - rect.top;
                const pt = this.screenToCanvas(clientX, clientY);

                const customIdx = e.dataTransfer.getData('text/custom-idx');
                if (customIdx !== '') {
                    const imgItem = this.customImages[parseInt(customIdx)];
                    if (imgItem) {
                        this.addCustomImageNode(imgItem.url, imgItem.name, pt.x - (imgItem.width || 130) / 2, pt.y - (imgItem.height || 90) / 2, imgItem.width, imgItem.height, {
                            aspectRatio: imgItem.aspectRatio
                        });
                        return;
                    }
                }

                const type = e.dataTransfer.getData('text/plain');
                if (type && COMPONENT_DEFINITIONS[type]) {
                    const def = COMPONENT_DEFINITIONS[type];
                    let x = pt.x - def.width / 2;
                    let y = pt.y - def.height / 2;
                    if (this.snapToGrid) {
                        x = Math.round(x / this.gridSize) * this.gridSize;
                        y = Math.round(y / this.gridSize) * this.gridSize;
                    }
                    this.addNode(type, x, y);
                }
            });

            this.svg.addEventListener('mousedown', (e) => {
                if (e.button === 1 || e.target === this.svg || e.target.id === 'svg-grid-rect') {
                    this.isPanning = true;
                    this.panStart = { x: e.clientX - this.pan.x, y: e.clientY - this.pan.y };
                    this.svg.style.cursor = 'grabbing';
                    this.deselectAll();
                }
            });

            window.addEventListener('mousemove', (e) => {
                if (this.isPanning) {
                    this.pan.x = e.clientX - this.panStart.x;
                    this.pan.y = e.clientY - this.panStart.y;
                    this.updateTransform();
                    this.updateFloatingToolbarPos();
                } else if (this.isDraggingNode && this.draggedNode) {
                    const rect = this.container.getBoundingClientRect();
                    const clientX = e.clientX - rect.left;
                    const clientY = e.clientY - rect.top;
                    const pt = this.screenToCanvas(clientX, clientY);
                    
                    let newX = pt.x - this.dragOffset.x;
                    let newY = pt.y - this.dragOffset.y;

                    if (this.snapToGrid) {
                        newX = Math.round(newX / this.gridSize) * this.gridSize;
                        newY = Math.round(newY / this.gridSize) * this.gridSize;
                    }

                    this.draggedNode.x = newX;
                    this.draggedNode.y = newY;
                    this.updateNodePosition(this.draggedNode);
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                } else if (this.isDraggingWaypoint && this.draggedConn) {
                    const rect = this.container.getBoundingClientRect();
                    const clientX = e.clientX - rect.left;
                    const clientY = e.clientY - rect.top;
                    let pt = this.screenToCanvas(clientX, clientY);

                    if (this.snapToGrid) {
                        pt.x = Math.round(pt.x / this.gridSize) * this.gridSize;
                        pt.y = Math.round(pt.y / this.gridSize) * this.gridSize;
                    }

                    if (!this.draggedConn.waypoints) {
                        this.draggedConn.waypoints = [];
                    }
                    this.draggedConn.waypoints[this.draggedWaypointIndex] = { x: pt.x, y: pt.y };
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                } else if (this.isConnecting && this.connectingSource) {
                    const rect = this.container.getBoundingClientRect();
                    const clientX = e.clientX - rect.left;
                    const clientY = e.clientY - rect.top;
                    let pt = this.screenToCanvas(clientX, clientY);

                    // Magnetic preview snap to nearest port
                    let snapped = false;
                    this.nodes.forEach(node => {
                        if (node.id === this.connectingSource.nodeId) return;
                        const ports = this.getNodePorts(node);
                        ports.forEach(port => {
                            const portPos = { x: node.x + port.x, y: node.y + port.y };
                            const dist = Math.sqrt(Math.pow(pt.x - portPos.x, 2) + Math.pow(pt.y - portPos.y, 2));
                            if (dist < 30) {
                                pt = portPos;
                                snapped = true;
                            }
                        });
                    });

                    this.renderTempConnection(this.connectingSource, pt);
                }
            });

            window.addEventListener('mouseup', (e) => {
                if (this.isPanning) {
                    this.isPanning = false;
                    this.svg.style.cursor = 'default';
                }
                if (this.isDraggingNode) {
                    this.isDraggingNode = false;
                    this.draggedNode = null;
                    this.pushHistory();
                }
                if (this.isDraggingWaypoint) {
                    this.isDraggingWaypoint = false;
                    this.draggedConn = null;
                    this.pushHistory();
                }
                if (this.isConnecting && this.connectingSource) {
                    const rect = this.container.getBoundingClientRect();
                    const clientX = e.clientX - rect.left;
                    const clientY = e.clientY - rect.top;
                    const pt = this.screenToCanvas(clientX, clientY);

                    // Find closest target port across all other nodes within 35px radius
                    let closestTarget = null;
                    let minDistance = 35;

                    this.nodes.forEach(node => {
                        if (node.id === this.connectingSource.nodeId) return;
                        const ports = this.getNodePorts(node);
                        ports.forEach(port => {
                            const portPos = { x: node.x + port.x, y: node.y + port.y };
                            const dist = Math.sqrt(Math.pow(pt.x - portPos.x, 2) + Math.pow(pt.y - portPos.y, 2));
                            if (dist < minDistance) {
                                minDistance = dist;
                                closestTarget = {
                                    nodeId: node.id,
                                    portId: port.id,
                                    portType: port.type
                                };
                            }
                        });
                    });

                    if (closestTarget) {
                        this.addConnection(
                            this.connectingSource.nodeId,
                            this.connectingSource.portId,
                            closestTarget.nodeId,
                            closestTarget.portId,
                            this.activePipeType || this.connectingSource.portType || 'discharge'
                        );
                    }

                    this.isConnecting = false;
                    this.removeTempConnection();
                    this.connectingSource = null;
                }
            });

            this.container.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomFactor = e.deltaY < 0 ? 1.1 : 0.9;
                const rect = this.container.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;

                const newZoom = Math.min(Math.max(this.zoom * zoomFactor, 0.3), 3.0);

                this.pan.x = mouseX - (mouseX - this.pan.x) * (newZoom / this.zoom);
                this.pan.y = mouseY - (mouseY - this.pan.y) * (newZoom / this.zoom);
                this.zoom = newZoom;
                this.updateTransform();
                this.updateZoomDisplay();
                this.updateFloatingToolbarPos();
            }, { passive: false });

            window.addEventListener('keydown', (e) => {
                if ((e.key === 'Delete' || e.key === 'Backspace') && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                    this.deleteSelected();
                }
                if (e.ctrlKey && e.key.toLowerCase() === 'z') {
                    e.preventDefault();
                    if (e.shiftKey) this.redo();
                    else this.undo();
                } else if (e.ctrlKey && e.key.toLowerCase() === 'y') {
                    e.preventDefault();
                    this.redo();
                } else if (e.ctrlKey && e.key.toLowerCase() === 's') {
                    e.preventDefault();
                    this.saveToServer();
                }
            });

            // Toolbar Bindings
            document.getElementById('btn-save-schematic')?.addEventListener('click', () => this.saveToServer());
            document.getElementById('btn-export-png')?.addEventListener('click', () => this.exportPNG());
            document.getElementById('btn-zoom-in')?.addEventListener('click', () => this.setZoom(this.zoom * 1.2));
            document.getElementById('btn-zoom-out')?.addEventListener('click', () => this.setZoom(this.zoom / 1.2));
            document.getElementById('btn-zoom-reset')?.addEventListener('click', () => {
                this.zoom = 1;
                this.pan = { x: 40, y: 40 };
                this.updateTransform();
                this.updateZoomDisplay();
                this.updateFloatingToolbarPos();
            });

            document.getElementById('btn-undo')?.addEventListener('click', () => this.undo());
            document.getElementById('btn-redo')?.addEventListener('click', () => this.redo());
            document.getElementById('btn-clear-canvas')?.addEventListener('click', () => {
                if (confirm('Kosongkan semua komponen di canvas?')) {
                    this.nodes = [];
                    this.connections = [];
                    this.renderAll();
                    this.deselectAll();
                    this.pushHistory();
                }
            });

            document.getElementById('snap-to-grid-toggle')?.addEventListener('change', (e) => {
                this.snapToGrid = e.target.checked;
            });

            document.getElementById('pipe-type-selector')?.addEventListener('change', (e) => {
                this.activePipeType = e.target.value;
            });
        }

        setupFloatingToolbarEvents() {
            if (!this.floatingToolbar) return;

            // Line Mode buttons (Elbow, Curved, Straight)
            this.floatingToolbar.querySelectorAll('.btn-line-mode').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mode = btn.getAttribute('data-mode');
                    if (this.selectedItem && this.selectedItem.type === 'line') {
                        const conn = this.connections.find(c => c.id === this.selectedItem.id);
                        if (conn) {
                            conn.lineMode = mode;
                            this.renderConnections();
                            this.updateFloatingToolbarPos();
                            this.updateInspector();
                            this.pushHistory();
                        }
                    }
                });
            });

            // Route Direction Flip button (Horizontal-first vs Vertical-first)
            document.getElementById('btn-float-flip-route')?.addEventListener('click', () => {
                if (this.selectedItem && this.selectedItem.type === 'line') {
                    const conn = this.connections.find(c => c.id === this.selectedItem.id);
                    if (conn) {
                        conn.routeDir = (conn.routeDir === 'v-first') ? 'h-first' : 'v-first';
                        this.renderConnections();
                        this.updateFloatingToolbarPos();
                        this.updateInspector();
                        this.pushHistory();
                    }
                }
            });

            // Add manual waypoint button
            document.getElementById('btn-float-add-waypoint')?.addEventListener('click', () => {
                if (this.selectedItem && this.selectedItem.type === 'line') {
                    const conn = this.connections.find(c => c.id === this.selectedItem.id);
                    if (conn) {
                        const p1 = this.getPortCoords(conn.fromNodeId, conn.fromPortId);
                        const p2 = this.getPortCoords(conn.toNodeId, conn.toPortId);
                        if (p1 && p2) {
                            const midX = (p1.x + p2.x) / 2;
                            const midY = (p1.y + p2.y) / 2 - 40; // Offset upwards
                            if (!conn.waypoints) conn.waypoints = [];
                            conn.waypoints.push({ x: midX, y: midY });
                            this.renderConnections();
                            this.updateFloatingToolbarPos();
                            this.updateInspector();
                            this.pushHistory();
                        }
                    }
                }
            });

            // Arrow Mode buttons (End, None, Both, Flow)
            this.floatingToolbar.querySelectorAll('.btn-arrow-mode').forEach(btn => {
                btn.addEventListener('click', () => {
                    const arrow = btn.getAttribute('data-arrow');
                    if (this.selectedItem && this.selectedItem.type === 'line') {
                        const conn = this.connections.find(c => c.id === this.selectedItem.id);
                        if (conn) {
                            conn.arrowMode = arrow;
                            this.renderConnections();
                            this.updateFloatingToolbarPos();
                            this.updateInspector();
                            this.pushHistory();
                        }
                    }
                });
            });

            // Pattern buttons (Solid, Dashed)
            this.floatingToolbar.querySelectorAll('.btn-line-pattern').forEach(btn => {
                btn.addEventListener('click', () => {
                    const pattern = btn.getAttribute('data-pattern');
                    if (this.selectedItem && this.selectedItem.type === 'line') {
                        const conn = this.connections.find(c => c.id === this.selectedItem.id);
                        if (conn) {
                            conn.linePattern = pattern;
                            this.renderConnections();
                            this.updateFloatingToolbarPos();
                            this.updateInspector();
                            this.pushHistory();
                        }
                    }
                });
            });

            // Delete button on floating toolbar
            document.getElementById('btn-float-delete-line')?.addEventListener('click', () => {
                this.deleteSelected();
            });
        }

        screenToCanvas(screenX, screenY) {
            return {
                x: (screenX - this.pan.x) / this.zoom,
                y: (screenY - this.pan.y) / this.zoom
            };
        }

        canvasToScreen(canvasX, canvasY) {
            return {
                x: canvasX * this.zoom + this.pan.x,
                y: canvasY * this.zoom + this.pan.y
            };
        }

        updateTransform() {
            this.viewport.setAttribute('transform', `translate(${this.pan.x}, ${this.pan.y}) scale(${this.zoom})`);
            const gridPattern = document.getElementById('grid-pattern');
            if (gridPattern) {
                gridPattern.setAttribute('x', this.pan.x % (this.gridSize * this.zoom));
                gridPattern.setAttribute('y', this.pan.y % (this.gridSize * this.zoom));
            }
        }

        setZoom(newZoom) {
            this.zoom = Math.min(Math.max(newZoom, 0.3), 3.0);
            this.updateTransform();
            this.updateZoomDisplay();
            this.updateFloatingToolbarPos();
        }

        updateZoomDisplay() {
            const el = document.getElementById('zoom-percentage');
            if (el) el.textContent = `${Math.round(this.zoom * 100)}%`;
        }

        // --- CUSTOM IMAGE HANDLER ---
        addCustomImage(dataUrl, name = 'Custom Image') {
            const img = new Image();
            img.onload = () => {
                const naturalW = img.naturalWidth || 130;
                const naturalH = img.naturalHeight || 90;

                const maxDim = 130;
                let finalW, finalH;
                if (naturalW >= naturalH) {
                    finalW = maxDim;
                    finalH = Math.max(Math.round(maxDim * (naturalH / naturalW)), 30);
                } else {
                    finalH = maxDim;
                    finalW = Math.max(Math.round(maxDim * (naturalW / naturalH)), 30);
                }

                this.customImages.push({
                    url: dataUrl,
                    name: name,
                    width: finalW,
                    height: finalH,
                    aspectRatio: naturalW / naturalH
                });
                this.renderPalette();

                const rect = this.container.getBoundingClientRect();
                const centerPt = this.screenToCanvas(rect.width / 2, rect.height / 2);
                this.addCustomImageNode(dataUrl, name, centerPt.x - finalW / 2, centerPt.y - finalH / 2, finalW, finalH, {
                    aspectRatio: naturalW / naturalH
                });
            };
            img.src = dataUrl;
        }

        addCustomImageNode(imageUrl, name = 'Custom Image', x = 100, y = 100, width = null, height = null, customProps = {}) {
            const id = 'node_img_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const countSame = this.nodes.filter(n => n.type === 'custom_image').length + 1;
            const defaultTag = 'IMG-' + String(countSame).padStart(2, '0');

            let nodeW = width || 130;
            let nodeH = height || 90;

            let nodeX = x;
            let nodeY = y;
            if (this.snapToGrid) {
                nodeX = Math.round(nodeX / this.gridSize) * this.gridSize;
                nodeY = Math.round(nodeY / this.gridSize) * this.gridSize;
            }

            const node = {
                id,
                type: 'custom_image',
                name: name,
                imageUrl: imageUrl,
                x: nodeX,
                y: nodeY,
                width: nodeW,
                height: nodeH,
                aspectRatio: customProps.aspectRatio || (nodeW / nodeH),
                rotation: 0,
                tag: customProps.tag || defaultTag,
                spec: customProps.spec || '',
                notes: customProps.notes || '',
                pipeSize: customProps.pipeSize || '',
                ...customProps
            };

            this.nodes.push(node);
            this.renderNode(node);
            this.selectItem('node', node.id);
            this.pushHistory();
            return node;
        }

        // --- NODES MANAGEMENT ---
        addNode(type, x, y, customProps = {}) {
            const def = COMPONENT_DEFINITIONS[type];
            if (!def) return;

            const id = 'node_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const countSame = this.nodes.filter(n => n.type === type).length + 1;
            const defaultTag = (def.name.split(' ')[0].toUpperCase().substring(0, 4)) + '-' + String(countSame).padStart(2, '0');

            const node = {
                id,
                type,
                x,
                y,
                width: def.width,
                height: def.height,
                rotation: 0,
                tag: customProps.tag || defaultTag,
                spec: customProps.spec || '',
                notes: customProps.notes || '',
                pipeSize: customProps.pipeSize || '',
                ...customProps
            };

            this.nodes.push(node);
            this.renderNode(node);
            this.selectItem('node', node.id);
            this.pushHistory();
            return node;
        }

        getNodePorts(node) {
            if (node.type === 'custom_image' || node.imageUrl) {
                return [
                    { id: 'left', name: 'Inlet (Left)', x: 0, y: node.height / 2, dir: 'left', type: 'general' },
                    { id: 'right', name: 'Outlet (Right)', x: node.width, y: node.height / 2, dir: 'right', type: 'general' },
                    { id: 'top', name: 'Top Port', x: node.width / 2, y: 0, dir: 'top', type: 'general' },
                    { id: 'bottom', name: 'Bottom Port', x: node.width / 2, y: node.height, dir: 'bottom', type: 'general' }
                ];
            }
            const def = COMPONENT_DEFINITIONS[node.type];
            return def ? def.ports : [];
        }

        renderNode(node) {
            const def = COMPONENT_DEFINITIONS[node.type] || COMPONENT_DEFINITIONS['custom_image'];
            if (!def) return;

            let group = document.getElementById(node.id);
            if (!group) {
                group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                group.setAttribute('id', node.id);
                group.setAttribute('class', 'schematic-node');
                group.style.cursor = 'move';
                this.nodesLayer.appendChild(group);

                group.addEventListener('mousedown', (e) => {
                    if (e.target.classList.contains('port-circle') || e.target.classList.contains('port-hitbox')) {
                        return;
                    }
                    e.stopPropagation();
                    this.selectItem('node', node.id);
                    this.isDraggingNode = true;
                    this.draggedNode = node;
                    
                    const rect = this.container.getBoundingClientRect();
                    const pt = this.screenToCanvas(e.clientX - rect.left, e.clientY - rect.top);
                    this.dragOffset = { x: pt.x - node.x, y: pt.y - node.y };
                });
            }

            this.updateNodePosition(node);

            const isSelected = this.selectedItem && this.selectedItem.type === 'node' && this.selectedItem.id === node.id;
            const borderStroke = isSelected ? '#3b82f6' : 'transparent';
            const borderDash = isSelected ? '4,3' : 'none';

            let graphicHtml = '';
            if (node.type === 'custom_image' || node.imageUrl) {
                graphicHtml = `
                    <image href="${node.imageUrl}" x="0" y="0" width="${node.width}" height="${node.height}" preserveAspectRatio="none"/>
                `;
            } else {
                graphicHtml = def.svg;
            }

            const ports = this.getNodePorts(node);
            let portsHtml = '';
            ports.forEach(p => {
                const portColor = (PIPING_TYPES[p.type] || PIPING_TYPES.general).color;
                portsHtml += `
                    <g class="node-port" data-node-id="${node.id}" data-port-id="${p.id}" transform="translate(${p.x}, ${p.y})">
                        <circle class="port-hitbox" r="11" fill="transparent" style="cursor: crosshair;"/>
                        <circle class="port-circle" r="5" fill="${portColor}" stroke="#ffffff" stroke-width="1.8" style="cursor: crosshair; transition: transform 0.15s ease; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));"/>
                        <title>${p.name} (${p.type})</title>
                    </g>
                `;
            });

            group.innerHTML = `
                <!-- Selection Box -->
                <rect x="-6" y="-6" width="${node.width + 12}" height="${node.height + 12}" rx="6" fill="none" stroke="${borderStroke}" stroke-width="2" stroke-dasharray="${borderDash}"/>
                <!-- Component Graphic -->
                <g class="node-graphic">
                    ${graphicHtml}
                </g>
                <!-- Tag Badge -->
                <g class="node-tag-badge" transform="translate(${node.width / 2}, ${node.height + 16})">
                    <rect x="-40" y="-10" width="80" height="18" rx="4" fill="#1e293b" fill-opacity="0.85"/>
                    <text x="0" y="3" font-size="10" font-weight="bold" fill="#ffffff" text-anchor="middle">${node.tag || ''}</text>
                </g>
                <!-- Connection Ports -->
                ${portsHtml}
            `;

            group.querySelectorAll('.node-port').forEach(portEl => {
                const portId = portEl.getAttribute('data-port-id');
                const pDef = ports.find(p => p.id === portId);

                portEl.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                    this.isConnecting = true;
                    this.connectingSource = {
                        nodeId: node.id,
                        portId: portId,
                        portType: pDef ? pDef.type : 'general',
                        x: node.x + (pDef ? pDef.x : 0),
                        y: node.y + (pDef ? pDef.y : 0),
                        dir: pDef ? pDef.dir : 'right'
                    };
                });

                portEl.addEventListener('mouseenter', () => {
                    portEl.querySelector('.port-circle').setAttribute('r', '8');
                });

                portEl.addEventListener('mouseleave', () => {
                    portEl.querySelector('.port-circle').setAttribute('r', '5');
                });

                portEl.addEventListener('mouseup', (e) => {
                    if (this.isConnecting && this.connectingSource) {
                        e.stopPropagation();
                        if (this.connectingSource.nodeId !== node.id) {
                            this.addConnection(
                                this.connectingSource.nodeId,
                                this.connectingSource.portId,
                                node.id,
                                portId,
                                this.activePipeType || this.connectingSource.portType || 'discharge'
                            );
                        }
                    }
                });
            });
        }

        updateNodePosition(node) {
            const group = document.getElementById(node.id);
            if (group) {
                group.setAttribute('transform', `translate(${node.x}, ${node.y}) rotate(${node.rotation || 0}, ${node.width / 2}, ${node.height / 2})`);
            }
        }

        // --- CONNECTIONS MANAGEMENT ---
        addConnection(fromNodeId, fromPortId, toNodeId, toPortId, pipeType = 'discharge', customProps = {}) {
            const exists = this.connections.some(c => 
                (c.fromNodeId === fromNodeId && c.fromPortId === fromPortId && c.toNodeId === toNodeId && c.toPortId === toPortId) ||
                (c.fromNodeId === toNodeId && c.fromPortId === toPortId && c.toNodeId === fromNodeId && c.toPortId === fromPortId)
            );
            if (exists) return;

            const id = 'conn_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const conn = {
                id,
                fromNodeId,
                fromPortId,
                toNodeId,
                toPortId,
                pipeType: pipeType || this.activePipeType || 'discharge',
                lineMode: this.activeLineMode || 'elbow',   // 'elbow', 'curved', 'straight'
                arrowMode: this.activeArrowMode || 'end',  // 'end', 'both', 'none', 'flow'
                routeDir: 'h-first',                       // 'h-first' (ke samping dulu) or 'v-first' (ke atas/bawah dulu)
                waypoints: [],                             // Custom draggable bend points [{x, y}]
                linePattern: 'solid',                      // 'solid', 'dashed', 'dotted'
                size: customProps.size || '',
                label: customProps.label || '',
                ...customProps
            };

            this.connections.push(conn);
            this.renderConnections();
            this.selectItem('line', conn.id);
            this.pushHistory();
            return conn;
        }

        getPortCoords(nodeId, portId) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return null;
            
            const ports = this.getNodePorts(node);
            const port = ports.find(p => p.id === portId);
            if (!port) return { x: node.x + node.width / 2, y: node.y + node.height / 2, dir: 'right' };

            return {
                x: node.x + port.x,
                y: node.y + port.y,
                dir: port.dir || 'right'
            };
        }

        // Generate discrete route points sequence for a connection
        getConnectionPoints(p1, p2, conn = {}) {
            const routeDir = conn.routeDir || 'h-first';
            const waypoints = conn.waypoints || [];

            if (waypoints.length > 0) {
                // Multi-waypoint routing
                return [p1, ...waypoints, p2];
            }

            if (routeDir === 'v-first') {
                // Ke Atas / Ke Bawah dulu baru ke samping
                const midY = (p1.y + p2.y) / 2;
                return [
                    p1,
                    { x: p1.x, y: midY },
                    { x: p2.x, y: midY },
                    p2
                ];
            } else {
                // Ke Samping dulu baru ke atas / bawah (h-first default)
                const midX = (p1.x + p2.x) / 2;
                return [
                    p1,
                    { x: midX, y: p1.y },
                    { x: midX, y: p2.y },
                    p2
                ];
            }
        }

        // Whimsical Multi-point Rounded Fillet Path Generator
        renderRoundedPath(points, r = 12) {
            if (!points || points.length < 2) return '';
            if (points.length === 2) {
                return `M ${points[0].x} ${points[0].y} L ${points[1].x} ${points[1].y}`;
            }

            let d = `M ${points[0].x} ${points[0].y}`;

            for (let i = 1; i < points.length - 1; i++) {
                const prev = points[i - 1];
                const curr = points[i];
                const next = points[i + 1];

                const d1x = curr.x - prev.x;
                const d1y = curr.y - prev.y;
                const len1 = Math.sqrt(d1x * d1x + d1y * d1y);

                const d2x = next.x - curr.x;
                const d2y = next.y - curr.y;
                const len2 = Math.sqrt(d2x * d2x + d2y * d2y);

                const currentR = Math.min(r, len1 / 2, len2 / 2);

                if (currentR < 2 || len1 === 0 || len2 === 0) {
                    d += ` L ${curr.x} ${curr.y}`;
                } else {
                    const startX = curr.x - (d1x / len1) * currentR;
                    const startY = curr.y - (d1y / len1) * currentR;
                    const endX = curr.x + (d2x / len2) * currentR;
                    const endY = curr.y + (d2y / len2) * currentR;

                    d += ` L ${startX} ${startY} Q ${curr.x} ${curr.y} ${endX} ${endY}`;
                }
            }

            d += ` L ${points[points.length - 1].x} ${points[points.length - 1].y}`;
            return d;
        }

        // Whimsical Path Calculation: Smooth Rounded Elbows, Bezier Curves, and Straight Lines
        calculatePipePath(p1, p2, conn = {}) {
            const mode = conn.lineMode || 'elbow';

            if (mode === 'straight') {
                return `M ${p1.x} ${p1.y} L ${p2.x} ${p2.y}`;
            }

            if (mode === 'curved') {
                const waypoints = conn.waypoints || [];
                if (waypoints.length > 0) {
                    // Curved with waypoints
                    return this.renderRoundedPath([p1, ...waypoints, p2], 30);
                }

                const dx = p2.x - p1.x;
                const dy = p2.y - p1.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const curvature = Math.min(Math.max(dist * 0.45, 30), 160);

                let c1x = p1.x, c1y = p1.y, c2x = p2.x, c2y = p2.y;
                if (p1.dir === 'right') c1x += curvature;
                else if (p1.dir === 'left') c1x -= curvature;
                else if (p1.dir === 'bottom') c1y += curvature;
                else if (p1.dir === 'top') c1y -= curvature;
                else c1x += dx * 0.4;

                if (p2.dir === 'right') c2x += curvature;
                else if (p2.dir === 'left') c2x -= curvature;
                else if (p2.dir === 'bottom') c2y += curvature;
                else if (p2.dir === 'top') c2y -= curvature;
                else c2x -= dx * 0.4;

                return `M ${p1.x} ${p1.y} C ${c1x} ${c1y}, ${c2x} ${c2y}, ${p2.x} ${p2.y}`;
            }

            // Elbow with rounded corners (Default signature look)
            const points = this.getConnectionPoints(p1, p2, conn);
            return this.renderRoundedPath(points, 12);
        }

        renderConnections() {
            this.linesLayer.innerHTML = '';

            this.connections.forEach(conn => {
                const p1 = this.getPortCoords(conn.fromNodeId, conn.fromPortId);
                const p2 = this.getPortCoords(conn.toNodeId, conn.toPortId);
                if (!p1 || !p2) return;

                const arrowMode = conn.arrowMode || 'end';
                const pathData = this.calculatePipePath(p1, p2, conn);
                const styleDef = PIPING_TYPES[conn.pipeType] || PIPING_TYPES.general;
                const isSelected = this.selectedItem && this.selectedItem.type === 'line' && this.selectedItem.id === conn.id;

                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                g.setAttribute('class', 'schematic-connection');
                g.setAttribute('id', conn.id);

                // Invisible wide hitbox for clicking
                const hitbox = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                hitbox.setAttribute('d', pathData);
                hitbox.setAttribute('fill', 'none');
                hitbox.setAttribute('stroke', 'transparent');
                hitbox.setAttribute('stroke-width', '18');
                hitbox.style.cursor = 'pointer';

                // Visible Pipe Line
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                line.setAttribute('d', pathData);
                line.setAttribute('fill', 'none');
                line.setAttribute('stroke', isSelected ? '#3b82f6' : styleDef.color);
                line.setAttribute('stroke-width', isSelected ? (styleDef.width + 2) : styleDef.width);
                line.setAttribute('stroke-linecap', 'round');
                line.setAttribute('stroke-linejoin', 'round');

                if (conn.linePattern === 'dashed') {
                    line.setAttribute('stroke-dasharray', '8,5');
                } else if (conn.linePattern === 'dotted') {
                    line.setAttribute('stroke-dasharray', '3,4');
                } else if (styleDef.dash) {
                    line.setAttribute('stroke-dasharray', styleDef.dash);
                }

                g.appendChild(hitbox);
                g.appendChild(line);

                // Animated Flow Pulse (Whimsical Flow feature)
                if (arrowMode === 'flow') {
                    const flowLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    flowLine.setAttribute('d', pathData);
                    flowLine.setAttribute('fill', 'none');
                    flowLine.setAttribute('stroke', '#ffffff');
                    flowLine.setAttribute('stroke-width', '2');
                    flowLine.setAttribute('class', 'pipe-flow-pulse');
                    flowLine.setAttribute('stroke-linecap', 'round');
                    g.appendChild(flowLine);
                }

                // Arrowheads
                const color = isSelected ? '#3b82f6' : styleDef.color;
                if (arrowMode === 'end' || arrowMode === 'both') {
                    const points = this.getConnectionPoints(p1, p2, conn);
                    const lastPt = points[points.length - 1];
                    const prevPt = points[points.length - 2] || p1;
                    const angle = Math.atan2(lastPt.y - prevPt.y, lastPt.x - prevPt.x) * (180 / Math.PI);

                    const arrowEnd = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                    arrowEnd.setAttribute('points', '-8,-5 2,0 -8,5');
                    arrowEnd.setAttribute('fill', color);
                    arrowEnd.setAttribute('transform', `translate(${p2.x}, ${p2.y}) rotate(${angle})`);
                    g.appendChild(arrowEnd);
                }

                if (arrowMode === 'both') {
                    const points = this.getConnectionPoints(p1, p2, conn);
                    const firstPt = points[0];
                    const nextPt = points[1] || p2;
                    const angle1 = Math.atan2(firstPt.y - nextPt.y, firstPt.x - nextPt.x) * (180 / Math.PI);

                    const arrowStart = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                    arrowStart.setAttribute('points', '-8,-5 2,0 -8,5');
                    arrowStart.setAttribute('fill', color);
                    arrowStart.setAttribute('transform', `translate(${p1.x}, ${p1.y}) rotate(${angle1})`);
                    g.appendChild(arrowStart);
                }

                // Interactive Waypoint Drag Handles (when selected)
                if (isSelected) {
                    const points = this.getConnectionPoints(p1, p2, conn);
                    // Add handles on bend points
                    for (let i = 1; i < points.length - 1; i++) {
                        const wp = points[i];
                        const handleG = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        handleG.setAttribute('class', 'pipe-waypoint-handle');
                        handleG.setAttribute('transform', `translate(${wp.x}, ${wp.y})`);
                        handleG.innerHTML = `
                            <circle r="14" fill="transparent" style="cursor: grab;"/>
                            <circle r="6" fill="#3b82f6" stroke="#ffffff" stroke-width="2" style="cursor: grab; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"/>
                        `;

                        handleG.addEventListener('mousedown', (e) => {
                            e.stopPropagation();
                            this.isDraggingWaypoint = true;
                            this.draggedConn = conn;
                            // Ensure waypoints array is initialized if dragging default elbow bend
                            if (!conn.waypoints || conn.waypoints.length === 0) {
                                conn.waypoints = points.slice(1, points.length - 1);
                            }
                            this.draggedWaypointIndex = i - 1;
                        });

                        g.appendChild(handleG);
                    }
                }

                // Pipe Label Badge
                const points = this.getConnectionPoints(p1, p2, conn);
                let midPt = { x: (p1.x + p2.x) / 2, y: (p1.y + p2.y) / 2 };
                if (points.length >= 4) {
                    midPt = { x: (points[1].x + points[2].x) / 2, y: (points[1].y + points[2].y) / 2 };
                }

                if (conn.size || conn.label) {
                    const textBadge = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    textBadge.setAttribute('transform', `translate(${midPt.x}, ${midPt.y})`);
                    textBadge.style.cursor = 'pointer';
                    textBadge.innerHTML = `
                        <rect x="-35" y="-10" width="70" height="20" rx="4" fill="#ffffff" stroke="${color}" stroke-width="1.5" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));"/>
                        <text x="0" y="3.5" font-size="9" font-weight="bold" fill="#1e293b" text-anchor="middle">${conn.size || conn.label}</text>
                    `;
                    g.appendChild(textBadge);
                }

                g.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectItem('line', conn.id);
                });

                // Double click to edit label quickly
                g.addEventListener('dblclick', (e) => {
                    e.stopPropagation();
                    const newLabel = prompt('Masukkan ukuran pipa / label alur:', conn.size || conn.label || '');
                    if (newLabel !== null) {
                        conn.size = newLabel;
                        this.renderConnections();
                        this.updateInspector();
                        this.pushHistory();
                    }
                });

                this.linesLayer.appendChild(g);
            });
        }

        renderTempConnection(source, mousePt) {
            let tempLine = document.getElementById('temp-connection-line');
            if (!tempLine) {
                tempLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                tempLine.setAttribute('id', 'temp-connection-line');
                tempLine.setAttribute('fill', 'none');
                tempLine.setAttribute('stroke', '#3b82f6');
                tempLine.setAttribute('stroke-width', '3');
                tempLine.setAttribute('stroke-dasharray', '5,5');
                tempLine.style.pointerEvents = 'none';
                this.linesLayer.appendChild(tempLine);
            }
            const pathData = this.calculatePipePath(source, mousePt, { lineMode: 'elbow' });
            tempLine.setAttribute('d', pathData);
        }

        removeTempConnection() {
            const tempLine = document.getElementById('temp-connection-line');
            if (tempLine) tempLine.remove();
        }

        // --- SELECTION & FLOATING TOOLBAR ---
        selectItem(type, id) {
            this.selectedItem = { type, id };
            this.renderAll();
            this.updateInspector();
            this.updateFloatingToolbarPos();
        }

        deselectAll() {
            this.selectedItem = null;
            this.renderAll();
            this.updateInspector();
            if (this.floatingToolbar) this.floatingToolbar.style.display = 'none';
        }

        updateFloatingToolbarPos() {
            if (!this.floatingToolbar) return;

            if (!this.selectedItem || this.selectedItem.type !== 'line') {
                this.floatingToolbar.style.display = 'none';
                return;
            }

            const conn = this.connections.find(c => c.id === this.selectedItem.id);
            if (!conn) {
                this.floatingToolbar.style.display = 'none';
                return;
            }

            const p1 = this.getPortCoords(conn.fromNodeId, conn.fromPortId);
            const p2 = this.getPortCoords(conn.toNodeId, conn.toPortId);
            if (!p1 || !p2) return;

            const points = this.getConnectionPoints(p1, p2, conn);
            let midCanvasX = (p1.x + p2.x) / 2;
            let midCanvasY = (p1.y + p2.y) / 2;
            if (points.length >= 4) {
                midCanvasX = (points[1].x + points[2].x) / 2;
                midCanvasY = (points[1].y + points[2].y) / 2;
            }

            const screenPt = this.canvasToScreen(midCanvasX, midCanvasY);

            this.floatingToolbar.style.display = 'flex';
            this.floatingToolbar.style.left = `${screenPt.x}px`;
            this.floatingToolbar.style.top = `${screenPt.y}px`;

            // Update active states on floating toolbar buttons
            const lineMode = conn.lineMode || 'elbow';
            const arrowMode = conn.arrowMode || 'end';
            const linePattern = conn.linePattern || 'solid';
            const routeDir = conn.routeDir || 'h-first';

            this.floatingToolbar.querySelectorAll('.btn-line-mode').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-mode') === lineMode);
            });
            this.floatingToolbar.querySelectorAll('.btn-arrow-mode').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-arrow') === arrowMode);
            });
            this.floatingToolbar.querySelectorAll('.btn-line-pattern').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-pattern') === linePattern);
            });

            const flipBtn = document.getElementById('btn-float-flip-route');
            if (flipBtn) {
                flipBtn.classList.toggle('active', routeDir === 'v-first');
                flipBtn.title = routeDir === 'v-first' ? 'Arah: Ke Atas/Bawah Dulu (Klik untuk Horizontal)' : 'Arah: Ke Samping Dulu (Klik untuk Ke Atas/Bawah)';
            }
        }

        deleteSelected() {
            if (!this.selectedItem) return;

            if (this.selectedItem.type === 'node') {
                const nodeId = this.selectedItem.id;
                this.nodes = this.nodes.filter(n => n.id !== nodeId);
                this.connections = this.connections.filter(c => c.fromNodeId !== nodeId && c.toNodeId !== nodeId);
                const el = document.getElementById(nodeId);
                if (el) el.remove();
            } else if (this.selectedItem.type === 'line') {
                const lineId = this.selectedItem.id;
                this.connections = this.connections.filter(c => c.id !== lineId);
            }

            this.selectedItem = null;
            this.renderAll();
            this.updateInspector();
            if (this.floatingToolbar) this.floatingToolbar.style.display = 'none';
            this.pushHistory();
        }

        updateInspector() {
            const inspectorEl = document.getElementById('properties-panel-content');
            if (!inspectorEl) return;

            if (!this.selectedItem) {
                inspectorEl.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="mdi mdi-cursor-default-outline fs-1 opacity-50 mb-2"></i>
                        <p class="mb-0 fw-semibold">Pilih Komponen atau Pipa</p>
                        <small>Klik komponen atau garis pipa untuk mengubah bentuk, jalur, dan ukurannya.</small>
                    </div>
                `;
                return;
            }

            if (this.selectedItem.type === 'node') {
                const node = this.nodes.find(n => n.id === this.selectedItem.id);
                if (!node) return;
                const def = COMPONENT_DEFINITIONS[node.type] || COMPONENT_DEFINITIONS['custom_image'];
                const isCustomImage = node.type === 'custom_image' || !!node.imageUrl;

                let customImageExtraHtml = '';
                if (isCustomImage) {
                    customImageExtraHtml = `
                        <div class="mb-3 p-2 bg-light rounded text-center">
                            <img src="${node.imageUrl}" class="img-fluid rounded border mb-2" style="max-height: 80px; object-fit: contain;">
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Lebar (px)</label>
                                    <input type="number" class="form-control form-control-sm" id="prop-node-width" value="${node.width}" min="20" max="1000">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Tinggi (px)</label>
                                    <input type="number" class="form-control form-control-sm" id="prop-node-height" value="${node.height}" min="20" max="1000">
                                </div>
                            </div>
                            <div class="form-check form-switch text-start small">
                                <input class="form-check-input" type="checkbox" id="prop-lock-ratio" checked>
                                <label class="form-check-label small fw-semibold" for="prop-lock-ratio">Kunci Rasio Gambar</label>
                            </div>
                        </div>
                    `;
                }

                inspectorEl.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-primary"><i class="mdi mdi-cube-outline me-1"></i>${node.name || def.name}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-delete-node" title="Hapus Komponen">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>

                    ${customImageExtraHtml}

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tag / Equipment ID</label>
                        <input type="text" class="form-control form-control-sm" id="prop-node-tag" value="${node.tag || ''}" placeholder="misal: COMP-01, TANK-01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kapasitas / Spesifikasi</label>
                        <input type="text" class="form-control form-control-sm" id="prop-node-spec" value="${node.spec || ''}" placeholder="misal: 50 HP, 1000 Liter, R404A">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Ukuran Pipa / Connection</label>
                        <input type="text" class="form-control form-control-sm" id="prop-node-pipe" value="${node.pipeSize || ''}" placeholder="misal: 1-1/8 inch, 2 inch NPT">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Rotasi (Derajat)</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-rotate" data-angle="0">0°</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-rotate" data-angle="90">90°</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-rotate" data-angle="180">180°</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-rotate" data-angle="270">270°</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Teknis</label>
                        <textarea class="form-control form-control-sm" id="prop-node-notes" rows="3" placeholder="Keterangan tambahan...">${node.notes || ''}</textarea>
                    </div>
                `;

                document.getElementById('prop-node-tag').addEventListener('input', (e) => {
                    node.tag = e.target.value;
                    this.renderNode(node);
                });
                document.getElementById('prop-node-spec').addEventListener('input', (e) => {
                    node.spec = e.target.value;
                });
                document.getElementById('prop-node-pipe').addEventListener('input', (e) => {
                    node.pipeSize = e.target.value;
                });
                document.getElementById('prop-node-notes').addEventListener('input', (e) => {
                    node.notes = e.target.value;
                });

                if (isCustomImage) {
                    const widthInput = document.getElementById('prop-node-width');
                    const heightInput = document.getElementById('prop-node-height');
                    const lockRatioCheck = document.getElementById('prop-lock-ratio');

                    widthInput?.addEventListener('input', (e) => {
                        const val = parseInt(e.target.value) || 60;
                        node.width = val;
                        if (lockRatioCheck?.checked && node.aspectRatio) {
                            node.height = Math.max(Math.round(val / node.aspectRatio), 20);
                            if (heightInput) heightInput.value = node.height;
                        }
                        this.renderNode(node);
                        this.renderConnections();
                    });

                    heightInput?.addEventListener('input', (e) => {
                        const val = parseInt(e.target.value) || 40;
                        node.height = val;
                        if (lockRatioCheck?.checked && node.aspectRatio) {
                            node.width = Math.max(Math.round(val * node.aspectRatio), 20);
                            if (widthInput) widthInput.value = node.width;
                        }
                        this.renderNode(node);
                        this.renderConnections();
                    });
                }

                document.getElementById('btn-delete-node').addEventListener('click', () => {
                    this.deleteSelected();
                });
                inspectorEl.querySelectorAll('.btn-rotate').forEach(btn => {
                    btn.addEventListener('click', () => {
                        node.rotation = parseInt(btn.getAttribute('data-angle'));
                        this.updateNodePosition(node);
                        this.renderConnections();
                        this.pushHistory();
                    });
                });
            } else if (this.selectedItem.type === 'line') {
                const conn = this.connections.find(c => c.id === this.selectedItem.id);
                if (!conn) return;

                let pipeOptions = '';
                for (const [key, val] of Object.entries(PIPING_TYPES)) {
                    const selected = conn.pipeType === key ? 'selected' : '';
                    pipeOptions += `<option value="${key}" ${selected}>${val.name}</option>`;
                }

                const lineMode = conn.lineMode || 'elbow';
                const arrowMode = conn.arrowMode || 'end';
                const linePattern = conn.linePattern || 'solid';
                const routeDir = conn.routeDir || 'h-first';
                const hasWaypoints = conn.waypoints && conn.waypoints.length > 0;

                inspectorEl.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-primary"><i class="mdi mdi-pipe me-1"></i>Pipa & Konektor</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-delete-line" title="Hapus Pipa">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>

                    <!-- Custom Route Direction (Ke Atas Dulu / Ke Samping Dulu) -->
                    <div class="mb-3 p-2 bg-light rounded">
                        <label class="form-label fw-bold small mb-1"><i class="mdi mdi-directions-fork me-1"></i>Arah Alur Belokan</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary ${routeDir === 'h-first' ? 'active' : ''}" id="btn-route-h-first">
                                ↔️ Ke Samping Dulu
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary ${routeDir === 'v-first' ? 'active' : ''}" id="btn-route-v-first">
                                ↕️ Ke Atas/Bawah Dulu
                            </button>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-xs btn-outline-secondary flex-grow-1" id="btn-add-bend-point" style="font-size: 11px;">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>Tambah Titik Belok
                            </button>
                            ${hasWaypoints ? `
                                <button type="button" class="btn btn-xs btn-outline-warning" id="btn-reset-route" style="font-size: 11px;" title="Reset Jalur ke Otomatis">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </button>
                            ` : ''}
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 10.5px;">💡 Tarik titik biru di garis pipa pada canvas untuk menggeser jalur secara bebas.</small>
                    </div>

                    <!-- Line Shape Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Bentuk Garis Pipa</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-mode ${lineMode === 'elbow' ? 'active' : ''}" data-mode="elbow">
                                <i class="mdi mdi-vector-polyline me-1"></i>Siku Halus
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-mode ${lineMode === 'curved' ? 'active' : ''}" data-mode="curved">
                                <i class="mdi mdi-vector-curve me-1"></i>Kurva
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-mode ${lineMode === 'straight' ? 'active' : ''}" data-mode="straight">
                                <i class="mdi mdi-vector-line me-1"></i>Lurus
                            </button>
                        </div>
                    </div>

                    <!-- Arrow & Flow Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Arah Aliran Fluida</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-arrow-mode ${arrowMode === 'end' ? 'active' : ''}" data-arrow="end" title="Satu Arah">
                                <i class="mdi mdi-arrow-right"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-arrow-mode ${arrowMode === 'both' ? 'active' : ''}" data-arrow="both" title="Dua Arah">
                                <i class="mdi mdi-arrow-left-right"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-arrow-mode ${arrowMode === 'flow' ? 'active' : ''}" data-arrow="flow" title="Animasi Aliran Pulsa">
                                <i class="mdi mdi-motion-play-outline"></i> Flow
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-arrow-mode ${arrowMode === 'none' ? 'active' : ''}" data-arrow="none" title="Tanpa Panah">
                                <i class="mdi mdi-minus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Line Pattern -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Gaya Garis</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-pat ${linePattern === 'solid' ? 'active' : ''}" data-pattern="solid">Solid</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-pat ${linePattern === 'dashed' ? 'active' : ''}" data-pattern="dashed">Dashed</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-inspect-line-pat ${linePattern === 'dotted' ? 'active' : ''}" data-pattern="dotted">Dotted</button>
                        </div>
                    </div>

                    <!-- Fluid / Pipe Type -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jenis Jalur / Fluida</label>
                        <select class="form-select form-select-sm" id="prop-line-type">
                            ${pipeOptions}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Ukuran Pipa (Diameter)</label>
                        <input type="text" class="form-control form-control-sm" id="prop-line-size" value="${conn.size || ''}" placeholder='misal: 1-1/8", 2-5/8", DN50'>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Label / Keterangan Jalur</label>
                        <input type="text" class="form-control form-control-sm" id="prop-line-label" value="${conn.label || ''}" placeholder="misal: Main Discharge, Cold Room 1">
                    </div>
                `;

                // Bind route direction buttons
                document.getElementById('btn-route-h-first')?.addEventListener('click', () => {
                    conn.routeDir = 'h-first';
                    conn.waypoints = [];
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                    this.updateInspector();
                    this.pushHistory();
                });

                document.getElementById('btn-route-v-first')?.addEventListener('click', () => {
                    conn.routeDir = 'v-first';
                    conn.waypoints = [];
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                    this.updateInspector();
                    this.pushHistory();
                });

                document.getElementById('btn-add-bend-point')?.addEventListener('click', () => {
                    const p1 = this.getPortCoords(conn.fromNodeId, conn.fromPortId);
                    const p2 = this.getPortCoords(conn.toNodeId, conn.toPortId);
                    if (p1 && p2) {
                        const midX = (p1.x + p2.x) / 2;
                        const midY = (p1.y + p2.y) / 2 - 40;
                        if (!conn.waypoints) conn.waypoints = [];
                        conn.waypoints.push({ x: midX, y: midY });
                        this.renderConnections();
                        this.updateFloatingToolbarPos();
                        this.updateInspector();
                        this.pushHistory();
                    }
                });

                document.getElementById('btn-reset-route')?.addEventListener('click', () => {
                    conn.waypoints = [];
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                    this.updateInspector();
                    this.pushHistory();
                });

                // Bind line mode
                inspectorEl.querySelectorAll('.btn-inspect-line-mode').forEach(btn => {
                    btn.addEventListener('click', () => {
                        conn.lineMode = btn.getAttribute('data-mode');
                        this.renderConnections();
                        this.updateFloatingToolbarPos();
                        this.updateInspector();
                        this.pushHistory();
                    });
                });

                // Bind arrow mode
                inspectorEl.querySelectorAll('.btn-inspect-arrow-mode').forEach(btn => {
                    btn.addEventListener('click', () => {
                        conn.arrowMode = btn.getAttribute('data-arrow');
                        this.renderConnections();
                        this.updateFloatingToolbarPos();
                        this.updateInspector();
                        this.pushHistory();
                    });
                });

                // Bind pattern
                inspectorEl.querySelectorAll('.btn-inspect-line-pat').forEach(btn => {
                    btn.addEventListener('click', () => {
                        conn.linePattern = btn.getAttribute('data-pattern');
                        this.renderConnections();
                        this.updateFloatingToolbarPos();
                        this.updateInspector();
                        this.pushHistory();
                    });
                });

                document.getElementById('prop-line-type').addEventListener('change', (e) => {
                    conn.pipeType = e.target.value;
                    this.renderConnections();
                    this.updateFloatingToolbarPos();
                    this.pushHistory();
                });
                document.getElementById('prop-line-size').addEventListener('input', (e) => {
                    conn.size = e.target.value;
                    this.renderConnections();
                });
                document.getElementById('prop-line-label').addEventListener('input', (e) => {
                    conn.label = e.target.value;
                    this.renderConnections();
                });
                document.getElementById('btn-delete-line').addEventListener('click', () => {
                    this.deleteSelected();
                });
            }
        }

        renderAll() {
            this.nodes.forEach(node => this.renderNode(node));
            this.renderConnections();
        }

        // --- HISTORY ---
        pushHistory() {
            if (this.historyIndex < this.history.length - 1) {
                this.history = this.history.slice(0, this.historyIndex + 1);
            }
            const state = JSON.stringify(this.toJSON());
            this.history.push(state);
            this.historyIndex = this.history.length - 1;
            this.updateHistoryButtons();
        }

        undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.loadJSON(state, false);
                this.updateHistoryButtons();
            }
        }

        redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.loadJSON(state, false);
                this.updateHistoryButtons();
            }
        }

        updateHistoryButtons() {
            const btnUndo = document.getElementById('btn-undo');
            const btnRedo = document.getElementById('btn-redo');
            if (btnUndo) btnUndo.disabled = this.historyIndex <= 0;
            if (btnRedo) btnRedo.disabled = this.historyIndex >= this.history.length - 1;
        }

        // --- SERIALIZATION ---
        toJSON() {
            return {
                version: '1.0',
                nodes: this.nodes,
                connections: this.connections,
                customImages: this.customImages
            };
        }

        loadJSON(data, resetHistory = true) {
            if (!data) return;
            this.nodesLayer.innerHTML = '';
            this.linesLayer.innerHTML = '';
            this.nodes = data.nodes || [];
            this.connections = data.connections || [];
            this.customImages = data.customImages || [];
            this.selectedItem = null;

            this.renderPalette();
            this.renderAll();
            this.updateInspector();

            if (resetHistory) {
                this.history = [JSON.stringify(this.toJSON())];
                this.historyIndex = 0;
                this.updateHistoryButtons();
            }
        }

        // --- EXPORT & SAVE ---
        async generateThumbnail() {
            try {
                if (this.nodes.length === 0) return null;

                let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
                this.nodes.forEach(n => {
                    minX = Math.min(minX, n.x - 20);
                    minY = Math.min(minY, n.y - 20);
                    maxX = Math.max(maxX, n.x + n.width + 20);
                    maxY = Math.max(maxY, n.y + n.height + 20);
                });

                const w = Math.max(maxX - minX, 400);
                const h = Math.max(maxY - minY, 300);

                const svgClone = this.svg.cloneNode(true);
                svgClone.setAttribute('viewBox', `${minX} ${minY} ${w} ${h}`);
                svgClone.setAttribute('width', '600');
                svgClone.setAttribute('height', String(Math.round(600 * (h / w))));

                const cloneViewport = svgClone.querySelector('#svg-viewport');
                if (cloneViewport) cloneViewport.removeAttribute('transform');

                const svgString = new XMLSerializer().serializeToString(svgClone);
                const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(svgBlob);

                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        canvas.width = 600;
                        canvas.height = Math.round(600 * (h / w));
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0);
                        URL.revokeObjectURL(url);
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                    img.onerror = () => resolve(null);
                    img.src = url;
                });
            } catch (e) {
                console.error('Thumbnail generation error:', e);
                return null;
            }
        }

        async exportPNG() {
            const thumbnail = await this.generateThumbnail();
            if (!thumbnail) {
                alert('Tidak ada komponen untuk diexport.');
                return;
            }
            const a = document.createElement('a');
            const title = (document.getElementById('schematic-title-input')?.value || 'schematic-diagram').toLowerCase().replace(/\s+/g, '_');
            a.download = `${title}.png`;
            a.href = thumbnail;
            a.click();
        }

        async saveToServer() {
            const btnSave = document.getElementById('btn-save-schematic');
            const originalText = btnSave ? btnSave.innerHTML : '';
            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...`;
            }

            const title = document.getElementById('schematic-title-input')?.value || 'Skematik Proyek';
            const clientId = document.getElementById('schematic-client-select')?.value || null;
            const projectName = document.getElementById('schematic-project-input')?.value || '';
            const diagramType = document.getElementById('schematic-type-select')?.value || 'Refrigeration System';
            const status = document.getElementById('schematic-status-select')?.value || 'Draft';
            const description = document.getElementById('schematic-desc-input')?.value || '';

            const canvasData = JSON.stringify(this.toJSON());
            const previewImage = await this.generateThumbnail();

            const payload = {
                title,
                client_id: clientId || null,
                project_name: projectName,
                diagram_type: diagramType,
                status,
                description,
                canvas_data: canvasData,
                preview_image: previewImage,
                _token: this.csrfToken
            };

            try {
                const response = await fetch(this.saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Skematik diagram berhasil disimpan ke database.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(data.message || 'Skematik diagram berhasil disimpan!');
                    }

                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } else {
                    throw new Error(data.message || 'Gagal menyimpan skematik.');
                }
            } catch (err) {
                console.error(err);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: err.message || 'Terjadi kesalahan saat menyimpan ke database.'
                    });
                } else {
                    alert('Error: ' + err.message);
                }
            } finally {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalText;
                }
            }
        }
    }

    window.SchematicBuilder = SchematicBuilder;
})(window);
