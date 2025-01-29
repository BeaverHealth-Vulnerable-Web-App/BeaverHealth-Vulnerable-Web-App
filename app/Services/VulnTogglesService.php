<?php

namespace App\Services;

use App\Http\Requests\VulnTogglesRequest;

class VulnTogglesService
{
    public function updateVulnToggles(VulnTogglesRequest $request)
    {
        $toggle = $request->input('toggle');
        $value = $request->input('value') ? true : false;
        if (array_key_exists($toggle, auth()->user()->getAttributes())) {
            auth()->user()->{$toggle} = $value;
            auth()->user()->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
