<?php

namespace App\Features\IpRules\Models;

use Illuminate\Database\Eloquent\Model;

class IpRule extends Model
{
    protected $fillable = ['ip_address', 'type', 'label'];

    public static function isBlocked(string $ip): bool
    {
        $rules      = static::all();
        $allowRules = $rules->where('type', 'allow');
        $blockRules = $rules->where('type', 'block');

        if ($allowRules->isNotEmpty()) {
            return !$allowRules->contains(fn ($rule) => static::matches($ip, $rule->ip_address));
        }

        return $blockRules->contains(fn ($rule) => static::matches($ip, $rule->ip_address));
    }

    private static function matches(string $ip, string $ruleIp): bool
    {
        if (!str_contains($ruleIp, '/')) {
            return $ip === $ruleIp;
        }

        [$network, $prefix] = explode('/', $ruleIp, 2);
        $prefix      = (int) $prefix;
        $ipLong      = ip2long($ip);
        $networkLong = ip2long($network);

        if ($ipLong === false || $networkLong === false || $prefix < 0 || $prefix > 32) {
            return false;
        }

        $mask = $prefix === 0 ? 0 : (~0 << (32 - $prefix));

        return ($ipLong & $mask) === ($networkLong & $mask);
    }
}
