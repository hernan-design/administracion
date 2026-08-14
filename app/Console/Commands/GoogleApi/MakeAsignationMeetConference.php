<?php

namespace App\Console\Commands\GoogleApi;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Core\AsignacionJurado;

use URL, Log, Lang, Google, SendEmail, TuProyecto;

class MakeAsignationMeetConference extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleapi:makeasignationmeetconference {user}{judge}{register}{evaluators}{hour}{date}{institution}';

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
            $judge = $this->argument('judge');
            $register = $this->argument('register');
            $hour = $this->argument('hour');
            $date = $this->argument('date');
            $evaluators = $this->argument('evaluators');
            $institutionID = $this->argument('institution');
            $objectUrl = 'http://static.tuproyecto.com/documents/asignaciones/Guia+Evaluadores-ALDEA-Conversa+con+expertos.pdf';

            Log::info('Entry to Command');

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
                sprintf('%s %s', $judge['fecha_asignacion_jurado'], isset($judge['hora_asignacion_jurado']) ? $judge['hora_asignacion_jurado'] : date('H:i:s'))
            );
			$start = $start->greaterThan($now) ? $start : $now;
			$start = $start->subMinutes(13)->subSecond()->toIso8601String();
			$eventDateTimeStart =  Google::make('Calendar_EventDateTime');
            $eventDateTimeStart->setDateTime($start);

            // end event datetime
			// $end  format = '2020-05-05T17:01:00-07:00';
			$now =  Carbon::now()->addHour();
            $end = Carbon::createFromFormat('Y-m-d H:i:s',
                sprintf('%s %s', $judge['fecha_asignacion_jurado'], isset($judge['hora_asignacion_jurado']) ? $judge['hora_asignacion_jurado'] : date('H:i:s'))
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
            Log::info(sprintf('Conference created: %s', $url_conference));

            AsignacionJurado::where('codigo_registro_fase_usuario', $register['codigo_registro_fase_usuario'])
                ->update(['url_meet_asignacion_jurado' => $url_conference]);

            $data = [];
            $data['_meet'] = $url_conference;
            $data['_template'] = 'emails.program.innpulsa.moment__four__group_disscusion';
            $data['_subject'] = 'Conversatorio Reto 2';
            $data['_date'] = $date;
            $data['_hour'] = $hour;
            $data['_register'] = $register['numero_registro_convocatoria'];
            $data['_place'] = 'Virtual';
            $data['_modality'] = 'Virtual';
            $data['_inst'] = $institutionID;
            $data['_url_action'] = $objectUrl;

            foreach ($evaluators as $evaluator) {
                $data['_to_name'] = ucwords(strtolower($evaluator['nombre_usuario']));
                $data['_to'] = $evaluator['correo_electronico'];

                SendEmail::notification($data);
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'App\Console\Commands\GoogleApi\MakeAsignationMeetConference', $e->getMessage()));
        }
    }
}
