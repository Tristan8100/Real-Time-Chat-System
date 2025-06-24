<div class="conversation-container border border-gray-200 dark:border-[#3E3E3A] bg-white dark:bg-[#1B1B18] shadow-sm h-screen">
    <div class="conversation-container h-full flex flex-col border-r border-gray-200 dark:border-[#3E3E3A] bg-white dark:bg-[#1B1B18]">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-[#3E3E3A] bg-gray-50 dark:bg-[#161615]">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-[#EDEDEC]">Conversations</h2>
            <div class="mt-2 relative">
                <input id="search-input" type="text" placeholder="Search conversations..." 
                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-[#3E3E3A] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#FF4433] bg-white dark:bg-[#1B1B18] text-gray-800 dark:text-[#EDEDEC] placeholder-gray-400 dark:placeholder-[#706F6C]">
                <svg class="absolute right-3 top-2.5 h-4 w-4 text-gray-400 dark:text-[#706F6C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Scrollable conversation list -->
        <div id="conversationList" class="flex-1 overflow-y-auto">
            <!-- Conversation items will go here -->
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let searchTimer = null;

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    fetchConversations();

    function fetchConversations() {
        $('#conversationList').empty();
        $.ajax({
            url: '/fetch-previous', // Replace with your actual route
            method: 'GET',
            success: function (response) {
                const conversations = response.conversations;
                const queryparams = window.location.pathname.split('/')[2];

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
                    <a href="/conversation/${item.conversation.conversation_id}" 
                    class="flex items-center gap-4 p-4 border-b border-gray-200 dark:border-[#3E3E3A] hover:bg-gray-50 dark:hover:bg-[#252523] ${queryparams == item.conversation.conversation_id ? 'bg-blue-50 dark:bg-[#252523]' : 'bg-white dark:bg-[#1B1B18]'}">
                        <div class="w-10 h-10 bg-gray-200 dark:bg-[#3E3E3A] rounded-full flex-shrink-0 overflow-hidden">
                            <img src="${profile}" alt="User Photo" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between items-baseline">
                                <div class="text-gray-800 dark:text-[#EDEDEC] font-medium truncate">${user.email}</div>
                                <span class="text-xs text-gray-500 dark:text-[#A1A09A] ml-2">${formatTime(convo.created_at)}</span>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-500 dark:text-[#A1A09A] truncate">${user.name}</p>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-[#D4D4D1] truncate mt-1">${convo.body}</p>
                        </div>
                    </a>`;
                    
                    $('#conversationList').append(html);
                });
            },
            error: function () {
                $('#conversationList').html('<p class="text-red-500">Failed to load conversations.</p>');
            }
        });
    }
    
    function search() {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            console.log('searching');
            const value = $('#search-input').val().trim();
            if (value === '') return fetchConversations();
            
            $.ajax({
                url: '/search2',
                method: 'POST',
                data: {
                    search: $('#search-input').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                $('#conversationList').empty();
                console.log(response);
                const conversations = response.conversations;
                const queryparams = window.location.pathname.split('/')[2];

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
                    <a href="/conversation/${item.conversation.conversation_id}" 
                    class="flex items-center gap-4 p-4 border-b border-gray-200 dark:border-[#3E3E3A] hover:bg-gray-50 dark:hover:bg-[#252523] ${queryparams == item.conversation.conversation_id ? 'bg-blue-50 dark:bg-[#252523]' : 'bg-white dark:bg-[#1B1B18]'}">
                        <div class="w-10 h-10 bg-gray-200 dark:bg-[#3E3E3A] rounded-full flex-shrink-0 overflow-hidden">
                            <img src="${profile}" alt="User Photo" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between items-baseline">
                                <div class="text-gray-800 dark:text-[#EDEDEC] font-medium truncate">${user.email}</div>
                                <span class="text-xs text-gray-500 dark:text-[#A1A09A] ml-2">${formatTime(convo.created_at)}</span>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-500 dark:text-[#A1A09A] truncate">${user.name}</p>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-[#D4D4D1] truncate mt-1">${convo.body}</p>
                        </div>
                    </a>`;
                    
                    $('#conversationList').append(html);
                });
            },

                error: function(err) {
                    console.error('Error fetching users:', err);
                }
            });
        }, 1000); 
    }

    $('#search-input').on('input', function() {
        search();
    });

</script>