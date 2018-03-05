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

    'accepted'             => ':attribute doit être accepté.',
    'active_url'           => ":attribute n'est pas une adresse valide.",
    'after'                => ':attribute doit être un date après :date.',
    'alpha'                => ':attribute doit contenir uniquement des lettres.',
    'alpha_dash'           => ':attribute doit contenir uniquement des lettres, chiffres et tirets.',
    'alpha_num'            => ':attribute doit contenir uniquement des lettres et chiffres.',
    'array'                => ':attribute doit être un tableau.',
    'before'               => ':attribute doit être un date avant :date.',
    'between'              => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file'    => ':attribute doit être entre :min et :max kilobytes.',
        'string'  => ':attribute doit être entre :min et :max characters.',
        'array'   => ':attribute doit avoir entre :min et :max lignes.',
    ],
    'boolean'              => ':attribute doit être activé ou désactivé.',
    'confirmed'            => ":attribute n'est pas correct.",
    'date'                 => ":attribute n'est pas une date valide.",
    'date_format'          => ":attribute n'est pas une date correcte (:format).",
    'different'            => ':attribute et :other doivent être différents.',
    'digits'               => ':attribute doit contenir :digits chiffres.',
    'digits_between'       => ':attribute doit contenir entre :min et :max chiffres.',
    'email'                => ':attribute doit être une adresse email valide.',
    'filled'               => 'Le champ :attribute est requis.',
    'exists'               => 'La sélection sur :attribute est incorrecte.',
    'image'                => ':attribute doit être une image.',
    'in'                   => 'La sélection sur :attribute est incorrecte.',
    'integer'              => ':attribute doit être un nombre.',
    'ip'                   => ':attribute doit être une adresse IP valide.',
    'max'                  => [
        'numeric' => ':attribute doit être inférieur à :max.',
        'file'    => ':attribute doit être inférieur à :max ko.',
        'string'  => ':attribute doit être inférieur à :max caractères.',
        'array'   => ':attribute doit contenir moins de :max lignes.',
    ],
    'mimes'                => ':attribute doit être du type :values.',
    'min'                  => [
        'numeric' => ":attribute doit être d'au moins :min.",
        'file'    => ':attribute doit être supérieur à :min ko.',
        'string'  => ':attribute doit être supérieur à :min caractères.',
        'array'   => ':attribute doit contenir au moins :min lignes.',
    ],
    'not_in'               => ':attribute est invalide.',
    'numeric'              => ':attribute doit être un nombre.',
    'regex'                => 'Le format de :attribute est invalide.',
    'required'             => 'Le champ :attribute est requis.',
    'required_if'          => 'Le champ :attribute est requis quand :other contient :value.',
    'required_with'        => 'Le champ :attribute est requis quand :values est présent.',
    'required_with_all'    => 'Le champ :attribute est requis quand :values est présent.',
    'required_without'     => "Le champ :attribute est requis quand :values n'est pas présent.",
    'required_without_all' => "Le champ :attribute est requis quand :values ne sont pas présent.",
    'same'                 => ':attribute et :other ne correspondent pas.',
    'size'                 => [
        'numeric' => ':attribute doit être à :size.',
        'file'    => ':attribute doit être à :size ko.',
        'string'  => ':attribute doit être à :size caractères.',
        'array'   => ':attribute doit contenir :size lignes.',
    ],
    'unique'               => ':attribute est déjà pris.',
    'url'                  => ':attribute a un format incorrect.',
    'timezone'             => ':attribute doit être un fuseau horaire valide.',

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
            'required' => 'Veuillez accepter les conditions de service.'
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
