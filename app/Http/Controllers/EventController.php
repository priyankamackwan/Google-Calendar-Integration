<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreEventsRequest;
use App\Http\Requests\UpdateEventsRequest;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Google\CalendarService;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public CalendarService $googleCalendarService;

    public function google($code = null)
    {
        /** @var User $user */
        $user = auth()->user();

        $this->googleCalendarService = new CalendarService();

        if ($code != null) {
            $tokens = $this->googleCalendarService->getTokensFromCode($code);
            $user->update([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
        }

        $accessToken = $user->access_token;
        $refreshToken = $user->refresh_token;

        if ($accessToken  != null || $refreshToken != null) {
            $this->googleCalendarService->setTokens($accessToken, $refreshToken);
        } else {
            return [
                'status' => false,
                'url' => $this->googleCalendarService->getAuthUrl(),
            ];
        }

        if (!$this->googleCalendarService->status()) {
            // Refresh tokens
        }

        return [
            'status' => true,
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $google = $this->google();

        if ($google['status'] == false) {
            return redirect($google['url']);
        }

        return view('events/index');
    }

    public function datatable()
    {
        $this->google();

        $events = $this->googleCalendarService->getEvents();

        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'id' => $event->id,
                'name' => $event->summary,
                'description' => $event->description,
                'startDateTime' => date('j F Y h:i:s A', strtotime($event->start->dateTime)),
                'endDateTime' => date('j F Y h:i:s A', strtotime($event->end->dateTime)),
                'action' => "<a href='" . route('events.edit', $event->id) . "' class='edit btn btn-primary btn-sm' title='edit'><i class='fa fa-edit'></i></a>"
                    . " <a href='" . route('events.destroy', $event->id) . "' class='delete btn btn-danger btn-sm' title='delete'><i class='fa fa-trash'></i></a>"
            ];
        }

        return DataTables::of($data)
            ->make(true);
    }

    public function callbackGoogle(Request $request)
    {
        $this->google($request->code);

        return redirect()->route('events.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventsRequest $request)
    {
        $this->google();

        $eventDetails = [
            'summary' => $request->name ?? 'No name',
            'description' => $request->description ?? '',
            'start' => [
                'dateTime' => $request->startDateTime . ':00+05:30',
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => $request->endDateTime . ':00+05:30',
                'timeZone' => 'Asia/Kolkata',
            ],
        ];

        $event = $this->googleCalendarService->createEvent($eventDetails);

        if ($event) {
            Session::flash('successMsg', 'Event Created SuccessFully!');
            return redirect('events');
        }

        Session::flash('errorMsg', 'Something went wrong!');
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($eventId)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($eventId)
    {
        $this->google();

        $eventObj = $this->googleCalendarService->getEvent($eventId);

        $event = (object)[
            'id' => $eventObj->id,
            'name' => $eventObj->summary,
            'description' => $eventObj->description,
            'startDateTime' => date('Y-m-d\TH:i', strtotime($eventObj->start->dateTime)),
            'endDateTime' => date('Y-m-d\TH:i', strtotime($eventObj->end->dateTime)),
        ];

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventsRequest $request, $eventId)
    {
        $this->google();

        $eventDetails = [
            'summary' => $request->name ?? 'No name',
            'description' => $request->description ?? '',
            'start' => [
                'dateTime' => $request->startDateTime . ':00+05:30',
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => $request->endDateTime . ':00+05:30',
                'timeZone' => 'Asia/Kolkata',
            ],
        ];

        $event = $this->googleCalendarService->updateEvent($eventId, $eventDetails);

        if ($event) {
            Session::flash('successMsg', 'Event Created SuccessFully!');
            return redirect('events');
        }

        Session::flash('errorMsg', 'Something went wrong!');
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($eventId)
    {
        $this->google();

        if ($this->googleCalendarService->deleteEvent($eventId)) {
            return response()->json([
                'success' => true,
                'message' => 'Event Deleted SuccessFully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong!'
        ]);
    }
}
