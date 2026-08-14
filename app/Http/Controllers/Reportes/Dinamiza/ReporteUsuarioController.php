<?php

namespace App\Http\Controllers\Reportes\Dinamiza;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Models\Core\NivelEducacion, App\Models\Core\ListaOcupacion, App\Models\Core\LocalidadCiudad, App\Models\Core\BarrioLocalidad, App\Models\Core\Estrato, App\Models\Core\TipoVivienda, App\Models\Core\SectorTrabajo, App\Models\Core\Autodenominacion, App\Models\Core\GrupoPoblacional, App\Models\Core\GrupoComunitario, App\Models\Core\GrupoSocial, App\Models\Seguridad\Usuario, App\Models\Core\Estado;

class ReporteUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stratum = Estrato::stratum();
		$zone = BarrioLocalidad::zone([]);
		$workSector = SectorTrabajo::workSector();
		$socialGroup = GrupoSocial::socialGroup();
		$housingType = TipoVivienda::housingType();
		$locations = LocalidadCiudad::locations([]);
		$levels = NivelEducacion::levelsEducation();
		$employment = ListaOcupacion::employmentList();
		$communityGroup = GrupoComunitario::communityGroup();
		$selfDomination = Autodenominacion::selfDomination();
		$populationGroup = GrupoPoblacional::populationGroup();
        return view('reports.dinamiza.users.index', compact('levels', 'employment', 'zone', 'stratum', 'locations', 'housingType', 'workSector', 'selfDomination', 'populationGroup', 'communityGroup', 'socialGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Artisan::queue('dinamiza:users', [
            'user' => \Auth::user()->codigo_usuario,
            'institution' => session('institution')->codigo_institucion,
            'filters' => $request->all()
        ]);

        $user = Usuario::find(\Auth::user()->codigo_usuario);
        if(!$user instanceof Usuario) {
            throw new \Exception("Error recuperando usuario de dinamiza ".\Auth::user()->codigo_usuario);
        }

        activity()
            ->performedOn($user)
            ->withProperties([
                'attributes' => [
                    'user' => $user->nombre_usuario,
                    'user_id' => $user->codigo_usuario,
                    'report' => 'usuarios dinamiza'
                ] 
            ])
        ->log('activitylog.reports');

        return redirect()->route('reportes.dinamiza.usuarios.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
    }
}
