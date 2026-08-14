<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		@php
			$bgcolor_inst = $_inst_color;
			$color_white = '#ffffff';
			$font_color = '#000000';
			$font_weigth = 'normal';
			$font = "'Lucida Sans Unicode', 'Lucida Grande', sans-serif";
			$font_family = "font-family:$font;";
			$font_size_text = "font-size:13.5px;";
			$font_size_text_2 = "font-size:18px;";
			$font_size_text_3 = "font-size:12px;";
			$font_weight_light = "font-weight:lighter;";
			$font_color_white = "color:$color_white;";
			$color_inst_alt = $bgcolor_inst; // #ff5656
			$bgcolor_inst_alt = "background-color:$color_inst_alt;";
			$font_color_inst_alt = "color:$color_inst_alt;";
			$color_inst_alt_dark = $bgcolor_inst; // #f23d3d
			$gray_1 = "#f7f7f7";
			$gray_2 = "#ededed";
			$gray_3 = "#727272";
			$gray_4 = "#3a3a3a";
			$text_color_1 = "color:$gray_4;";
			$text_color_2 = "color:$gray_3";
			$bg_gray_1 = "background-color:$gray_1;";
			$header_border_top = "border-top:7px solid $bgcolor_inst;";
			$borders = "border-left:1px solid $gray_2;border-right:1px solid $gray_2;";
			$border_bottom = "border-bottom:1px solid $gray_2;";
			$padding_10 = "padding:10px;";
			$table_wrap_width = '100%';
			$table_wrap_height = '100%';
			$table_wrap_style = "background-color:#ffffff";
			$table_inner_width = 600;
			$table_inner_border = 0;
			$table_inner_cellspacing = 0;
			$table_inner_cellpadding = 0;
			$table_inner_align = 'center';
			$table_inner_width_content = 520;
			$table_msg_style = "$font_family font-size: 9px; padding: 9px 7px;";
			$table_content_style = "background-color:$color_white; $font_color_white $font-family $font_size_text $font_weight_light padding:54px 38px 63px;";
			$table_footer_style_inst = "background-color:$bgcolor_inst; $font_color_white $font_family font-size:11px; font-weight:normal";
		@endphp

		<table border="0" cellspacing="0" cellpadding="0" height="{{ $table_wrap_height }}" width="{{ $table_wrap_width }}">
	        <tr>
	        	<td align="{{ $table_inner_align }}" valign="top">

	                {{-- header --}}
	                <table border="0" cellpadding="10" cellspacing="0" width="{{ $table_inner_width }}"; id="templatePreheader">
	                	{{-- header logo --}}
	                    <tr>
	                        <td align="{{ $table_inner_align }}" valign="top" class="header-logo" style="{{ $header_border_top }}{{ $borders }}">
	                            <table border="0" cellspacing="0" cellpadding="0" width="{{ $table_inner_width_content }}";>
	                            	<tr>
	                            		<td valign="top">
	                                    	<div>
	                                    		<div style="text-align:center; padding: 10px 0;">
	                                    			<span>
        												<a href="{{{ $_inst_url }}}" title="{{ $_inst_name }}">
	                                    					<img align="{{ $table_inner_align }}" id="logo-reb-header" src="{{ $_inst_img }}" style="max-width: 600px; height: auto;">
        												</a>
	                                    			</span>
	                                    		</div>
											</div>
	                                	</td>
	                                </tr>
	                            </table>
	                        </td>
	                    </tr>
	                </table>
	                {{-- header end --}}

	                {{-- content --}}
	            	<table border="0" cellspacing="0" cellpadding="0" width="{{ $table_inner_width }}";>
						@if( isset($_to_name) )
	            		<tr>
	                    	<td align="{{ $table_inner_align }}" valign="top" style="{{ $bg_gray_1 }} {{ $borders }}">
	                        	<table border="0" cellpadding="0" cellspacing="0" width="{{ $table_inner_width_content }}">
	                            	<tr>
	                                    <td valign="top">
	                                        <table border="0" cellspacing="0" width="100%">
	                                            <tr>
	                                                <td valign="top">
	                                                    <div>
	                                                    	<div style="text-align:center; margin-top: 15px;">
	                                                    		<span style="{{ $font_family }} {{ $font_size_text }} {{ $text_color_2 }}">{{ Lang::get('app.hello') }}</span>
	                                                    		<span style="{{ $font_family }} {{ $font_size_text }} {{ $text_color_1 }}">{{ trim($_to_name) }}:</span><br><br>
	                                                    	</div>
	                                                    </div>
													</td>
	                                        	</tr>
	                                    	</table>
	                                	</td>
	                            	</tr>
	                        	</table>
	                    	</td>
	                	</tr>
	                	@endif

	                	<tr>
	                    	<td align="{{ $table_inner_align }}" valign="top" style="{{ $bg_gray_1 }} {{ $borders }}">
	                        	<table border="0" cellspacing="0" cellpadding="0" width="440" style="{{ $font_family }} {{ $font_size_text }} {{ $text_color_2 }}">
	                                <tr>
	                                	<td>
	                                		@yield('content')
	                                	</td>
	                                </tr>
	                            </table>
	                        </td>
	                    </tr>
						
						<tr>
	                    	<td align="{{ $table_inner_align }}" valign="top" style="{{ $bg_gray_1 }} {{ $borders }}">
	                        	<table border="0" cellspacing="0" cellpadding="0" width="90%" style="{{ $font_family }} {{ $font_size_text_3 }} {{ $text_color_2 }}">
	                                <tr>
	                                	<td>
											{!! transtp('text_security.email', [], $_inst_slug) !!}
	                                	</td>
	                                </tr>
	                            </table>
	                        </td>
	                    </tr>

	                	{{-- end block --}}
						<tr>
	                    	<td align="{{ $table_inner_align }}" valign="top" style="{{ $bg_gray_1 }} {{ $borders }} {{ $border_bottom }}">
								<table>
									<tr>
				                    	<td align="{{ $table_inner_align }}" valign="top" style="{{ $bg_gray_1 }}">
				                        	<table border="0" cellpadding="0" cellspacing="0" width="{{ $table_inner_width_content }}">
				                            	<tr>
				                                    <td valign="top">
				                                        <table border="0" cellpadding="15" cellspacing="0" width="100%">
				                                            <tr>
				                                                <td valign="top">
				                                                    <div>
				                                                    	<div style="text-align:center;">
				                                                    		<span style="{{ $font_family }} {{ $font_size_text }} {{ $text_color_2 }}">Gracias,</span>
				                                                    		<span style="{{ $font_family }} {{ $font_size_text }} {{ $text_color_1 }}">{{{ $_inst_name }}}.</span>
				                                                    	</div>
				                                                    </div>
																</td>
				                                        	</tr>
				                                    	</table>
				                                	</td>
				                            	</tr>
				                        	</table>
				                    	</td>
				                	</tr>
								</table>
	                        </td>
	                    </tr>
	            	</table>
	            </td>
	        </tr>
		</table>

	</body>
</html>
