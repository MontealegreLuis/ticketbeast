<?php

namespace App\Console\Commands;

use App\IdentifierGenerator;
use App\Invitation;
use Illuminate\Console\Command;

class InvitePromoterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promoter:invite {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite a new promoter to create an account';

    /** @var IdentifierGenerator */
    private $generator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IdentifierGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $invitation = Invitation::create([
            'code' => $this->generator->generateConfirmationNumber(),
            'email' => $this->argument('email'),
        ]);
    }
}
