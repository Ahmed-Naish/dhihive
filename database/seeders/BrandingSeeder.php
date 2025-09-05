<?php

namespace Database\Seeders;

use App\Models\Branding;
use Illuminate\Database\Seeder;

class BrandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brandings = [
            ['name' => 'primaryColor', 'value' => '#FFFFFF'],
            ['name' => 'primaryLight', 'value' => '#2872c7'],
            ['name' => 'primaryDark', 'value' => '#1b64ab'],
            ['name' => 'cardBackgroundDefault', 'value' => '#FFFFFF'],
            ['name' => 'cardBackgroundSubdued', 'value' => '#F0F0F0'],
            ['name' => 'textPrimary', 'value' => '#000000'],
            ['name' => 'textSecondary', 'value' => '#666666'],
            ['name' => 'textTertiary', 'value' => '#999999'],
            ['name' => 'textHint', 'value' => '#BBBBBB'],
            ['name' => 'textDisabled', 'value' => '#DDDDDD'],
            ['name' => 'textInversePrimary', 'value' => '#FFFFFF'],
            ['name' => 'textInverseSecondary', 'value' => '#EEEEEE'],
            ['name' => 'textInverseTertiary', 'value' => '#CCCCCC'],
            ['name' => 'brandTextLink', 'value' => '#1E90FF'],
            ['name' => 'iconPrimary', 'value' => '#000000'],
            ['name' => 'iconSecondary', 'value' => '#666666'],
            ['name' => 'iconDisabled', 'value' => '#DDDDDD'],
            ['name' => 'iconInverse', 'value' => '#FFFFFF'],
            ['name' => 'iconAccent', 'value' => '#FFA500'],
            ['name' => 'iconNavigationBar', 'value' => '#000080'],
            ['name' => 'iconAccentInactive', 'value' => '#FFA07A'],
            ['name' => 'dividerPrimary', 'value' => '#CCCCCC'],
            ['name' => 'dividerInverse', 'value' => '#333333'],
            ['name' => 'buttonDefault', 'value' => '#007BFF'],
            ['name' => 'buttonSecondary', 'value' => '#6C757D'],
            ['name' => 'buttonInverse', 'value' => '#FFFFFF'],
            ['name' => 'buttonDisabled', 'value' => '#DDDDDD'],
            ['name' => 'container', 'value' => '#F8F9FA'],
            ['name' => 'containerSecondary', 'value' => '#E9ECEF'],
            ['name' => 'logo_url', 'value' => 'assets/branding-logo.png'],
            ['name' => 'app_name', 'value' => 'Onesttech'],
            ['name' => 'font_family', 'value' => 'Arial, sans-serif'],
        ];

        $input = session()->get('input');

        foreach ($brandings as $branding) {
            Branding::firstOrCreate([
                'name'       => $branding['name'],
                'value'      => $branding['value'],
                'company_id' => $input['company_id'] ?? 1,
                'branch_id'  => $input['branch_id'] ?? 1,
            ]);
        }
    }
}
