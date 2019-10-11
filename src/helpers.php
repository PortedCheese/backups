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

if (! function_exists("backup_storage_path")) {
    /**
     * Get the path to the storage for backup folder.
     *
     * @param  string  $path
     * @return string
     */
    function backup_storage_path($path = '')
    {
        $folder = DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."public";
        return app('path.storage').($path ? $folder.DIRECTORY_SEPARATOR.$path : $folder);
    }
}

if (! function_exists("backup_archive_path")) {
    /**
     * Get the path to the storage for backup folder.
     *
     * @param  string  $path
     * @return string
     */
    function backup_archive_path($path = '')
    {
        $folder = DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."backups".DIRECTORY_SEPARATOR."archive";
        return app('path.storage').($path ? $folder.DIRECTORY_SEPARATOR.$path : $folder);
    }
}
