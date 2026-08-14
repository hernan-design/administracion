{{-- views/dashboard/index.blade.php --}}
@extends('dashboard.dashboard')

@section('dashboard.content')
	<div class="row tp_row-content">
		<div class="row">
			<div id="dashboard-list" class="hidden" style="width: 100%; margin:30px">
        <div id="filters" style="display: block; margin-bottom:10px;">
          <div id="div_start_filter"  style="display:inline-block; width:30%;">
            <label for="start_date">Fecha de inicio</label>
            <input type="date" class="form-control" name="start_filter" id="start_filter">
          </div>
          <div id="div_end_filter"  style="display:inline-block; width:30%;">
            <label for="start_date">Fecha fin</label>
            <input type="date" class="form-control" name="end_filter" id="end_filter">
          </div>


          <button id="send_filter" class="btn btn-warning" style="display:inline-block;">Filtrar</button>
          <span style="display:block;">*La información de las estadisticas es actualizada diariamente, por defecto se muestran la información de los ultimos 5 meses</span>
        </div>
        <div class="container container-dashboard" style="width: 90%">
				{{-- Render template id="dashboard-list-tpl" --}}
          <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_usuarios" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px; height: 95%;">
                <h5 class="usuarios" style="color: #000;font-weight: bold;font-size: 1.2vw;font-size: 1.2vw;">Trazabilidad de usuarios por periodo</h5>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_roles" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px; height: 95%;">
                <h5 class="roles" style="color: #000;font-weight: bold;font-size: 1.2vw;">Usuarios por rol</h5>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_registros" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px; height: 95%;">
                <div class="registros_completos">
                  <h5 class="registros" style="color: #000;font-weight: bold;font-size: 1.2vw;">Registro de usuarios (Completo)</h5>
                  <img src="https://s3.us-west-2.amazonaws.com/static-dev.tuproyecto.com/images/analitica/usuarios.png" alt="" style="
                  width: 50%;">
                  <div class="values" style="
                  display: inline;">
                    <span class="qty" style="
                    font-size: 3vw;
                    color: #000;
                    font-weight: bold;
                ">0</span>
                    <span class="accuary" style="
                    font-size: 1vw;
                ">(0%)</span>
                  </div>

                </div>

                <div class="registros_incompletos">
                  <h5 class="registros" style="color: #000;font-weight: bold;font-size: 1.2vw;">Registro de usuarios (Incompleto)</h5>
                  <img src="https://s3.us-west-2.amazonaws.com/static-dev.tuproyecto.com/images/analitica/usuarios.png" alt="" style="
                  width: 50%;">
                  <div class="values" style="
                  display: inline; position: static;">
                    <span class="qty" style="
                    font-size: 3vw;
                    color: #000;
                    font-weight: bold;
                ">0</span>
                    <span class="accuary" style="
                    font-size: 1vw;
                ">(0%)</span>
                  </div>
                </div>


              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_convocatorias_activas" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;height: 98%;">
                <h5 class="convocatorias_activas" style="color: #000;font-weight: bold;font-size: 1.2vw;">Convocatorias en estado activo</h5>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_cantidad_convocatorias" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;height: 98%;">
                <h5 class="cantidad_convocatorias" style="color: #000;font-weight: bold;font-size: 1.2vw;">Convocatorias en estado abiertas por periodo de tiempo</h5>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_eventos" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;height: 98%;">
                <h5 class="eventos" style="color: #000;font-weight: bold;font-size: 1.2vw;">Eventos</h5>
              </div>
            </div>
            {{-- <div class="col-12 col-sm-6 col-md-6 col-lg-4">
              <div class="container_cursos" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px;">
                <h5 class="cursos" style="color: #000;font-weight: bold;">Cursos en estado activo</h5>
              </div>
            </div> --}}
          </div>
          <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
              <div class="container_cursos_activos" style="margin: 5px;border: none;padding: 10px;border-radius: 10px;background: #dcd7d3;text-align: center;">
                <h5 class="cursos" style="color: #000;font-weight: bold;font-size: 1.2vw;">Cursos en estado activo</h5>
                <span class="cursos_activos" style="font-size: 2vw;
                color: #000;
                font-weight: bold;"></span>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
              <div class="container_cursos_inactivos" style="margin: 5px;border: none;padding: 10px;border-radius: 10px;background: #dcd7d3;text-align: center;">
                <h5 class="cursos" style="color: #000;font-weight: bold;font-size: 1.2vw;">Cursos en estado inactivo</h5>
                <span class="cursos_inactivos" style="font-size: 2vw;
                color: #000;
                font-weight: bold;"></span>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
              <div class="container_entidades_empresas" style="margin: 5px;border: none;padding: 10px;border-radius: 10px;background: #dcd7d3;text-align: center;">
                <h5 class="empresas" style="color: #000;font-weight: bold; font-size: 1.2vw;font-size: 1.2vw;">Empresas registradas</h5>
                <span class="empresas_registradas" style="font-size: 2vw;
                color: #000;
                font-weight: bold;">0</span>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
              <div class="container_entidades_proyectos" style="margin: 5px;border: none;padding: 10px;border-radius: 10px;background: #dcd7d3;text-align: center;">
                <h5 class="proyectos" style="color: #000;font-weight: bold;font-size: 1.2vw;">proyectos registrados</h5>
                <span class="proyectos_registrados" style="font-size: 2vw;
                color: #000;
                font-weight: bold;">0</span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
              <div class="container_articulos" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;">
                <h5 class="articulos" style="color: #000;font-weight: bold;font-size: 1.2vw;">Articulos Publicados</h5>
                <p style="
                color: #464647;
            ">Representación de los artículos publicados en la plataforma por estado <b>(Esta información es general)</b></p>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
              <div class="container_etapas" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;">
                <h5 class="etapas" style="color: #000;font-weight: bold;font-size: 1.2vw;">Etapa</h5>
                <p style="
                color: #464647;
            ">Representación de las distintias etapas en las que registra un proyecto o empresa <b>(Esta información es general)</b></p>
              </div>
            </div>
          </div>



          <div class="row">
            <div class="products-block" style="
            border: aliceblue;
            width: 100%;
            margin: 5px;
            border: 0.2px solid #8989894f;
            padding: 10px;
            border-radius: 10px;
        ">
              <h5 class="cursos" style="/*  */display: block;color: #000;width: 100%;font-weight: bold;padding-left: 10px;font-size: 1.2vw;">Productos y/o servicios (Marketplace)</h5>
              <p style="
                color: #464647;
            "><b>(Esta información es general)</b></p>
            </div>
          </div>

          <div class="row">

            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
              <div class="container_mentorias" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;">
                <h5 class="mentorias" style="color: #000;font-weight: bold;font-size: 1.2vw;">Proceso de mentoría y/o acompañamiento</h5>
                <p style="
                color: #464647;
            "><b>(Esta información es general)</b></p>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
              <div class="container_mentores" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;">
                <h5 class="mentores" style="color: #000;font-weight: bold;font-size: 1.2vw;">Mentores por estado</h5>
                <p style="
                color: #464647;
            "><b>(Esta información es general)</b></p>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
              <div class="container_discusiones" style="margin: 5px;border: 0.2px solid #8989894f;padding: 10px; border-radius: 10px;">
                <h5 class="discusiones" style="color: #000;font-weight: bold;font-size: 1.2vw;">Discusiones</h5>
                <p style="
                color: #464647;
            ">Comentarios realizados en cada módulo de la plataforma <b>(Esta información es general)</b></p>
              </div>
            </div>
          </div>


















        </div>
			</div>
		</div>
	</div>

	<script type="text/template" id="dashboard-list-tpl">
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div id="echart_<%- elementId %>" style="height:350px;"></div>
		</div>
	</script>
@stop

