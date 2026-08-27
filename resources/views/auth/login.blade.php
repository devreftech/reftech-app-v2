<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In &bull; Reftech Apps</title>

    <meta name="description" content="Reftech Enterprise Management System Sign In" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.png') }}" />

    <!-- Fonts (Preconnect for lightning-fast loading) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Core CSS (Lightweight load) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />

    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('reftech_theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark-mode');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #3b82f6 100%);
            --accent-glow: rgba(99, 102, 241, 0.15);
            --card-bg: rgba(255, 255, 255, 0.94);
            --border-soft: rgba(226, 232, 240, 0.85);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
        }

        .dark-mode {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);
            --accent-glow: rgba(99, 102, 241, 0.35);
            --card-bg: rgba(15, 23, 42, 0.86);
            --border-soft: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(30, 41, 59, 0.65);
            --input-border: rgba(71, 85, 105, 0.6);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            min-height: 100vh;
            margin: 0;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #334155;
            position: relative;
            overflow-x: hidden;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* Dark Mode Body Background */
        body.dark-mode {
            background-color: #030712 !important;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.14) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 1) 0px, #030712 100%) !important;
            color: #f1f5f9;
        }

        /* ======================================================== */
        /* ANIMATED BACKGROUND MESH & DRIFTING FLOATING ORBS        */
        /* ======================================================== */
        .bg-canvas-wrap {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(85px);
            opacity: 0.65;
            animation: floatOrb 18s ease-in-out infinite alternate;
            transition: opacity 0.5s ease, filter 0.5s ease;
        }

        .orb-1 {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, rgba(96, 165, 250, 0.05) 70%);
            top: -10%;
            left: 10%;
            animation-duration: 16s;
        }

        .orb-2 {
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.28) 0%, rgba(251, 191, 36, 0.05) 70%);
            bottom: -8%;
            right: 8%;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, rgba(192, 132, 252, 0.05) 70%);
            top: 35%;
            right: 22%;
            animation-duration: 22s;
            animation-delay: -8s;
        }

        .orb-4 {
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.25) 0%, rgba(56, 189, 248, 0.05) 70%);
            bottom: 20%;
            left: 20%;
            animation-duration: 19s;
            animation-delay: -3s;
        }

        body.dark-mode .orb-1 {
            background: radial-gradient(circle, rgba(99, 102, 241, 0.45) 0%, transparent 70%);
        }
        body.dark-mode .orb-2 {
            background: radial-gradient(circle, rgba(244, 63, 94, 0.25) 0%, transparent 70%);
        }
        body.dark-mode .orb-3 {
            background: radial-gradient(circle, rgba(168, 85, 247, 0.4) 0%, transparent 70%);
        }
        body.dark-mode .orb-4 {
            background: radial-gradient(circle, rgba(6, 182, 212, 0.35) 0%, transparent 70%);
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, 40px) scale(1.12); }
            100% { transform: translate(-40px, 80px) scale(0.95); }
        }

        #koiCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
            pointer-events: auto;
        }

        /* ======================================================== */
        /* FLOATING THEME TOGGLE BUTTON (DARK / LIGHT)              */
        /* ======================================================== */
        .theme-toggle-btn {
            position: fixed;
            top: 20px;
            right: 24px;
            z-index: 50;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 50px;
            color: #334155;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.08);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            user-select: none;
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.98);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px -4px rgba(99, 102, 241, 0.25);
            border-color: #cbd5e1;
        }

        .theme-icon-wrap {
            position: relative;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-sun, .icon-moon {
            position: absolute;
            font-size: 1.125rem;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;
        }

        .icon-sun {
            color: #f59e0b;
            opacity: 0;
            transform: rotate(-90deg) scale(0.5);
        }

        .icon-moon {
            color: #6366f1;
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }

        /* Dark Mode Theme Button */
        body.dark-mode .theme-toggle-btn {
            background: rgba(30, 41, 59, 0.85);
            border-color: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5);
        }

        body.dark-mode .theme-toggle-btn:hover {
            background: rgba(51, 65, 85, 0.95);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 28px -4px rgba(139, 92, 246, 0.4);
        }

        body.dark-mode .icon-sun {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }

        body.dark-mode .icon-moon {
            opacity: 0;
            transform: rotate(90deg) scale(0.5);
        }

        /* ======================================================== */
        /* AUTH CONTAINER & MODERN GLASS CARD                       */
        /* ======================================================== */
        .auth-container {
            width: 100%;
            max-width: 1060px;
            min-height: 600px;
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            box-shadow: 
                0 25px 60px -15px rgba(15, 23, 42, 0.12),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset,
                0 10px 30px -10px rgba(37, 99, 235, 0.08);
            overflow: hidden;
            display: flex;
            position: relative;
            z-index: 10;
            animation: fadeInCard 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: background 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        body.dark-mode .auth-container {
            box-shadow: 
                0 25px 60px -15px rgba(0, 0, 0, 0.75),
                0 0 0 1px rgba(255, 255, 255, 0.06) inset,
                0 10px 35px -10px rgba(99, 102, 241, 0.15);
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.99);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left Hero Pane */
        .auth-hero-pane {
            flex: 1.1;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #1e3a8a 100%);
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            transition: background 0.4s ease;
        }

        body.dark-mode .auth-hero-pane {
            background: linear-gradient(135deg, #090d16 0%, #171c2e 50%, #0d1322 100%);
            border-right-color: rgba(255, 255, 255, 0.05);
        }

        .hero-shape-1 {
            position: absolute;
            top: -15%;
            right: -15%;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, transparent 70%);
            filter: blur(40px);
            pointer-events: none;
        }

        .hero-shape-2 {
            position: absolute;
            bottom: -20%;
            left: -15%;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.3) 0%, transparent 70%);
            filter: blur(50px);
            pointer-events: none;
        }

        .hero-pattern-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            pointer-events: none;
        }

        .hero-top {
            position: relative;
            z-index: 2;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: #e0e7ff;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseEmerald 2s infinite;
        }

        @keyframes pulseEmerald {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .hero-body {
            position: relative;
            z-index: 2;
            margin: 2.5rem 0;
        }

        .hero-title {
            font-size: 2.125rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.025em;
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .hero-title span {
            background: linear-gradient(135deg, #a5b4fc 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 0.9375rem;
            color: #c7d2fe;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 400px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            color: #e0e7ff;
            font-weight: 500;
        }

        .feature-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .hero-footer {
            position: relative;
            z-index: 2;
            font-size: 0.75rem;
            color: #a5b4fc;
        }

        /* Right Form Section */
        .auth-form-pane {
            flex: 1;
            padding: 3.25rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: transparent;
            position: relative;
            z-index: 2;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .app-brand-logo {
            display: inline-flex;
            align-items: center;
            margin-bottom: 0;
            text-decoration: none;
        }

        .app-brand-logo img {
            height: 52px;
            max-width: 230px;
            width: auto;
            object-fit: contain;
            transition: transform 0.2s ease, opacity 0.3s ease;
        }

        .brand-logo-light {
            display: block !important;
        }

        .brand-logo-dark {
            display: none !important;
        }

        body.dark-mode .brand-logo-light {
            display: none !important;
        }

        body.dark-mode .brand-logo-dark {
            display: block !important;
        }

        .app-brand-logo:hover img {
            transform: scale(1.02);
        }

        .form-header p {
            color: #64748b;
            font-size: 0.875rem;
            margin: 0;
        }

        /* Input Controls */
        .form-group-modern {
            margin-bottom: 1.25rem;
        }

        .form-label-modern {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.45rem;
            letter-spacing: 0.01em;
            transition: color 0.3s ease;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1.25rem;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .form-control-modern {
            width: 100%;
            height: 46px;
            padding: 0.625rem 1rem 0.625rem 2.75rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--text-main);
            background-color: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            box-shadow: none;
        }

        .form-control-modern.has-toggle {
            padding-right: 2.85rem;
        }

        .form-control-modern:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
        }

        .form-control-modern:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        body.dark-mode .form-control-modern:hover {
            background-color: rgba(30, 41, 59, 0.9);
            border-color: #64748b;
        }

        body.dark-mode .form-control-modern:focus {
            background-color: rgba(30, 41, 59, 1);
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.25);
        }

        /* Autofill Styling (Chrome, Edge, Safari, Firefox) */
        .form-control-modern:-webkit-autofill,
        .form-control-modern:-webkit-autofill:hover,
        .form-control-modern:-webkit-autofill:focus,
        .form-control-modern:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #f8fafc inset !important;
            -webkit-text-fill-color: #1e293b !important;
            caret-color: #1e293b !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        body.dark-mode .form-control-modern:-webkit-autofill,
        body.dark-mode .form-control-modern:-webkit-autofill:hover,
        body.dark-mode .form-control-modern:-webkit-autofill:focus,
        body.dark-mode .form-control-modern:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #1e293b inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            caret-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .input-group-modern:focus-within .input-icon-left {
            color: #6366f1;
        }
        body.dark-mode .input-group-modern:focus-within .input-icon-left {
            color: #818cf8;
        }

        .form-control-modern.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }
        body.dark-mode .form-control-modern.is-invalid {
            background-color: rgba(239, 68, 68, 0.12);
        }

        .form-control-modern.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15) !important;
        }

        .btn-toggle-password {
            position: absolute;
            right: 10px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.25rem;
            padding: 6px;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease, background-color 0.2s ease;
            z-index: 5;
        }

        .btn-toggle-password:hover {
            color: #475569;
            background-color: #f1f5f9;
        }

        body.dark-mode .btn-toggle-password:hover {
            color: #f1f5f9;
            background-color: rgba(51, 65, 85, 0.6);
        }

        /* Checkbox & Links */
        .remember-forgot-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.8125rem;
        }

        .custom-checkbox-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: var(--text-main);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .custom-checkbox-wrap input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #6366f1;
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-link {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        body.dark-mode .forgot-link {
            color: #818cf8;
        }
        body.dark-mode .forgot-link:hover {
            color: #a5b4fc;
        }

        /* Submit Button */
        .btn-submit-modern {
            position: relative;
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            font-size: 0.9375rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 18px 0 rgba(99, 102, 241, 0.4), 0 0 0 1px rgba(99, 102, 241, 0.1);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            letter-spacing: 0.01em;
            overflow: hidden;
            z-index: 1;
        }

        .btn-submit-modern:hover {
            box-shadow: 0 6px 22px 0 rgba(99, 102, 241, 0.5), 0 0 16px 2px rgba(139, 92, 246, 0.3);
            transform: translateY(-1px);
        }

        .btn-submit-modern:active {
            transform: translateY(1px);
        }

        /* Alerts & Feedback */
        .alert-modern-danger {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
            animation: shake 0.35s ease;
        }

        body.dark-mode .alert-modern-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }

        .alert-modern-success {
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        body.dark-mode .alert-modern-success {
            background-color: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .invalid-feedback-modern {
            color: #ef4444;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .caps-lock-warning {
            display: none;
            align-items: center;
            gap: 6px;
            color: #d97706;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.35rem;
        }

        body.dark-mode .caps-lock-warning {
            color: #fbbf24;
        }

        /* Footer Badge */
        .form-footer-badge {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .auth-container {
                max-width: 480px;
                min-height: auto;
                border-radius: 20px;
            }
            .auth-hero-pane {
                display: none;
            }
            .auth-form-pane {
                padding: 2.75rem 2rem;
            }
            .theme-toggle-btn {
                top: 14px;
                right: 16px;
                padding: 6px 12px;
            }
            .theme-toggle-btn .theme-text {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding: 1rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Dark/Light Mode Switcher -->
    <button id="themeToggle" class="theme-toggle-btn" type="button" title="Ganti Tema (Dark / Light)" aria-label="Toggle Theme">
        <div class="theme-icon-wrap">
            <i class="mdi mdi-white-balance-sunny icon-sun"></i>
            <i class="mdi mdi-weather-night icon-moon"></i>
        </div>
        <span class="theme-text" id="themeText">Mode Gelap</span>
    </button>

    <!-- Background Canvas with Floating Drifting Orbs & Interactive HTML5 Koi Engine -->
    <div class="bg-canvas-wrap">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
        <canvas id="koiCanvas"></canvas>
    </div>

@php
    $appVersion = \Illuminate\Support\Facades\Cache::remember('app_git_commit_version', 600, function () {
        try {
            $baseVersion = env('APP_VERSION', 'v2.2.0');
            $hash = trim((string) @shell_exec('git rev-parse --short HEAD 2>nul'));
            if (!empty($hash)) {
                return $baseVersion . ' (' . $hash . ')';
            }
            return $baseVersion;
        } catch (\Throwable $e) {
            return env('APP_VERSION', 'v2.2.0');
        }
    });
@endphp

    <div class="auth-container">
        <!-- Left Hero Section (Enterprise Branding & Info) -->
        <div class="auth-hero-pane">
            <div class="hero-shape-1"></div>
            <div class="hero-shape-2"></div>
            <div class="hero-pattern-grid"></div>

            <div class="hero-top">
                <div class="brand-pill">
                    <span class="pulse-dot"></span>
                    <span>Reftech Enterprise Portal &bull; {{ $appVersion }}</span>
                </div>
            </div>

            <div class="hero-body">
                <h2 class="hero-title">
                    Operational &<br>
                    <span>Enterprise Hub</span>
                </h2>
                <p class="hero-subtitle">
                    Integrated management platform for inventory, sales orders, finance, quotation, and operational monitoring.
                </p>

                <ul class="feature-list">
                    <li class="feature-item">
                        <div class="feature-item-icon">
                            <i class="mdi mdi-shield-check-outline"></i>
                        </div>
                        <span>Role-based secure access and protected sessions</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-item-icon">
                            <i class="mdi mdi-lightning-bolt-outline"></i>
                        </div>
                        <span>Real-time monitoring, reporting, and quotation tracking</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-item-icon">
                            <i class="mdi mdi-sync"></i>
                        </div>
                        <span>Automated logistics and warehouse synchronization</span>
                    </li>
                </ul>
            </div>

            <div class="hero-footer">
                <span>&copy; {{ date('Y') }} Reftech. All Rights Reserved.</span>
                <span class="d-flex align-items-center gap-1">
                    <i class="mdi mdi-lock-outline"></i> 256-bit Encrypted
                </span>
            </div>
        </div>

        <!-- Right Form Section -->
        <div class="auth-form-pane">
            <div class="form-header">
                <a href="{{ url('/') }}" class="app-brand-logo" title="Reftech">
                    {{-- Light Mode Logo --}}
                    <img src="{{ asset('asset/logo/Reftech-Log.png') }}" alt="Reftech Logo" class="brand-logo-light" onerror="this.src='{{ asset('assets/img/favicon/logo-hitam-app.png') }}'">
                    {{-- Dark Mode White Logo --}}
                    <img src="{{ asset('assets/img/favicon/logo-putih-app.png') }}" alt="Reftech Logo White" class="brand-logo-dark" onerror="this.src='{{ asset('assets/img/branding/brand-img-light.png') }}'">
                </a>
            </div>

            {{-- Flash status or error message --}}
            @if (session('status'))
                <div class="alert-modern-success">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->has('email') && !$errors->has('password'))
                <div class="alert-modern-danger">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Email Input -->
                <div class="form-group-modern">
                    <label for="email" class="form-label-modern">
                        <span>Email Address</span>
                    </label>
                    <div class="input-group-modern">
                        <i class="mdi mdi-email-outline input-icon-left"></i>
                        <input 
                            id="email" 
                            type="email" 
                            class="form-control-modern @error('email') is-invalid @enderror" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autocomplete="email" 
                            placeholder="name@reftech.id"
                            autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback-modern">
                            <i class="mdi mdi-alert-circle-outline"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group-modern">
                    <div class="form-label-modern">
                        <label for="password" class="mb-0">Password</label>
                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        @endif
                    </div>
                    <div class="input-group-modern">
                        <i class="mdi mdi-lock-outline input-icon-left"></i>
                        <input 
                            id="password" 
                            type="password" 
                            class="form-control-modern has-toggle @error('password') is-invalid @enderror" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••••••">
                        <button type="button" class="btn-toggle-password" id="togglePasswordBtn" title="Toggle Password Visibility" aria-label="Toggle password visibility">
                            <i class="mdi mdi-eye-off-outline" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <div id="capsLockWarning" class="caps-lock-warning">
                        <i class="mdi mdi-keyboard-caps"></i>
                        <span>Caps Lock is ON</span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback-modern">
                            <i class="mdi mdi-alert-circle-outline"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="remember-forgot-row">
                    <label class="custom-checkbox-wrap" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me on this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit-modern" id="btnLogin">
                    <span id="btnText">Sign In to System</span>
                    <i class="mdi mdi-arrow-right" id="btnIcon"></i>
                </button>
            </form>

            <div class="form-footer-badge">
                <i class="mdi mdi-shield-check-outline text-success"></i>
                <span>Authorized Staff Access &bull; {{ $appVersion }}</span>
            </div>
        </div>
    </div>

    <!-- Pure Vanilla JS for Ultra Fast Execution & Interactive Koi Pond Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Password Show/Hide Toggle
            var toggleBtn = document.getElementById('togglePasswordBtn');
            var passwordInput = document.getElementById('password');
            var toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.className = isPassword ? 'mdi mdi-eye-outline' : 'mdi mdi-eye-off-outline';
                    passwordInput.focus();
                });
            }

            // Caps Lock Detector
            var capsWarning = document.getElementById('capsLockWarning');
            if (passwordInput && capsWarning) {
                passwordInput.addEventListener('keyup', function (e) {
                    if (e.getModifierState && e.getModifierState('CapsLock')) {
                        capsWarning.style.display = 'inline-flex';
                    } else {
                        capsWarning.style.display = 'none';
                    }
                });

                passwordInput.addEventListener('blur', function () {
                    capsWarning.style.display = 'none';
                });
            }

            // Prevent double submission & show instant feedback
            var form = document.getElementById('loginForm');
            var btn = document.getElementById('btnLogin');
            var btnText = document.getElementById('btnText');
            var btnIcon = document.getElementById('btnIcon');

            if (form && btn) {
                form.addEventListener('submit', function () {
                    if (form.checkValidity && form.checkValidity()) {
                        btn.disabled = true;
                        btnText.textContent = 'Signing in...';
                        btnIcon.className = 'mdi mdi-loading mdi-spin';
                    }
                });
            }

            // ========================================================
            // INTERACTIVE HTML5 KOI POND CANVAS ENGINE WITH FEEDING AI
            // ========================================================
            var canvas = document.getElementById('koiCanvas');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var width, height;
            var dpr = Math.min(window.devicePixelRatio || 1, 2);

            var mouse = { x: -1000, y: -1000, active: false, lastMove: 0 };
            var ripples = [];
            var streams = [];
            var fishes = [];
            var foods = [];
            var biteParticles = [];

            // Gamified Feeding Progression
            var feedCount = 0;                        // Berapa kali user sudah memberi makan
            var FEED_COUNT_TO_PERMANENT = 5;          // Setelah 5x makan, ikan aktif permanen di kolam
            var emergeTimer = 0;                      // Jeda 1.2 - 1.5 detik sebelum ikan keluar dari card
            var postEatDelayTimer = 0;                // Waktu santai setelah makan sebelum kembali sembunyi
            var lastFoodPos = { x: 0, y: 0 };

            // Palette definitions for Koi varieties
            var KOI_PALETTES = [
                {
                    name: 'Kohaku (Orange & White)',
                    body: '#f8fafc',
                    accent: '#ea580c',
                    accent2: '#f97316',
                    fin: 'rgba(253, 186, 116, 0.45)',
                    tailFin: 'rgba(254, 215, 170, 0.55)',
                    opacity: 0.85
                },
                {
                    name: 'Asagi (Azure Cyan & Coral)',
                    body: '#e0f2fe',
                    accent: '#0284c7',
                    accent2: '#38bdf8',
                    fin: 'rgba(125, 211, 252, 0.45)',
                    tailFin: 'rgba(186, 230, 253, 0.55)',
                    opacity: 0.8
                },
                {
                    name: 'Yamabuki (Gold Amber)',
                    body: '#fef3c7',
                    accent: '#d97706',
                    accent2: '#f59e0b',
                    fin: 'rgba(253, 224, 71, 0.45)',
                    tailFin: 'rgba(254, 240, 138, 0.55)',
                    opacity: 0.85
                },
                {
                    name: 'Sakura (Rose & Crimson)',
                    body: '#fff1f2',
                    accent: '#e11d48',
                    accent2: '#fb7185',
                    fin: 'rgba(251, 207, 232, 0.45)',
                    tailFin: 'rgba(254, 205, 211, 0.55)',
                    opacity: 0.78
                },
                {
                    name: 'Indigo Twilight',
                    body: '#e0e7ff',
                    accent: '#4338ca',
                    accent2: '#6366f1',
                    fin: 'rgba(165, 180, 252, 0.45)',
                    tailFin: 'rgba(199, 210, 254, 0.55)',
                    opacity: 0.8
                }
            ];

            function resizeCanvas() {
                width = window.innerWidth;
                height = window.innerHeight;
                canvas.width = width * dpr;
                canvas.height = height * dpr;
                ctx.scale(dpr, dpr);
            }

            // Helper to get Login Card Bounding Box
            function getCardBounds() {
                var loginCard = document.querySelector('.auth-container');
                if (loginCard) {
                    var r = loginCard.getBoundingClientRect();
                    return {
                        cx: r.left + r.width / 2,
                        cy: r.top + r.height / 2,
                        left: r.left,
                        top: r.top,
                        right: r.right,
                        bottom: r.bottom,
                        width: r.width,
                        height: r.height
                    };
                }
                return {
                    cx: width / 2,
                    cy: height / 2,
                    left: width / 2 - 250,
                    top: height / 2 - 250,
                    right: width / 2 + 250,
                    bottom: height / 2 + 250,
                    width: 500,
                    height: 500
                };
            }

            // Food Pellet Class (Floating fish food dropped on click)
            function FoodPellet(x, y) {
                this.x = x + (Math.random() - 0.5) * 30;
                this.y = y + (Math.random() - 0.5) * 30;
                this.r = 3.0 + Math.random() * 1.5;
                this.life = 700; // ~12 seconds
                this.maxLife = 700;
                this.wobble = Math.random() * Math.PI * 2;
                this.wobbleSpeed = 0.04 + Math.random() * 0.03;
                this.eaten = false;
                this.color = Math.random() < 0.5 ? '#b45309' : '#d97706';
            }

            FoodPellet.prototype.update = function () {
                this.life--;
                this.wobble += this.wobbleSpeed;
                this.y += Math.sin(this.wobble) * 0.12;
            };

            FoodPellet.prototype.draw = function (c) {
                if (this.life <= 0 || this.eaten) return;
                var alpha = Math.min(1, this.life / 60);
                c.save();
                c.beginPath();
                c.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                c.fillStyle = this.color;
                c.globalAlpha = alpha;
                c.shadowColor = 'rgba(0, 0, 0, 0.35)';
                c.shadowBlur = 5;
                c.shadowOffsetY = 3;
                c.fill();

                // Highlight glare on top of food pellet
                c.beginPath();
                c.arc(this.x - 1, this.y - 1, this.r * 0.45, 0, Math.PI * 2);
                c.fillStyle = '#fef08a';
                c.fill();
                c.restore();
            };

            // Food Bite Crumbs / Splash Particle
            function BiteParticle(x, y, color) {
                this.x = x;
                this.y = y;
                this.vx = (Math.random() - 0.5) * 2.8;
                this.vy = (Math.random() - 0.5) * 2.8;
                this.r = 1.2 + Math.random() * 1.6;
                this.alpha = 0.9;
                this.color = color || '#d97706';
            }

            BiteParticle.prototype.update = function () {
                this.x += this.vx;
                this.y += this.vy;
                this.alpha -= 0.035;
            };

            BiteParticle.prototype.draw = function (c) {
                if (this.alpha <= 0) return;
                c.save();
                c.beginPath();
                c.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                c.fillStyle = this.color;
                c.globalAlpha = this.alpha;
                c.fill();
                c.restore();
            };

            // Ripple Class (Water ripple waves)
            function Ripple(x, y, maxR, strength) {
                this.x = x;
                this.y = y;
                this.r = 2;
                this.maxR = maxR || 70;
                this.alpha = 0.5 * (strength || 1);
                this.speed = 1.6 + Math.random() * 0.8;
            }

            Ripple.prototype.update = function () {
                this.r += this.speed;
                this.alpha = Math.max(0, this.alpha - 0.012);
            };

            Ripple.prototype.draw = function (c) {
                if (this.alpha <= 0) return;
                c.save();
                c.beginPath();
                c.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                c.strokeStyle = 'rgba(99, 102, 241, ' + (this.alpha * 0.4) + ')';
                c.lineWidth = 2.5;
                c.stroke();

                c.beginPath();
                c.arc(this.x, this.y, Math.max(0, this.r - 8), 0, Math.PI * 2);
                c.strokeStyle = 'rgba(56, 189, 248, ' + (this.alpha * 0.25) + ')';
                c.lineWidth = 1.5;
                c.stroke();
                c.restore();
            };

            // Flowing Water Streamline / Current Ribbon Class (Air Mengalir)
            function WaterStream() {
                this.reset(true);
            }

            WaterStream.prototype.reset = function (initial) {
                this.angle = 0.55 + (Math.random() - 0.5) * 0.25;
                this.cosA = Math.cos(this.angle);
                this.sinA = Math.sin(this.angle);

                this.length = 120 + Math.random() * 180;
                this.speed = 0.9 + Math.random() * 1.3;

                if (initial) {
                    this.x = Math.random() * (width + 300) - 150;
                    this.y = Math.random() * (height + 300) - 150;
                } else {
                    if (Math.random() < 0.6) {
                        this.x = Math.random() * (width + 200) - 100;
                        this.y = -60 - Math.random() * 80;
                    } else {
                        this.x = -100 - Math.random() * 80;
                        this.y = Math.random() * (height + 200) - 100;
                    }
                }

                this.width = 1.2 + Math.random() * 2.2;
                this.alpha = 0.12 + Math.random() * 0.32;
                this.wavePhase = Math.random() * Math.PI * 2;
                this.waveSpeed = 0.018 + Math.random() * 0.025;
                this.waveAmp = 7 + Math.random() * 11;
                this.waveFreq = 0.025 + Math.random() * 0.02;

                var hues = [
                    '56, 189, 248',   // Sky Cyan
                    '125, 211, 252',  // Light Aquamarine
                    '186, 230, 253',  // Crystal Pearl
                    '99, 102, 241'    // Subtle Indigo
                ];
                this.hue = hues[Math.floor(Math.random() * hues.length)];
            };

            WaterStream.prototype.update = function () {
                this.x += this.cosA * this.speed;
                this.y += this.sinA * this.speed;
                this.wavePhase += this.waveSpeed;

                if (this.x > width + 180 || this.y > height + 180) {
                    this.reset(false);
                }
            };

            WaterStream.prototype.draw = function (c) {
                c.save();
                c.beginPath();

                var steps = 8;
                var stepLen = this.length / steps;
                var normX = -this.sinA;
                var normY = this.cosA;

                var startX = this.x;
                var startY = this.y;

                for (var i = 0; i <= steps; i++) {
                    var distAlong = i * stepLen;
                    var wave = Math.sin(this.wavePhase + i * this.waveFreq * 10) * this.waveAmp;
                    var envelope = Math.sin((i / steps) * Math.PI);
                    var currentWave = wave * envelope;

                    var px = startX + this.cosA * distAlong + normX * currentWave;
                    var py = startY + this.sinA * distAlong + normY * currentWave;

                    if (i === 0) {
                        c.moveTo(px, py);
                    } else {
                        c.lineTo(px, py);
                    }
                }

                var endX = startX + this.cosA * this.length;
                var endY = startY + this.sinA * this.length;
                var grad = c.createLinearGradient(startX, startY, endX, endY);
                grad.addColorStop(0, 'rgba(' + this.hue + ', 0)');
                grad.addColorStop(0.5, 'rgba(' + this.hue + ', ' + this.alpha + ')');
                grad.addColorStop(1, 'rgba(' + this.hue + ', 0)');

                c.strokeStyle = grad;
                c.lineWidth = this.width;
                c.lineCap = 'round';
                c.stroke();
                c.restore();
            };

            // Koi Fish Class (Multi-segment inverse kinematics with Hidden/Emerge/Return States)
            function Koi(index) {
                this.index = index;
                var bounds = getCardBounds();

                // Start hidden dormant directly under the login card
                this.x = bounds.cx + (Math.random() - 0.5) * (bounds.width * 0.3);
                this.y = bounds.cy + (Math.random() - 0.5) * (bounds.height * 0.3);
                this.palette = KOI_PALETTES[index % KOI_PALETTES.length];
                this.scale = 0.85 + Math.random() * 0.45;
                this.angle = Math.random() * Math.PI * 2;
                this.targetAngle = this.angle;
                this.turnSpeed = 0.038 + Math.random() * 0.02;

                this.baseSpeed = 1.4 + Math.random() * 0.8;
                this.speed = this.baseSpeed;
                this.burstTimer = 0;
                this.state = 'HIDDEN'; // 'HIDDEN', 'SEEKING_FOOD', 'RETURNING', 'ROAMING'
                this.opacity = 0;

                // Spine joints (Head to tail)
                this.numSegments = 10;
                this.segmentLength = 6.5 * this.scale;
                this.spine = [];
                for (var i = 0; i < this.numSegments; i++) {
                    this.spine.push({
                        x: this.x - Math.cos(this.angle) * (i * this.segmentLength),
                        y: this.y - Math.sin(this.angle) * (i * this.segmentLength)
                    });
                }

                this.wiggle = Math.random() * Math.PI * 2;
                this.finCycle = Math.random() * Math.PI * 2;

                this.widths = [
                    10.5 * this.scale, // 0: Head tip / mouth
                    13.0 * this.scale, // 1: Forehead
                    14.5 * this.scale, // 2: Shoulders (widest)
                    14.0 * this.scale, // 3: Mid-body
                    12.0 * this.scale, // 4: Dorsal region
                    9.5 * this.scale,  // 5: Lower body
                    7.0 * this.scale,  // 6: Pre-tail
                    4.8 * this.scale,  // 7: Tail stem
                    3.2 * this.scale,  // 8: Tail base
                    2.0 * this.scale   // 9: Caudal root
                ];

                this.targetX = this.x;
                this.targetY = this.y;
                this.targetTimer = 0;
            }

            Koi.prototype.update = function () {
                // If completely hidden, stay still under the card
                if (this.state === 'HIDDEN') {
                    this.opacity = 0;
                    return;
                }

                var bounds = getCardBounds();

                // 1. STATE: SEEKING_FOOD
                if (this.state === 'SEEKING_FOOD') {
                    // Fade in smoothly
                    if (this.opacity < 1) {
                        this.opacity = Math.min(1, this.opacity + 0.05);
                    }

                    // Find nearest food pellet
                    var nearestFood = null;
                    var minFoodDist = Infinity;

                    for (var f = 0; f < foods.length; f++) {
                        var food = foods[f];
                        if (!food.eaten && food.life > 0) {
                            var fdx = food.x - this.x;
                            var fdy = food.y - this.y;
                            var fdist = Math.sqrt(fdx * fdx + fdy * fdy);
                            if (fdist < minFoodDist) {
                                minFoodDist = fdist;
                                nearestFood = food;
                            }
                        }
                    }

                    if (nearestFood) {
                        this.targetX = nearestFood.x;
                        this.targetY = nearestFood.y;
                        this.speed = Math.min(this.speed + 0.12, this.baseSpeed * 2.2);
                        this.turnSpeed = 0.065;

                        // Check bite distance
                        var mouthX = this.spine[0].x;
                        var mouthY = this.spine[0].y;
                        var biteDist = Math.sqrt(Math.pow(mouthX - nearestFood.x, 2) + Math.pow(mouthY - nearestFood.y, 2));

                        if (biteDist < 18 * this.scale) {
                            nearestFood.eaten = true;
                            ripples.push(new Ripple(nearestFood.x, nearestFood.y, 45 * this.scale, 0.85));
                            ripples.push(new Ripple(nearestFood.x, nearestFood.y, 20 * this.scale, 0.5));
                            for (var p = 0; p < 7; p++) {
                                biteParticles.push(new BiteParticle(nearestFood.x, nearestFood.y, nearestFood.color));
                            }
                            this.burstTimer = 50;
                        }
                    } else {
                        // All food eaten!
                        if (feedCount >= FEED_COUNT_TO_PERMANENT) {
                            // Milestone achieved! Become permanent roamers!
                            this.state = 'ROAMING';
                        } else {
                            // Not yet 5th feed: Wait a couple seconds then return to hide
                            if (postEatDelayTimer <= 0) {
                                postEatDelayTimer = 110; // ~1.8 seconds delay
                            }
                        }
                    }
                }

                // 2. STATE: RETURNING (Swimming back under login card to hide)
                else if (this.state === 'RETURNING') {
                    this.turnSpeed = 0.045;
                    this.targetX = bounds.cx;
                    this.targetY = bounds.cy;
                    this.speed = this.baseSpeed * 1.2;

                    // Distance to card center
                    var cdx = this.x - bounds.cx;
                    var cdy = this.y - bounds.cy;
                    var cdist = Math.sqrt(cdx * cdx + cdy * cdy);

                    // When fish enters under the card bounds, fade out smoothly into hiding
                    if (this.x > bounds.left && this.x < bounds.right && this.y > bounds.top && this.y < bounds.bottom) {
                        this.opacity = Math.max(0, this.opacity - 0.035);
                        if (this.opacity <= 0) {
                            this.state = 'HIDDEN';
                            this.x = bounds.cx + (Math.random() - 0.5) * 40;
                            this.y = bounds.cy + (Math.random() - 0.5) * 40;
                            return;
                        }
                    }
                }

                // 3. STATE: ROAMING (Permanent natural swimming after 5th feeding)
                else if (this.state === 'ROAMING') {
                    this.opacity = Math.min(1, this.opacity + 0.05);

                    // Check if new food is dropped
                    var activeFood = foods.find(function (f) { return !f.eaten && f.life > 0; });
                    if (activeFood) {
                        this.state = 'SEEKING_FOOD';
                        return;
                    }

                    this.turnSpeed = 0.038;
                    this.targetTimer--;
                    if (this.targetTimer <= 0) {
                        this.targetX = Math.random() * (width - 160) + 80;
                        this.targetY = Math.random() * (height - 160) + 80;
                        this.targetTimer = 180 + Math.floor(Math.random() * 240);
                    }

                    // Mouse avoidance
                    if (mouse.active) {
                        var mdx = this.x - mouse.x;
                        var mdy = this.y - mouse.y;
                        var mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                        if (mdist < 180) {
                            this.targetAngle = Math.atan2(mdy, mdx);
                            this.speed = Math.min(this.speed + 0.3, this.baseSpeed * 2.4);
                            if (mdist < 80 && Math.random() < 0.15) {
                                ripples.push(new Ripple(this.x, this.y, 45, 0.7));
                            }
                        }
                    }

                    // Random gliding speed bursts
                    this.burstTimer--;
                    if (this.burstTimer <= 0 && Math.random() < 0.008) {
                        this.speed = this.baseSpeed * (1.6 + Math.random() * 0.8);
                        this.burstTimer = 80 + Math.random() * 80;
                        ripples.push(new Ripple(this.spine[6].x, this.spine[6].y, 35 * this.scale, 0.45));
                    } else {
                        this.speed += (this.baseSpeed - this.speed) * 0.03;
                    }
                }

                var dx = this.targetX - this.x;
                var dy = this.targetY - this.y;
                this.targetAngle = Math.atan2(dy, dx);

                // Screen edge soft avoidance when roaming
                if (this.state === 'ROAMING') {
                    var margin = 100;
                    if (this.x < margin) this.targetAngle = 0;
                    if (this.x > width - margin) this.targetAngle = Math.PI;
                    if (this.y < margin) this.targetAngle = Math.PI / 2;
                    if (this.y > height - margin) this.targetAngle = -Math.PI / 2;
                }

                // Smooth steering
                var diff = this.targetAngle - this.angle;
                while (diff < -Math.PI) diff += Math.PI * 2;
                while (diff > Math.PI) diff -= Math.PI * 2;
                this.angle += diff * this.turnSpeed;

                // Move head
                this.x += Math.cos(this.angle) * this.speed;
                this.y += Math.sin(this.angle) * this.speed;
                this.spine[0].x = this.x;
                this.spine[0].y = this.y;

                // Tail undulation frequency
                this.wiggle += 0.09 * (this.speed / this.baseSpeed);
                this.finCycle += 0.12 * (this.speed / this.baseSpeed);

                // Spine follow logic
                for (var i = 1; i < this.numSegments; i++) {
                    var prev = this.spine[i - 1];
                    var curr = this.spine[i];
                    var segDx = curr.x - prev.x;
                    var segDy = curr.y - prev.y;
                    var segAngle = Math.atan2(segDy, segDx);

                    var waveOffset = Math.sin(this.wiggle - i * 0.5) * (i * 0.65 * this.scale);
                    var perpAngle = segAngle + Math.PI / 2;

                    curr.x = prev.x + Math.cos(segAngle) * this.segmentLength + Math.cos(perpAngle) * (waveOffset * 0.12);
                    curr.y = prev.y + Math.sin(segAngle) * this.segmentLength + Math.sin(perpAngle) * (waveOffset * 0.12);
                }
            };

            Koi.prototype.draw = function (c) {
                if (this.state === 'HIDDEN' || this.opacity <= 0) {
                    return;
                }

                var p = this.palette;
                c.save();
                c.globalAlpha = p.opacity * this.opacity;

                var leftPts = [];
                var rightPts = [];

                for (var i = 0; i < this.numSegments; i++) {
                    var jointAngle;
                    if (i === 0) {
                        jointAngle = Math.atan2(this.spine[0].y - this.spine[1].y, this.spine[0].x - this.spine[1].x);
                    } else if (i === this.numSegments - 1) {
                        jointAngle = Math.atan2(this.spine[i - 1].y - this.spine[i].y, this.spine[i - 1].x - this.spine[i].x);
                    } else {
                        jointAngle = Math.atan2(this.spine[i - 1].y - this.spine[i + 1].y, this.spine[i - 1].x - this.spine[i + 1].x);
                    }

                    var normal = jointAngle + Math.PI / 2;
                    var w = this.widths[i];

                    leftPts.push({
                        x: this.spine[i].x + Math.cos(normal) * w,
                        y: this.spine[i].y + Math.sin(normal) * w
                    });
                    rightPts.push({
                        x: this.spine[i].x - Math.cos(normal) * w,
                        y: this.spine[i].y - Math.sin(normal) * w
                    });
                }

                // 1. Pectoral Fins
                var finJoint = this.spine[2];
                var finAngle = Math.atan2(this.spine[1].y - this.spine[3].y, this.spine[1].x - this.spine[3].x);
                var finFlap = Math.sin(this.finCycle) * 0.25;

                // Left fin
                c.save();
                c.translate(leftPts[2].x, leftPts[2].y);
                c.rotate(finAngle + Math.PI / 2 + 0.4 + finFlap);
                c.beginPath();
                c.moveTo(0, 0);
                c.bezierCurveTo(18 * this.scale, -12 * this.scale, 26 * this.scale, 8 * this.scale, 4 * this.scale, 16 * this.scale);
                c.closePath();
                c.fillStyle = p.fin;
                c.fill();
                c.restore();

                // Right fin
                c.save();
                c.translate(rightPts[2].x, rightPts[2].y);
                c.rotate(finAngle - Math.PI / 2 - 0.4 - finFlap);
                c.beginPath();
                c.moveTo(0, 0);
                c.bezierCurveTo(18 * this.scale, 12 * this.scale, 26 * this.scale, -8 * this.scale, 4 * this.scale, -16 * this.scale);
                c.closePath();
                c.fillStyle = p.fin;
                c.fill();
                c.restore();

                // 2. Caudal Tail Fin
                var tailTip = this.spine[this.numSegments - 1];
                var tailPrev = this.spine[this.numSegments - 2];
                var tailAngle = Math.atan2(tailTip.y - tailPrev.y, tailTip.x - tailPrev.x);
                var tailWag = Math.sin(this.wiggle - this.numSegments * 0.5) * 0.35;

                c.save();
                c.translate(tailTip.x, tailTip.y);
                c.rotate(tailAngle + tailWag);
                c.beginPath();
                c.moveTo(0, 0);
                c.bezierCurveTo(25 * this.scale, -22 * this.scale, 48 * this.scale, -18 * this.scale, 52 * this.scale, 0);
                c.bezierCurveTo(48 * this.scale, 18 * this.scale, 25 * this.scale, 22 * this.scale, 0, 0);
                c.fillStyle = p.tailFin;
                c.fill();
                c.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                c.lineWidth = 0.8;
                c.stroke();
                c.restore();

                // 3. Body Silhouette
                c.beginPath();
                c.moveTo(this.spine[0].x, this.spine[0].y);

                for (var j = 0; j < leftPts.length - 1; j++) {
                    var mx = (leftPts[j].x + leftPts[j + 1].x) / 2;
                    var my = (leftPts[j].y + leftPts[j + 1].y) / 2;
                    c.quadraticCurveTo(leftPts[j].x, leftPts[j].y, mx, my);
                }
                c.lineTo(leftPts[leftPts.length - 1].x, leftPts[leftPts.length - 1].y);
                c.lineTo(this.spine[this.numSegments - 1].x, this.spine[this.numSegments - 1].y);
                c.lineTo(rightPts[rightPts.length - 1].x, rightPts[rightPts.length - 1].y);

                for (var k = rightPts.length - 1; k > 0; k--) {
                    var rmx = (rightPts[k].x + rightPts[k - 1].x) / 2;
                    var rmy = (rightPts[k].y + rightPts[k - 1].y) / 2;
                    c.quadraticCurveTo(rightPts[k].x, rightPts[k].y, rmx, rmy);
                }
                c.lineTo(this.spine[0].x, this.spine[0].y);
                c.closePath();

                c.fillStyle = p.body;
                var isDark = document.documentElement.classList.contains('dark-mode') || document.body.classList.contains('dark-mode');
                c.shadowColor = isDark ? p.accent2 : 'rgba(15, 23, 42, 0.08)';
                c.shadowBlur = isDark ? 24 : 14;
                c.shadowOffsetY = isDark ? 0 : 8;
                c.fill();
                c.shadowColor = 'transparent';

                // 4. Color Patches
                c.save();
                c.clip();

                c.beginPath();
                c.arc(this.spine[1].x, this.spine[1].y, this.widths[1] * 0.85, 0, Math.PI * 2);
                c.fillStyle = p.accent;
                c.fill();

                c.beginPath();
                c.ellipse(this.spine[3].x, this.spine[3].y, this.widths[3] * 1.1, this.widths[3] * 0.7, this.angle, 0, Math.PI * 2);
                c.fillStyle = p.accent2;
                c.fill();

                c.beginPath();
                c.arc(this.spine[6].x, this.spine[6].y, this.widths[6] * 0.9, 0, Math.PI * 2);
                c.fillStyle = p.accent;
                c.fill();
                c.restore();

                // 5. Head Eyes
                var headAngle = Math.atan2(this.spine[0].y - this.spine[1].y, this.spine[0].x - this.spine[1].x);
                var eyeNormal = headAngle + Math.PI / 2;
                var eyeDist = this.widths[0] * 0.65;

                c.beginPath();
                c.arc(this.spine[0].x + Math.cos(eyeNormal) * eyeDist, this.spine[0].y + Math.sin(eyeNormal) * eyeDist, 1.6 * this.scale, 0, Math.PI * 2);
                c.arc(this.spine[0].x - Math.cos(eyeNormal) * eyeDist, this.spine[0].y - Math.sin(eyeNormal) * eyeDist, 1.6 * this.scale, 0, Math.PI * 2);
                c.fillStyle = '#0f172a';
                c.fill();

                c.restore();
            };

            // Feeding Limit Settings
            var MAX_ACTIVE_FOODS = 8;
            var FEEDING_COOLDOWN_MS = 450;
            var lastFoodDropTime = 0;

            // Trigger emergence of Koi after 1.2-1.5s delay
            function triggerKoiEmergence(x, y) {
                var bounds = getCardBounds();
                for (var i = 0; i < fishes.length; i++) {
                    var fish = fishes[i];

                    // If fish was hidden or returning, position at card edge facing food
                    if (fish.state === 'HIDDEN' || fish.state === 'RETURNING') {
                        var angleToFood = Math.atan2(y - bounds.cy, x - bounds.cx);
                        var spreadAngle = angleToFood + (i - (fishes.length - 1) / 2) * 0.32;

                        var edgeX = bounds.cx + Math.cos(spreadAngle) * (bounds.width * 0.48);
                        var edgeY = bounds.cy + Math.sin(spreadAngle) * (bounds.height * 0.48);

                        fish.x = edgeX;
                        fish.y = edgeY;
                        fish.angle = spreadAngle;
                        fish.targetAngle = angleToFood;
                        fish.targetX = x;
                        fish.targetY = y;
                        fish.speed = fish.baseSpeed * 2.8;
                        fish.burstTimer = 90;
                        fish.opacity = 0.2;

                        for (var j = 0; j < fish.numSegments; j++) {
                            fish.spine[j].x = fish.x - Math.cos(fish.angle) * (j * fish.segmentLength);
                            fish.spine[j].y = fish.y - Math.sin(fish.angle) * (j * fish.segmentLength);
                        }

                        ripples.push(new Ripple(edgeX, edgeY, 50 * fish.scale, 0.85));
                    }

                    fish.state = 'SEEKING_FOOD';
                }
            }

            // Function to drop food pellets
            function dropFood(x, y) {
                var now = Date.now();

                // Ripple at click spot
                ripples.push(new Ripple(x, y, 70, 0.8));
                ripples.push(new Ripple(x, y, 38, 0.45));

                var activeFoodsCount = foods.filter(function (f) { return !f.eaten && f.life > 0; }).length;

                if (now - lastFoodDropTime < FEEDING_COOLDOWN_MS) {
                    return;
                }

                if (activeFoodsCount >= MAX_ACTIVE_FOODS) {
                    return;
                }

                // Increment feeding counter
                feedCount++;
                lastFoodPos = { x: x, y: y };

                // Set 1.3 second delay before fish emerge!
                emergeTimer = 78; // 78 frames @ 60fps ~= 1.3 seconds
                postEatDelayTimer = 0;

                // Drop 2-3 food pellets
                var availableSlots = MAX_ACTIVE_FOODS - activeFoodsCount;
                var pelletCount = Math.min(availableSlots, 2 + Math.floor(Math.random() * 2));

                for (var p = 0; p < pelletCount; p++) {
                    foods.push(new FoodPellet(x, y));
                }

                lastFoodDropTime = now;
            }

            // Initialize Scene
            function initScene() {
                resizeCanvas();
                fishes = [];
                ripples = [];
                streams = [];
                foods = [];
                biteParticles = [];
                lastFoodDropTime = 0;
                feedCount = 0;
                emergeTimer = 0;
                postEatDelayTimer = 0;

                // Spawn 6 Koi fish (initially hidden)
                var count = Math.max(4, Math.min(7, Math.floor(width / 240)));
                for (var i = 0; i < count; i++) {
                    fishes.push(new Koi(i));
                }

                // Spawn 22 flowing water streamlines
                var streamCount = Math.max(16, Math.min(26, Math.floor(width / 65)));
                for (var s = 0; s < streamCount; s++) {
                    streams.push(new WaterStream());
                }
            }

            // Mouse & Touch Listeners
            window.addEventListener('resize', function () {
                resizeCanvas();
            });

            window.addEventListener('mousemove', function (e) {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
                mouse.active = true;

                var now = Date.now();
                if (now - mouse.lastMove > 120) {
                    ripples.push(new Ripple(mouse.x, mouse.y, 35, 0.35));
                    mouse.lastMove = now;
                }
            });

            window.addEventListener('mouseleave', function () {
                mouse.active = false;
            });

            // Click outside login card -> Feed Fish!
            window.addEventListener('click', function (e) {
                var loginCard = document.querySelector('.auth-container');
                var themeButton = document.querySelector('.theme-toggle-btn');
                var isInsideCard = (loginCard && loginCard.contains(e.target)) || (themeButton && themeButton.contains(e.target));

                if (!isInsideCard) {
                    dropFood(e.clientX, e.clientY);
                }
            });

            window.addEventListener('touchmove', function (e) {
                if (e.touches && e.touches[0]) {
                    mouse.x = e.touches[0].clientX;
                    mouse.y = e.touches[0].clientY;
                    mouse.active = true;
                }
            }, { passive: true });

            initScene();

            // Main Animation Loop
            var isVisible = true;
            document.addEventListener('visibilitychange', function () {
                isVisible = !document.hidden;
            });

            function render() {
                if (isVisible) {
                    ctx.clearRect(0, 0, width, height);

                    // 1. Handle Emergence Timer (1.3s delay after food dropped)
                    if (emergeTimer > 0) {
                        emergeTimer--;
                        if (emergeTimer === 0) {
                            var hasActiveFood = foods.some(function (f) { return !f.eaten && f.life > 0; });
                            if (hasActiveFood) {
                                triggerKoiEmergence(lastFoodPos.x, lastFoodPos.y);
                            }
                        }
                    }

                    // 2. Handle Post-Eating Timer (Return to hide if feedCount < 5)
                    if (postEatDelayTimer > 0) {
                        postEatDelayTimer--;
                        if (postEatDelayTimer === 0) {
                            var activeFoodCheck = foods.some(function (f) { return !f.eaten && f.life > 0; });
                            if (!activeFoodCheck && feedCount < FEED_COUNT_TO_PERMANENT) {
                                for (var k = 0; k < fishes.length; k++) {
                                    if (fishes[k].state !== 'HIDDEN') {
                                        fishes[k].state = 'RETURNING';
                                    }
                                }
                            }
                        }
                    }

                    // 3. Update and draw Flowing Water Streams
                    for (var s = 0; s < streams.length; s++) {
                        streams[s].update();
                        streams[s].draw(ctx);
                    }

                    // 4. Update and draw Koi Fishes
                    for (var f = 0; f < fishes.length; f++) {
                        fishes[f].update();
                        fishes[f].draw(ctx);
                    }

                    // 5. Update and draw Food Pellets
                    for (var fd = foods.length - 1; fd >= 0; fd--) {
                        foods[fd].update();
                        foods[fd].draw(ctx);
                        if (foods[fd].eaten || foods[fd].life <= 0) {
                            foods.splice(fd, 1);
                        }
                    }

                    // 6. Update and draw Bite Crumb Particles
                    for (var bp = biteParticles.length - 1; bp >= 0; bp--) {
                        biteParticles[bp].update();
                        biteParticles[bp].draw(ctx);
                        if (biteParticles[bp].alpha <= 0) {
                            biteParticles.splice(bp, 1);
                        }
                    }

                    // 7. Update and draw Water Ripples on top
                    for (var r = ripples.length - 1; r >= 0; r--) {
                        ripples[r].update();
                        ripples[r].draw(ctx);
                        if (ripples[r].alpha <= 0) {
                            ripples.splice(r, 1);
                        }
                    }
                }
                requestAnimationFrame(render);
            }

            requestAnimationFrame(render);

            // ========================================================
            // THEME TOGGLE CONTROLLER (DARK / LIGHT MODE)
            // ========================================================
            var themeBtn = document.getElementById('themeToggle');
            var themeText = document.getElementById('themeText');

            function updateThemeUI(isDark) {
                if (themeText) {
                    themeText.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
                }
                if (themeBtn) {
                    themeBtn.setAttribute('title', isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap');
                }
            }

            // Sync initial state
            var isDarkInitial = document.documentElement.classList.contains('dark-mode') || document.body.classList.contains('dark-mode');
            if (isDarkInitial) {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
                updateThemeUI(true);
            } else {
                updateThemeUI(false);
            }

            if (themeBtn) {
                themeBtn.addEventListener('click', function () {
                    var isNowDark = document.documentElement.classList.toggle('dark-mode');
                    document.body.classList.toggle('dark-mode', isNowDark);
                    try {
                        localStorage.setItem('reftech_theme', isNowDark ? 'dark' : 'light');
                    } catch (e) {}
                    updateThemeUI(isNowDark);

                    // Add celebratory atmospheric ripple across the pond on switch
                    ripples.push(new Ripple(window.innerWidth / 2, window.innerHeight / 2, Math.max(window.innerWidth, window.innerHeight) * 0.75, 0.85));
                });
            }
        });
    </script>
</body>
</html>
