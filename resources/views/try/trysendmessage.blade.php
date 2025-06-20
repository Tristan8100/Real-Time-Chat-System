<x-app-layout>
    <style>
    .chat-container {
        width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
    }
    .chat-header {
        padding-bottom: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .chat-header h2 {
        margin: 0;
    }
    .chat-header .email {
        color: #666;
        margin: 5px 0 0 0;
    }
    .messages {
        height: 60vh;
        overflow-y: auto;
        padding-right: 10px;
    }
    .message {
        margin-bottom: 15px;
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 18px;
    }
    .message.sent {
        background: #007bff;
        color: white;
        margin-left: auto;
    }
    .message.received {
        background: #f1f1f1;
        margin-right: auto;
    }
    .message-time {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 5px;
    }
    .you-indicator {
        font-style: italic;
    }
    .message-form {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .message-form input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
    }
    .message-form button {
        padding: 10px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 20px;
        cursor: pointer;
    }
    </style>
<main class="flex ">
    <div class="conversation-container border border-gray-200 bg-white shadow-sm w-[300px] h-screen">
        <div class="conversation-container h-full flex flex-col border-r border-gray-200 bg-white">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Conversations</h2>
                <div class="mt-2 relative">
                    <input type="text" placeholder="Search conversations..." 
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Scrollable conversation list -->
            <div id="conversationList" class="flex-1 overflow-y-auto">
                <!-- Conversation item (active state example) -->
                
            </div>
        </div>
        
    </div>

    <div class="chat-container">
    <!-- Chat Header -->
    <div class="chat-header">
        <h2>
            @if($current_user == $participants['user_one']->id)
                Chat with {{ $participants['user_two']->name }}
            @else
                Chat with {{ $participants['user_one']->name }}
            @endif
        </h2>
        <p class="email">
            @if($current_user == $participants['user_one']->id)
                {{ $participants['user_two']->email }}
            @else
                {{ $participants['user_one']->email }}
            @endif
        </p>
    </div>

    <!-- Messages Container -->
    <div class="messages">
        @foreach($messages as $message)
            <div class="message {{ $message->sender_id == $current_user ? 'sent' : 'received' }}">
                <div class="message-content">{{ $message->body }}</div>
                <div class="message-time">
                    {{ $message->created_at->format('M j, g:i a') }}
                    @if($message->sender_id == $current_user)
                        <span class="you-indicator">(You)</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div id="toast-container" class="toast bg-blue-600 text-white rounded shadow-lg cursor-pointer transition hover:bg-blue-700">

    </div>
    {{$messages->links()}}

    <!-- Message Input Form -->
    <form class="message-form" method="POST" action="">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
        <input type="text" name="body" placeholder="Type your message..." required>
        <button type="submit">Send</button>
    </form>
    </div>
</main>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    const lastPage = {{ $lastPage }};
    const currentPage = {{ $currentPage }};
    const pathSegments = window.location.pathname.split('/');
    const queryparams = pathSegments[2];

    $.ajax({
        url: '/fetch-previous', // Replace with your actual route
        method: 'GET',
        success: function (response) {
            const conversations = response.conversations;

            conversations.forEach(function (item) {
                const user = item.other_user;
                const convo = item.conversation;
                if (!convo) return;
                let profile = '';
                console.log('picture is ' + item.other_user.profile_icon_path);
                if(item.other_user.profile_icon_path != null) {
                    profile = `/images/icon/${item.other_user.profile_icon_path}`;
                } else {
                    profile = 'https://img.icons8.com/?size=100&id=98957&format=png&color=000000'; // Default unknown user icon
                }
                const html = `
                <a href="/conversation/${item.conversation.conversation_id}" class="flex items-center gap-4 p-4 border-b border-gray-200 message-user cursor-pointer hover:bg-gray-50 ${queryparams == item.conversation.conversation_id ? 'bg-blue-50' : 'bg-white'}">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex-shrink-0 overflow-hidden">
                        <img src="${profile}" alt="User Photo" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex justify-between items-baseline">
                            <div class="text-gray-800 font-medium truncate">${user.email}</div>
                            <span class="text-xs text-gray-500 ml-2">${formatTime(convo.created_at)}</span>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-500 truncate">${user.name}</p>
                        </div>
                        <p class="text-sm text-gray-600 truncate mt-1">${convo.body}</p>
                    </div>
                </a>`;
                
                $('#conversationList').append(html);
            });
        },
        error: function () {
            $('#conversationList').html('<p class="text-red-500">Failed to load conversations.</p>');
        }
    });

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }



    const value = $('.message-form input[name="conversation_id"]').val();
    console.log(value);

    $('.messages').animate({
                scrollTop: $('.messages')[0].scrollHeight
    }, 500);

    // Handle form submission
    $('.message-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const form = $(this);
        const formData = form.serialize(); // Serialize form data
        const messagesContainer = $('.messages-container');
        const contain = $('.messages');

        $.ajax({
            url: '/send-message', // Use form's action or default route
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Clear input on success
                form.find('input[name="body"]').val('');

                const formattedDate = new Date(response.created_at).toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                if (currentPage < lastPage) {
                 //Redirect to last page to append new message
                    console.log('Redirecting to last page');
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', lastPage);
                    window.location.href = url.toString();
                } 

                contain.append(`
                <div class="message sent">
                    <div class="message-content">${response.body}</div>
                    <div class="message-time">
                        ${formattedDate}
                        <span class="you-indicator">(You)</span>
                    </div>
                </div>
                `);

                $('.messages').animate({
                scrollTop: $('.messages')[0].scrollHeight
                }, 500);
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseJSON?.message || 'Message sending failed');
                alert('Failed to send message. Please try again.');
            }
        });
    });
    

    function showToast(messageText, link) {
        const toast = $(`
            <div class="bg-blue-600 text-white px-4 py-2 rounded shadow-lg cursor-pointer transition hover:bg-blue-700">
                ${messageText}
            </div>
        `);

        toast.on('click', function() {
            window.location.href = link;
        });

        $('#toast-container').append(toast);
        console.log('trigger toast');
    }

    function appendMessage(message, formattedDate) {
        $('.messages').append(`
            <div class="message received">
                <div class="message-content">${message.body}</div>
                <div class="message-time">
                    ${formattedDate}
                    <span class="sender-name">(${message.sender.name})</span>
                </div>
            </div>
        `);
    }

    
    window.Pusher.logToConsole = true;
    if (window.Echo) {
    window.Echo.private(`conversation.${value}`)
        .listen('NewMessageEvent', (e) => {

            console.log('received');

            const message = e.message;

            const formattedDate = new Date(message.created_at).toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            if (currentPage < lastPage) {
                console.log('Redirecting to last page');
                showToast(message.body, `/conversation/${message.conversation_id}/?page=${lastPage}`);
                return;
            }

            appendMessage(message, formattedDate);

            $('.messages').animate({
                scrollTop: $('.messages')[0].scrollHeight
            }, 500);
        });
    }
});

</script>
</x-app-layout>