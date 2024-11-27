<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnviarReporte;

class EnviarReporteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $correo;
    protected $subject;
    protected $content;
    protected $attachment;
    /**
     * Create a new job instance.
     */
    public function __construct($correo, $subject, $content, $attachment)
    {
        $this->correo = $correo;
        $this->subject = $subject;
        $this->content = $content;
        $this->attachment = $attachment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'subject' => $this->subject,
            'content' => $this->content,
            'attachment' => $this->attachment,
        ];

        $email = new EnviarReporte($data);
        Mail::to($this->correo)->send($email);
    }
}
