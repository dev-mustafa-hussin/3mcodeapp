<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'المستودع الرئيسي',
            'location' => 'الرياض - المنطقة الصناعية',
            'contact_person' => 'مدير المستودع',
            'phone' => '0500000000',
        ]);

        Warehouse::create([
            'name' => 'مستودع جدة',
            'location' => 'جدة - شارع الميناء',
            'contact_person' => 'مشرف الفرع',
            'phone' => '0500000001',
        ]);

        Warehouse::create([
            'name' => 'معرض العليا',
            'location' => 'الرياض - طريق الملك فهد',
            'contact_person' => 'مدير المعرض',
            'phone' => '0500000002',
        ]);
    }
}
