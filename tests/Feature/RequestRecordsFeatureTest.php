<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\PatientFile;

class RequestRecordsFeatureTest extends TestCase
{
    private $user;
    private $patient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->get(route('records.request'));
        $this->patient = Patient::factory()->create();
    }

    protected function tearDown(): void
    {
        $this->cleanupStorageDirectory();
        parent::tearDown();
    }

    private function getStorageDirectory(): string
    {
        return storage_path("app/public/patient_records/{$this->patient->patient_id}");
    }

    private function createStorageDirectory(int $permissions = 0755): string
    {
        $directory = $this->getStorageDirectory();
        if (!is_dir($directory)) {
            mkdir($directory, $permissions, true);
        }
        return $directory;
    }

    private function createTestFile(string $fileName, string $content = 'dummy content'): string
    {
        $directory = $this->createStorageDirectory();
        $filePath = "$directory/$fileName";
        file_put_contents($filePath, $content);
        PatientFile::create([
            'patient_id' => $this->patient->patient_id,
            'filename'   => $fileName,
            'path'       => $filePath,
        ]);
        return $filePath;
    }

    private function cleanupStorageDirectory(): void
    {
        $directory = $this->getStorageDirectory();
        if (is_dir($directory)) {
            File::deleteDirectory($directory);
        }
    }

    public function testPageLoadsSuccessfully()
    {
        $response = $this->get(route('records.request'));
        $response->assertStatus(200);
        $response->assertViewIs('records.request');
    }

    public function testSearchReturnsCorrectPatientInformation()
    {
        $response = $this->post(route('records.search'), [
            '_token'     => csrf_token(),
            'patient_id' => $this->patient->patient_id,
        ]);

        $response->assertRedirect(route('records.request'));
        $response->assertSessionHas('patient_info');
    }

    public function testSearchWithValidPatientButNoFiles()
    {
        $this->createStorageDirectory();

        $response = $this->post(route('records.search'), [
            '_token'     => csrf_token(),
            'patient_id' => $this->patient->patient_id,
        ]);

        $response->assertRedirect(route('records.request'));
        $response->assertSessionHas('patient_files', 'No files found.');
    }

    public function testSearchWithValidPatientAndFiles()
    {
        $this->createTestFile('test-file.pdf');

        $response = $this->post(route('records.search'), [
            '_token'     => csrf_token(),
            'patient_id' => $this->patient->patient_id,
        ]);

        $response->assertRedirect(route('records.request'));
        $response->assertSessionHas('patient_files');
    }

    public function testSearchWithKeywordFiltering()
    {
        $this->createTestFile('report1.pdf');
        $this->createTestFile('report2.pdf');

        $response = $this->post(route('records.search'), [
            '_token'     => csrf_token(),
            'patient_id' => $this->patient->patient_id,
            'keyword'    => '2',
        ]);

        $response->assertRedirect(route('records.request'));

        $patientFiles = session('patient_files');
        $this->assertIsString($patientFiles, 'Expected patient_files to be a string');
        $this->assertStringContainsString('report2.pdf', $patientFiles);
        $this->assertStringNotContainsString('report1.pdf', $patientFiles);
    }

    public function testDownloadFileSuccess()
    {
        $fileName = "valid-file.pdf";
        $this->createTestFile($fileName);

        $response = $this->get(route('records.download', [
            'patient_id' => $this->patient->patient_id,
            'filename'   => $fileName,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', "attachment; filename=$fileName");
    }
}
