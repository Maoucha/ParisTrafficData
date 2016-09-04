<?php

function usage ($name)
{
    echo "Usage: $name <command>\n";
    echo "    Commands are:\n";
    echo "    sensor <filename>: populate the sensor data from a CSV file\n";
}

function populateSensor ($filename)
{
    if (is_file($filename)) {
        $connection = new PDO('mysql:host=localhost;dbname=ParisTraffic', 'root', null);
        $connection->exec("truncate Sensor");
        $connection->exec("truncate Coordinates");
        $source = fopen($filename, 'r');
        $count = 0;

        while (false !== $line = fgets($source, 4096)) {
            if ($count >= 1) {
                $elements = explode(";", $line);
                if (count($elements) >= 6) {
                    $coordinates = trim($elements[5]) !== "" ? array_map("floatval", explode(", ", $elements[5])) : [null, null];
                    if (count($coordinates) < 2) {
                        var_dump($count);
                        var_dump($elements[5]);
                        var_dump($coordinates);
                    }
                    $query = "insert into Sensor values ({$elements[0]}, {$elements[1]}, {$elements[2]}, {$elements[3]}, {$coordinates[0]}, {$coordinates[1]})";
                    $connection->exec($query);
                    $geo = preg_replace("/\"\"/", "\"", $elements[4]);
                    if (null !== $data = json_decode(substr($geo, 1, strlen($geo) - 2), true)) {
                        if (strtolower($data["type"]) == "linestring") {
                            foreach ($data["coordinates"] as $coordinates) {
                                $query = "insert into Coordinates values (DEFAULT, {$elements[0]}, {$coordinates[0]}, {$coordinates[1]})";
                                $connection->exec($query);
                            }
                        }
                    }
                }
            }
            $count++;
        }
    } else {
        throw new Exception("No such file $filename");
    }
}

if ($argc > 0) {
    switch ($argv[1]) {
        case "sensor":
            try {
                populateSensor($argv[2]);
            } catch (Exception $e) {
                echo "Error:  {$e->getMessage()}";
                exit(1);
            }
            break;
        default:
            usage(basename($argv[0]));
            exit(1);
    }
}