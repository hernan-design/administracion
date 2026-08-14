@extends('users.user')

@section('users.content')

    <div class="col-xs-12">
        <div class="x_panel content-users">
            <div class="x_content">
                <div class="x_title">
                    <h2>Usuario</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{route('usuario.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="well">
                    <div id="create-user">
                        {{-- Render template id="create-user-tpl" --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop