<?php

namespace App\Console\Commands;

use App\ResponseHandler;
use App\ShrinkConsultation;
use Illuminate\Console\Command;

class Sandra extends Command Implements ResponseHandler
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandra {scommand?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $shrink ;

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
        $command = $this->argument('scommand');
        $this->shrink = new ShrinkConsultation($this);
        if(isset ($command)) {
            $this->shrink->examineCommand($command);
            return ;
        }
        $this->shrink->start();
        return ;


    }

    public function handleResponse($group, $message,$closure=false)
    {
        $this->line($message);
    }

    public function prompt($group, $message)
    {
        $prompt = $this->ask("$message");
        $shrink = $this->shrink ;
        /** @var ShrinkConsultation $shrink */
        $shrink->examineCommand($prompt);

    }
}
