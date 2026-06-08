<?php

namespace App\Features\IpRules\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpRuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ip_address' => ['required', 'string', 'max:50', function ($attr, $value, $fail) {
                if (!$this->isValidIpOrCidr($value)) {
                    $fail('The ip_address must be a valid IPv4 address or CIDR range (e.g. 192.168.1.0/24).');
                }
            }],
            'type'  => ['required', 'in:allow,block'],
            'label' => ['nullable', 'string', 'max:100'],
        ];
    }

    private function isValidIpOrCidr(string $value): bool
    {
        if (!str_contains($value, '/')) {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
        }

        [$network, $prefix] = explode('/', $value, 2);

        return filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            && ctype_digit($prefix)
            && (int) $prefix >= 0
            && (int) $prefix <= 32;
    }
}
