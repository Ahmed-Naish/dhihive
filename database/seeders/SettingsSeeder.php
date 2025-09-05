<?php

namespace Database\Seeders;

use App\Models\coreApp\Setting\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->settings() as $key => $value) {
            Setting::firstOrCreate([
                'name'       => $key,
                'value'      => $value,
                'context'    => 'app',
                'company_id' => 1,
            ]);
        }
    }

    protected function settings()
    {
        $input = session()->get('input');

        $companyName = $input['company_name'] ?? 'Onesttech';
        $email = $input['email'] ?? 'info@onesttech.com';
        $phone = $input['phone'] ?? '0XXXXXXXXXX';
        $address = $input['address'] ?? 'Unknown Street, Texas, USA';

        $settings = [
            'company_name'              => $companyName,
            'company_domain'            => config('app.domain'),
            'company_logo_backend'      => 'uploads/settings/logo/logo-white.png',
            'company_logo_frontend'     => 'uploads/settings/logo/logo-black.png',
            'company_icon'              => 'uploads/settings/logo/favicon.png',
            'android_url'               => 'https://play.google.com/store/apps/details?id=com.worx24hour.hrm',
            'android_icon'              => 'assets/favicon.png',
            'ios_url'                   => 'https://apps.apple.com/us/app/24hourworx/id1620313188',
            'ios_icon'                  => 'assets/favicon.png',
            'language'                  => 'en',
            'site_under_maintenance'    => '0',
            'company_description'       => $companyName . ' believes in painting the perfect picture of your idea while maintaining industry standards.',
            
            
            'default_theme'             => 'app_theme_1',
            'app_theme_1'               => 'static/app-screen/screen-1.png',
            'app_theme_2'               => 'static/app-screen/screen-2.png',
            'app_theme_3'               => 'static/app-screen/screen-3.png',
            'app_theme_4'               => 'static/app-screen/screen-4.png',
            
            
            'email'                     => $email,
            'phone'                     => $phone,
            'address'                   => $address,
            'twitter_link'              => 'https://twitter.com',
            'linkedin_link'             => 'https://linkedin.com',
            'facebook_link'             => 'https://facebook.com',
            'instagram_link'            => 'https://instagram.com',
            'dribbble_link'             => 'https://dribbble.com',
            'behance_link'              => 'https://behance.com',
            'pinterest_link'            => 'https://pinterest.com',
            'contact_title'             => 'Send A Message To Get Your Free Quote',
            'contact_short_description' => 'Lorem Ipsum Dolor Sit Amet Consectetur. Est Commodo Pharetra Ac Netus Enim A Eget. Tristique Malesuada Donec Condimentum Mi Quis Porttitor Non Vitae Ultrices.',
            
            
            'mail_mailer'               => 'smtp',
            'mail_host'                 => 'smtp.gmail.com',
            'mail_port'                 => '587',
            'mail_username'             => 'test@test.com',
            'mail_password'             => '1234512345',
            'mail_from_address'         => 'test@test.com',
            'mail_encryption'           => 'tls',
            'mail_from_name'            => $companyName,


            'file_system_driver'          => 'local',
            'aws_access_key_id'           => '',
            'aws_secret_access_key'       => '',
            'aws_default_region'          => '',
            'aws_bucket'                  => '',
            'aws_url'                     => '',
            'aws_endpoint'                => '',
            'aws_use_path_style_endpoint' => 0,

            
            'firebase_api_key'              => '',
            'firebase_auth_domain'          => '',
            'firebase_auth_database_url'    => '',
            'firebase_project_id'           => '',
            'firebase_storage_bucket'       => '',
            'firebase_messaging_sender_id'  => '',
            'firebase_app_id'               => '',
            'firebase_measurement_id'       => '',
            'firebase_auth_collection_name' => '',


            'geocoding_api_key'  => '',
            'geocoding_base_url' => 'https://maps.googleapis.com/maps/api/geocode/json',


            'pusher_app_id'      => '',
            'pusher_app_key'     => '',
            'pusher_app_secret'  => '',
            'pusher_app_cluster' => '',
        ];

        if (!session()->has('input') && env('APP_MOOD') == 'Saas') {
            $settings['stripe_key']              = 'pk_test_51NaH9CAEFWsTKUlUhOrl8P1yBT5Yx8bOmFFRwRWz7JzmLnk1LxvfWmD49bl31KvRCL9jxLKeKexNCxIzEV0kPl4n00lvX1LLaS';
            $settings['stripe_secret']           = 'sk_test_51NaH9CAEFWsTKUlUAKFJVBaYapJZr9pHwS8X8eaXcqFDcZbqrUaoQQqKM3iSYuy8Rb6zdm5aXYNpKkuuR6298IrH00697HeaHt';
            $settings['is_recaptcha_enable']     = '0';
            $settings['recaptcha_sitekey']       = '6Lc9bg0pAAAAAKoWkSe7B-rNdpvVgpJVTsR9JekP';
            $settings['recaptcha_secret']        = '6Lc9bg0pAAAAABd90JQSSjznnCaHAt5X2ca35IzQ';
            $settings['is_whatsapp_chat_enable'] = 1;
            $settings['whatsapp_chat_number']    = '01234567890';
            $settings['is_tawk_enable']          = 1;
            $settings['tawk_chat_widget_script'] = '<script type="text/javascript">
            var Tawk_API = Tawk_API||{}, Tawk_LoadStart = new Date();
            (function(){
            var s1         = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
                s1.async   = true;
                s1.src     = "https://embed.tawk.to/6551ee59958be55aeaaf15d9/1hf40m3te";
                s1.charset = "UTF-8";
            s1.setAttribute("crossorigin","*");
            s0.parentNode.insertBefore(s1,s0);
            })();
            </script>';
            $settings['meta_title']                    = 'Onesttech';
            $settings['meta_description']              = 'Onesttech revolutionizes human resource management, offering a comprehensive solution for businesses. Streamline your HR processes, from recruitment to employee management, with advanced features and intuitive tools. Optimize workforce efficiency, enhance employee engagement, and stay compliant effortlessly. Explore the power of Onesttech for a seamless and strategic approach to HR.';
            $settings['meta_keywords']                 = 'HR management software, Human resource solution, Employee management tool, Workforce optimization, Employee engagement platform, Compliance management, HR software solution, Talent management system.';
            $settings['meta_image']                    = '';
            $settings['is_credit_card_payment']        = 1;
            $settings['is_demo_checkout']              = 1;
            $settings['is_offline_payment']            = 1;
            $settings['is_payment_type_cash']          = 1;
            $settings['is_payment_type_cheque']        = 1;
            $settings['is_payment_type_bank_transfer'] = 1;
        }

        return $settings;
    }
}
