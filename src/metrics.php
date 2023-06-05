<?php

require_once "./vendor/autoload.php";

$queryParams = array(
    "tokenId" => getenv("TOKEN_ID"),
    "sn"      => getenv("SN")
);
$builtQueryParams = http_build_query($queryParams);
$query = "https://www.solaxcloud.com:9443/proxy/api/getRealtimeInfo.do?".$builtQueryParams;
$data = json_decode(file_get_contents($query), true);

// misc
$queryStatus = $data['success'];
$queryException = $data['exception'];
$inverterSN = $data["result"]["inverterSN"];
$sn = $data["result"]["inverterSN"];
// solar panels in
$dcPower = $data["result"]["powerdc1"];
$dcPower2 = $data["result"]["powerdc2"];
// solar panels in total
$pvPower = $dcPower+$dcPower2;
// inverter output in AC
$acPower = $data["result"]["acpower"];
// grid in
$gridPower = gridPowerFormatter($data["result"]["feedinpower"]);
// load power
$loadPower = $gridPower + $acPower;

// if query failed kill the script
if ($queryStatus === false) {
    die();
}

function gridPowerFormatter($gridPower) {
    if ($gridPower == "-0") {
        $gridPower = "0";
    } elseif (str_contains($gridPower, "-")) {
        $gridPower = str_replace("-", "", $gridPower);
    } else {
        $gridPower = "-".$gridPower;
    }

    return $gridPower;
}

// set correct header
header("Content-Type: text/plain");

// create prometheus data
createPrometheusGauge("dc_power_1", "DC Power 1", $dcPower);
createPrometheusGauge("dc_power_2", "DC Power 2", $dcPower2);
createPrometheusGauge("pv_power", "PV Power", $pvPower);
createPrometheusGauge("ac_power", "AC Power", $acPower);
createPrometheusGauge("grid_power", "Grid Power", $gridPower);
createPrometheusGauge("load_power", "Load Power", $loadPower);

use PNX\Prometheus\Gauge;
use PNX\Prometheus\Serializer\MetricSerializerFactory;

function createPrometheusGauge($name, $description, $value) {
    $gauge = new Gauge("solax", $name, $description);
    $gauge->set($value);
    $serializer = MetricSerializerFactory::create();
    $output = $serializer->serialize($gauge, 'prometheus');
    echo $output;
}

?>