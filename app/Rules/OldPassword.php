<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class OldPassword implements ValidationRule
{
    protected $count;

    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * Makes the password not same as the old password.
     *
     * @return $this
     */
    public static function notSame($count)
    {
        return new static($count);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var User $user */
        $user = auth()->user();
        $oldPasswords = $user->passwordHistories()
            ->select('password')
            ->latest()
            ->take($this->count)
            ->get()
            ->pluck('password');

        foreach ($oldPasswords as $oldPassword) {
            if (Hash::check($value, $oldPassword)) {
                $fail('validation.old_password.not_same')->translate([
                    'count' => $this->count
                ]);
            }
        }
    }
}
