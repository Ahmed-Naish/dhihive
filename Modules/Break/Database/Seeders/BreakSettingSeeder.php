<?php

namespace Modules\Break\Database\Seeders;

use App\Models\Upload;
use BaconQrCode\Writer;
use Illuminate\Database\Seeder;
use Modules\Break\Entities\BreakType;
use BaconQrCode\Renderer\ImageRenderer;
use Modules\Break\Entities\BreakSetting;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class BreakSettingSeeder extends Seeder
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

        if ($input) {
            $encrypt_text = @$input['tenantDomain'] ?? config('app.domain');
        } else {
            $encrypt_text = config('app.domain');
        }

        $encrypt_code = encrypt($encrypt_text);

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($encrypt_code);

        $directory = public_path('uploads/qrcodes');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $svgFilePath = 'uploads/qrcodes/' . uniqid() . '.svg';
        file_put_contents(public_path($svgFilePath), $qrCode);

        BreakSetting::firstOrCreate([
            'company_id'    => $company_id,
            'branch_id'     => $branch_id,
            'title'         => 'Break',
        ], [
            'path'          => $svgFilePath,
            'encrypt_text'  => $encrypt_text,
            'encrypt_code'  => $encrypt_code
        ]);
    }
}
