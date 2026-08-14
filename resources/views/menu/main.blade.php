@extends('menu.menu')

@section('menu.content')
	{{-- ------------------ Menu Templates JS -------------------- --}}
	{{-- template menu aside filter --}}
	<script type="text/template" id="menu-list-tpl">
	    <div id="content-menu-list">
	      <div class="row">
	        <div class="small-12 columns">
	          <div class="row">
	            <div class="col-md-6 col-sm-6 col-xs-12">
	              <div class="x_panel">
	                <div class="x_title">
	                  <h2>Menu <small>Plataforma</small></h2>
	                  <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                  <div class="dashboard-widget-content">
	                      <div class="validation-errors" role="alert"></div>
	                      <ul id='menu-list-platform' class="list-unstyled timeline widget">
	                        <!-- Render menu list -->
	                      </ul>
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="col-md-6 col-sm-6 col-xs-12">
	              <div class="x_panel">
	                <div class="x_title">
	                  <h2>Menu <small>Sitemap</small></h2>
	                    <ul class="nav navbar-right panel_toolbox">
	                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
	                      <li class="dropdown">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                        <ul class="dropdown-menu" role="menu">
	                          <li><a href="#">Settings 1</a>
	                          </li>
	                          <li><a href="#">Settings 2</a>
	                          </li>
	                        </ul>
	                      </li>
	                      <li><a class="close-link"><i class="fa fa-close"></i></a></li>
	                    </ul>
	                  <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                  <div class="dashboard-widget-content">
	                      <ul id='menu-list-sitemap' class="list-unstyled timeline widget">
	                        <!-- Render menu list -->
	                      </ul>
	                  </div>
	                </div>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	</script>
    {{-- item menu list --}}
	<script type="text/template" id="menu-item-list-tpl">
		<%- nombre_item_menu %> <a href="#" class="menu-post-hide" title="Remover Menu">×</a>
	</script>
	{{-- item menu list --}}
	<script type="text/template" id="menu-item-sitemap-list-tpl">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="headingTwo">
		      <h4 class="panel-title">
		        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#<%- codigo_menu_institucion %>" aria-expanded="false" aria-controls="<%- nombre_item_menu %>">
		          <%- nombre_item_menu %>
		        </a>
		      </h4>
		    </div>
		    <div id="<%- codigo_menu_institucion %>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
		      <div class="panel-body">
		        Anim pariatur cliche
		      </div>
		    </div>
		  </div>
		</div>
	</script>

	<script type="text/template" id="menu-confirm-hide-tpl">
	  <p>{{ Lang::get('menu.remove.confirm') }}</p>
	</script>

@stop
