<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\PatientFile;

class AddRecordsUploadTest extends TestCase
{
    private $user;
    private $patient;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->get(route('records.add'));

        $this->patient = Patient::factory()->create();
    }

    private function uploadFile($fileName, $size = 100)
    {
        $response = $this->post(route('records.add.upload'), [
            '_token'         => csrf_token(),
            'patient_id'     => $this->patient->patient_id,
            'medical_record' => UploadedFile::fake()->create($fileName, $size),
        ]);

        $response->assertSessionHas('success', 'File uploaded successfully!');
        Storage::disk('public')->assertExists("patient_records/{$this->patient->patient_id}/$fileName");

        return $response;
    }

    public function testPageLoadsSuccessfully()
    {
        $response = $this->get(route('records.add'));
        $response->assertStatus(200);
        $response->assertViewIs('records.add');
    }

    public function testFileTypeUploadSuccess()
    {
        $files = [
            'test-record.pdf',
            'malicious.exe',
            'no-extension',
            'double-extension.pdf.jpg',
        ];

        foreach ($files as $fileName) {
            $this->uploadFile($fileName);
        }
    }

    public function testLargeFileUploadSuccess()
    {
        $fileName = 'large-file.zip';
        $this->uploadFile($fileName, 102400);
    }

    public function testDatabaseEntryCreated()
    {
        $fileName = 'db-entry-test.pdf';
        $this->uploadFile($fileName);

        $this->assertDatabaseHas('patient_files', [
            'patient_id' => $this->patient->patient_id,
            'filename'   => $fileName,
        ]);
    }
}
