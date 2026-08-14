@php
	$color_inst_alt = $color;
	$color_inst_alt_dark = $color_inst_alt;

	$font = "'Lucida Sans Unicode', 'Lucida Grande', sans-serif";
	$font_family = "font-family:$font;";
	$font_size_text = "font-size:13.5px;";
	$font_weight_light = "font-weight:lighter;";
	$font_color_white = "color:#ffffff;";
@endphp
<table align="center" border="0" cellpadding="0" cellspacing="0" width="520">
	<tr>
		<td valign="top" width="0" style="padding:10px;">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" style="min-width:100%;">
		        <tr>
		            <td style="padding:10px;" valign="top" align="center">
		            	<table border="0" cellspacing="0" cellpadding="0" style="background-color:{{ $color_inst_alt }};border-radius: 3px;border-right:2px solid {{ $color_inst_alt_dark }};border-bottom:2px solid {{ $color_inst_alt_dark }};">
							<tr>
								<td align="center" style="{{ $font_family }} {{ $font_size_text }} padding:10px;" valign="middle">
									<span style="{{ $font_family }}">
										<a href="{{ $action }}" style="{{ $font_weight_light }} {{ $font_color_white }} text-decoration:none;" target="_blank">{{ $label }}</a>
									</span>
								</td>
							</tr>
						</table>
					</td>
		        </tr>
			</table>
		</td>
	</tr>
</table>
