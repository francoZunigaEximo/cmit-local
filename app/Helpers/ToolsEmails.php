<?php

namespace App\Helpers;

trait ToolsEmails 
{
    public function getEmailsReporte(string $correos): array
    {
        $emails = explode(",", $correos);
        $emails = array_map('trim', $emails);

        return $emails;
    }

}