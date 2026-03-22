<x-chirper.layout>
    <x-slot:headTitle>
        Feed
    </x-slot:headTitle>

    {{-- Chirp form --}}
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100">
            <div class="card-body">
                <div>
                    <h1 class="text-3xl font-bold">Welcome to Chirper!</h1>
                    <p class="mt-2 mb-4 text-muted">Time to make it sing (or chirp)!</p>

                    <form method="POST" action="{{ route('chirps.store') }}">
                        @csrf

                        <div class="form-control w-full">
                            <textarea
                                name="message"
                                placeholder="What's on your mind?"
                                aria-label="Chirp message"
                                class="textarea textarea-bordered w-full resize-none"
                                rows="4"
                                maxlength="255"
                                required
                            >{{ old('message') }}</textarea>

                            @error('message')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <div class="mt-4 flex items-center justify-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Chirp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Chirp feed --}}
    <div class="max-w-2xl mx-auto mt-8">
        <h2 class="text-2xl font-bold">
            Latest Chirps
        </h2>

        <div class="space-y-4 mt-4">
            @forelse ($chirps as $chirp)
                <x-chirper.chirp :chirp="$chirp" />
            @empty
                <div class="hero">
                    <div class="hero-content text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="mt-4 text-muted">No chirps yet. Be the first to chirp!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-chirper.layout>
