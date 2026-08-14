<?php

namespace App\Console\Commands\GoogleApi;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Core\Evento;

use URL, Log, Lang, Google, SendEmail, TuProyecto;

class MakeMeetConference extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleapi:makemeetconference {user}{event}{institution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create meet conference';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $user = $this->argument('user');
            $eventData = $this->argument('event');
            $institutionID = $this->argument('institution');

            $client = Google::getClient();
			$client->setSubject('comunicaciones@tuproyecto.com');
            $service =  Google::make('Calendar');

            // Print the next 10 events on the user's calendar.
			$calendarId = 'comunicaciones@tuproyecto.com';
			$optParams = array(
			'maxResults' => 10,
			'orderBy' => 'startTime',
			'singleEvents' => true,
			'timeMin' => date('c'),
            );


            // start event datetime
			// $start format = '2020-05-05T17:00:00-07:00';
			$now = Carbon::now();
            $start = Carbon::createFromFormat('Y-m-d H:i:s',
                sprintf('%s %s', $eventData['fecha_evento'], isset($eventData['hora_inicio_evento']) ? $eventData['hora_inicio_evento'] : date('H:i:s'))
            );
			$start = $start->greaterThan($now) ? $start : $now;
			$start = $start->subMinutes(13)->subSecond()->toIso8601String();
			$eventDateTimeStart =  Google::make('Calendar_EventDateTime');
            $eventDateTimeStart->setDateTime($start);

            // end event datetime
			// $end  format = '2020-05-05T17:01:00-07:00';
			$now =  Carbon::now()->addHour();
            $end = Carbon::createFromFormat('Y-m-d H:i:s',
                sprintf('%s %s', $eventData['fecha_evento'], isset($eventData['hora_inicio_evento']) ? $eventData['hora_inicio_evento'] : date('H:i:s'))
            );
			$end = $end->greaterThan($now) ? $end : $now;
			$end = $end->addMinutes(13)->addSecond()->toIso8601String();
			$eventDateTimeEnd =  Google::make('Calendar_EventDateTime');
            $eventDateTimeEnd->setDateTime($end);

            $eventAtendeesList = [];
			// event attendee
			$eventAtendee =  Google::make('Calendar_EventAttendee');
			$eventAtendee->setEmail($user['correo_electronico']);
			$eventAtendeesList[] = $eventAtendee;

			$eventAtendee =  Google::make('Calendar_EventAttendee');
			$eventAtendee->setEmail('sebastians@me.com');
            $eventAtendeesList[] = $eventAtendee;

            $events = [];

			$results = $service->events->listEvents($calendarId, $optParams);

			$events = $results->getItems();

            $count =  !empty($events) ? count($events) : 0 ;

            // $event
			$event =  Google::make('Calendar_Event');
			$event->setSummary('VideoConference start at ' . $start);
			$event->setLocation('VideoConference');
			$event->setDescription("VideoConference");
			$event->setStart($eventDateTimeStart);
			$event->setEnd($eventDateTimeEnd);
			$event->setAttendees($eventAtendeesList);

			$this->info(sprintf("Summary: %s\n", $event->getSummary()));
			$this->info(sprintf("Location: %s\n", $event->getLocation()));
			$this->info(sprintf("Description: %s\n", $event->getDescription()));
			$this->info(sprintf("start: %s\n", $event->start->getDateTime()));
            $this->info(sprintf("end: %s\n", $event->end->getDateTime()));

            $response = '';

			$event = $service->events->insert($calendarId, $event);
			$this->info('Event Created!');

			$conference = Google::make('Calendar_ConferenceData');
			$conferenceRequest = Google::make('Calendar_CreateConferenceRequest');
			$conferenceRequest->setRequestId('randomString12345');
			$conference->setCreateRequest($conferenceRequest);
			$event->setConferenceData($conference);
			$event->setAnyoneCanAddSelf(true);
            $event = $service->events->patch($calendarId, $event->id, $event, ['conferenceDataVersion' => 1]);

            // get URL conference
			$url_conference = $event->hangoutLink;
            Log::info(sprintf('Conference created: %s',$url_conference));

            // Update Event
            Evento::where('codigo_evento', $eventData['codigo_evento'])
                ->update(['url_meet_evento' => $url_conference]);
        } catch (\Exception $e) {
			Log::error(sprintf('%s:%s', 'App\Console\Commands\GoogleApi\MakeMeetConference', $e->getMessage()));
        }

    }
}
