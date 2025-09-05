<?php

namespace Modules\Break\Database\Seeders;

use App\Models\Upload;
use Illuminate\Database\Seeder;
use Modules\Break\Entities\BreakType;

class BreakTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input      = session()->get('input');
        $company_id = $input['company_id'] ?? 1;
        $branch_id  = $input['branch_id'] ?? 1;

        $breakTypes = [
            [
                'name'                  => 'Lunch Break',
                'slug'                  => 'lunch-break',
                'description'           => 'A break for lunch.',
                'limit'                 => 1,
                'limit_type'            => 'day',
                'duration_type'         => 'hour',
                'max_duration'          => 1,
            ],
            [
                'name'                  => 'Tea/Coffee Break',
                'slug'                  => 'tea-coffee-break',
                'description'           => 'A short break for tea.',
                'limit'                 => 3,
                'limit_type'            => 'day',
                'duration_type'         => 'minute',
                'max_duration'          => 15,
            ],
            [
                'name'                  => 'Dinner Break',
                'slug'                  => 'dinner-break',
                'description'           => 'A break for dinner.',
                'limit'                 => 1,
                'limit_type'            => 'day',
                'duration_type'         => 'hour',
                'max_duration'          => 1,
            ],
        ];

        foreach ($breakTypes as $breakType) {

            $icon   = Upload::firstOrCreate([
                        'img_path'  => 'assets/break/' . $breakType['slug'] . '.png',
                        'type'      => 'png',
                        'extension' => '.png'
                    ]);

            BreakType::firstOrCreate([
                'name'               => $breakType['name'],
                'slug'               => $breakType['slug'],
                'company_id'         => $company_id,
                'branch_id'          => $branch_id,
            ], [
                'description'        => $breakType['description'],
                'is_remark_required' => @$breakType['is_remark_required'] ?? false,
                'will_ask_next_meal' => @$breakType['will_ask_next_meal'] ?? false,
                'status_id'          => 1,
                'limit'              => $breakType['limit'],
                'limit_type'         => $breakType['limit_type'],
                'duration_type'      => $breakType['duration_type'],
                'max_duration'       => $breakType['max_duration'],
                'icon_id'            => $icon?->id ?? null,
            ]);
        }
    }
}
