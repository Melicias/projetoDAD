<?php

namespace App\Rules;

use App\Models\Vcard;
use Illuminate\Contracts\Validation\Rule;

class isBlocked implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
		$Vcard = Vcard::find($value);
		return $Vcard->blocked == 1 ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Vcard is blocked!';
    }
}
