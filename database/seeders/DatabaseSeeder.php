<?php

namespace Database\Seeders;

use App\Models\ChargingStation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['name'=>'ChargeGrid Urban 7','model'=>'CG-U7','charging_type'=>'AC','power_kw'=>7,'price_mxn_cents'=>890000,'status'=>'active','description'=>'Equipo urbano de carga AC. Producto de demostración.'],
            ['name'=>'ChargeGrid Pro 22','model'=>'CG-P22','charging_type'=>'AC','power_kw'=>22,'price_mxn_cents'=>1850000,'status'=>'active','description'=>'Equipo comercial de carga AC. Producto de demostración.'],
            ['name'=>'ChargeGrid Rapid 60','model'=>'CG-R60','charging_type'=>'DC','power_kw'=>60,'price_mxn_cents'=>4200000,'status'=>'active','description'=>'Estación rápida de carga DC. Producto de demostración.'],
        ];
        foreach ($stations as $station) ChargingStation::firstOrCreate(['model'=>$station['model']], $station);
    }
}
