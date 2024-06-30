<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturasMailable;
use App\Models\FacturaDeVenta;

class FacturaEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $correo;
    protected $subject;
    protected $content;
    protected $attachment;
    protected $tipo;
    /**
     * Create a new job instance.
     */
    public function __construct($correo, $subject, $content, $attachment, $tipo)
    {
        $this->correo = $correo;
        $this->subject = $subject;
        $this->content = $content;
        $this->attachment = $attachment;
        $this->tipo = $tipo;
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
            'tipo' => $this->tipo,
        ];

        $email = new FacturasMailable($data);
        Mail::to($this->correo)->send($email);
    }
}
