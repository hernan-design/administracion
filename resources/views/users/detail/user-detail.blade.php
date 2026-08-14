@extends('users.user')

@section('users.content')
  <div class="col-xs-12">
    <div class="x_panel">
      <div class="x_content" id="section-content">
        <div class="" role="tabpanel" data-example-id="togglable-tabs">
          <div class="x_title title">
            <ul class="nav navbar-right panel_toolbox">
              <li>
                <a href="{{ route('usuario.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
              </li>
            </ul>
          </div>
          <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="events" class="active"><a href="#tab_events" id="events-tab" role="tab" data-toggle="tab" aria-expanded="true"> Eventos </a>
            </li>
            <li role="program"><a href="#tab_program" role="tab" id="program-tab" data-toggle="tab" aria-expanded="false"> Convocatorias </a>
            </li>
            <li role="curses"><a href="#tab_channels" role="tab" id="channel-tab" data-toggle="tab" aria-expanded="false"> Cursos </a>
            </li>
            <li role="asing_mentor"><a href="#tab_asing_mentor" role="tab" id="asing-mentor-users-tab" data-toggle="tab" aria-expanded="false"> Asignación de mentor y el proyecto </a>
            </li>
            <li role="asign_project"><a href="#tab_asing_project" id="asing-project-users-tab" role="tab" data-toggle="tab" aria-expanded="true"> Asignación de evaluador y el proyecto </a>
            </li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <div role="events" class="tab-pane fade active in" id="tab_events" aria-labelledby="events-tab">
              <div class="well">
                <div class="row">
                  <div class="col-md-7 col-md-offset-4 ">
                    <a
                      href="{{ route('eventos.usuario.index', ['codigo_usuario' => $codigo_usuario]) }}"
                      class="btn btn-success btn-create"
                    >
                      Ir a Eventos por usuario
                    </a>
                  </div>
                </div>
              </div>
              <div class="well">
                <div class="row">
                 <ul>
                   @if(isset($activity_events) && !empty($activity_events) && count($activity_events) > 0)
                     @foreach($activity_events as $events)
                       <li>
                         <div class="block_content">
                           {!! $events->description !!}, <cite>{{  TuProyecto::dateFormat($events->created_at, 'complete') }}</cite>
                         </div>
                       </li>
                     @endforeach
                   @else
                     <p> Sin resultados </p>
                   @endif
                 </ul>
                </div>
              </div>
            </div>
            <div role="program" class="tab-pane fade" id="tab_program" aria-labelledby="program-tab">
              <div class="well">
                <div class="row">
                  <ul>
                    @if(isset($activity_call) && !empty($activity_call) && count($activity_call) > 0)
                      @foreach($activity_call as $call)
                        <li>
                          <div class="block_content">
                            {!! $call->description !!} por <a class="blue" href="{{ route('usuario.show', ['id' => $call->codigo_usuario]) }}">{{ $call->nombre_usuario }}</a>, <cite>{{  TuProyecto::dateFormat($call->created_at, 'complete') }}</cite>
                          </div>
                        </li>
                      @endforeach
                    @else
                      <p> Sin resultados </p>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
            <div role="curses" class="tab-pane fade" id="tab_channels" aria-labelledby="channel-tab">
              <div class="well">
                <div class="row">
                  <ul>
                    @if(isset($activity_curses) && !empty($activity_curses) && count($activity_curses) > 0)
                      @foreach($activity_curses as $curses)
                        <li>
                          <div class="block_content">
                            {!! $curses->description !!}, <cite>{{  TuProyecto::dateFormat($curses->created_at, 'complete') }}</cite>
                          </div>
                        </li>
                      @endforeach
                    @else
                      <p> Sin resultados </p>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
            <div role="asing_mentor" class="tab-pane fade" id="tab_asing_mentor" aria-labelledby="asing-mentor-users-tab">
              <div class="well">
                <div class="row">
                  <ul>
                    @if(isset($activity_asing_mentor_project) && !empty($activity_asing_mentor_project) && count($activity_asing_mentor_project) > 0)
                      @foreach($activity_asing_mentor_project as $asing_mentor_project)
                        <li>
                          <div class="block_content">
                            {!! $asing_mentor_project->description !!},  <cite>{{  TuProyecto::dateFormat($asing_mentor_project->created_at, 'complete') }}</cite>
                          </div>
                        </li>
                      @endforeach
                    @else
                      <p> Sin resultados </p>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
            <div role="asign_project" class="tab-pane fade" id="tab_asing_project" aria-labelledby="asing-project-users-tab">
              <div class="well">
                <div class="row">
                  <ul>
                    @if(isset($activity_asing_judge_project) && !empty($activity_asing_judge_project) && count($activity_asing_judge_project) > 0)
                      @foreach($activity_asing_judge_project as $asing_judge_project)
                        <li>
                          <div class="block_content">
                            {!! $asing_judge_project->description !!},  <cite>{{  TuProyecto::dateFormat($asing_judge_project->created_at, 'complete') }}</cite>
                          </div>
                        </li>
                      @endforeach
                    @else
                      <p> Sin resultados </p>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
