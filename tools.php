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
        $connection->exec("delete from Coordinates");
        $connection->exec("delete from Sensor");
        $source = fopen($filename, 'r');
        $count = 0;

        while (false !== $line = fgets($source, 4096)) {
            if ($count >= 1) {
                $elements = explode(";", $line);
                if (count($elements) >= 6) {
                    $coordinates = trim($elements[5]) !== "" ? array_map("floatval", explode(", ", $elements[5])) : [null, null];
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

function populateExtract ($filename)
{
    if (is_file($filename)) {
        $connection = new PDO('mysql:host=localhost;dbname=ParisTraffic', 'root', null);
        $connection->beginTransaction();
        $connection->exec("delete from Extract");
        $source = fopen($filename, 'r');
        $count = 0;
        $range = 100;
        $inserts = [];
        $query = "insert into Extract values " . implode(", ", array_fill(0, $range, "(DEFAULT, ?, ?, ?, ?)"));
        $statement = $connection->prepare($query);

        while (false !== $line = fgets($source, 4096)) {
            if ($count >= 1) {
                $elements = explode(";", $line);
                if (count($elements) >= 4) {
                    $date = DateTime::createFromFormat(DateTime::ISO8601, $elements[1]);
                    $inserts[] = $elements[0];
                    $inserts[] = $date->format(("Y-m-d H:i:s"));
                    $inserts[] = $elements[2];
                    $inserts[] = $elements[3];
                    if (sizeof($inserts) == (4 * $range)) {
                        $test = $statement->execute($inserts);
                        if (!$test) {
                            $connection->rollBack();
                            throw new Exception(implode(", ", $statement->errorInfo()));
                        }
                        $inserts = [];
                    }
                }
            }
            $count++;
        }
        $connection->commit();
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
        case "extract":
            try {
                populateExtract($argv[2]);
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
