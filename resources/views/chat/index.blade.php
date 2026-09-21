@extends('layouts.master')

@section('title')
<title> Chat System </title>
@endsection

@section('content')
{{-- ✅ 1. Pure Flexbox Wrapper with mx-n4 mt-n4 to break out of Velzon page-content padding --}}
<div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n5 mb-n3 p-0" style="height: calc(100vh - 145px);">
    {{-- ===== LEFT SIDEBAR (Users List) ===== --}}
    <div class="chat-leftsidebar minimal-border ms-3" style="width: 320px; border-right: 1px solid #eff2f7; background: #fff;">
        <div class="px-4 pt-4 mb-3 ">
            <h5 class="mb-4">Chats</h5>
            {{-- ✅ 6. Search box added as requested --}}
            <div class="search-box">
                <input type="text" id="user-search" class="form-control bg-light border-light" placeholder="Search users...">
                <i class="ri-search-2-line search-icon"></i>
            </div>
        </div>

        <div class="chat-room-list pt-3" style="flex-grow: 1; overflow-y: auto;">
            <div class="px-4 mb-2">
                <h4 class="mb-0 fs-11 text-muted text-uppercase">Direct Messages</h4>
            </div>
            <ul class="list-unstyled chat-list chat-user-list" id="userList">
                @foreach($users as $user)
                <li class="user-item" 
                    data-user-id="{{ $user->id }}"
                    data-user-name="{{ $user->name}}">
                    <a href="javascript:void(0)" class="d-flex align-items-center p-3">
                        <div class="flex-shrink-0 me-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff"
                            alt="{{ $user->name }}" 
                            class="rounded-circle"
                            width="40" height="40">
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-truncate mb-0 fw-medium">{{ $user->name }}</p>
                            <small class="text-muted" style="font-size: 11px;">{{ $user->email}}</small>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ===== RIGHT SIDE: CHAT WINDOW ===== --}}
    <div class="user-chat w-100 overflow-hidden minimal-border d-flex flex-column" style="background: #fff;">
        
        {{-- Empty state (Visible by default) --}}
        <div id="no-chat-selected" class="d-flex align-items-center justify-content-center h-100">
            <div class="text-center text-muted p-5">
                <i class="ri-chat-3-line fs-1 d-block mb-3"></i>
                <p class="mt-3">Select a user to Start Chatting</p>
            </div>
        </div>

        {{-- ✅ 4. Chat Content Area (Hidden initially with d-none, toggled to d-flex) --}}
        <div id="chat-content-area" class="d-none flex-column h-100">
            
            {{-- Chat Header --}}
            <div class="p-3 user-chat-topbar border-bottom" style="background: #fff;"> 
                <h5 class="card-title mb-0 text-dark" id="chat-with-user">Select a user to start chatting</h5>
            </div>
            
            {{-- Chat Messages (Scrollable) --}}
            <div class="chat-conversation p-3 p-lg-4 flex-grow-1 overflow-auto" id="chat-messages" style="background: #f8f9fa;"> 
                <!-- Messages will be appended here -->
            </div>

            {{-- ✅ NEW FILE SHARING ADDITION: File Preview Area --}}
            <div id="file-preview-area" class="px-4 py-2 border-top bg-light d-none">
                <div class="d-flex flex-wrap gap-2" id="file-preview-container">
                    <!-- JavaScript yahan dynamically file previews inject karega -->
                </div>
                <button id="clear-files" class="btn btn-sm btn-link text-danger p-0 mt-2" type="button">
                    <i class="ri-close-line"></i> Clear all files
                </button>
            </div>
            
            {{-- Chat Footer / Input --}}
            <div class="chat-input-section p-3 border-top bg-white"> 
                <form id="message-form" class="d-flex align-items-center gap-2">
                    <input type="hidden" id="receiver-id" value=""> 
                    
                    {{-- ✅ NEW FILE SHARING ADDITION: Attachment Button & Hidden Input --}}
                    <label for="file-upload" class="btn btn-ghost-secondary btn-icon p-2" title="Attach file" style="cursor: pointer;">
                        <i class="ri-attachment-2 fs-20"></i>
                    </label>
                    <input type="file" id="file-upload" class="d-none" multiple 
                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.7z">
                    
                    <input type="text"
                        id="message-input" 
                        class="form-control bg-light border-light" 
                        placeholder="Type your message ..."
                        autocomplete="off">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-send-plane-2-line"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ✅ NEW FILE SHARING ADDITION: Video Preview Modal --}}
<div class="modal fade" id="videoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="videoModalTitle">Video Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center">
                <video id="modalVideoPlayer" class="w-100" style="max-height: 80vh; border-radius: 8px; background: #000;" controls preload="metadata">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-css')
<style>
/* ✅ 1 & 2. Professional Chat Layout Fix (Flexbox) */
.chat-wrapper {
    display: flex !important;
    flex-direction: row !important;
    background: #fdfdfd;
    border: 1px solid #f5f5f5;
    border-radius: 4px;
}

.chat-leftsidebar {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    overflow: hidden !important;
    width: 320px !important;
    flex-shrink: 0 !important;
}

.chat-room-list {
    flex-grow: 1 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

.user-chat {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    overflow: hidden !important;
    flex-grow: 1 !important;
}

.user-chat-topbar, .chat-input-section, #file-preview-area {
    flex-shrink: 0 !important; 
}

.chat-conversation {
    flex-grow: 1 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

/* Custom Scrollbar */
.chat-room-list::-webkit-scrollbar, .chat-conversation::-webkit-scrollbar { width: 6px; }
.chat-room-list::-webkit-scrollbar-thumb, .chat-conversation::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }

/* Existing Styles (Preserved) */
.user-item {
    transition: all 0.3s;
}
.user-item:hover {
    background-color: #f8f9fa;
}
.user-item.active {
    background-color: #e7f3ff;
    border-left: 3px solid #0d6efd;
}

/* ✅ 3. Bulletproof renderMessage styling (left/right bubbles) */
.chat-list.right {
    text-align: right !important;
}
.chat-list.right .conversation-list {
    display: inline-flex !important;
    flex-direction: column !important;
    align-items: flex-end !important;
    width:100%
}
.chat-list.left .conversation-list {
    display: inline-flex !important;
    width:100%
}
.chat-list .user-chat-content {
    max-width: 75% !important;
    text-align: left !important; /* Bubble ke andar text hamesha left-aligned rahe */
}
#chat-messages li {
    list-style: none !important;
}

/* ✅ NEW FILE SHARING ADDITION: Attachment Styles */
.file-preview-item {
    width: 60px; height: 60px;
    position: relative;
}
.file-preview-item img {
    width: 100%; height: 100%; object-fit: cover;
}
</style>
@endsection

@section('script-bottom')
//Pusher CDN
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    $(document).ready(function(){
        let currentUserId = {{ auth()->id()}};
        let selectedUserId = null;
        let pusher = null;
        let channel = null;
        
        // ✅ NEW FILE SHARING ADDITION: State variables
        let selectedFiles = [];
        const fileInput = document.getElementById('file-upload');
        const previewArea = document.getElementById('file-preview-area');
        const previewContainer = document.getElementById('file-preview-container');
        const clearFilesBtn = document.getElementById('clear-files');

        // ✅ XSS Protection Helper Function
        function escapeHtml(text) {
            if (!text) return text;
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Pusher Initialization (Reverb ke liye Best & Direct Approach)
        pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', // App key seedha Reverb se
        { 
            wsHost: '{{ env("REVERB_HOST", "127.0.0.1") }}', // WebSocket server address // webscokket(http real time version)
            wsPort: '{{ env("REVERB_PORT", 8081) }}',        // Port number jahan Reverb chal raha hai (8081) //server address 12.0.0.1
            wssPort: '{{ env("REVERB_PORT", 8081) }}',       // Secure port (local mein same rehta hai) //port number jaha reverb chal ra hy 
            forceTLS: '{{ env("REVERB_SCHEME") }}' === 'https', // Dynamic HTTPS check (local mein false hoga)
            enabledTransports: ['ws', 'wss'],                // Allowed protocols
            cluster: 'mt1',                                  // Reverb ke liye dummy cluster kaafi hai // Pusher.com k liay :server ki location, but reverb k liay mandatory so mt1 US east
            // Authentication endpoint for private channels ... Pusher.subscribe likhnay pr yeh hit hoga
            // ✅ 5. Fixed Auth Endpoint to point to current HTTP server, not Reverb port
            authEndpoint: window.location.origin + '/broadcasting/auth', 
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'     // Laravel security token
                }
            }
        });

        // ✅ 3. Adopted renderMessage function for professional left/right bubbles
        function renderMessage(msg) {
            let isOwnMessage = msg.sender_id == currentUserId;
            
            // ✅ CRITICAL FIX: Dono sources (AJAX aur Pusher) se naam safely nikalna
            let rawSenderName = msg.sender_name || (msg.sender ? msg.sender.name : 'Unknown');
            
            let senderName = isOwnMessage ? 'You' : rawSenderName;
            let bgColor = isOwnMessage ? 'bg-primary text-white' : 'bg-light text-dark';
        
            return `
                <li class="chat-list ${isOwnMessage ? 'right' : 'left'} mb-3" data-id="${msg.id}">
                    <div class="conversation-list">
                        ${!isOwnMessage ? `<div class="chat-avatar me-2 align-self-start"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(rawSenderName)}&background=random&color=fff" class="rounded-circle avatar-xs" alt=""></div>` : ''}
                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content px-3 py-2 ${bgColor}" style="border-radius:12px; max-width:420px; word-break:break-word;">
                                    ${!isOwnMessage ? `<small class="fw-bold d-block mb-1">${escapeHtml(rawSenderName)}</small>` : ''}
                                    <p class="mb-0 ctext-content">${escapeHtml(msg.message || '')}</p>
                                    
                                    {{-- ✅ NEW FILE SHARING ADDITION: Attachment Rendering Logic --}}
                                    ${msg.attachments && msg.attachments.length > 0 ? `
                                    <div class="mt-2 d-flex flex-column gap-2">
                                        ${msg.attachments.map(att => {
                                            const sizeMB = (att.file_size / (1024 * 1024)).toFixed(2);
                                            if (att.file_category === 'image') {
                                                return `<div class="position-relative" style="max-width: 250px;">
                                                    <img src="/storage/${att.file_path}" class="img-fluid rounded border" style="cursor: pointer;" onclick="window.open('/storage/${att.file_path}', '_blank')">
                                                    <a href="/chat/download/${att.id}" class="btn btn-sm btn-light position-absolute bottom-0 end-0 m-1" title="Download" download><i class="ri-download-line"></i></a>
                                                </div>`;
                                            } else if (att.file_category === 'video') {
                                                return `<div class="position-relative" style="max-width: 250px;">
                                                    <div class="video-preview-trigger bg-dark rounded d-flex align-items-center justify-content-center" style="height: 150px; cursor: pointer;" data-video-url="/storage/${att.file_path}" data-video-name="${escapeHtml(att.file_name)}">
                                                        <i class="ri-play-circle-line text-white" style="font-size: 48px; opacity: 0.8;"></i>
                                                        <small class="text-white position-absolute bottom-0 end-0 m-2 bg-black bg-opacity-50 px-2 py-1 rounded">${sizeMB} MB</small>
                                                    </div>
                                                    <a href="/chat/download/${att.id}" class="btn btn-sm btn-light position-absolute bottom-0 end-0 m-1" title="Download" download style="z-index: 2;"><i class="ri-download-line"></i></a>
                                                </div>`;
                                            } else {
                                                return `<a href="/chat/download/${att.id}" class="d-flex align-items-center gap-2 p-2 rounded border bg-light text-decoration-none text-dark" style="max-width: 250px;" download>
                                                    <i class="ri-file-text-line text-primary fs-4"></i>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="text-truncate fw-medium" style="font-size: 13px;">${escapeHtml(att.file_name)}</div>
                                                        <small class="text-muted" style="font-size: 11px;">${sizeMB} MB</small>
                                                    </div>
                                                    <i class="ri-download-cloud-line fs-5 text-muted"></i>
                                                </a>`;
                                            }
                                        }).join('')}
                                    </div>
                                    ` : ''}
                                    
                                    <div class="d-flex align-items-center justify-content-end gap-1 mt-1">
                                        <small class="opacity-75" style="font-size:10px;">${new Date(msg.created_at).toLocaleTimeString()}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            `;
        }

        //User Selection
        $('.user-item').on('click', function(){
            selectedUserId = $(this).data('user-id');
            let selectedUserName = $(this).data('user-name');

            //updating header:
            $('#chat-with-user').text('Chat With '+ selectedUserName);
            $('#receiver-id').val(selectedUserId);
            
            // ✅ 4. Toggling d-none and d-flex classes instead of .hide()/.show()
            $('#no-chat-selected').addClass('d-none');
            $('#chat-content-area').removeClass('d-none').addClass('d-flex');

            //Load Previous messages
            loadMessages(selectedUserId);

            //subscribe to private Channel
            subscribeToChannel(selectedUserId);

            //Highlights Selected User
            $('.user-item').removeClass('active');
            $(this).addClass('active');
            
            // Focus input
            $('#message-input').focus();
        })

        // 3. LOAD PREVIOUS MESSAGES (AJAX)
        function loadMessages(userId)
        {
            $.ajax({
                url: '/chat/' + userId + '/messages',
                method: 'GET',
                success: function(messages)
                {
                    $('#chat-messages').empty();
                    if(messages.length === 0)
                    {
                        $('#chat-messages').html('<li class="text-center text-muted py-4"><small> Start the conversation!</small></li>');
                    } else {
                        messages.forEach(function(msg){
                            // Using the adopted renderMessage function
                            $('#chat-messages').append(renderMessage(msg));
                        })
                    }
                    scrollToBottom();
                },
                error: function()
                {
                    Swal.fire("Error!", "Failed to load messages", "error");
                }
            });
        }

        // 4. SEND MESSAGE (AJAX POST) - ✅ UPDATED TO HANDLE FILES
        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            let messageText = $('#message-input').val().trim();
            
            if (messageText === '' && selectedFiles.length === 0) {
                return;
            }

            if (selectedFiles.length > 0) {
                // ✅ File Upload Logic
                let formData = new FormData();
                formData.append('receiver_id', selectedUserId);
                if (messageText) formData.append('message', messageText);
                
                selectedFiles.forEach((file, index) => {
                    formData.append(`files[${index}]`, file);
                });

                $.ajax({
                    url: '/chat/send-file',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response){
                        $('#message-input').val('');
                        selectedFiles = [];
                        previewContainer.innerHTML = '';
                        previewArea.classList.add('d-none');
                        // Real-time event will handle appending
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", xhr.responseJSON?.message || "Failed to send file", "error");
                    }
                });
            } else {
                // ✅ Existing Text-Only Logic (Preserved Exactly)
                $.ajax({
                    url: '/chat',
                    method: 'POST',
                    data: {
                        message: messageText,
                        receiver_id : selectedUserId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response){
                        $('#message-input').val('');
                        // Message already appended by real-time event
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", xhr.responseJSON?.message || "Failed to send message", "error");
                    }
                });
            }
        });

        //subscribeTo Private Channel
        // ✅ CRITICAL: Channel, subscribe, and bind code kept EXACTLY as you requested
        function subscribeToChannel(userId)
        {
            // Unsubscribe from previous channel if exists
            if (channel) 
            {
                pusher.unsubscribe(channel.name);
            }
            // Create channel name: chat.{min(id)}.{max(id)}
            let ids = [currentUserId, userId].sort((a, b) => a - b);
            let channelName = 'private-chat.' + ids[0] + '.' + ids[1];

            //Subscribe to Private channel 
            channel = pusher.subscribe(channelName);
            
            // (The Listener for Text)
            channel.bind('message.sent', function(data){
                console.log('Message Received:', data);
                $('#chat-messages').append(renderMessage(data));
                scrollToBottom();
            });

            // ✅ NEW FILE SHARING ADDITION: (The Listener for Files)
            channel.bind('file.sent', function(data){
                console.log('File Received:', data);
                $('#chat-messages').append(renderMessage(data));
                scrollToBottom();
            });
        }
         
        function scrollToBottom() {
            let chatBox = document.getElementById('chat-messages');
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // ✅ 6. Search Users Logic
        $('#user-search').on('input', function() {
            let query = $(this).val().toLowerCase();
            $('.user-item').each(function() {
                let name = $(this).data('user-name').toLowerCase();
                if (name.includes(query)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // ✅ NEW FILE SHARING ADDITION: File Input & Preview Logic
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (selectedFiles.length + files.length > 5) {
                alert('You can only select 5 files at a single time.');
                fileInput.value = '';
                return;
            }

            files.forEach(file => {
                selectedFiles.push(file);
                let previewHtml = '';
                
                if (file.type.startsWith('image/')) {
                    const imgUrl = URL.createObjectURL(file);
                    previewHtml = `<div class="file-preview-item"><img src="${imgUrl}" class="rounded"></div>`;
                } else {
                    const iconClass = file.type.startsWith('video/') ? 'ri-movie-line text-danger' : 'ri-file-text-line text-primary';
                    previewHtml = `<div class="d-flex align-items-center justify-content-center bg-light rounded border file-preview-item"><i class="${iconClass} fs-3"></i></div>`;
                }

                const div = document.createElement('div');
                div.className = 'position-relative';
                div.innerHTML = previewHtml;
                previewContainer.appendChild(div);
            });

            previewArea.classList.remove('d-none');
            fileInput.value = '';
        });

        clearFilesBtn.addEventListener('click', function() {
            selectedFiles = [];
            previewContainer.innerHTML = '';
            previewArea.classList.add('d-none');
        });

        // ✅ NEW FILE SHARING ADDITION: Video Modal Logic
        const videoModalEl = document.getElementById('videoPreviewModal');
        const videoModal = new bootstrap.Modal(videoModalEl);
        const modalVideoPlayer = document.getElementById('modalVideoPlayer');
        const videoModalTitle = document.getElementById('videoModalTitle');

        document.getElementById('chat-messages').addEventListener('click', function(e) {
            const videoTrigger = e.target.closest('.video-preview-trigger');
            if (videoTrigger) {
                videoModalTitle.textContent = videoTrigger.dataset.videoName;
                modalVideoPlayer.src = videoTrigger.dataset.videoUrl;
                videoModal.show();
            }
        });

        videoModalEl.addEventListener('hidden.bs.modal', function () {
            modalVideoPlayer.pause();
            modalVideoPlayer.src = "";
            modalVideoPlayer.load();
        });

    });
</script>
@endsection