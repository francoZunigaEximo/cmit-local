<?php

namespace App\Jobs;

use App\Mail\ExamenesImpagosMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ExamenesImpagosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $correo;
    protected string $subject;
    protected array $content;
    /**
     * Create a new job instance.
     */
    public function __construct(string $correo, string $subject, array $content)
    {
        $this->correo = $correo;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'subject' => $this->subject,
            'content' => $this->content,
        ];

        $email = new ExamenesImpagosMail($data);
        Mail::to($this->correo)->send($email);
    }
}
