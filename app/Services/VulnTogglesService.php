<?php

namespace App\Services;

use App\Http\Requests\VulnTogglesRequest;
use App\Services\UserActivityLogger;
use Illuminate\Http\Response;

class VulnTogglesService
{
    public function __construct(private UserActivityLogger $logger)
    {
    }

    public function updateVulnToggles(VulnTogglesRequest $request)
    {
        $user = auth()->user();
        $toggle = $request->input('toggle');
        $value = $request->input('value') ? true : false;
        if (array_key_exists($toggle, $user->getAttributes())) {
            $user->update([$toggle => $value]);
            $user->save();
            $this->logToggleUpdateAttempt($toggle, $value, null);
            return response()->json(['success' => true]);
        } else {
            $error = 'Invalid toggle name';
            $this->logToggleUpdateAttempt($toggle, $value, $error);
            return response()->json(['success' => false, 'error' => $error], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function logToggleUpdateAttempt(string $toggle, bool $value, ?string $error)
    {
        $this->logger->info('Vulnerability toggle update attempt', [
            'toggle_name'  => $toggle,
            'toggle_value' => $value,
            'success'      => $error === null,
            'error'        => $error
        ]);
    }
}
