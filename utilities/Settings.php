<?php

namespace app\utilities;

class Settings
{
	/**
	 * Returns a setting from the settings.json file.
	 *
	 * @param string[] ...$keys
	 * @return mixed|null
	 */
	public static function get(string ...$keys)
	{
		$settings = json_decode(file_get_contents(__DIR__ . "/../config/settings.json"));
		foreach($keys as $key)
		{
			$settings = $settings->{$key} ?? NULL;
			if($settings === NULL)
			{
				return $settings;
			}
		}
		return $settings;
	}
}