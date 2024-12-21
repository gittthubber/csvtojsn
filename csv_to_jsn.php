<?php 

// CSV to JSON Converter - Convert CSV file to JSON
function csvToJson($csvFile, $jsonFile, $delimiter = ',', $enclosure = '"', $escape = '\\', $debug = false) {
    
    if (!file_exists($csvFile) || !is_readable($csvFile)) {
        return "Errore: il file CSV non esiste o non è leggibile.";
    }

    if (!is_writable(dirname($jsonFile))) {
        return "Errore: impossibile scrivere il file JSON nella directory specificata.";
    }

    if (($handle = fopen($csvFile, 'r')) !== false) {
        $data = [];
        $headers = fgetcsv($handle, 0, $delimiter, $enclosure, $escape); // Read the header row

        if ($headers === false) {
            return "Errore: il file CSV è vuoto o non valido.";
        }

        if ($debug) {
            echo "Intestazioni trovate: " . implode(', ', $headers) . "\n";
        }

        $rowCount = 0;
        while ($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) {
            if (count($headers) === count($row)) {
                $data[] = array_combine($headers, $row);
            } else {
                return "Errore: Righe non coerenti nel file CSV alla riga " . ($rowCount + 2) . ".";
            }
            $rowCount++;

            if ($debug) {
                echo "Elaborata riga $rowCount.\n";
            }
        }
        fclose($handle);

        $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        if (file_put_contents($jsonFile, json_encode($data, $jsonOptions)) === false) {
            return "Errore: impossibile scrivere il file JSON.";
        }

        if ($debug) {
            echo "Totale righe elaborate: $rowCount\n";
        }

        return "Conversione completata. File JSON: $jsonFile";
    }

    return "Errore: impossibile aprire il file CSV.";
}

?>
