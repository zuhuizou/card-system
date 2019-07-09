<?php
namespace App; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Facades\Cache; use Illuminate\Support\Facades\Log; class System extends Model { protected $guarded = array(); private static $systems = array(); public static function _init() { Log::debug('SystemSetting._init'); static::$systems = Cache::remember('settings.all', 1, function () { Log::debug('SystemSetting._init.fetch from database'); $sp04b3bf = System::query()->get()->toArray(); foreach ($sp04b3bf as $sp86f37f) { static::$systems[$sp86f37f['name']] = $sp86f37f['value']; } return static::$systems; }); static::$systems['_initialized'] = true; } public static function _get($spcc609a, $sp5768dd = NULL) { if (!isset(static::$systems['_initialized'])) { static::_init(); } if (isset(static::$systems[$spcc609a])) { return static::$systems[$spcc609a]; } return $sp5768dd; } public static function _getInt($spcc609a, $sp5768dd = NULL) { return (int) static::_get($spcc609a, $sp5768dd); } public static function _set($spcc609a, $spc82d84) { static::$systems[$spcc609a] = $spc82d84; $spd7786b = System::query()->where('name', $spcc609a)->first(); if ($spd7786b) { $spd7786b->value = $spc82d84; $spd7786b->save(); } else { try { System::query()->insert(array('name' => $spcc609a, 'value' => $spc82d84)); } catch (\Exception $spece20f) { } } self::flushCache(); } public static function flushCache() { Log::debug('SystemSetting.flushCache'); Cache::forget('settings.all'); } protected static function boot() { parent::boot(); static::updated(function () { self::flushCache(); }); static::created(function () { self::flushCache(); }); } }