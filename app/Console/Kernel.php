<?php

namespace App\Console;

use Aws\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Admin
        Commands\SendEmails::class,
        Commands\PercentageAccompaniments::class,
        Commands\DuplicateSurvey::class,
        Commands\LoadExcel::class,
        Commands\LoadInstitution::class,
        Commands\LoadUserRol::class,
        Commands\LoadEntities::class,
        Commands\LoadCiiu::class,

        // Reports
        Commands\Reports\Users::class,
        Commands\Reports\UsersModule::class,
        Commands\Reports\RegisteredProgram::class,
        Commands\Reports\MonitoringProgram::class,
        Commands\Reports\ReportVotes::class,
        Commands\Reports\Cursos::class,
        Commands\Reports\CursoIndividual::class,
        Commands\Reports\Formularios::class,
        Commands\Reports\ReportUniqueEmpodera::class,
        Commands\Reports\ReporteUniqueTerpel::class,
        Commands\Reports\Evaluation::class,
        Commands\Compensar\Events::class,
        Commands\Mentorias::class,
        Commands\Proyectos\ReportProyect::class,
        Commands\Innpulsa\TestCommand::class,
        Commands\Sesiones::class,
        Commands\Reports\RolsModule::class,
        Commands\Reports\ReportProgramAppsCoInnpulsa::class,
        Commands\Reports\NoCompleteFormAppsCo::class,
        Commands\Reports\ReportSessionsOfMentoring::class,
        Commands\Reports\ReportEntities::class,

        // Notifications
        Commands\Notifications\MentorAlerts::class,

        // Innpulsa
        Commands\Innpulsa\ReportNotAbsolute::class,
        Commands\Innpulsa\ReportNotAbsoluteEmpodera::class,
        Commands\Innpulsa\ReportReto3::class,
        Commands\Innpulsa\ReportReto3Aldea20::class,
        Commands\Innpulsa\ReportReto4::class,
        Commands\Innpulsa\ReportMonitoreo::class,
        Commands\Innpulsa\ReportMonitoreoAldea20::class,
        Commands\Innpulsa\UpdateScoringEmpodera::class,
        Commands\Innpulsa\ReportEvents::class,
        Commands\Innpulsa\ReportEmpodera::class,
        Commands\Innpulsa\loadproducts::class,
        Commands\Innpulsa\CloseAgreement::class,
        Commands\Innpulsa\ReportEvaluationGeneral::class,
        Commands\Innpulsa\AssignmentEmpodera::class,
        Commands\Innpulsa\ValidateResources::class,
        Commands\Innpulsa\ChangeNumberApplication::class,
        Commands\Innpulsa\UpdateScoringAldea20::class,
        Commands\Innpulsa\validateFunctionAldea20::class,
        Commands\Innpulsa\CompareScore::class,
        Commands\Innpulsa\MigratePurchaseAgreement::class,
        Commands\Innpulsa\LoadUsersEntities::class,

        // RedCoomeva
        Commands\Redcoomeva\SyncMembership::class,

        // Dinamiza
        Commands\Dinamiza\Users::class,
        Commands\Dinamiza\Program::class,

        // Algolia
        Commands\Algolia\AlgoliaSync::class,
        Commands\Algolia\AlgoliaSyncItem::class,
        Commands\Algolia\AlgoliaDesync::class,
        Commands\Algolia\AlgoliaDesyncItem::class,

        // Compensar
        Commands\Compensar\Users::class,
        Commands\Compensar\LoadUsers::class,
        Commands\Compensar\LoadUserEntities::class,
        Commands\Compensar\LoadProducts::class,
        Commands\Hogares\LoadProducts::class,
        Commands\Compensar\SesionesMentor::class,

        // Bolivar
        Commands\Bolivar\ReportUsersProgram::class,

        // Terpel
        Commands\Terpel\sendInvitation::class,

        // Propace
        Commands\Propace\LoadUsers::class,
        Commands\Propace\UpdateScoreEvaluation::class,
        Commands\Propace\EmailCertificate ::class,
        Commands\Propace\EmailCertificatePfc30 ::class,


        // Bwee
        Commands\Bewee\LoadUsers::class,

        // Sja
        Commands\Sja\LoadUsers::class,

        // Promotions
        Commands\Promotions\ChangeStatePromotions::class,

        // Google Api
        Commands\GoogleApi\MakeMeetConference::class,
        Commands\GoogleApi\MakeAsignationMeetConference::class,
        Commands\GoogleApi\TestMakeMeetConference::class,

        // FNJ
        Commands\FNJ\LoadUsers::class,

        //RECORDATORIOS DE EVENTOS
        Commands\RecordatorioEvento::class,

        //RutaN
        Commands\Rutan\ReportConvocatorias::class,
        Commands\Rutan\ReportConvocatoria200::class,
        Commands\Rutan\ReportConvocatoriaRutaEmprendimiento::class,
        Commands\Rutan\MentoringReport::class,
        Commands\Rutan\SessionReport::class,
        Commands\Rutan\LoadUsers::class,
        Commands\Rutan\LoadProfiling::class,

        //Siinova Bolivar
        Commands\SiinovaBolivar\LoadData::class,
        Commands\SiinovaBolivar\LoadDataDiagnosis::class,


        Commands\Imports\LoadUsers::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Admin
        $schedule->command('admin:percentageaccompaniments')->daily();

//        // Innpulsa
        // $filePath = storage_path('cron/scheduled_tasks_innpulsa.txt');
        // $schedule->command('innpulsa:notabsolute')->dailyAt('08:00')
        //     ->appendOutputTo($filePath)
        //     ->emailOutputTo(['desarrollo@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

//      $filePathCompensar = storage_path('cron/scheduled_tasks_compensar.txt');
//      $schedule->command('admin:loadexcel')->dailyAt('09:39')
//        ->appendOutputTo($filePathCompensar)
//        ->emailOutputTo(['backend01tuproyecto@gmail.com', 'backend01tuproyecto@gmail.com']);

        //  $filePathEmpodera = storage_path('cron/scheduled_tasks_innpulsa_empodera_2020.txt');
        //  $schedule->command('innpulsa:notabsoluteempodera')->dailyAt('08:00')
        //      ->appendOutputTo($filePathEmpodera)
        //      ->emailOutputTo(['administracion@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

        // Redcoomeva
        // $filePath = storage_path('cron/scheduled_tasks_redcoomeva.txt');
        // $schedule->command('redcoomeva:syncmembership')->dailyAt('01:00')
        //     ->appendOutputTo($filePath)
        //     ->emailOutputTo(['desarrollo@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

        $filePathPromotion = storage_path('cron/scheduled_promotion-inactive.txt');
        $schedule->command('promotions:state')->dailyAt('01:00')
            ->appendOutputTo($filePathPromotion)
            ->emailOutputTo(['administracion@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

        //Propace
        $filePathPromotion = storage_path('cron/scheduled_certificate-inactive.txt');
        $schedule->command('propace:emailcerfiticate')->dailyAt('13:00')
            ->appendOutputTo($filePathPromotion)
            ->emailOutputTo(['diego.gomez@tuproyecto.com']);

        //Propace pfc ibague
        $filePathPromotion = storage_path('cron/scheduled_certificate_pfc_30.txt');
        $schedule->command('propace:emailCerfiticatePfc30')->dailyAt('13:00')
                 ->appendOutputTo($filePathPromotion)
                 ->emailOutputTo(['administracion@tuproyecto.com', 'diego.gomez@tuproyecto.com']);


        //recordatorio de eventos
        $filePathPromotion = storage_path('cron/scheduled_recordatorios_eventos.txt');
        $schedule->command('recordatorios:recordatorioevento')->twiceDaily(6, 12)
                 ->appendOutputTo($filePathPromotion)
                 ->emailOutputTo(['administracion@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

        // Limpieza de caché
        $schedule->command('cache:clear')->daily()->at('02:00')
            ->appendOutputTo(storage_path('logs/cache_clear.txt'))
            ->emailOutputTo(['administracion@tuproyecto.com', 'diego.gomez@tuproyecto.com']);

    }
}
