<?php

namespace App\Console\Commands\Notifications;

use Illuminate\Console\Command;

use App\Models\Seguridad\Usuario, App\Models\Core\Asesoria;

use DB;

class MentorAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:mentoralerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alertas inactividad mentores';

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
        // Notificacion no absolutos
        $this->info("Buscando mentores con inactividad.");

        $query = Asesoria::query();
        $query->select('asesoria.codigo_asesoria', 'descripcion_asesoria', 'fecha_asesoria', 'hora_asesoria', 'contador_discusiones_asesoria', 'asesoria_institucion.codigo_estado', DB::raw("initcap(nombre_estado) as nombre_estado"),
            DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"),
            DB::raw("initcap( (CASE emprendedor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN emprendedor.nombre_usuario || ' ' || COALESCE(pemprendedor.primer_apellido_persona, '') || ' ' || COALESCE(pemprendedor.segundo_apellido_persona, '') ELSE emprendedor.nombre_usuario END) ) as nombre_emprendedor"),
            DB::raw("(CURRENT_DATE - fecha_asesoria ) as dias")
        );
        $query->join('aprende.asesoria_institucion', function($join) {
            $join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
                ->where('asesoria_institucion.codigo_institucion', '=', 51);
        });

        $query->join('core.estado', 'asesoria_institucion.codigo_estado', '=', 'estado.codigo_estado');
        $query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

        // Mentor
        $query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
        $query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');

        // Emprendedor
        $query->join('seguridad.usuario as emprendedor', 'asesoria.codigo_usuario', '=', 'emprendedor.codigo_usuario');
        $query->leftJoin('core.persona as pemprendedor', 'emprendedor.codigo_usuario', '=', 'pemprendedor.codigo_usuario');

        $query->orderBy('fecha_asesoria', 'desc');
        $query->orderBy('hora_asesoria', 'desc');

        $x = $query->toSql();
        $this->info("Sql -> $x");

    }
}
