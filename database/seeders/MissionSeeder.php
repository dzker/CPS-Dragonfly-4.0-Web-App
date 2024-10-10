<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\FlightDetail;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Read data from JSON file
        $jsonPath = database_path('seeders/missions.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        // Seed the database
        foreach ($data as $item) {
            // Create a Mission
            $mission = Mission::create(
                [
                    'mission_id' => $item['mission']['mission_id'],
                    'start_time' => $item['mission']['start_time'],
                    'end_time' => $item['mission']['end_time'],
                    'flight_path' => $item['mission']['flight_path'],


                ]);
            // $mission = Mission::create($item['mission']);
            echo (var_dump($mission));
            $created_mission = Mission::where('mission_id', $item['mission']['mission_id'])->first();

            // Create related FlightDetails
         // Create related FlightDetails
         // Create related FlightDetails
    if (isset($item['flight_details']) && is_array($item['flight_details'])) {
        foreach ($item['flight_details'] as $flightDetailData) {
            FlightDetail::create([
                'mission_id' =>  $created_mission->mission_id,
                'detected_qr_code' => $flightDetailData['detected_qr_code'],
                'detected_time' => $flightDetailData['detected_time'],
            ]);
        }
    }
        }
    }

}
