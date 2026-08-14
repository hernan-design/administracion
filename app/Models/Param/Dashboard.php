<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado, App\Models\Core\AsignacionJurado, App\Models\Core\DetalleVoucher, App\Models\Core\EtapaProyecto, App\Models\Core\ClaseEmprendimiento, App\Models\Seguridad\RolUsuario, App\Models\Seguridad\Rol, App\Models\Seguridad\Usuario;
use Custom\Innpulsa;
use DB;

class Dashboard extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'param.dashboard';

    protected $primaryKey = 'codigo_dashboard';

    public $timestamps = false;

    public static function data(array $filters = [])
    {
        $newData = [];
        $where = "WHERE fecha_registro_usuario >= NOW() - INTERVAL '5 months'";
        $date_range = "WITH date_range AS (SELECT generate_series(date_trunc('month', NOW() - INTERVAL '5 months'),date_trunc('month', NOW()),INTERVAL '1 month') AS x)";
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $where = "WHERE fecha_registro_usuario >= '" . $filters['start_date'] . "' AND fecha_registro_usuario <= '" . $filters['end_date'] . "'";
            $date_range = "WITH date_range AS (SELECT generate_series(date_trunc('month', '" . $filters['start_date'] . "'::date),date_trunc('month', '" . $filters['end_date'] . "'::date),INTERVAL '1 month') AS x)";
        }

        $institution = session('institution')->slug_institucion;

        if ($institution == 'compensar' || $institution == 'demo' || $institution == 'reval') {
          $information = ['usuarios', 'roles', 'registros', 'convocatorias_activas', 'convocatorias_abiertas', 'eventos', 'cursos', 'entidades', 'etapas', 'mentorias', 'mentores', 'articulos', 'discusiones', 'productos'];

          $colors = " SELECT color_primario, color_secundario FROM " . $institution . ".colors";
          $colors = DB::select($colors);

          $newData['colors'] = [
              'data' => [
                  $colors
              ],
          ];

          foreach ($information as $item) {
              switch ($item) {
                  case 'usuarios':
                      $query = $date_range . " SELECT date_range.x AS x, COALESCE(COUNT(eu.codigo_usuario), 0) AS y FROM date_range LEFT JOIN " . $institution . ".estadistica_usuarios eu ON date_trunc('month', eu.fecha_registro_usuario) = date_range.x GROUP BY date_range.x ORDER BY date_range.x ASC";

                      $resultados = DB::select($query);

                      // Crear un array para las etiquetas del eje X (años)
                      $labels = [];
                      // Crear un array para los datos de cada año
                      $data = [];

                      // Crear un array asociativo para agrupar los datos por año
                      $agrupadoPorAnio = [];
                      foreach ($resultados as $resultado) {
                          $fecha = new \DateTime($resultado->x);
                          $year = $fecha->format('Y');

                          if (!isset($agrupadoPorAnio[$year])) {
                              $agrupadoPorAnio[$year] = [];
                              $agrupadoPorAnio[$year]['colors'] = [
                                  $colors[0]->color_primario,
                                  $colors[0]->color_secundario
                              ];
                          }

                          $agrupadoPorAnio[$year][] = [
                              'x' => $resultado->x,
                              'y' => $resultado->y,
                          ];
                      }
                      // Reorganizar los datos en un nuevo array con índices numéricos
                      $agrupadoPorAnioNumerico = array_values($agrupadoPorAnio);

                      $newData['usuarios'] = [
                          'name' => 'Trazabilidad de usuarios por periodo',
                          'type' => 'line',
                          'data' => [
                              $agrupadoPorAnioNumerico
                          ],
                          'labels' => [
                              'Enero',
                              'Febrero',
                              'Marzo',
                              'Abril',
                              'Mayo',
                              'Junio',
                              'Julio',
                              'Agosto',
                              'Septiembre',
                              'Octubre',
                              'Noviembre',
                              'Diciembre'
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#fff475',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;

                  case 'roles':
                      $where_roles = ">= NOW() - INTERVAL '5 months'";
                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where_roles = "BETWEEN '" . $filters['start_date'] . "' AND '" . $filters['end_date'] . "'";
                      }
                      $query = "(
              SELECT name AS x, COUNT(role_id) AS y
              FROM " . $institution . ".estadistica_roles
              WHERE fecha_registro_usuario " . $where_roles . "
              GROUP BY x
          )
          UNION
          (
              SELECT name AS x, 0 AS y
              FROM " . $institution . ".estadistica_roles
              WHERE name NOT IN (
                  SELECT DISTINCT name
                  FROM " . $institution . ".estadistica_roles
                  WHERE fecha_registro_usuario " . $where_roles . "
              )
          )
          ORDER BY x";
                      //dd($query);
                      $resultados = DB::select($query);
                      //dd($query);
                      $roles = [];
                      $count = 0;
                      foreach ($resultados as $rol) {
                          $roles[$count][0] = $rol;
                          $roles[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['roles'] = [
                          'name' => 'Usuarios por rol',
                          'type' => 'bar',
                          'data' => [
                              $roles
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#fff475',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;

                  case 'registros':
                      $where_registros = ">= NOW() - INTERVAL '5 months'";
                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where_registros = "BETWEEN '" . $filters['start_date'] . "' AND '" . $filters['end_date'] . "'";
                      }
                      $query = "
            SELECT
                CASE
                    WHEN codigo_estado = 8 THEN 'Registros Completos'
                    ELSE 'Registros Incompletos'
                END AS x,
                COALESCE(COUNT(codigo_usuario), 0) AS y
            FROM
                " . $institution . ".seguimiento_registros
            WHERE fecha_registro_usuario " . $where_registros . "
            GROUP BY
                x

            UNION

            SELECT
                CASE
                    WHEN codigo_estado = 8 THEN 'Registros Completos'
                    ELSE 'Registros Incompletos'
                END,
                0 AS x
            FROM
                " . $institution . ".seguimiento_registros
            WHERE
                codigo_estado NOT IN (
                    SELECT DISTINCT
                        codigo_estado
                    FROM
                        " . $institution . ".seguimiento_registros
                    WHERE
                        fecha_registro_usuario " . $where_registros . "
                )
            ORDER BY
                x;
            ";
                      //  dd($query);
                      $resultados = DB::select($query);

                      $registros = [];
                      $count = 0;
                      foreach ($resultados as $registro) {
                          $registros[$count][0] = $registro;
                          $registros[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      // dd($registros);
                      $show = false;
                      foreach ($registros as $value) {
                          if ($value[0]->y != 0) {
                              $show = true;
                          }
                      }
                      if (!$show) {
                          if (isset($newData['registros'])) {
                              unset($newData['registros']);
                          }
                          break;
                      }
                      $newData['registros'] = [
                          'name' => 'Registro de usuarios (Completo)',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true
                          ]
                      ];
                      break;
                  case 'convocatorias_activas':
                      $where = "WHERE fecha_creacion_convocatoria >= NOW() - INTERVAL '5 months'";
                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where = "WHERE fecha_creacion_convocatoria >= '" . $filters['start_date'] . "' AND fecha_creacion_convocatoria <= '" . $filters['end_date'] . "'";
                      }
                      $query = "SELECT CASE WHEN codigo_estado = 8 THEN 'Activas' ELSE 'Inactivas' END AS x, COALESCE(COUNT(codigo_convocatoria), 0) AS y  FROM " . $institution . ".estadistica_convocatorias_activas " . $where . " GROUP BY x";
                      //dd($query);
                      $resultados = DB::select($query);
                      // dd(count($resultados));
                      $show = true;
                      if (count($resultados) == 0) {
                          $show = false;
                      }
                      $type = "doughnut";
                      if (!$show) {
                          $type = "bar";
                      }


                      $newData['convocatorias_activas'] = [
                          'name' => 'Convocatorias en estado activo',
                          'type' => $type,
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00'
                      ];
                      break;
                  case 'convocatorias_abiertas':
                      $where = "WHERE fecha_creacion_convocatoria >= NOW() - INTERVAL '5 months'";
                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where = "WHERE fecha_creacion_convocatoria >= '" . $filters['start_date'] . "' AND fecha_creacion_convocatoria <= '" . $filters['end_date'] . "'";
                      }
                      $query = "SELECT SUM(cerrados) AS x, SUM(abiertos) AS y FROM ( SELECT
              CASE
                  WHEN fecha_cierre_convocatoria <= CURRENT_DATE AND NOT permanente_convocatoria THEN 1
                  ELSE 0
              END AS cerrados,
              CASE
                  WHEN fecha_cierre_convocatoria > CURRENT_DATE OR permanente_convocatoria THEN 1
                  ELSE 0
              END AS abiertos
              FROM " . $institution . ".estadistica_convocatorias_abiertas) AS subconsulta";
                      //dd($query);
                      $resultados = DB::select($query);
                      $count = 0;
                      $convocatorias = array(
                          array(
                              'x' => 'Abiertas',
                              'y' => $resultados[0]->y
                          ),
                          array(
                              'x' => 'Cerradas',
                              'y' => $resultados[0]->x
                          )
                      );

                      // dd($convocatorias);
                      $show = false;
                      foreach ($convocatorias as $value) {
                          if ($value["y"] != 0) {
                              $show = true;
                          }
                      }

                      if (!$show) {
                          if (isset($newData['cantidad_convocatorias'])) {
                              unset($newData['cantidad_convocatorias']);
                              break;
                          }
                      }
                      $newData['cantidad_convocatorias'] = [
                          'name' => 'Convocatorias en estado abiertas por periodo de tiempo',
                          'type' => 'doughnut',
                          'data' => [
                              $convocatorias
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00'
                      ];
                      break;

                  case 'convocatorias_abiertas':
                      $where = "WHERE fecha_creacion_convocatoria >= NOW() - INTERVAL '5 months'";
                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where = "WHERE fecha_creacion_convocatoria >= '" . $filters['start_date'] . "' AND fecha_creacion_convocatoria <= '" . $filters['end_date'] . "'";
                      }
                      $query = "SELECT SUM(cerrados) AS x, SUM(abiertos) AS y FROM ( SELECT
              CASE
                  WHEN fecha_cierre_convocatoria <= CURRENT_DATE AND NOT permanente_convocatoria THEN 1
                  ELSE 0
              END AS cerrados,
              CASE
                  WHEN fecha_cierre_convocatoria > CURRENT_DATE OR permanente_convocatoria THEN 1
                  ELSE 0
              END AS abiertos
              FROM " . $institution . ".estadistica_convocatorias_abiertas) AS subconsulta";
                      //dd($query);
                      $resultados = DB::select($query);
                      $count = 0;
                      $convocatorias = array(
                          array(
                              'x' => 'Abiertas',
                              'y' => $resultados[0]->y
                          ),
                          array(
                              'x' => 'Cerradas',
                              'y' => $resultados[0]->x
                          )
                      );
                      $newData['cantidad_convocatorias'] = [
                          'name' => 'Convocatorias en estado abiertas por periodo de tiempo',
                          'type' => 'doughnut',
                          'data' => [
                              $convocatorias
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00'
                      ];
                      break;


                  case 'eventos':
                      $where_eventos = "fecha_inicio_evento >= NOW() - INTERVAL '5 days'";

                      // if (isset($filters['start_date']) && isset($filters['end_date'])) {
                      //   $where_cursos = "fecha_inicio_evento >= '".$filters['start_date']. "' AND fecha_fin_evento <= '".$filters['end_date']. "'";
                      // }

                      $query = "
      SELECT
          'En curso' AS x,
          COALESCE(COUNT(codigo_evento), 0) AS y
      FROM
          " . $institution . ".estadistica_eventos
      WHERE
          fecha_fin_evento >= NOW() - INTERVAL '5 days'

      UNION

      SELECT
          'Vencidos' AS x,
          COALESCE(COUNT(codigo_evento), 0) AS y
      FROM
          " . $institution . ".estadistica_eventos
      WHERE
          fecha_fin_evento < NOW()
      ORDER BY
          x;
  ";



                      $resultados = DB::select($query);

                      // dd($resultados);
                      $eventos = [];
                      $count = 0;
                      foreach ($resultados as $curso) {
                          $eventos[$count][0] = $curso;
                          $eventos[$count]['colors'] = [
                              '#' . bin2hex(random_bytes(3)),
                              '#' . bin2hex(random_bytes(3))
                          ];
                          $count++;
                      }
                      $newData['eventos'] = [
                          'name' => 'Eventos',
                          'type' => 'bar',
                          'data' => [
                              $eventos
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;





                  case 'cursos':
                      $where_cursos = "fecha_apertura_curso >= NOW() - INTERVAL '5 months'";

                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where_cursos = "fecha_apertura_curso >= '" . $filters['start_date'] . "' AND fecha_cierre_curso <= '" . $filters['end_date'] . "'";
                      }

                      $query = "
            SELECT
                CASE
                    WHEN codigo_estado = 8 THEN 'Activos'
                    ELSE 'Inactivos'
                END AS x,
                COALESCE(COUNT(codigo_curso), 0) AS y
            FROM
                " . $institution . ".estadistica_cursos
            WHERE " . $where_cursos . "
            GROUP BY
                x

            UNION

            SELECT
                CASE
                    WHEN codigo_estado = 8 THEN 'Activos'
                    ELSE 'Inactivos'
                END,
                0 AS x
            FROM
                " . $institution . ".estadistica_cursos
            WHERE
                codigo_estado NOT IN (
                    SELECT DISTINCT
                        codigo_estado
                    FROM
                        " . $institution . ".estadistica_cursos
                    WHERE
                        " . $where_cursos . "
                )
            ORDER BY
                x;
            ";



                      $resultados = DB::select($query);
                      //dd($resultados);
                      $cursos = [];
                      $count = 0;
                      foreach ($resultados as $curso) {
                          $cursos[$count][0] = $curso;
                          $cursos[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['cursos'] = [
                          'name' => 'Cursos en estado activo',
                          'type' => 'bar',
                          'data' => [
                              $cursos
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;

                  case 'entidades':
                      $where_entidades = ">= NOW() - INTERVAL '5 months'";

                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where_entidades = "BETWEEN '" . $filters['start_date'] . "' AND '" . $filters['end_date'] . "'";
                      }

                      $query = "
      SELECT
          x,
          SUM(y) AS y
      FROM (
          SELECT
              DISTINCT nombre_clase_emprendimiento AS x,
              COALESCE(COUNT(codigo_entidad), 0) AS y
          FROM
              " . $institution . ".entidades
          WHERE created_at " . $where_entidades . "
          GROUP BY
              x

          UNION

          SELECT
              DISTINCT nombre_clase_emprendimiento,
              0 AS y
          FROM
              " . $institution . ".entidades
          WHERE
              nombre_clase_emprendimiento NOT IN (
                  SELECT DISTINCT
                      nombre_clase_emprendimiento
                  FROM
                      " . $institution . ".estadistica_cursos
                  WHERE created_at " . $where_entidades . "
              )
      ) AS subquery
      GROUP BY
          x
      ORDER BY
          x;
  ";



                      $resultados = DB::select($query);
                      //  dd($resultados);
                      $entidades = [];
                      $count = 0;
                      foreach ($resultados as $entidad) {
                          $entidades[$count][0] = $entidad;
                          $entidades[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }

                      $show = false;
                      foreach ($resultados as $value) {
                          if ($value->y != 0) {
                              $show = true;
                          }
                      }

                      if (!$show) {
                          if (isset($newData['entidades'])) {
                              unset($newData['entidades']);
                          }
                          break;
                      }

                      $newData['entidades'] = [
                          'name' => 'Entidades registradas',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;



                  case 'etapas':

                      $query = "
        SELECT
            nombre_etapa_proyecto AS x,
          COALESCE(COUNT(codigo_etapa_proyecto), 0) AS y
          FROM
          " . $institution . ".estadistica_etapas
          GROUP BY
          x;
    ";



                      $resultados = DB::select($query);
                      // dd($resultados);
                      $etapas = [];
                      $count = 0;
                      foreach ($resultados as $entidad) {
                          $etapas[$count][0] = $entidad;
                          $etapas[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['etapas'] = [
                          'name' => 'Etapas',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;

                  case 'mentorias':

                      $query = "
          SELECT
              nombre_estado AS x,
            COALESCE(COUNT(codigo_asesoria), 0) AS y
            FROM
            " . $institution . ".estadistica_mentorias
            GROUP BY
            x;
      ";



                      $resultados = DB::select($query);
                      // dd($resultados);
                      $mentorias = [];
                      $count = 0;
                      foreach ($resultados as $entidad) {
                          $mentorias[$count][0] = $entidad;
                          $mentorias[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['mentorias'] = [
                          'name' => 'Proceso de mentoría y/o acompañamiento',
                          'type' => 'bar',
                          'data' => [
                              $mentorias
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;


                  case 'mentores':
                      $where_entidades = ">= NOW() - INTERVAL '5 months'";

                      if (isset($filters['start_date']) && isset($filters['end_date'])) {
                          $where_entidades = "BETWEEN '" . $filters['start_date'] . "' AND '" . $filters['end_date'] . "'";
                      }

                      $query = "
                      SELECT
                      codigo_estado AS x,
                  COALESCE(COUNT(codigo_usuario), 0) AS y
                  FROM
                  " . $institution . ".estadistica_mentores
                  GROUP BY
                  x;
            ";



                      $resultados = DB::select($query);
                      // dd($resultados);
                      $mentorias = [];
                      $count = 0;
                      foreach ($resultados as $entidad) {
                          $mentorias[$count][0] = $entidad;
                          $mentorias[$count][0]->x = Estado::getNameState($mentorias[$count][0]->x);
                          $mentorias[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['mentores'] = [
                          'name' => 'Mentores por estado',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,
                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;



                  case 'articulos':

                      $query = "
              SELECT
                  nombre_estado AS x,
                COALESCE(COUNT(codigo_contenido), 0) AS y
                FROM
                " . $institution . ".estadistica_articulos
                GROUP BY
                x;
          ";



                      $resultados = DB::select($query);
                      // dd($resultados);
                      $articulos = [];
                      $count = 0;
                      foreach ($resultados as $entidad) {
                          $articulos[$count][0] = $entidad;
                          $articulos[$count]['colors'] = [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ];
                          $count++;
                      }
                      $newData['articulos'] = [
                          'name' => 'Articulos publicados',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'indexAxis' => 'y',
                              'responsive' => true,
                              'plugins' => [
                                  'legend' => [
                                      'position' => 'right',
                                  ],
                              ],

                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;



                  case 'productos':

                      $query = "
                SELECT
                    nombre_estado AS x,
                  COALESCE(COUNT(codigo_producto), 0) AS y
                  FROM
                  " . $institution . ".estadistica_productos
                  GROUP BY
                  x;
            ";



                      $resultados = DB::select($query);

                      $newData['productos'] = [
                          'name' => 'Productos',
                          'type' => 'doughnut',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'indexAxis' => 'y',
                              'responsive' => true,
                              'plugins' => [
                                  'legend' => [
                                      'position' => 'right',
                                  ],
                              ],

                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;


                  case 'discusiones':

                      $query = "
                  SELECT
                      nombre_modulo AS x,
                    COALESCE(COUNT(codigo_discucion), 0) AS y
                    FROM
                    " . $institution . ".estadistica_discusiones
                    GROUP BY
                    x;
              ";



                      $resultados = DB::select($query);

                      $newData['discusiones'] = [
                          'name' => 'Discusiones',
                          'type' => 'bar',
                          'data' => [
                              $resultados
                          ],
                          'colors' => [
                              $colors[0]->color_primario,
                              $colors[0]->color_secundario
                          ],
                          'border' => '#ffffff00',
                          'options' => [
                              'responsive' => true,

                              'interaction' => [
                                  'mode' => 'index',
                                  'intersect' => false
                              ]
                          ]
                      ];
                      break;

                  default:
                      # code...
                      break;
              }
          }
        }
        return $newData;
    }
}
