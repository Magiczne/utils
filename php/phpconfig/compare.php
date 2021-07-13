<?php

function ini_flatten ($config) {
    $flat = [];

    foreach ($config as $key => $value) {
        $flat[$key] = $value['local_value'];
    }

    return $flat;
}

function ini_diff ($config_a, $config_b) {
    return array_diff_assoc(ini_flatten($config_a), ini_flatten($config_b));
}

$config_a = ini_get_all();

$config_b_url = 'https://radio17.pl/phpconfig_export.php';
$config_b = unserialize(file_get_contents($config_b_url));

$diff = ini_diff($config_a, $config_b);

echo '<pre>';
print_r($diff);
echo '</pre>';