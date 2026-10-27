<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    protected $table = 'homepage_settings';

    protected $fillable = ['key', 'value'];

    /**
     * Obtener el valor de una configuración.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Guardar o actualizar una configuración.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\HomepageSetting
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
