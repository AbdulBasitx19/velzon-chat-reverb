@extends('layouts.master')

@section('title')
<title> Chat System </title>
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- ================= LEFT SIDE: USERS SIDEBAR ================= -->
        <div class="col-md-4 col-lg-3">
            <!-- ✅ chat-main-card class add ki hai fixed height ke liye -->
            <div class="card chat-main-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">Users</h5>
                </div>
                
                <!-- ✅ user-list-container class add ki hai taake sirf yeh hissa scroll ho -->
                <div class="card-body p-0 user-list-container">
                    <div class="list-group list-group-flush">  
                        @foreach($users as $user)
                            <a href="javascript:void(0)"
                                class="list-group-item list-group-item-action user-item"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name}}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff"
                                        alt="{{ $user->name }}" 
                                        class="rounded-circle"
                                        width="40" height="40">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email}}</small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT SIDE: CHAT WINDOW ================= -->
        <div class="col-md-8 col-lg-9"> 
            <!-- ✅ chat-main-card class add ki hai taake left aur right barabar hon -->
            <div class="card chat-main-card">
                
                <!-- 1. Chat Header (Fixed) -->
                <div class="card-header bg-primary text-white"> 
                    <h5 class="card-title mb-0 text-white" id="chat-with-user">Select a user to start chatting</h5>
                </div>
                
                <!-- 2. Chat Messages (Scrollable) -->
                <!-- ✅ chat-messages-container class add ki hai -->
                <div class="card-body p-0 chat-messages-container" id="chat-messages"> 
                    <div class="text-center text-muted d-flex align-items-center justify-content-center h-100" id="no-chat-selected">
                        <div>
                            <i class="ri-chat-3-line" style="font-size: 3rem"></i>
                            <p class="mt-3">Select a user to Start Chatting</p>
                        </div>
                    </div>
                </div>
                
                <!-- 3. Chat Footer / Input (Fixed) -->
                <div class="card-footer bg-white"> 
                    <form id="message-form" style="display: none">
                        <input type="hidden" id="receiver-id" value=""> 
                        <div class="input-group">
                            <!-- ✅ 'form-controll' ki spelling 'form-control' theek kar di hai -->
                            <input type="text"
                                id="message-input" 
                                class="form-control" 
                                placeholder="Type your message ..."
                                autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-send-plane-2-line"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
         </div>
    </div>
</div>
@endsection

@section('page-css')
<style>
/* 1. Dono Cards (Sidebar aur Chat) ko ek fixed height do */
.chat-main-card {
    height: calc(100vh - 200px); /* Screen ki height minus header/footer */
    display: flex;
    flex-direction: column;
    overflow: hidden; /* Card ke bahar kuch show na ho */
}

/* 2. Left Side: Users list ko scrollable banayein */
.user-list-container {
    overflow-y: auto;
    flex-grow: 1;
}

/* 3. Right Side: Messages area ko scrollable banayein */
.chat-messages-container {
    overflow-y: auto;
    flex-grow: 1;
}

/* Custom Scrollbar (Optional: Thora clean dikhne ke liye) */
.user-list-container::-webkit-scrollbar,
.chat-messages-container::-webkit-scrollbar {
    width: 6px;
}
.user-list-container::-webkit-scrollbar-thumb,
.chat-messages-container::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}

/* Existing Styles */
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
.own-message .rounded {
    border-bottom-right-radius: 0 !important;
}
.other-message .rounded {
    border-bottom-left-radius: 0 !important;
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
            authEndpoint: '/broadcasting/auth',              // Private channel authorization endpoint
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'     // Laravel security token
                }
            }
        });

       

        //User Selection
        $('.user-item').on('click', function(){
            selectedUserId = $(this).data('user-id');
            let selectedUserName = $(this).data('user-name');

            //updating header:
            $('#chat-with-user').text('Chat With '+ selectedUserName);
            $('#receiver-id').val(selectedUserId);
            
            //Input Message form with send button
            $('#message-form').show();
            $('#no-no-chat-selected').hide();

            //Load Previous messages
            loadMessages(selectedUserId);

            //subscribe to private Channel
            subscribeToChannel(selectedUserId);

            //Highlights Selected User
            $('.user-item').removeClass('active');
            $(this).addClass('active');
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
                        $('#chat-messages').html('<div class="text-center text-muted"><p>No messages yet. Start the conversation!</p></div>');
                    } else {
                        messages.forEach(function(msg){
                            appendMessage(msg);
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

        // 4. SEND MESSAGE (AJAX POST)
        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            let message = $('#message-input').val().trim(); // value trim ki hy
            if (message === '') 
            {
                return;
            }
            
            $.ajax({
                url: '/chat',
                method: 'POST',
                data: 
                {
                    message: message,
                    receiver_id : selectedUserId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response){
                    $('#message-input').val('');
                    // Message already appended by real-time event
                    // (No need to append here to avoid duplication)
                },
                error: function(xhr) {
                    Swal.fire("Error!", xhr.responseJSON?.message || "Failed to send message", "error");
                }
            });
        });

        //subscribeTo Private Channel
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
            // (The Listener)
            channel.bind('message.sent', function(data){
                console.log('Message Received:', data);
                appendMessage(data);
                scrollToBottom();
            });
        }
        // 6. HELPER FUNCTIONS
        function appendMessage(msg) {
            let isOwnMessage = msg.sender_id == currentUserId;
            let messageClass = isOwnMessage ? 'own-message' : 'other-message';
            let alignment = isOwnMessage ? 'text-end' : 'text-start';
            let bgColor = isOwnMessage ? 'bg-primary text-white' : 'bg-light';

            let html = `
                <div class="mb-3 ${alignment}">
                    <div class="d-inline-block p-3 rounded ${bgColor}" style="max-width: 70%;">
                        <div class="small fw-bold mb-1">
                            ${isOwnMessage ? 'You' : (msg.sender_name || 'Unknown')} 
                        </div>
                        <div>${msg.message}</div>
                        <div class="small mt-1 opacity-75">
                            ${new Date(msg.created_at).toLocaleTimeString()}
                        </div>
                    </div>
                </div>
            `;
            
            $('#chat-messages').append(html);
        }
         
        function scrollToBottom() {
            let chatBox = document.getElementById('chat-messages');
            chatBox.scrollTop = chatBox.scrollHeight;
        }
});
</script>
@endsection