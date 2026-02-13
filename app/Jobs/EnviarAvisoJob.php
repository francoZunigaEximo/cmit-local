<?php

namespace App\Jobs;

use App\Mail\EnviarAvisoMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarAvisoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $correo;
    protected $subject;
    protected $content;
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

        try {
            $email = new EnviarAvisoMail($data);
            Mail::to($this->correo)->send($email);
            Log::info('Correo enviado a: ' . $this->correo);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            throw $e;
        }
    }
}
