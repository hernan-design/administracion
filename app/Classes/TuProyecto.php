<?php

class TuProyecto
{
	public static function encryptSrcParams($data)
	{
		foreach($data as $index => $value) {
			$data[$index] = Crypt::encrypt($value);
	    }
	    return implode(Config::get('tuproyecto.src_params_separate'), $data);
	}

	public static function decryptSrcParams($data = null)
	{
		if($data != null) {
			$data = explode(Config::get('tuproyecto.src_params_separate'), $data);
			foreach($data as $index => $value) {
				$data[$index] = Crypt::decrypt($value);
			}
		}
		return $data;
	}

	/**
	  * Return name user string
	  * @param string $string
	  * @return string
	  */
	public static function unwantedCharacters($string)
	{
		$string = trim($string);
		// Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
	    $string = strtolower($string);
	    // Strip accents
		$string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	    // Strip any unwanted characters
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    return $string;
	}

  public static function dateFormat($date = NULL, $type = 'large')
  {
    if($date === NULL) {
      return;
    }

    $date = strtotime($date);
    $month = strtolower(jdmonthname(gregoriantojd(date('m',$date), 1, 1), CAL_MONTH_GREGORIAN_LONG));
    $day_week = strtolower(jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m",$date),date("d",$date), date("Y",$date)) , 1 ));
    $day_month = strftime('%e',strtotime($date));

    switch ($type) {
      case 'short':
        $date_format = "%s/%d";
        return sprintf($date_format, substr(strtoupper(Lang::get("app.$month")) ,0, 3), date('d',$date));
      case 'large':
        $date_format = "%d-%s-%d";
        return sprintf($date_format, date('d',$date), substr(Lang::get("app.$month") ,0, 3), date('y',$date));
      case 'complete':
        $date_format = "%s, %s %d, %d";
        return sprintf($date_format, substr(Lang::get("app.$day_week") ,0), substr(Lang::get("app.$month") ,0), date('d',$date), date('Y',$date));
      case 'daymonthnameyear':
        $date_format = "%d de %s, %d";
        return sprintf($date_format, date('d',$date),  substr(Lang::get("app.$month") ,0), date('Y',$date));
      case 'month':
        $date_format = "%s %s";
        return sprintf($date_format, date('Y',$date), Lang::get("app.$month"));
      case 'day':
        $date_format = "%s";
        return sprintf($date_format, mb_substr(Lang::get("app.$day_week") ,0, 3));
      default: return $date;
    }
  }

  public static function timeFormat($time = NULL)
  {
	  if($time === NULL) {
		  return;
	  };
	  return sprintf(date("g:i A", strtotime($time)));
	}

	public static function getPastYears($data)
	{
	    $anios = [];
	    for($i = date('Y'); $i >= (date('Y') - $data); $i--){
	        $anios[$i] = $i;
	    }
	    return $anios;
	}

	public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

		$exp = floor(log($bytes, 1024)) | 0;
        return round($bytes / (pow(1024, $exp)), 1) . ' ' . $units[$pow];
    }
}
