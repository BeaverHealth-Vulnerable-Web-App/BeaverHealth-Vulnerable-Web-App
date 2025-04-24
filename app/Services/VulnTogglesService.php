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

        $response = null;
        $error = null;

        if (array_key_exists($toggle, $user->getAttributes())) {
            $user->update([$toggle => $value]);
            $user->save();
            $response = response()->json(['success' => true]);
        } else {
            $error = 'Invalid toggle name';
            $response = response()->json(
                ['success' => false, 'error' => $error],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->logger->info('Vulnerability toggle update attempt', [
            'toggle_name'  => $toggle,
            'toggle_value' => $value,
            'success'      => $error === null,
            'error'        => $error
        ]);

        return $response;
    }
}
