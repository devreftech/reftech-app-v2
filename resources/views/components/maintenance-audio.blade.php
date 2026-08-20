@if (!empty($details['bgm_enabled']))
    <div id="maintenance-audio-widget" class="maintenance-audio-pill shadow-lg">
        <audio id="maint-audio" loop preload="auto" src="{{ asset(ltrim($details['bgm_url'] ?? 'assets/audio/Water_Lily.mp3', '/')) }}"></audio>

        <!-- Visualizer & Play Button -->
        <button type="button" id="btn-toggle-audio" class="audio-control-btn" title="Nyalakan / Hentikan Musik">
            <span class="equalizer-bars" id="audio-visualizer">
                <span class="eq-bar bar-1"></span>
                <span class="eq-bar bar-2"></span>
                <span class="eq-bar bar-3"></span>
                <span class="eq-bar bar-4"></span>
            </span>
            <i class="mdi mdi-play fs-5 ms-1" id="audio-icon-play" style="display: none;"></i>
        </button>

        <!-- Title & Info -->
        <div class="audio-info-wrap" onclick="toggleAudioPlayback()">
            <span class="audio-title text-truncate" id="audio-title-display">
                {{ $details['bgm_title'] ?? 'Lofi Ambient Chill' }}
            </span>
            <span class="audio-subtext">Musik Santai Pemeliharaan</span>
        </div>

        <!-- Volume Slider Control -->
        <div class="audio-volume-wrap">
            <i class="mdi mdi-volume-medium text-muted" id="volume-icon"></i>
            <input type="range" class="volume-slider" id="audio-volume-range" min="0" max="1" step="0.05" value="0.35" title="Volume">
        </div>
    </div>

    <style>
        .maintenance-audio-pill {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 9999px;
            padding: 6px 14px 6px 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 99999;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 340px;
        }

        /* Dark Theme Support for Audio Pill */
        body.dark-mode .maintenance-audio-pill,
        .bg-glow ~ .maintenance-card ~ .maintenance-audio-pill,
        body[style*="#0f172a"] .maintenance-audio-pill {
            background: rgba(30, 41, 59, 0.92);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
        }

        body.dark-mode .audio-title,
        body[style*="#0f172a"] .audio-title {
            color: #f1f5f9 !important;
        }

        body.dark-mode .audio-subtext,
        body[style*="#0f172a"] .audio-subtext {
            color: #94a3b8 !important;
        }

        .maintenance-audio-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px -5px rgba(15, 23, 42, 0.2);
        }

        .audio-control-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

        .audio-control-btn:hover {
            transform: scale(1.06);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
        }

        /* Animated Equalizer Bars */
        .equalizer-bars {
            display: flex;
            align-items: flex-end;
            gap: 2.5px;
            height: 14px;
        }

        .eq-bar {
            width: 2.5px;
            background-color: #ffffff;
            border-radius: 2px;
            transition: height 0.2s ease;
        }

        .playing .bar-1 { animation: eqJump 1.1s ease-in-out infinite alternate; height: 12px; }
        .playing .bar-2 { animation: eqJump 0.8s ease-in-out infinite alternate 0.2s; height: 8px; }
        .playing .bar-3 { animation: eqJump 1.3s ease-in-out infinite alternate 0.4s; height: 14px; }
        .playing .bar-4 { animation: eqJump 0.9s ease-in-out infinite alternate 0.1s; height: 10px; }

        .paused .eq-bar {
            height: 3px !important;
            animation: none !important;
        }

        @keyframes eqJump {
            0% { height: 3px; }
            100% { height: 14px; }
        }

        .audio-info-wrap {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            min-width: 0;
        }

        .audio-title {
            font-size: 0.785rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            max-width: 140px;
        }

        .audio-subtext {
            font-size: 0.675rem;
            color: #64748b;
            line-height: 1.2;
            margin-top: 1px;
        }

        .audio-volume-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
            padding-left: 4px;
            border-left: 1px solid rgba(226, 232, 240, 0.8);
        }

        .volume-slider {
            width: 50px;
            height: 4px;
            border-radius: 2px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        @media (max-width: 576px) {
            .maintenance-audio-pill {
                bottom: 16px;
                right: 16px;
                left: auto;
            }
            .audio-volume-wrap {
                display: none;
            }
        }
    </style>

    <script>
        (function() {
            const audio = document.getElementById('maint-audio');
            const visualizer = document.getElementById('audio-visualizer');
            const playIcon = document.getElementById('audio-icon-play');
            const volumeSlider = document.getElementById('audio-volume-range');
            const volumeIcon = document.getElementById('volume-icon');
            const toggleBtn = document.getElementById('btn-toggle-audio');

            if (!audio) return;

            // Set initial soft volume (35%)
            audio.volume = 0.35;

            function updateUIPlaying() {
                if (visualizer) visualizer.classList.add('playing');
                if (visualizer) visualizer.classList.remove('paused');
                if (visualizer) visualizer.style.display = 'flex';
                if (playIcon) playIcon.style.display = 'none';
            }

            function updateUIPaused() {
                if (visualizer) visualizer.classList.remove('playing');
                if (visualizer) visualizer.classList.add('paused');
                if (visualizer) visualizer.style.display = 'none';
                if (playIcon) playIcon.style.display = 'inline-block';
            }

            window.toggleAudioPlayback = function() {
                if (audio.paused) {
                    audio.play().then(() => {
                        updateUIPlaying();
                        sessionStorage.setItem('maint_bgm_user_action', 'play');
                    }).catch(() => {});
                } else {
                    audio.pause();
                    updateUIPaused();
                    sessionStorage.setItem('maint_bgm_user_action', 'pause');
                }
            };

            if (toggleBtn) {
                toggleBtn.addEventListener('click', window.toggleAudioPlayback);
            }

            // Volume Control
            if (volumeSlider) {
                volumeSlider.addEventListener('input', function(e) {
                    const val = parseFloat(e.target.value);
                    audio.volume = val;
                    if (val === 0) {
                        if (volumeIcon) volumeIcon.className = 'mdi mdi-volume-mute text-muted';
                    } else if (val < 0.5) {
                        if (volumeIcon) volumeIcon.className = 'mdi mdi-volume-medium text-muted';
                    } else {
                        if (volumeIcon) volumeIcon.className = 'mdi mdi-volume-high text-primary';
                    }
                });
            }

            // Smart Autoplay Attempt & First User Interaction Listener
            function tryAutoPlay() {
                if (sessionStorage.getItem('maint_bgm_user_action') === 'pause') {
                    updateUIPaused();
                    return;
                }

                audio.play().then(() => {
                    updateUIPlaying();
                }).catch(() => {
                    // Browser policy blocked sound -> start muted or on first click
                    updateUIPaused();
                    const onFirstClick = function() {
                        if (sessionStorage.getItem('maint_bgm_user_action') !== 'pause') {
                            audio.play().then(() => {
                                updateUIPlaying();
                            }).catch(() => {});
                        }
                        document.removeEventListener('click', onFirstClick);
                        document.removeEventListener('keydown', onFirstClick);
                        document.removeEventListener('touchstart', onFirstClick);
                    };

                    document.addEventListener('click', onFirstClick, { once: true });
                    document.addEventListener('keydown', onFirstClick, { once: true });
                    document.addEventListener('touchstart', onFirstClick, { once: true });
                });
            }

            // Init on load
            setTimeout(tryAutoPlay, 500);
        })();
    </script>
@endif
