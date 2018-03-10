<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ' .يجب أن يكون مقبول :attribute ال .',
    'active_url'           => '. صحيح URL ليس برابط  :attribut ال.',
    'after'                => ':date يجب ان يكون تاريخا بعد  :attribute  ال.',
    'alpha'                => '.يجب ان تحتوي على حروف فقط :attribute ال.',
    'alpha_dash'           => '.يجب ان تحتوي على حروف أرقام و رموز فقط :attribute ال',
    'alpha_num'            => '.يجب ان يضم حروفو أرقام فقط :attribute ال',
    'array'                => '.يجب ان يكون على شكل جدول :attribute ال',
    'before'               => ':date يجب أن يكون تاريخا قبل  :attribute ال',
    'between'              => [
        'numeric' => '.:max و :min يجب أن تكون بين :attribute ال.',
        'file'    => '.كيلوبايت :max و :min يجب أن تكون بين :attribute ال',
        'string'  => '.حرفا :max و :min يجب ان تكون بين :attribute ال',
        'array'   => '.عنصرا :max و :min يجب ان تكون بين  :attribute ال ',
    ],
    'boolean'              => '.يجب أن يكون إما صحيحا أو خطأ :attribute حقل ال.',
    'confirmed'            => '.عير مطابق :attribute تأكيد ال',
    'date'                 => '.تاريخ خطأ :attribute ال',
    'date_format'          => '.:format لا يتطابق مع التنسيق :attribute ال', 
    'different'            => '. :other يجب ان يكون مخالفا لل :attribute ال ',
    'digits'               => '.:digits يجب ان يكون على شكل ارقام :attribute ال',
    'digits_between'       => '.رقما :max و :min يجب ان تكون بين :attribute ال',
    'email'                => '.يجب أن يكون عنوان بريد صالح :attribute ال.',
    'filled'               => '. إلزامي :attribute حقل ال.',
    'exists'               => '. المختار غير صالح :attribute ال ',
    'image'                => '.على شكل صورة :attribute يجب ان يكون ال.',
    'in'                   => '.المختار غير صالح :attribute ال',
    'integer'              => '.عددا صحيحا :attribute يجب ان يكون ال',
    'ip'                   => '.صالحا IP عنوان :attribute  يجب أن يكون',
    'max'                  => [
        'numeric' => '.:max لا يجب ان يتجاوز  :attribute ال',
        'file'    => '. كيلوبايت :max لا يجب ان يتجاوز  :attribute ال',
        'string'  => '. حرفا :max لا يجب ان يتجاوز  :attribute ال',
        'array'   => '. عنصرا :max لا يجب ان يتجاوز  :attribute ال',
    ],
    'mimes'                => ' .:values : يجب ان يكون ملف من نوع :attribute ال',
    'min'                  => [
        'numeric' => '.:min يجب ان يتكون على الأقل من :attribute ال.',
        'file'    => '. كيلوبايت :min يجب ان يتكون على الأقل من :attribute ال',
        'string'  => '. حرفا :min يجب ان يتكون على الأقل من :attribute ال',
        'array'   => '. عنصرا :min يجب ان يتكون على الأقل من :attribute ال',
    ],
    'not_in'               => '.المختار غير صالح :attribute ال',
    'numeric'              => '.يجب ان يكون رقما  :attribute ال',
    'regex'                => '.غير صالح  :attribute تنسيق ال',
    'required'             => '.إلزامي :attribute حقل ال',
    'required_if'          => '.:value تساوي :other إلزامي عندما تكون :attribute  حقل ال ',
    'required_with'        => '.:value إلزامي عند تواجد :attribute  حقل ال ',
    'required_with_all'    => '.:value إلزامي عند تواجد :attribute  حقل ال',
    'required_without'     => '.:value إلزامي عند عدم تواجد :attribute  حقل ال',
    'required_without_all' => '.:value إلزامي عند عدم وجود أية :attribute  حقل ال',
    'same'                 => '. متطابقتان :other و :attribute يجب أن تكون  ',
    'size'                 => [
        'numeric' => '.:size  يجب ان يكون :attribute ال',
        'file'    => '. كيلوبايت :size  يجب ان يكون :attribute ال',
        'string'  => '. رقما :size  يجب ان يكون :attribute ال',
        'array'   => '. عنصرا:size  يجب ان يكون :attribute ال',
    ],
    'unique'               => 'مسبقا :attribute  تم أخذ ال',
    'url'                  => '.غير صالح  :attribute تنسيق ال',
    'timezone'             => 'يجب أن تكون منطقة زمنية صالحة :attribute ال.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'terms_agreed' => [
            'required' => 'يرجى الموافقة على بنود الخدمة.'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
