<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvioReporteEspecial;

use Illuminate\Support\Facades\Log;

class EnvioReporteEspecialJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $correo;
    protected $subject;
    protected $content;
    protected $attachments;

    /**
     * Create a new job instance.
     */
    public function __construct($correo, $subject, $content, $attachments)
    {
        $this->correo = $correo;
        $this->subject = $subject;
        $this->content = $content;
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'subject' => $this->subject,
            'content' => $this->content,
            'attachments' => $this->attachments,
        ];

        try {
            $email = new EnvioReporteEspecial($data);
            Mail::to($this->correo)->send($email);
            Log::info('Correo enviado a: ' . $this->correo);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            throw $e;
        }
    }
}
