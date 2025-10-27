<?php

namespace App\Services;

use App\Models\Mesinabsensi;
use App\Models\AttendanceLog;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class AttendanceService
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 3,
            'connect_timeout' => 2
        ]);
    }

    // Hapus method fetchDataFromMachine yang lama
    // Ganti dengan implementasi async

    /**
     * Fetch data dari beberapa mesin sekaligus
     */
    public function fetchMultipleMachines(array $machines)
    {
        $promises = [];

        foreach ($machines as $machine) {
            $promises[$machine->id] = $this->httpClient->postAsync("http://{$machine->ip_address}:{$machine->port}/iWsService", [
                'body' => $this->buildSoapRequest($machine)
            ]);
        }

        $results = Utils::settle($promises)->wait();

        return $this->processResults($results);
    }

    /**
     * Bangun SOAP Request
     */
    private function buildSoapRequest(Mesinabsensi $machine)
    {
        return "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">{$machine->comm_key}</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
    }

    /**
     * Proses hasil response
     */
    private function processResults(array $results)
    {
        $output = [];

        foreach ($results as $machineId => $result) {
            if ($result['state'] === 'fulfilled') {
                $data = $this->parseAttendanceData(
                    $result['value']->getBody()->getContents()
                );

                // Simpan ke database
                foreach ($data as $log) {
                    AttendanceLog::create([
                        'machine_id' => $machineId,
                        'pin' => $log['pin'],
                        'datetime' => $log['datetime'],
                        'verified' => $log['verified'],
                        'status' => $log['status']
                    ]);
                }

                $output[$machineId] = $data;
            }
        }

        return $output;
    }

    /**
     * Parse data absensi dari response SOAP.
     *
     * @param string $buffer
     * @return array
     */
    private function parseAttendanceData($buffer)
    {
        $result = [];

        // Parse respons SOAP
        $buffer = $this->parseData($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
        $buffer = explode("\r\n", $buffer);

        foreach ($buffer as $row) {
            if (empty($row)) continue;

            $data = $this->parseData($row, "<Row>", "</Row>");
            if (empty($data)) continue;

            $pin = $this->parseData($data, "<PIN>", "</PIN>");
            $dateTime = $this->parseData($data, "<DateTime>", "</DateTime>");
            $verified = $this->parseData($data, "<Verified>", "</Verified>");
            $status = $this->parseData($data, "<Status>", "</Status>");

            $result[] = [
                'pin' => $pin,
                'datetime' => $dateTime,
                'verified' => $verified,
                'status' => $status
            ];
        }

        return $result;
    }

    /**
     * Helper untuk parse data dari string.
     *
     * @param string $data
     * @param string $start
     * @param string $end
     * @return string
     */
    private function parseData($data, $start, $end)
    {
        $data = " " . $data;
        $startPos = strpos($data, $start);
        if ($startPos === false) {
            return "";
        }

        $startPos += strlen($start);
        $endPos = strpos($data, $end, $startPos);

        if ($endPos === false) {
            return "";
        }

        return substr($data, $startPos, $endPos - $startPos);
    }

    /**
     * Ambil data log absensi terbaru dari mesin.
     *
     * @param Mesinabsensi $machine
     * @param string $fromDateTime Format: 'Y-m-d H:i:s'
     * @return array|null
     */
    public function getNewAttendanceData(Mesinabsensi $machine, $fromDateTime = null)
    {
        try {
            // Koneksi ke mesin absensi
            $connect = fsockopen($machine->ip_address, $machine->port ?? 80, $errno, $errstr, 1);

            if (!$connect) {
                Log::error("Failed to connect to machine: {$machine->nama} at {$machine->ip_address}. Error: {$errstr}");
                return null;
            }

            // Jika ada parameter waktu, format untuk request
            $dateTimeArg = "";
            if ($fromDateTime) {
                $dt = new \DateTime($fromDateTime);
                $formattedDt = $dt->format('Y-m-d H:i:s');
                $dateTimeArg = "<DateTime xsi:type=\"xsd:string\">{$formattedDt}</DateTime>";
            }

            // Membuat SOAP request untuk GetAttLog dengan filter waktu jika ada
            $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">{$machine->comm_key}</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN>{$dateTimeArg}</Arg></GetAttLog>";
            $newLine = "\r\n";

            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer .= $response;
            }

            fclose($connect);

            // Parse dan return data
            return $this->parseAttendanceData($buffer);
        } catch (Exception $e) {
            Log::error("Error fetching new data from machine {$machine->nama}: " . $e->getMessage());
            return null;
        }
    }
}
