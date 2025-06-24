<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Dashboard Header -->
        <div class="bg-white shadow-sm dark:bg-gray-800">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Connect with Team</h1>
                        <p class="text-gray-500 mt-1 dark:text-gray-400">Browse and message your colleagues</p>
                    </div>
                    <!-- Dark Mode Toggle Button - Placed strategically next to search for clean layout -->
                    <button id="theme-toggle" type="button" class="flex-shrink-0 p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <!-- Sun Icon (light mode) -->
                        <svg id="sun-icon" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.325 5.825l-.707.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (dark mode) -->
                        <svg id="moon-icon" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            id="search-input"
                            type="text"
                            placeholder="Search by name"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 transition-all duration-200
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-400"
                        >
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium
                        dark:bg-blue-800 dark:text-blue-100">
                        All <span class="ml-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full dark:bg-blue-600 dark:text-white">{{ $totalUsers }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- User Grid -->
            <div id="user-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Your Laravel content will go here -->
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-10 flex justify-center space-x-2"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let searchTimer = null;
    fetchallUser();

    function fetchallUser() {
        $.ajax({
        url: '/dashboard/api',
        method: 'GET',
        success: function(response) {
        const users = response.users.data;
        $('#user-list').empty();
        users.forEach(user => {
            const iconUrl = user.profile_icon_path 
                ? `/images/icon/${user.profile_icon_path}` 
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`;

            const userCard = `
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-all duration-200 hover:border-blue-100 hover:-translate-y-0.5
                dark:bg-gray-800 dark:border-gray-700 dark:shadow-lg dark:hover:shadow-xl dark:hover:border-blue-700">
                <div class="p-5 flex flex-col h-full">
                    <div class="relative self-center mb-4">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 p-1">
                            <img
                                src="${iconUrl}"
                                alt="${user.name}"
                                class="w-full h-full rounded-full object-cover border-2 border-white dark:border-gray-800"
                            />
                        </div>
                        <span class="absolute bottom-1 right-1 block h-4 w-4 rounded-full bg-green-500 border-2 border-white dark:border-gray-800"></span>
                    </div>
                    <div class="text-center mb-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${user.name}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">${user.email}</p>
                    </div>
                    <form action="/conversation/${user.id}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 transform hover:shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Message
                        </button>
                    </form>
                </div>
            </div>
        `;

            $('#user-list').append(userCard);
        });
        },
        beforeSend: function() {
            $('#user-list').html('<div class="flex items-center justify-center py-16"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div></div>');
        },
        error: function(err) {
            console.error('Error fetching users:', err);
        }
        });
    }

    //search
    function search() {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            console.log('searching');
            const value = $('#search-input').val().trim();
            if (value === '') return fetchallUser();
            $.ajax({
                url: '/search',
                method: 'POST',
                data: {
                    search: $('#search-input').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                $('#user-list').empty();
                const users = response.users.data;

                users.forEach(user => {
                    const iconUrl = user.profile_icon_path 
                        ? `/images/icon/${user.profile_icon_path}` 
                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`;

                    const userCard = `
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-all duration-200 hover:border-blue-100 hover:-translate-y-0.5
                        dark:bg-gray-800 dark:border-gray-700 dark:shadow-lg dark:hover:shadow-xl dark:hover:border-blue-700">
                        <div class="p-5 flex flex-col h-full">
                            <div class="relative self-center mb-4">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 p-1">
                                    <img
                                        src="${iconUrl}"
                                        alt="${user.name}"
                                        class="w-full h-full rounded-full object-cover border-2 border-white dark:border-gray-800"
                                    />
                                </div>
                                <span class="absolute bottom-1 right-1 block h-4 w-4 rounded-full bg-green-500 border-2 border-white dark:border-gray-800"></span>
                            </div>
                            <div class="text-center mb-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${user.name}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${user.email}</p>
                            </div>
                            <form action="/conversation/${user.id}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 transform hover:shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Message
                                </button>
                            </form>
                        </div>
                    </div>
                `;

                    $('#user-list').append(userCard);
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
});
</script>

</x-app-layout>
