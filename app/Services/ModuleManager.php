<?php

namespace App\Services;

class ModuleManager {
    public static function all() {
        $modules = [];
        foreach (glob(base_path('modules/*/Config/module.php')) as $configPath) {
            $modules[] = include $configPath;
        }
        return $modules;
    }
}
