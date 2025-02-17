<?php

namespace App\Presenters;

class FilePresenter
{
    public function generateDownloadLinks(array $fileList, $patientId)
    {
        return array_map(function ($file) use ($patientId) {
            $downloadUrl = route('records.download', [
                'patient_id' => $patientId,
                'filename'   => $file,
            ]);
            return "<a href='{$downloadUrl}' class='text-blue-500 underline'>{$file}</a>";
        }, $fileList);
    }
}
