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
        return app('path.storage').($path ? DIRECTORY_SEPARATOR."app/backups/$path" : "app/backups");
    }
}
