<?php

namespace App\Jobs;

use App\Mail\ReporteMapasMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ReporteMapasJob implements ShouldQueue
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

        $email = new ReporteMapasMail($data);
        Mail::to($this->correo)->send($email);
    }
}
