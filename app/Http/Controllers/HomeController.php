<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['publicList', 'viewEvent', 'arrivings']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::with(['drivers'])
            ->orderBy('time', 'ASC')
            //->whereBetween('time', [Carbon::now()->subHours(24),Carbon::now()->addHours(24)])//TODO:timezone issue
            ->get();
        foreach ($events as &$event) {
            $event->me = false;
            foreach ($event->drivers as $driver) {
                if ($driver->id == Auth::id()) {
                    $event->me = true;
                    $event->mode = $driver->pivot->manual ? 'manual' : 'auto';
                    $event->arrived = $driver->pivot->arrived;
                    continue;
                }
            }
        }
        return view('home', ["events" => $events]);
    }

    public function start(Request $request)
    {
        $data = $request->all();
        $event = Event::find($data['event']);
        if (!$event) {
            return response()->json(["result" => false, "error" => "Event not found"]);
        }
        $event->drivers()->save(Auth::user());

        $manual = $data['mode'] == 'manual';

        $event->drivers()->updateExistingPivot(Auth::user()->id, [
            'eta' => $manual ? $data['eta'] : $event->calcEta($data['lat'], $data['lng']),
            'lat' => $manual ? null : $data['lat'],
            'lng' => $manual ? null : $data['lng'],
            'manual' => $manual,
            'arrived' => false
        ]);

        $url = url("tracking", ['event' => $event->id]);
        if ($manual) {
            $url = url("home");
        }
        return response()->json(["result" => true, "url" => $url]);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $event = Event::find($data['event']);
        if (!$event) {
            return response()->json(["result" => false, "error" => "Event not found"]);
        }
        $eta = null;
        $eta = $event->calcEta($data['lat'], $data['lng']);
        $event->drivers()->updateExistingPivot(Auth::id(), [
            'eta' => $eta,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'updated_at' => Carbon::now()
        ]);
        return response()->json(["result" => true, "eta" => $eta]);
    }

    public function tracking($event)
    {
        $event = Event::find($event);
        if (!$event || !$event->drivers()->whereUserId(Auth::id())->exists()) {
            abort(404);
        }
        return view('tracking', ["event" => $event, 'driver' => $event->drivers()->whereUserId(Auth::id())->first()]);
    }

    public function arrived($event)
    {
        $event = Event::find($event);
        if ($event) {
            $event->drivers()->updateExistingPivot(Auth::id(), [
                'arrived' => true,
                'updated_at' => Carbon::now()
            ]);
        }
        return redirect()->back();
    }

    public function cancel($event)
    {
        $event = Event::find($event);
        if ($event) {
            $event->drivers()->detach(Auth::user());
        }
        return redirect()->back();
    }

    public function publicList()
    {
        $events = Event::with(['drivers'])
            ->orderBy('time', 'ASC')
            ->get();

        return view('events', ["events" => $events]);
    }

    public function viewEvent($event)
    {
        $event = Event::with(['drivers'])
            ->whereId($event)
            ->first();

        if (!$event) {
            abort(404);
        }
        return view('event', ["event" => $event]);
    }

    public function arrivings(Request $request)
    {
        $data = $request->all();
        $event = Event::with(['drivers'])
            ->whereId($data['event'])
            ->first();

        if (!$event) {
            return response()->json(["result" => false, "error" => "Event not found"]);
        }
        $eta = null;
        $arrived = false;
        foreach ($event->drivers as $driver) {
            if ($driver->eta <= 0 && !$driver->pivot->arrived) {
                $eta = 1;
            }
            if ($driver->eta > 0) {
                if ($eta) {
                    $eta = min([$driver->eta, $eta]);
                } else {
                    $eta = $driver->eta;
                }
            }

            if ($driver->pivot->arrived) {
                $arrived = true;
            }
        }

        return response()->json(["result" => true, "eta" => $eta, "arrived" => $arrived]);
    }

    public function address(Request $request)
    {
        $data = $request->all();
        $event = Event::find($data['event']);
        if (!$event) {
            return response()->json(["result" => false, "error" => "Event not found"]);
        }

        $result = true;

        if ($data['mode'] == 'manual') {
            $event->address = $data['address'];
            $result = $event->geocode(true);
        } else {
            $event->lat = $data['lat'];
            $event->lng = $data['lng'];
            $result = $event->reverseGeocode(true);
        }
            
        if (!$result) {
            return response()->json(["result" => false, 'error' => 'Could not resolve address']);
        }

        return response()->json(["result" => true]);
    }
}
