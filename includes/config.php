<?php
/**
 * Returns the web-root path for this app (e.g. /medicine-store)
 */
function basePath() {
    static $path = null;
    if ($path !== null) {
        return $path;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (strpos($script, '/admin/') !== false || strpos($script, '/pharmacist/') !== false || strpos($script, '/driver/') !== false) {
        $path = rtrim(dirname(dirname($script)), '/');
    } else {
        $path = rtrim(dirname($script), '/');
    }

    return $path === '.' ? '' : $path;
}

function assetUrl($relativePath) {
    return basePath() . '/' . ltrim($relativePath, '/');
}

function pageUrl($relativePath) {
    return assetUrl($relativePath);
}
