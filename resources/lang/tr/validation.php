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
    "accepted" => ":attribute kabul edilmelidir.",
    "active_url" => ":attribute geçerli URL değildir.",
    "after" => ":attribute :date tarihinden sonra bir tarih olmalıdır.",
    "alpha" => ":attribute sadece harflerden oluşabilir.",
    "alpha_dash" => ":attribute sadece harf, rakam ve tireden oluşabilir.",
    "alpha_num" => ":attribute sadece harf ve rakamlardan oluşabilir.",
    "array" => ":attribute bir dizi olmalıdır.",
    "before" => ":attribute :date tarihinden önce bir tarih olmalıdır.",
    "between" => [
        "numeric" => ":attribute :min ve :max değerleri arasında bir değer alabilir.",
        "file" => ":attribute :min ve :max kilobayt arasında olmalıdır.",
        "string" => ":attribute en az :min ve en fazla :max karakterden oluşabilir.",
        "array" => ":attribute en az :min ve en fazla :max öğeden oluşabilir.",
    ],
    "boolean" => ":attribute alanı doğru veya yanlış olmalıdır.",
    "confirmed" => ":attribute onayıyla eşleşmedi.",
    "date" => ":attribute geçerli bir tarih değil.",
    "date_format" => ":attribute :format formatı ile eşleşmiyor.",
    "different" => ":attribute ve :other faklı olmalılar.",
    "digits" => ":attribute :digits rakamlı olmalıdır.",
    "digits_between" => ":attribute en az :min ve en fazla :max rakamlı olmalıdır.",
    "email" => ":attribute geçerli bir e-posta adresi değil.",
    "filled" => ":attribute alanı zorunludur.",
    "exists" => "Seçilmiş :attribute geçersizdir.",
    "image" => ":attribute bir resim olmak zorunda.",
    "in" => "Seçilmiş :attribute geçersizdir.",
    "integer" => ":attribute tamsayı olmalıdır.",
    "ip" => ":attribute geçerli bir IP adresi olmalıdır.",
    "max" => [
        "numeric" => ":attribute en fazla :max olabilir.",
        "file" => ":attribute :max kilobayttan fazla olamaz.",
        "string" => ":attribute en fazla :max karakter olabilir.",
        "array" => ":attribute en fazla :max öğeye sahip olabilir."
    ],
    "mimes" => ":attribute must be a file of type: :values.",
    "min" => [
        "numeric" => ":attribute en az :min olabilir.",
        "file" => ":attribute :min kilobayttan küçük olamaz.",
        "string" => ":attribute en az :min karakter olabilir.",
        "array" => ":attribute en az :min öğeye sahip olabilir."
    ],
    "not_in" => "Seçilmiş :attribute geçersizdir.",
    "numeric" => ":attribute bir rakam olmalıdır.",
    "regex" => ":attribute formatı yanlıştır.",
    "required" => ":attribute alanı zorunludur.",
    "required_if" => ":attribute alanı :other :value iken zorunludur.",
    "required_with" => ":attribute alanı :values değeri var iken zorunludur.",
    "required_with_all" => ":attribute alanı :values değerleri var iken zorunludur.",
    "required_without" => ":attribute alanı :values değeri yok iken zorunludur.",
    "required_without_all" => ":attribute alanı :values değerlerinin hiçbiri yok iken zorunludur.",
    "same" => ":attribute ve :other eşleşmedi.",
    "size" => [
        "numeric" => ":attribute :size rakamlı olmalıdır.",
        "file" => ":attribute :size kılobayt olmalıdır.",
        "string" => ":attribute :size karakter olmalıdır.",
        "array" => ":attribute :size öğe içermelidir.",
    ],
    "unique" => ":attribute kullanılmaktadır.",
    "url" => ":attribute formatı geçerli değildir.",
    "timezone" => ":attribute geçerli bir zaman dilimi olmalıdır.",

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
