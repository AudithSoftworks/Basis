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
    "accepted" => ":attribute qəbul edilməlidir.",
    "active_url" => ":attribute etibarlı URL deyil.",
    "after" => ":attribute :date tarixindən sonra bir tarix olmalıdır.",
    "alpha" => ":attribute sadəcə hərflərdən ibarət ola bilər.",
    "alpha_dash" => ":attribute sadəcə hərf, rəqəm və tiredən ibarət ola bilər.",
    "alpha_num" => ":attribute sadəcə hərf və rəqəmlərdən ibarət ola bilər.",
    "array" => ":attribute ardıcıllıq olmalıdır.",
    "before" => ":attribute :date tarixindən əvvəl bir tarix olmalıdır.",
    "between" => [
        "numeric" => ":attribute :min və :max arasında bir dəyər olmalıdır.",
        "file" => ":attribute :min və :max kilobayt arasında olmalıdır.",
        "string" => ":attribute :min və :max arasında xanadan ibarət olmalıdır.",
        "array" => ":attribute :min və :max arasında elementə sahib ola bilər.",
    ],
    "boolean" => ":attribute sahəsi ya doğru, ya da səhv dəyərə sahib olmalıdır.",
    "confirmed" => ":attribute təsdiqi ilə uyğun gəlmir.",
    "date" => ":attribute etibarlı tarix deyil.",
    "date_format" => ":attribute :format formatına uyğun gəlmir.",
    "different" => ":attribute və :other fərqli olmalıdır.",
    "digits" => ":attribute :digits rəqəmli olmalıdır.",
    "digits_between" => ":attribute ən az :min və ən çox :max rəqəmli olmalıdır.",
    "email" => ":attribute etibarlı e-poçt adresi olmalıdır.",
    "filled" => ":attribute sahəsi məcburidir.",
    "exists" => "Seçilmiş :attribute etibarli deyil.",
    "image" => ":attribute şəkil olmalıdır.",
    "in" => "Seçilmiş :attribute etibarli deyil.",
    "integer" => ":attribute tam ədəd olmalıdır.",
    "ip" => ":attribute etibarlı IP (İnternet Protokol) adresi olmalıdır.",
    "max" => [
        "numeric" => ":attribute ən çox :max ola bilər.",
        "file" => ":attribute :max kilobaytdan çox olmamalıdır.",
        "string" => ":attribute ən çox :max xanalı ola bilər.",
        "array" => ":attribute ən çox :max elementə sahib ola bilər.",
    ],
    "mimes" => ":attribute :values tiplərindən birində fayl olmalıdır.",
    "min" => [
        "numeric" => ":attribute ən az :min ola bilər.",
        "file" => ":attribute :min kilobaytdan az olmamalıdır.",
        "string" => ":attribute ən az :min xanalı ola bilər.",
        "array" => ":attribute ən az :min elementə sahib ola bilər.",
    ],
    "not_in" => "Seçilmiş :attribute etibarli deyil.",
    "numeric" => ":attribute rəqəmlərdən ibarət olmalıdır.",
    "regex" => ":attribute formatı düzgün deyil.",
    "required" => ":attribute sahəsi məcburidir.",
    "required_if" => ":attribute sahəsi, :other :value dəyəri aldığında məcburidir.",
    "required_with" => ":attribute sahəsi :values dəyəri mövcud olduğunda məcburidir.",
    "required_with_all" => ":attribute sahəsi :values dəyərləri mövcud olduğunda məcburidir.",
    "required_without" => ":attribute sahəsi :values dəyəri mövcud olmadığında məcburidir.",
    "required_without_all" => ":attribute sahəsi :values dəyərlərinin heç biri mövcud olmadığında məcburidir.",
    "same" => ":attribute və :other eyni olmalıdır.",
    "size" => [
        "numeric" => ":attribute :size rəqəmli olmalıdır.",
        "file" => ":attribute :size kilobayt olmalıdır.",
        "string" => ":attribute :size xanalı olmalıdır.",
        "array" => ":attribute :size elementə sahib olmalıdır.",
    ],
    "unique" => ":attribute artıq istifadə olunmaqdadır.",
    "url" => ":attribute formatı səhvdir.",
    "timezone" => ":attribute etibarlı saat qurşağı deyil.",

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
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
