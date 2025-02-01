<?php

namespace App\Services;

use App\Http\Requests\VulnTogglesRequest;

class VulnTogglesService
{
    public function updateVulnToggles(VulnTogglesRequest $request)
    {
        $user = auth()->user();
        $toggle = $request->input('toggle');
        $value = $request->input('value') ? true : false;
        if (array_key_exists($toggle, $user->getAttributes())) {
            $user->update([$toggle => $value]);
            $user->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'error' => 'Invalid toggle name'], 422);
        }
    }
}
