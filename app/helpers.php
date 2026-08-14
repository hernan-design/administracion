<?php

use App\Classes\ActivityLogger;

/**
 * Get the path to a versioned Elixir file.
 *
 * @param  string  $file
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function elixir($file, $buildPath = 'build/')
{
    $manifest = null;

    if (is_null($manifest)) {
        $manifest = json_decode(file_get_contents(public_path($buildPath .'rev-manifest.json')), true);
    }

    if (isset($manifest[$file])) {
        return url($buildPath . $manifest[$file]);
    }

    throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
}

function transtp($keyname, $parameters = [], $institution = null, $domain = 'messages', $locale = null)
{
	$localKey = sprintf('%s.%s', ($institution ?: session('institution')->slug_institucion), $keyname);
	$value = trans($localKey, $parameters, $domain, $locale);
	if($value != $localKey) {
		return $value;
	}

	return trans(sprintf('tuproyecto.%s', $keyname), $parameters, $domain, $locale);
}

function after($string, $inthat)
{
    if (!is_bool(strpos($inthat, $string)))
    return substr($inthat, strpos($inthat,$string) + strlen($string));
}

/**
 * Get URL friendly "slug" from the given string
 *
 * @param  string  $string
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function slugtp($string, $table, $field, $institution = false, $contador = 0)
{
	$string = str_slug($string, "-");
	$slug = $contador > 0 ? sprintf("%s-%d", $string, $contador) : $string;

	$query = DB::table($table);
	$query->whereRaw("trim(lower($field)) = trim(lower('$slug'))");
	if($institution) {
		$query->where("codigo_institucion", session('institution')->codigo_institucion);
	}
	$object = $query->first();

	if(!is_null($object)) {
		$contador ++;
		$slug = slugtp($string, $table, $field, $institution, $contador);
	}

	return $slug;
}

function urlPlatform($uri)
{
	$url = session('institution')->url_institucion;
	return $url.$uri;
}

if (! function_exists('activity')) {
    function activity(string $logName = null): ActivityLogger
    {
        $defaultLogName = config('activitylog.default_log_name');

        return app(ActivityLogger::class)->useLog($logName ?? $defaultLogName);
    }
}
