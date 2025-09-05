<?php

namespace App\Console\Commands;

use Database\Seeders\RegularSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\SaasSingleDBSeeder;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class RegularDBMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:migrate {--seed : Seed the database after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for hrm regular';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $confirmation = $this->confirm('Are you sure to run migration?');

        if ($confirmation)
            if (config('app.mood') != 'Saas' && !isModuleActive('Saas')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                Artisan::call('db:wipe', ['--force' => true, '-vvv' => true]);
                $this->customStyleText('Running migrations ...', '#0a0a0a', '#dffa7f');

                $migrationPaths = tenantMigrationPaths();

                foreach ($migrationPaths as $path) {
                    $this->customStyleText('Migrating: ' . $path . ' ...', '#0a0a0a', '#effcb1');

                    try {
                        Artisan::call('migrate', ['--path' => $path, '--force' => true, '-vvv' => true]);
                        $this->info(Artisan::output());
                    } catch (\Exception $e) {
                        $this->customStyleText("An error occurred while migrating: " . $e->getMessage() . ' ...', '#0a0a0a', '#ff9191');
                    }
                }

                $this->customStyleText('Migrations has been completed.', '#0a0a0a', '#42f569');

                if ($this->option('seed')) {
                    $this->customStyleText('Database seeding ...', '#0a0a0a', '#dffa7f');
                    Artisan::call('db:seed', [
                        '--class' => RegularSeeder::class,
                        '--force' => true,
                        '-vvv' => true
                    ]);
                    $this->info(Artisan::output());
                    $this->customStyleText('Database has been successfully seeded.', '#0a0a0a', '#42f569');
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } else {
                $this->customStyleText('Regular HRM is false. Skipping migrations.', '#0a0a0a', '#fcbbd7');
            }

        return 0;
    }

    function customStyleText($text, $textColorHex, $bgColorHex)
    {
        $output = new ConsoleOutput();
        $style = new OutputFormatterStyle($textColorHex, $bgColorHex);
        $output->getFormatter()->setStyle('custom-style', $style);
        $output->writeln('<custom-style>' . $text . '</>');
        $output->getFormatter()->setStyle('custom-style', new OutputFormatterStyle());
    }
}
