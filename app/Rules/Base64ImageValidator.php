<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64ImageValidator implements Rule
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
        // Check if it starts with "data:image/" and is followed by any image type
        if (preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $decoded = base64_decode($base64Data, true);

            // Ensure that the data is valid base64 and it decodes successfully
            if ($decoded !== false) {
                // Try to create an image from the string (or skip this if unnecessary)
                return @imagecreatefromstring($decoded) !== false;
            }
        }
        return false;
    }

    public function message()
    {
        return _trans('response.The :attribute must be a valid Base64-encoded image.');
    }
}
