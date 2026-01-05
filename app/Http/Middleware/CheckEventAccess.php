<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Models\EventRsvp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEventAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get event ID from route parameter
        $eventId = $request->route('event') ?? $request->route('id');

        if (!$eventId) {
            abort(404, 'Event not found');
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('redirect_to', $request->fullUrl())
                ->with('error', 'Please login to access this event');
        }

        // Get the event
        $event = Event::find($eventId);

        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the event creator or has Manage Event permission
        if (Auth::id() === $event->user_id || Auth::user()->can('Manage Event')) {
            return $next($request);
        }

        // Check if user has confirmed RSVP
        $rsvp = EventRsvp::where('event_id', $eventId)
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->first();

        if (!$rsvp) {
            abort(403, 'You do not have access to this event. Please register and complete payment if required.');
        }

        return $next($request);
    }
}
