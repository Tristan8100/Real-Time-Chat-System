<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Upload Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Upload your Profile Photo') }}
        </p>
    </header>

    @if (Auth::user())
        <div class="mb-4">
            <img src="{{ asset('images/original/' . Auth::user()->profile_path) }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover">
        </div>
    @endif

    <form method="post" action="{{ route('add.photo') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
    @csrf
    @method('put')
    
    <div>
        <x-input-label for="photo" :value="__('Photo')" />
        <input id="photo" name="photo" type="file" class="mt-1 block w-full text-gray-900 dark:text-gray-300" accept="image/*" />
        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
</form>
</section>
