<?php

namespace Modules\Notify\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Modules\Notify\Database\Seeders\NotificationTypeSeeder;

class NotifyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Model::unguard();

        // Create a new ConsoleOutput instance
        $output = new ConsoleOutput();

        // Create a new progress bar (6 steps, one for each seeder)
        $progressBar = new ProgressBar($output, 6);

        $progressBar->setFormat('verbose');
        $progressBar->start();

        $this->call(NotificationTypeSeeder::class);
        $progressBar->advance();

        // $this->call(MessageTemplateSeeder::class);
        // $progressBar->advance();

        // $this->call(NotificationLogSeeder::class);
        // $progressBar->advance();

        // $this->call(NotificationReceiptSeeder::class);
        // $progressBar->advance();

        // $this->call(NotificationSeeder::class);
        // $progressBar->advance();

        // $this->call(UserPreferenceSeeder::class);
        // $progressBar->advance();

        $progressBar->finish();
        $output->writeln(""); // Add a new line after the progress bar finishes
    }
}
