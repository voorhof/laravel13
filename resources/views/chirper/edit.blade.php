<x-chirper.layout>
    <x-slot:headTitle>
        Edit Chirp
    </x-slot:headTitle>

    {{-- Chirp edit form --}}
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-10">
            <div class="card-body">
                <div>
                    <h1 class="text-3xl font-bold">Edit Chirp</h1>
                    <p class="mt-2 mb-4 text-muted">Update your message or cancel to go back to the feed</p>

                    <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-control w-full">
                            <textarea
                                name="message"
                                placeholder="What was on your mind?"
                                aria-label="Chirp message"
                                class="textarea textarea-bordered w-full resize-none @error('message') textarea-error @enderror"
                                rows="4"
                                maxlength="255"
                                required
                            >{{ old('message', $chirp->message) }}</textarea>

                            @error('message')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <div class="card-actions justify-between mt-4">
                            <a href="{{ route('chirps.index') }}" class="btn btn-ghost btn-sm">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Update Chirp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-chirper.layout>
