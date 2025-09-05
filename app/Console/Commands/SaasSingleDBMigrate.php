<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\SaasSingleDBSeeder;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class SaasSingleDBMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *ph
     * @var string
     */
    protected $signature = 'saas:single-migrate {--seed : Seed the database after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations if SINGLE_DB is true and optionally seed the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $confirmation = $this->confirm('Are you sure to run Saas Single DB Migrate?');

        if ($confirmation)
        if (config('app.single_db')) {
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
                    '--class' => SaasSingleDBSeeder::class, 
                    '--force' => true, 
                    '-vvv' => true
                ]);
                $this->info(Artisan::output());
                $this->customStyleText('Database has been successfully seeded.', '#0a0a0a', '#42f569');
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            $this->customStyleText('SINGLE_DB is false. Skipping migrations.', '#0a0a0a', '#fcbbd7');
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
