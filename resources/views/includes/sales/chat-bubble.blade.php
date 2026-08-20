{{-- =========================================================================
     FLOATING CHAT BUBBLE WIDGET (LIVE BACKEND + AUDIO NOTIFICATION)
     ========================================================================= --}}

<style>
/* -------------------------------------------------------------------------- */
/* Chat Floating Trigger & Container Styling                                  */
/* -------------------------------------------------------------------------- */
.rf-chat-widget-wrapper {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1070;
    font-family: inherit;
}

/* Floating Action Button */
.rf-chat-bubble-btn {
    width: 60px;
    height: 60px;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #666cff 0%, #4f55d9 100%) !important;
    color: #ffffff !important;
    border: none !important;
    outline: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 8px 24px rgba(102, 108, 255, 0.45);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    text-decoration: none;
    -webkit-appearance: none;
    appearance: none;
}

.rf-chat-bubble-btn:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 12px 28px rgba(102, 108, 255, 0.6);
    color: #ffffff !important;
}

.rf-chat-bubble-btn:active {
    transform: scale(0.95);
}

/* Exact Icon Centering */
.rf-chat-bubble-btn .rf-btn-icon-inner {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100%;
    height: 100%;
    line-height: 1 !important;
    pointer-events: none;
}

.rf-chat-bubble-btn svg {
    width: 28px;
    height: 28px;
    display: block;
}

/* Pulse animation on trigger */
.rf-chat-bubble-btn::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    border: 2px solid rgba(102, 108, 255, 0.6);
    animation: rf-pulse 2.2s infinite;
    pointer-events: none;
}

@keyframes rf-pulse {
    0% { transform: scale(1); opacity: 0.8; }
    70% { transform: scale(1.25); opacity: 0; }
    100% { transform: scale(1.3); opacity: 0; }
}

/* Unread Badge */
.rf-chat-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background-color: #ff4d49;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(255, 77, 73, 0.5);
    animation: bounceIn 0.4s ease;
    line-height: 1;
}

/* -------------------------------------------------------------------------- */
/* Chat Window Box                                                            */
/* -------------------------------------------------------------------------- */
.rf-chat-box {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 390px;
    max-width: calc(100vw - 32px);
    height: 570px;
    max-height: calc(100vh - 120px);
    background-color: #ffffff;
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(76, 78, 100, 0.22);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 1075;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.95);
    transform-origin: bottom right;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(76, 78, 100, 0.12);
}

.rf-chat-box.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

/* Dark mode compatibility */
.dark-style .rf-chat-box {
    background-color: #2b2c40;
    border-color: #3e405b;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
    color: #dbdade;
}

/* Header */
.rf-chat-header {
    background: linear-gradient(135deg, #666cff 0%, #5a60e6 100%);
    color: #ffffff;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.rf-chat-header-user {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.rf-chat-avatar-wrap {
    position: relative;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    flex-shrink: 0;
}

.rf-chat-avatar-wrap img, .rf-chat-avatar-wrap .rf-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13.5px;
}

/* Presence Dot Indicators */
.rf-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-sizing: content-box;
    display: inline-block;
    transition: all 0.3s ease;
    z-index: 2;
}

.dark-style .rf-status-dot {
    border-color: #2b2c40;
}

.rf-status-online {
    background-color: #71dd37;
    box-shadow: 0 0 0 0 rgba(113, 221, 55, 0.7);
    animation: rf-status-pulse 2.2s infinite;
}

.rf-status-away {
    background-color: #ffab00;
}

.rf-status-offline {
    background-color: #adb5bd;
}

@keyframes rf-status-pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(113, 221, 55, 0.6);
    }
    70% {
        box-shadow: 0 0 0 5px rgba(113, 221, 55, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(113, 221, 55, 0);
    }
}

.rf-room-presence-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    opacity: 0.95;
}

.rf-room-presence-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.rf-chat-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.rf-chat-header-btn {
    background: rgba(255, 255, 255, 0.18);
    border: none;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    cursor: pointer;
    transition: all 0.2s;
}

.rf-chat-header-btn:hover {
    background: rgba(255, 255, 255, 0.32);
}

/* Search and Filter in Contact List */
.rf-chat-search-bar {
    padding: 10px 14px;
    background-color: #f7f7f9;
    border-bottom: 1px solid rgba(76, 78, 100, 0.08);
}

.dark-style .rf-chat-search-bar {
    background-color: #232333;
    border-bottom-color: #3e405b;
}

.rf-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.rf-search-input-wrap i {
    position: absolute;
    left: 12px;
    color: #8c90a4;
    font-size: 18px;
}

.rf-search-input {
    width: 100%;
    padding: 7px 12px 7px 36px;
    border-radius: 20px;
    border: 1px solid #dbdade;
    font-size: 13px;
    outline: none;
    background: #ffffff;
}

.dark-style .rf-search-input {
    background: #2b2c40;
    border-color: #444564;
    color: #dbdade;
}

.rf-search-input:focus {
    border-color: #666cff;
    box-shadow: 0 0 0 2px rgba(102, 108, 255, 0.15);
}

/* Contact List */
.rf-chat-contact-list {
    flex: 1;
    overflow-y: auto;
    padding: 4px 0;
}

.rf-contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid rgba(76, 78, 100, 0.04);
}

.rf-contact-item:hover {
    background-color: rgba(102, 108, 255, 0.06);
}

.dark-style .rf-contact-item:hover {
    background-color: rgba(102, 108, 255, 0.12);
}

.rf-contact-info {
    flex: 1;
    min-width: 0;
}

.rf-contact-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}

.rf-contact-name {
    font-weight: 600;
    font-size: 13.5px;
    color: #4c4e64;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dark-style .rf-contact-name {
    color: #e6e5e8;
}

.rf-contact-time {
    font-size: 11px;
    color: #8c90a4;
    flex-shrink: 0;
}

.rf-contact-msg-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.rf-contact-last-msg {
    font-size: 12px;
    color: #797c92;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rf-role-tag {
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-block;
}

.rf-role-tech { background: #e8fadf; color: #71dd37; }
.rf-role-sales { background: #e7e7ff; color: #666cff; }
.rf-role-finance { background: #fff1d6; color: #ffab00; }
.rf-role-wh { background: #d7f5fc; color: #03c3ec; }
.rf-role-admin { background: #ffe5e5; color: #ff3e1d; }

/* -------------------------------------------------------------------------- */
/* Chat Room Messages Stream                                                  */
/* -------------------------------------------------------------------------- */
.rf-chat-room {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.rf-chat-messages-wrap {
    flex: 1;
    overflow-y: auto;
    padding: 14px 16px;
    background-color: #f8f8fb;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.dark-style .rf-chat-messages-wrap {
    background-color: #232333;
}

/* Date separator */
.rf-chat-date-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 6px 0;
}

.rf-chat-date-divider span {
    font-size: 10.5px;
    padding: 3px 10px;
    border-radius: 12px;
    background: rgba(76, 78, 100, 0.08);
    color: #797c92;
    font-weight: 500;
}

.dark-style .rf-chat-date-divider span {
    background: #323348;
    color: #a8aaae;
}

/* Bubbles */
.rf-msg-row {
    display: flex;
    gap: 8px;
    max-width: 84%;
    animation: fadeInMsg 0.25s ease forwards;
}

@keyframes fadeInMsg {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.rf-msg-incoming {
    align-self: flex-start;
}

.rf-msg-outgoing {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.rf-msg-bubble {
    padding: 9px 13px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.45;
    position: relative;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.rf-msg-incoming .rf-msg-bubble {
    background-color: #ffffff;
    color: #4c4e64;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(76, 78, 100, 0.08);
}

.dark-style .rf-msg-incoming .rf-msg-bubble {
    background-color: #2e3046;
    color: #e6e5e8;
    border-color: #40425c;
}

.rf-msg-outgoing .rf-msg-bubble {
    background: linear-gradient(135deg, #666cff 0%, #575ce8 100%);
    color: #ffffff;
    border-bottom-right-radius: 4px;
}

.rf-msg-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    font-size: 10px;
    margin-top: 4px;
    opacity: 0.8;
}

.rf-msg-incoming .rf-msg-meta {
    justify-content: flex-start;
    color: #8c90a4;
}

/* Chat Input Bar */
.rf-chat-input-bar {
    padding: 10px 14px;
    background-color: #ffffff;
    border-top: 1px solid rgba(76, 78, 100, 0.08);
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
}

.dark-style .rf-chat-input-bar {
    background-color: #2b2c40;
    border-top-color: #3e405b;
}

.rf-chat-input {
    flex: 1;
    border: 1px solid #dbdade;
    border-radius: 20px;
    padding: 7px 14px;
    font-size: 13px;
    outline: none;
    background: #f8f8fb;
    color: inherit;
    height: 36px;
}

.dark-style .rf-chat-input {
    background: #232333;
    border-color: #444564;
    color: #dbdade;
}

.rf-chat-input:focus {
    border-color: #666cff;
    background: #fff;
}

.dark-style .rf-chat-input:focus {
    background: #2b2c40;
}

.rf-chat-action-btn {
    background: none;
    border: none;
    color: #797c92;
    font-size: 20px;
    cursor: pointer;
    padding: 6px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.rf-chat-action-btn:hover {
    color: #666cff;
    background-color: rgba(102, 108, 255, 0.08);
}

.rf-chat-send-btn {
    background: linear-gradient(135deg, #666cff 0%, #5257e0 100%);
    color: #fff;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(102, 108, 255, 0.35);
    transition: all 0.2s;
}

.rf-chat-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 14px rgba(102, 108, 255, 0.5);
}

/* File Attachment Card */
.rf-msg-attachment-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.15);
    padding: 7px 10px;
    border-radius: 8px;
    margin-top: 5px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: inherit;
    text-decoration: none;
}

.rf-msg-incoming .rf-msg-attachment-card {
    background: #f2f3f7;
    border-color: #e2e4ea;
}

.dark-style .rf-msg-incoming .rf-msg-attachment-card {
    background: #232333;
    border-color: #3e405b;
}

.rf-msg-img-preview {
    max-width: 100%;
    max-height: 180px;
    border-radius: 8px;
    margin-top: 5px;
    cursor: pointer;
    display: block;
}

/* Selected Attachment Preview Bar */
.rf-attachment-preview-bar {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: #ffffff;
    padding: 8px 14px;
    border-top: 1px solid rgba(76, 78, 100, 0.1);
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    box-shadow: 0 -4px 12px rgba(0,0,0,0.06);
}

.dark-style .rf-attachment-preview-bar {
    background: #2b2c40;
    border-top-color: #3e405b;
}

/* Empty State & Loader */
.rf-chat-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #8c90a4;
    padding: 24px;
    text-align: center;
}
</style>

<div class="rf-chat-widget-wrapper" id="rfChatWidget">

    <!-- FLOATING BUBBLE BUTTON -->
    <button type="button" class="rf-chat-bubble-btn" id="rfChatToggleBtn" title="Buka Chat Internal Reftech">
        <span class="rf-btn-icon-inner" id="rfChatBtnInner">
            <svg id="rfChatSvgIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 3c5.523 0 10 3.806 10 8.5c0 2.203-.984 4.223-2.64 5.733l.613 3.373a1 1 0 0 1-1.341 1.125l-3.83-1.637c-.89.266-1.83.406-2.802.406-5.523 0-10-3.806-10-8.5S6.477 3 12 3zm0 2c-4.418 0-8 2.91-8 6.5s3.582 6.5 8 6.5c.877 0 1.728-.124 2.528-.358a1 1 0 0 1 .74.075l2.673 1.144-.43-2.366a1 1 0 0 1 .38-.934C19.141 14.425 20 12.822 20 11.5 20 7.91 16.418 5 12 5zm-3.5 5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm7 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm-3.5 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"/>
            </svg>
        </span>
        <span class="rf-chat-badge" id="rfUnreadBadge">0</span>
    </button>

    <!-- CHAT POPUP WINDOW -->
    <div class="rf-chat-box" id="rfChatBox">

        <!-- ================= VIEW 1: CONTACTS / CHAT LIST ================= -->
        <div id="rfChatListView" style="display: flex; flex-direction: column; height: 100%;">
            <!-- Header List -->
            <div class="rf-chat-header">
                <div class="rf-chat-header-user">
                    <div class="rf-chat-avatar-wrap">
                        <div class="rf-avatar-placeholder bg-white text-primary">
                            <i class="mdi mdi-forum-outline" style="font-size: 20px;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 14.5px; line-height: 1.2;">Reftech Chat</div>
                        <small style="font-size: 11px; opacity: 0.85;">Pesan Internal Antar Staf</small>
                    </div>
                </div>
                <div class="rf-chat-header-actions">
                    <button type="button" class="rf-chat-header-btn" id="rfMuteToggleBtn" title="Suara Notifikasi: Aktif" onclick="toggleSoundEffect()">
                        <i class="mdi mdi-volume-high" id="rfMuteIcon"></i>
                    </button>
                    <button type="button" class="rf-chat-header-btn" id="rfCloseChatBtn" title="Tutup">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="rf-chat-search-bar">
                <div class="rf-search-input-wrap">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" class="rf-search-input" id="rfSearchUserInput" placeholder="Cari nama atau divisi...">
                </div>
                <div class="d-flex gap-1 mt-2 overflow-auto pb-1" style="white-space: nowrap;">
                    <button type="button" class="btn btn-xs btn-primary px-2 py-1" style="font-size: 11px; border-radius: 12px;" onclick="filterChatPresence('all', this)">Semua</button>
                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;" onclick="filterChatPresence('online', this)">
                        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background-color: #71dd37; margin-right: 4px;"></span>Online
                    </button>
                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;" onclick="filterChatPresence('away', this)">
                        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background-color: #ffab00; margin-right: 4px;"></span>Zzzz
                    </button>
                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;" onclick="filterChatPresence('offline', this)">
                        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background-color: #adb5bd; margin-right: 4px;"></span>Offline
                    </button>
                </div>
            </div>

            <!-- Contact List Items Container -->
            <div class="rf-chat-contact-list" id="rfContactContainer">
                <div class="rf-chat-empty-state">
                    <i class="mdi mdi-loading mdi-spin" style="font-size: 28px;"></i>
                    <div class="mt-2" style="font-size: 12px;">Memuat daftar rekan kerja...</div>
                </div>
            </div>
        </div>

        <!-- ================= VIEW 2: ACTIVE CHAT ROOM ================= -->
        <div id="rfChatRoomView" class="rf-chat-room" style="display: none;">
            
            <!-- Room Header -->
            <div class="rf-chat-header">
                <div class="rf-chat-header-user">
                    <button type="button" class="rf-chat-header-btn me-1" onclick="backToChatList()" title="Kembali ke kontak">
                        <i class="mdi mdi-arrow-left"></i>
                    </button>
                    <div class="rf-chat-avatar-wrap" id="rfRoomAvatarWrap">
                        <div class="rf-avatar-placeholder" id="rfRoomAvatar" style="background-color: #e8fadf; color: #71dd37;">US</div>
                    </div>
                    <div style="min-width: 0;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-truncate" id="rfRoomUserName" style="font-size: 14px;">User</span>
                            <span class="rf-role-tag" id="rfRoomRoleBadge" style="background: rgba(255,255,255,0.25); color: #fff;">Staff</span>
                        </div>
                        <small style="font-size: 11px; opacity: 0.9;" id="rfRoomStatusText">Obrolan Internal</small>
                    </div>
                </div>
                <div class="rf-chat-header-actions">
                    <button type="button" class="rf-chat-header-btn" onclick="toggleChatBox()" title="Minimize">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div class="rf-chat-messages-wrap" id="rfMessagesList">
                <div class="rf-chat-empty-state">
                    <i class="mdi mdi-loading mdi-spin" style="font-size: 24px;"></i>
                    <div class="mt-2" style="font-size: 12px;">Memuat riwayat chat...</div>
                </div>
            </div>

            <!-- Attachment Selected Bar -->
            <div class="rf-attachment-preview-bar" id="rfAttachmentPreviewBar">
                <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 85%;">
                    <i class="mdi mdi-paperclip text-primary" style="font-size: 18px;"></i>
                    <span id="rfAttachmentFileName" class="text-truncate fw-semibold">File.pdf</span>
                </div>
                <button type="button" class="btn btn-sm btn-icon text-danger p-0" onclick="clearSelectedAttachment()" title="Batal Lampiran">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>

            <!-- Input Bar -->
            <div class="rf-chat-input-bar">
                <input type="file" id="rfChatFileInput" style="display: none;" onchange="handleFileSelected(event)">
                <button type="button" class="rf-chat-action-btn" title="Lampirkan File/Foto" onclick="document.getElementById('rfChatFileInput').click()">
                    <i class="mdi mdi-paperclip"></i>
                </button>
                <input type="text" class="rf-chat-input" id="rfChatInputField" placeholder="Ketik pesan..." onkeypress="handleChatKeyPress(event)">
                <button type="button" class="rf-chat-send-btn" id="rfSendMsgBtn" onclick="sendChatMessage()" title="Kirim Pesan">
                    <i class="mdi mdi-send" id="rfSendIcon"></i>
                </button>
            </div>

        </div>

    </div>
</div>

<script>
/* ==========================================================================
   REFTECH CHAT ENGINE (LIVE BACKEND + WEB AUDIO API CHIME)
   ========================================================================== */

var isChatOpen = false;
var activeChatUser = null;
var lastMessageId = 0;
var contactsData = [];
var selectedAttachmentFile = null;
var isSoundEnabled = true;
var pollIntervalTimer = null;
var isPollingBusy = false;
var conversationCache = {};
var activeFetchController = null;
var isInitialPollComplete = false;
var originalDocTitle = document.title;
var titleBlinkTimer = null;

function triggerTitleBlink(text) {
    if (!document.hidden) return;
    if (titleBlinkTimer) clearInterval(titleBlinkTimer);

    var isBlink = false;
    titleBlinkTimer = setInterval(function() {
        if (!document.hidden) {
            stopTitleBlink();
            return;
        }
        document.title = isBlink ? ('💬 ' + (text || 'Pesan Baru!')) : originalDocTitle;
        isBlink = !isBlink;
    }, 1000);
}

function stopTitleBlink() {
    if (titleBlinkTimer) {
        clearInterval(titleBlinkTimer);
        titleBlinkTimer = null;
    }
    document.title = originalDocTitle;
}

var svgChatIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M12 3c5.523 0 10 3.806 10 8.5c0 2.203-.984 4.223-2.64 5.733l.613 3.373a1 1 0 0 1-1.341 1.125l-3.83-1.637c-.89.266-1.83.406-2.802.406-5.523 0-10-3.806-10-8.5S6.477 3 12 3zm0 2c-4.418 0-8 2.91-8 6.5s3.582 6.5 8 6.5c.877 0 1.728-.124 2.528-.358a1 1 0 0 1 .74.075l2.673 1.144-.43-2.366a1 1 0 0 1 .38-.934C19.141 14.425 20 12.822 20 11.5 20 7.91 16.418 5 12 5zm-3.5 5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm7 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm-3.5 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"/></svg>';
var svgCloseIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';

/* -------------------------------------------------------------------------- */
/* Web Audio API Notification Sound Synthesizer                               */
/* -------------------------------------------------------------------------- */
var audioCtx = null;

function unlockAudioContext() {
    try {
        var AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass && !audioCtx) {
            audioCtx = new AudioContextClass();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    } catch(e) {}
}
document.addEventListener('click', unlockAudioContext, { passive: true });
document.addEventListener('keydown', unlockAudioContext, { passive: true });
document.addEventListener('touchstart', unlockAudioContext, { passive: true });

function executeChimeSound() {
    try {
        if (!audioCtx) return;
        var now = audioCtx.currentTime;
        
        // Tone 1 (E5 note - crisp introductory ping)
        var osc1 = audioCtx.createOscillator();
        var gain1 = audioCtx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, now);
        gain1.gain.setValueAtTime(0, now);
        gain1.gain.linearRampToValueAtTime(0.3, now + 0.02);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc1.connect(gain1);
        gain1.connect(audioCtx.destination);
        osc1.start(now);
        osc1.stop(now + 0.36);

        // Tone 2 (A5 note - sweet harmonious chime)
        var osc2 = audioCtx.createOscillator();
        var gain2 = audioCtx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880.00, now + 0.10);
        gain2.gain.setValueAtTime(0, now + 0.10);
        gain2.gain.linearRampToValueAtTime(0.35, now + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.60);
        osc2.connect(gain2);
        gain2.connect(audioCtx.destination);
        osc2.start(now + 0.10);
        osc2.stop(now + 0.61);
    } catch (e) {
        console.warn('Audio chime error:', e);
    }
}

function playNotificationChime() {
    if (!isSoundEnabled) return;
    try {
        var AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;
        if (!audioCtx) {
            audioCtx = new AudioContextClass();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume().then(function() {
                executeChimeSound();
            }).catch(function() {});
        } else {
            executeChimeSound();
        }
    } catch (e) {
        console.warn('Audio chime notice:', e);
    }
}

function toggleSoundEffect() {
    isSoundEnabled = !isSoundEnabled;
    var icon = document.getElementById('rfMuteIcon');
    var btn = document.getElementById('rfMuteToggleBtn');
    if (isSoundEnabled) {
        icon.className = 'mdi mdi-volume-high';
        btn.title = 'Suara Notifikasi: Aktif';
        playNotificationChime();
    } else {
        icon.className = 'mdi mdi-volume-off text-warning';
        btn.title = 'Suara Notifikasi: Dibisukan';
    }
}

/* -------------------------------------------------------------------------- */
/* Initialization                                                             */
/* -------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('rfChatToggleBtn');
    var closeBtn = document.getElementById('rfCloseChatBtn');
    var searchInput = document.getElementById('rfSearchUserInput');

    if (toggleBtn) toggleBtn.addEventListener('click', toggleChatBox);
    if (closeBtn) closeBtn.addEventListener('click', toggleChatBox);

    // Filter contacts search
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            var val = e.target.value.toLowerCase().trim();
            renderFilteredContacts(val);
        });
    }

    // Initial Unread Count check
    checkInitialUnreadCount();

    // Start Delta Polling (2.5s when active)
    startChatPolling(2500);

    // Page visibility event to slow down or speed up polling
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            startChatPolling(5000); // 5s when tab is inactive (fast & responsive)
        } else {
            stopTitleBlink();
            startChatPolling(2500); // 2.5s when tab is active
            pollNewMessages(); // Instant fetch on tab focus
        }
    });
});

/* -------------------------------------------------------------------------- */
/* Toggle Chat Window                                                         */
/* -------------------------------------------------------------------------- */
function toggleChatBox() {
    var box = document.getElementById('rfChatBox');
    var iconInner = document.getElementById('rfChatBtnInner');
    isChatOpen = !isChatOpen;

    if (isChatOpen) {
        box.classList.add('active');
        if (iconInner) iconInner.innerHTML = svgCloseIcon;
        // Fetch fresh contact list if empty or on open
        loadContacts();
    } else {
        box.classList.remove('active');
        if (iconInner) iconInner.innerHTML = svgChatIcon;
    }
}

/* -------------------------------------------------------------------------- */
/* Load Contacts                                                              */
/* -------------------------------------------------------------------------- */
function loadContacts() {
    var container = document.getElementById('rfContactContainer');
    fetch('/chat/contacts', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            contactsData = data.contacts || [];
            updateTotalUnreadBadge(calculateTotalUnread(contactsData));
            applyContactFilters();
        }
    })
    .catch(function(err) {
        console.error('Failed to load contacts:', err);
    });
}

function calculateTotalUnread(list) {
    return list.reduce(function(acc, cur) { return acc + (cur.unread_count || 0); }, 0);
}

function renderContacts(list) {
    var container = document.getElementById('rfContactContainer');
    if (!container) return;

    if (!list || list.length === 0) {
        container.innerHTML = `
            <div class="rf-chat-empty-state">
                <i class="mdi mdi-account-off-outline" style="font-size: 28px;"></i>
                <div class="mt-2" style="font-size: 12px;">Tidak ada kontak ditemukan.</div>
            </div>
        `;
        return;
    }

    var html = '';
    list.forEach(function(u) {
        var avatarHtml = u.avatar_url 
            ? `<img src="${u.avatar_url}" alt="${escapeHtml(u.name)}">`
            : `<div class="rf-avatar-placeholder" style="background-color: ${u.avatar_color}22; color: ${u.avatar_color};">${u.avatar_text}</div>`;

        var presence = u.presence || { status: 'offline', badge_class: 'rf-status-offline', last_seen_text: 'Offline', label: 'Offline' };
        var presenceDotHtml = `<span class="rf-status-dot ${presence.badge_class}" id="rfContactPresence_${u.id}" title="${escapeHtml(presence.last_seen_text || presence.label)}"></span>`;

        var unreadBadgeHtml = (u.unread_count > 0)
            ? `<span class="badge bg-danger rounded-pill" style="font-size: 10px;">${u.unread_count}</span>`
            : '';

        var unreadClass = (u.unread_count > 0) ? 'fw-bold text-dark' : '';

        html += `
            <div class="rf-contact-item" id="rfContactItem_${u.id}" data-role="${escapeHtml(u.role)}" onclick="selectContact(${u.id})">
                <div class="rf-chat-avatar-wrap">
                    ${avatarHtml}
                    ${presenceDotHtml}
                </div>
                <div class="rf-contact-info">
                    <div class="rf-contact-name-row">
                        <span class="rf-contact-name">${escapeHtml(u.name)}</span>
                        <span class="rf-contact-time" id="rfContactTime_${u.id}">${u.last_message_time || ''}</span>
                    </div>
                    <div class="rf-contact-msg-row">
                        <span class="rf-contact-last-msg ${unreadClass}" id="rfContactMsg_${u.id}">${escapeHtml(u.last_message)}</span>
                        <span id="rfContactBadgeWrap_${u.id}">${unreadBadgeHtml}</span>
                    </div>
                    <div class="mt-1">
                        <span class="rf-role-tag ${u.role_class}">${escapeHtml(u.role)}</span>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

var currentFilterStatus = 'all';

function filterChatPresence(status, btn) {
    currentFilterStatus = status;

    if (btn) {
        var buttons = btn.parentElement.querySelectorAll('button');
        buttons.forEach(function(b) { 
            b.className = 'btn btn-xs btn-label-secondary px-2 py-1'; 
            b.style.borderRadius = '12px'; 
        });
        btn.className = 'btn btn-xs btn-primary px-2 py-1';
    }

    applyContactFilters();
}

function applyContactFilters() {
    var searchInput = document.getElementById('rfSearchUserInput');
    var kw = (searchInput ? searchInput.value : '').toLowerCase().trim();

    var filtered = contactsData.filter(function(c) {
        // Status presence filter
        var presenceStatus = (c.presence && c.presence.status) ? c.presence.status : 'offline';
        var matchStatus = (currentFilterStatus === 'all') || (presenceStatus === currentFilterStatus);

        // Search filter by name, role, or id
        var name = (c.name || '').toLowerCase();
        var role = (c.role || '').toLowerCase();
        var id = String(c.id || '');
        var matchSearch = !kw || name.indexOf(kw) > -1 || role.indexOf(kw) > -1 || id.indexOf(kw) > -1;

        return matchStatus && matchSearch;
    });

    renderContacts(filtered);
}

function renderFilteredContacts(keyword) {
    applyContactFilters();
}

/* -------------------------------------------------------------------------- */
/* Chat Room Presence Status Updater                                          */
/* -------------------------------------------------------------------------- */
function updateRoomPresenceHeader(presence) {
    if (!presence) return;
    var statusEl = document.getElementById('rfRoomStatusText');
    var dotColor = (presence.status === 'online') ? '#71dd37' : ((presence.status === 'away') ? '#ffab00' : '#adb5bd');
    if (statusEl) {
        statusEl.innerHTML = `
            <span class="rf-room-presence-badge">
                <span class="rf-room-presence-dot" style="background-color: ${dotColor};"></span>
                <span>${escapeHtml(presence.last_seen_text || presence.label)}</span>
            </span>
        `;
    }

    var avatarContainer = document.getElementById('rfRoomAvatarWrap');
    if (avatarContainer) {
        var existingDot = avatarContainer.querySelector('.rf-status-dot');
        if (existingDot) existingDot.remove();
        avatarContainer.insertAdjacentHTML('beforeend', `<span class="rf-status-dot ${presence.badge_class}" title="${escapeHtml(presence.last_seen_text || presence.label)}"></span>`);
    }
}

/* -------------------------------------------------------------------------- */
/* Chat Room Management                                                       */
/* -------------------------------------------------------------------------- */
function selectContact(userId) {
    var user = contactsData.find(function(c) { return c.id === userId; });
    if (!user) return;

    activeChatUser = user;

    // Switch View
    document.getElementById('rfChatListView').style.display = 'none';
    document.getElementById('rfChatRoomView').style.display = 'flex';

    // Populate Header
    document.getElementById('rfRoomUserName').innerText = user.name;
    document.getElementById('rfRoomRoleBadge').innerText = user.role;
    document.getElementById('rfRoomRoleBadge').className = 'rf-role-tag ' + user.role_class;

    var avatarContainer = document.getElementById('rfRoomAvatarWrap');
    if (user.avatar_url) {
        avatarContainer.innerHTML = `<img src="${user.avatar_url}" alt="${escapeHtml(user.name)}" class="rounded-circle w-100 h-100">`;
    } else {
        avatarContainer.innerHTML = `<div class="rf-avatar-placeholder" style="background-color: ${user.avatar_color}22; color: ${user.avatar_color};">${user.avatar_text}</div>`;
    }

    // Set Initial Room Presence
    updateRoomPresenceHeader(user.presence);

    // Reset Badge in Contact Item
    user.unread_count = 0;
    var badgeWrap = document.getElementById('rfContactBadgeWrap_' + user.id);
    if (badgeWrap) badgeWrap.innerHTML = '';
    var msgText = document.getElementById('rfContactMsg_' + user.id);
    if (msgText) msgText.className = 'rf-contact-last-msg';
    updateTotalUnreadBadge(calculateTotalUnread(contactsData));

    // Load Messages
    loadConversationMessages(user.id);
}

function backToChatList() {
    activeChatUser = null;
    document.getElementById('rfChatRoomView').style.display = 'none';
    document.getElementById('rfChatListView').style.display = 'flex';
    clearSelectedAttachment();
    loadContacts();
}

function loadConversationMessages(userId) {
    var list = document.getElementById('rfMessagesList');

    // 1. INSTANT RENDER FROM CACHE jika sudah pernah dimuat
    if (conversationCache[userId]) {
        renderMessageStream(conversationCache[userId]);
    } else {
        // Tampilkan loading cepat jika pertama kali dimuat
        list.innerHTML = `
            <div class="rf-chat-empty-state">
                <i class="mdi mdi-loading mdi-spin" style="font-size: 24px;"></i>
                <div class="mt-2" style="font-size: 12px;">Memuat obrolan...</div>
            </div>
        `;
    }

    // Cancel pending request if user clicks another contact quickly
    if (activeFetchController) {
        activeFetchController.abort();
    }
    activeFetchController = new AbortController();

    fetch('/chat/messages/' + userId, {
        signal: activeFetchController.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            if (data.target_user && data.target_user.presence) {
                if (activeChatUser && activeChatUser.id === userId) {
                    activeChatUser.presence = data.target_user.presence;
                    updateRoomPresenceHeader(data.target_user.presence);
                }
            }
            var msgs = data.messages || [];
            conversationCache[userId] = msgs;

            // Render fresh messages if current room is still active
            if (activeChatUser && activeChatUser.id === userId) {
                renderMessageStream(msgs);
            }
        }
    })
    .catch(function(err) {
        if (err.name !== 'AbortError') {
            console.error('Failed to fetch messages:', err);
        }
    });
}

function renderMessageStream(messages) {
    var list = document.getElementById('rfMessagesList');
    if (!list) return;

    if (!messages || messages.length === 0) {
        list.innerHTML = `
            <div class="rf-chat-empty-state">
                <i class="mdi mdi-chat-processing-outline" style="font-size: 32px;"></i>
                <div class="mt-2" style="font-size: 12.5px;">Mulai percakapan dengan ${escapeHtml(activeChatUser ? activeChatUser.name : 'rekan kerja')}.</div>
            </div>
        `;
        return;
    }

    var html = '';
    var lastDate = null;

    messages.forEach(function(m) {
        if (m.id > lastMessageId) {
            lastMessageId = m.id;
        }

        if (m.date_label && m.date_label !== lastDate) {
            html += `<div class="rf-chat-date-divider"><span>${m.date_label}</span></div>`;
            lastDate = m.date_label;
        }

        html += generateMessageItemHtml(m);
    });

    list.innerHTML = html;
    scrollToBottomChat();
}

function generateMessageItemHtml(m) {
    var isOut = m.is_outgoing;
    var rowClass = isOut ? 'rf-msg-outgoing' : 'rf-msg-incoming';

    var avatarHtml = '';
    if (!isOut && activeChatUser) {
        var av = activeChatUser.avatar_url 
            ? `<img src="${activeChatUser.avatar_url}" alt="${escapeHtml(activeChatUser.name)}" style="width: 28px; height: 28px; border-radius: 50%;">`
            : `<div class="rf-avatar-placeholder" style="background-color: ${activeChatUser.avatar_color}22; color: ${activeChatUser.avatar_color}; font-size: 11px;">${activeChatUser.avatar_text}</div>`;
        avatarHtml = `<div class="rf-chat-avatar-wrap" style="width: 28px; height: 28px;">${av}</div>`;
    }

    var attachmentHtml = '';
    if (m.attachment_url) {
        if (m.attachment_type === 'image') {
            attachmentHtml = `
                <a href="${m.attachment_url}" target="_blank" title="Buka Gambar">
                    <img src="${m.attachment_url}" alt="Foto Lampiran" class="rf-msg-img-preview">
                </a>
            `;
        } else {
            attachmentHtml = `
                <a href="${m.attachment_url}" target="_blank" class="rf-msg-attachment-card" download>
                    <i class="mdi mdi-file-document-outline text-primary" style="font-size: 22px;"></i>
                    <div class="text-truncate" style="flex: 1; min-width: 0;">
                        <div class="fw-semibold text-truncate" style="font-size: 12px;">${escapeHtml(m.attachment_name || 'Dokumen')}</div>
                        <small style="font-size: 10px; opacity: 0.8;">Klik untuk unduh</small>
                    </div>
                    <i class="mdi mdi-download" style="font-size: 18px;"></i>
                </a>
            `;
        }
    }

    var textHtml = m.message ? `<div>${escapeHtml(m.message).replace(/\n/g, '<br>')}</div>` : '';
    var checkmarkHtml = isOut 
        ? `<i class="mdi ${m.is_read ? 'mdi-check-all text-primary' : 'mdi-check'}" id="rfMsgCheck_${m.id}" style="font-size: 14px; margin-left: 2px;"></i>` 
        : '';

    return `
        <div class="rf-msg-row ${rowClass}" id="rfMsgRow_${m.id}">
            ${avatarHtml}
            <div>
                <div class="rf-msg-bubble">
                    ${textHtml}
                    ${attachmentHtml}
                    <div class="rf-msg-meta">
                        <span>${m.time || ''}</span>
                        ${checkmarkHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
}

/* -------------------------------------------------------------------------- */
/* Send Message                                                               */
/* -------------------------------------------------------------------------- */
function handleChatKeyPress(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendChatMessage();
    }
}

function handleFileSelected(e) {
    var file = e.target.files[0];
    if (!file) return;

    selectedAttachmentFile = file;
    document.getElementById('rfAttachmentFileName').innerText = file.name;
    document.getElementById('rfAttachmentPreviewBar').style.display = 'flex';
}

function clearSelectedAttachment() {
    selectedAttachmentFile = null;
    var fileInput = document.getElementById('rfChatFileInput');
    if (fileInput) fileInput.value = '';
    var preview = document.getElementById('rfAttachmentPreviewBar');
    if (preview) preview.style.display = 'none';
}

function sendChatMessage() {
    if (!activeChatUser) return;

    var input = document.getElementById('rfChatInputField');
    var text = input.value.trim();
    var file = selectedAttachmentFile;

    if (!text && !file) return;

    var csrfToken = document.querySelector('meta[name="csrf-token"]') 
        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        : '';

    var formData = new FormData();
    formData.append('receiver_id', activeChatUser.id);
    if (text) formData.append('message', text);
    if (file) formData.append('attachment', file);

    // Reset input immediately
    input.value = '';
    clearSelectedAttachment();

    var sendBtn = document.getElementById('rfSendMsgBtn');
    var sendIcon = document.getElementById('rfSendIcon');
    if (sendIcon) sendIcon.className = 'mdi mdi-loading mdi-spin';

    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (sendIcon) sendIcon.className = 'mdi mdi-send';
        if (data.status === 'success' && data.message_data) {
            var msg = data.message_data;
            if (msg.id > lastMessageId) lastMessageId = msg.id;

            // Sync with memory cache
            if (!conversationCache[activeChatUser.id]) conversationCache[activeChatUser.id] = [];
            conversationCache[activeChatUser.id].push(msg);

            var list = document.getElementById('rfMessagesList');
            // Remove empty state if present
            if (list.querySelector('.rf-chat-empty-state')) {
                list.innerHTML = '';
            }
            list.insertAdjacentHTML('beforeend', generateMessageItemHtml(msg));
            scrollToBottomChat();

            // Update contact last message in contacts list
            var contactMsg = document.getElementById('rfContactMsg_' + activeChatUser.id);
            if (contactMsg) contactMsg.innerText = 'Anda: ' + (msg.message || (msg.attachment_type === 'image' ? '🖼️ Gambar' : '📎 Dokumen'));
            var contactTime = document.getElementById('rfContactTime_' + activeChatUser.id);
            if (contactTime) contactTime.innerText = msg.time;
        }
    })
    .catch(function(err) {
        if (sendIcon) sendIcon.className = 'mdi mdi-send';
        console.error('Failed to send message:', err);
    });
}

/* -------------------------------------------------------------------------- */
/* Delta Polling Engine                                                       */
/* -------------------------------------------------------------------------- */
function startChatPolling(intervalMs) {
    if (pollIntervalTimer) clearInterval(pollIntervalTimer);
    pollIntervalTimer = setInterval(pollNewMessages, intervalMs);
}

function pollNewMessages() {
    if (isPollingBusy) return;
    isPollingBusy = true;

    var activeUserId = (isChatOpen && activeChatUser) ? activeChatUser.id : '';
    var url = `/chat/poll?last_id=${lastMessageId}&active_user_id=${activeUserId}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        isPollingBusy = false;
        if (data.status === 'success') {
            if (data.last_id && data.last_id > lastMessageId) {
                lastMessageId = data.last_id;
            }
            updateTotalUnreadBadge(data.total_unread || 0);

            var newMsgs = data.new_messages || [];
            if (newMsgs.length > 0) {
                var hasSoundPlayed = false;

                newMsgs.forEach(function(m) {
                    if (m.id > lastMessageId) lastMessageId = m.id;

                    // Sync into memory cache
                    var cacheUserId = m.is_outgoing ? m.receiver_id : m.sender_id;
                    if (conversationCache[cacheUserId]) {
                        if (!conversationCache[cacheUserId].some(function(item) { return item.id === m.id; })) {
                            conversationCache[cacheUserId].push(m);
                        }
                    }

                    // If message is for currently open conversation
                    if (isChatOpen && activeChatUser && m.sender_id === activeChatUser.id) {
                        var list = document.getElementById('rfMessagesList');
                        if (list.querySelector('.rf-chat-empty-state')) {
                            list.innerHTML = '';
                        }
                        list.insertAdjacentHTML('beforeend', generateMessageItemHtml(m));
                        scrollToBottomChat();
                    } else {
                        // Incoming message from another contact
                        updateContactSnippet(m);
                    }

                    // Play chime & trigger tab title alert HANYA jika halaman sudah selesai inisialisasi awal
                    if (isInitialPollComplete && !m.is_outgoing && !hasSoundPlayed) {
                        playNotificationChime();
                        triggerTitleBlink('💬 Pesan Baru!');
                        hasSoundPlayed = true;
                    }
                });
            }

            isInitialPollComplete = true;

            // Update read checkmarks for outgoing messages
            if (data.read_message_ids && data.read_message_ids.length > 0) {
                data.read_message_ids.forEach(function(id) {
                    var chk = document.getElementById('rfMsgCheck_' + id);
                    if (chk) chk.className = 'mdi mdi-check-all text-primary';
                });
            }

            // Update live room presence
            if (data.target_user_presence && isChatOpen && activeChatUser) {
                activeChatUser.presence = data.target_user_presence;
                updateRoomPresenceHeader(data.target_user_presence);
                var dotEl = document.getElementById('rfContactPresence_' + activeChatUser.id);
                if (dotEl) {
                    dotEl.className = 'rf-status-dot ' + data.target_user_presence.badge_class;
                    dotEl.title = data.target_user_presence.last_seen_text || data.target_user_presence.label;
                }
            }
        }
    })
    .catch(function(err) {
        isPollingBusy = false;
    });
}

function updateContactSnippet(msg) {
    var cId = msg.sender_id;
    var contact = contactsData.find(function(c) { return c.id === cId; });
    if (contact) {
        contact.unread_count = (contact.unread_count || 0) + 1;
        contact.last_message = msg.message || (msg.attachment_type === 'image' ? '🖼️ Gambar' : '📎 Dokumen');
        contact.last_message_time = msg.time;

        var msgEl = document.getElementById('rfContactMsg_' + cId);
        if (msgEl) {
            msgEl.innerText = contact.last_message;
            msgEl.className = 'rf-contact-last-msg fw-bold text-dark';
        }
        var timeEl = document.getElementById('rfContactTime_' + cId);
        if (timeEl) timeEl.innerText = contact.last_message_time;

        var badgeWrap = document.getElementById('rfContactBadgeWrap_' + cId);
        if (badgeWrap) {
            badgeWrap.innerHTML = `<span class="badge bg-danger rounded-pill" style="font-size: 10px;">${contact.unread_count}</span>`;
        }
    }
}

function checkInitialUnreadCount() {
    fetch('/chat/unread-count', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            updateTotalUnreadBadge(data.unread_count || 0);
            if (data.last_id && data.last_id > lastMessageId) {
                lastMessageId = data.last_id;
            }
        }
    })
    .catch(function() {});
}

function updateTotalUnreadBadge(count) {
    var badge = document.getElementById('rfUnreadBadge');
    if (!badge) return;

    if (count > 0) {
        badge.innerText = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

function scrollToBottomChat() {
    var list = document.getElementById('rfMessagesList');
    if (list) {
        setTimeout(function() {
            list.scrollTop = list.scrollHeight;
        }, 60);
    }
}

function escapeHtml(string) {
    if (!string) return '';
    return String(string).replace(/[&<>"'`=\/]/g, function (s) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        }[s];
    });
}
</script>
