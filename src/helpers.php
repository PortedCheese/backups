<?php

if (! function_exists('backup_path')) {
    /**
     * Get the path to the backup folder.
     *
     * @param  string  $path
     * @return string
     */
    function backup_path($path = '')
    {
        $folder = DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."backups";
        return app('path.storage').($path ? $folder.DIRECTORY_SEPARATOR.$path : $folder);
    }
}
