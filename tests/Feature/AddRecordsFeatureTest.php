<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;

class AddRecordsUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('patient_records');
    }

    public function testGetAddRecordsPage()
    {
        $user = User::factory()->create(['load_records' => true]);
        $this->actingAs($user)
            ->get(route('records.add'))
            ->assertOk()
            ->assertViewIs('records.add');
    }

    public function testFileUploadSuccess()
    {
        $user = User::factory()->create([
            'file_upload_on' => true,
        ]);

        $this->actingAs($user)
            ->get(route('records.add'));

        $patient_id = Patient::factory()->create()->patient_id;
        $files = [
            'plain.txt'              => 100,
            'script.sh'              => 100,
            'no-extension'           => 100,
            'test-record.pdf'        => 100,
            'malicious.exe'          => 100,
            'double-extension.pdf.jpg' => 100,
            'empty-file.txt'         => 0,
            'large-file.zip'         => 102400,
        ];

        foreach ($files as $filename => $size) {
            $this->postWithCsrf(
                route('records.add.upload'),
                [
                    'patient_id'     => $patient_id,
                    'medical_record' => UploadedFile::fake()->create($filename, $size),
                ]
            )->assertFound()
             ->assertSessionHas('records-status', [
                'type'    => 'success',
                'message' => "File '{$filename}' uploaded successfully!"
            ]);

            Storage::disk('patient_records')
                ->assertExists("$patient_id/$filename");

            $this->assertDatabaseHas(
                'patient_files',
                [
                    'patient_id' => $patient_id,
                    'filename'   => $filename,
                ]
            );
        }
    }

    public function testUploadFailsIfNoFileProvided()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('records.add'));

        $patient_id = Patient::factory()->create()->patient_id;

        $this->postWithCsrf(
            route('records.add.upload'),
            [
                'patient_id' => $patient_id,
            ]
        )->assertSessionHasErrors('medical_record');
    }

    public function testUploadFailsIfNoPatientIdProvided()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('records.add'));

        $this->postWithCsrf(
            route('records.add.upload'),
            [
                'medical_record' => UploadedFile::fake()->create('somefile.txt', 100),
            ]
        )->assertSessionHasErrors('patient_id');
    }

    public function testUploadFailsIfInvalidPatientIdProvided()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('records.add'));

        $invalidPatientId = 999999;
        $this->postWithCsrf(
            route('records.add.upload'),
            [
                'patient_id'     => $invalidPatientId,
                'medical_record' => UploadedFile::fake()->create('somefile.txt', 100),
            ]
        )->assertSessionHasErrors('patient_id');
    }
}
