<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws AuthorizationException
     */
    public function index(): View
    {
        // Authorize the user
        Gate::authorize('view-any', Chirp::class);

        $chirps = Chirp::with('user') // Eager load the user relationship
        ->latest() // order by created_at, newest first
        ->take(25) // Limit to 25 most recent chirps
        ->get();

        return view('chirper.index', compact('chirps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     */
    public function create(): void
    {
        // Not used in this demo, form is on the index page

        // Authorize the user
        Gate::authorize('create', Chirp::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Authorize the user
        Gate::authorize('create', Chirp::class);

        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'Please write something to chirp!',
            'message.max' => 'Chirps must be 255 characters or less.',
        ]);

        // Create the chirp and attach it to the authenticated user
        auth()->user()->chirps()->create($validated);

        // Redirect to the feed
        return redirect(route('chirps.index'))
            ->with('success', 'Your chirp has been posted!');
    }

    /**
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    public function show(Chirp $chirp): RedirectResponse
    {
        // Authorize the user
        Gate::authorize('view', $chirp);

        // No detail page in this demo, redirect to the feed
        return redirect(route('chirps.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(Chirp $chirp): View
    {
        // Authorize the user
        Gate::authorize('update', $chirp);

        // We'll add authorization in lesson 11
        return view('chirper.edit', compact('chirp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws AuthorizationException
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        // Authorize the user
        Gate::authorize('update', $chirp);

        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'Please write something to chirp!',
            'message.max' => 'Chirps must be 255 characters or less.',
        ]);

        // Update the Chirp
        $chirp->update($validated);

        // Redirect to the feed
        return redirect(route('chirps.index'))
            ->with('success', 'Chirp updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws AuthorizationException
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        // Authorize the user
        Gate::authorize('delete', $chirp);

        // Delete the Chirp
        $chirp->delete();

        // Redirect to the feed
        return redirect(route('chirps.index'))
            ->with('success', 'Chirp deleted!');
    }
}
