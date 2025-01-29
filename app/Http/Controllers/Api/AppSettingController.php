<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class AppSettingController extends Controller
{
    // Método para obtener todas las configuraciones
    public function getAllSettings()
    {
        $settings = AppSetting::all();
        return response()->json($settings);
    }

    // Método para actualizar una configuración específica
    public function updateSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        $setting = AppSetting::first();
        if (!$setting) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        $key = $request->input('key');
        $value = $request->input('value');

        // Asegurarse de que la clave sea válida
        if (!in_array($key, ['app_name', 'logo_url', 'languages'])) {
            return response()->json(['message' => 'Invalid key provided'], 400);
        }

        // Si la clave es 'languages', decodificar a JSON
        if ($key === 'languages' && is_array($value)) {
            $value = json_encode($value);
        }

        $setting->$key = $value;
        $setting->save();

        return response()->json(['message' => 'Setting updated successfully']);
    }

    public function updateLogo(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $imageName = time().'.'.$request->logo->extension();  
    $request->logo->move(public_path('images'), $imageName);

    $setting = AppSetting::first();
    if (!$setting) {
        return response()->json(['message' => 'Settings not found'], 404);
    }

    $setting->logo_url = url('images/'.$imageName);
    $setting->save();

    return response()->json(['message' => 'Logo updated successfully', 'logo_url' => $setting->logo_url]);
}

}
