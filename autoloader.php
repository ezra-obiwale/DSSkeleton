<?php

foreach ($moduleAutoload as $module => $auto) {
	if (isset($auto['files'])) {
		foreach ($auto['files'] as $file) {
			if (!is_readable(MODULES . $module . DIRECTORY_SEPARATOR . $file))
					throw new \Exception('File "' . MODULES . $module . DIRECTORY_SEPARATOR . $file . '" in ' . $module . '/Config/local.php cannot be loaded. Are you sure the file exists?');

			require_once MODULES . $module . DIRECTORY_SEPARATOR . $file;
		}
	}

	if (isset($auto['dirs'])) {
		foreach ($auto['dirs'] as $dir) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $dir);
			try {
				$handle = opendir(MODULES . $module . DIRECTORY_SEPARATOR . $dir);
				while ($current = readdir($handle)) {
					if (in_array($current, array('.', '..'))) continue;

					if (!is_readable(MODULES . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $current))
							continue;

					if (!in_array(MODULES . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $current, get_included_files()))
							require_once MODULES . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $current;
				}
			}
			catch (\Exception $ex) {
				throw new \Exception($ex->getMessage());
			}
		}
	}
}

require_once 'vendor/autoload.php';
