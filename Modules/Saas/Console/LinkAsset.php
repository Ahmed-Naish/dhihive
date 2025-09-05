<?php

namespace Modules\Saas\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LinkAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'link:saas-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to link the podcast assets with public.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $target = public_path('saas');
        // $link = base_path().'/Modules/Saas/Assets';
        // symlink($link, $target);
    }

}
