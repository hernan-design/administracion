<div class="btn-group tp_btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-right" role="menu">
        <% if(full.acuerdo_producto){ %>
            <li><a href="<%- window.Misc.urlFull(Route.route('marketplace.transacciones.acuerdo.show', {'acuerdo': full.codigo_acuerdo_compra})) %>" class="btn-agreement"><i class="fa fa-file-text-o"></i> Acuerdo</a>
            </li>
        <% } %>
        <li><a href="<%- window.Misc.urlFull(Route.route('marketplace.transacciones.evaluacion.show', {'evaluacion': full.codigo_compra})) %>" class="btn-evaluation"><i class="fa fa-file-text"></i> Evaluación</a>
        </li>
    </ul>
</div>