<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Последующие языковые строки содержат сообщения по-умолчанию, используемые
    | классом, проверяющим значения (валидатором). Некоторые из правил имеют
    | несколько версий, например, size. Вы можете поменять их на любые
    | другие, которые лучше подходят для вашего приложения.
    |
    */

    'accepted' => 'You must accept :attribute.',
    'active_url' => 'The :attribute field contains an invalid URL.',
    'after' => 'The :attribute must contain a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute field can only contain letters.',
    'alpha_dash' => 'The :attribute field can only contain letters, numbers, hyphens and underscores.',
    'alpha_num' => 'The :attribute field can only contain letters and numbers.',
    'array' => 'The :attribute field must be an array.',
    'before' => 'The :attribute field must contain a date before :date.',
    'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute field must be between :min and :max.',
        'file' => 'File size in :attribute must be between :min and :max Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be between :min and :max.',
        'array' => 'The number of elements in the :attribute must be between :min and :max.',
    ],
    'boolean' => 'The :attribute field must be a boolean value.',
    'confirmed' => 'The :attribute field does not match the confirmation.',
    'date' => 'The :attribute field is not a date.',
    'date_equals' => 'The :attribute field must be a date equal to :date.',
    'date_format' => 'The :attribute field does not match the format :format.',
    'different' => 'The :attribute and :other fields must be different.',
    'digits' => 'The length of the :attribute numeric field must be :digits.',
    'digits_between' => 'The length of the :attribute numeric field must be between :min and :max.',
    'dimensions' => 'The :attribute field has invalid image dimensions.',
    'distinct' => 'The :attribute field contains a duplicate value.',
    'email' => 'The :attribute field must be a valid email address.',
    'ends_with' => 'The :attribute field must end with one of the following values: :values',
    'exists' => 'The selected value for :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field is required.',
    'gt'             => [
        'numeric' => 'The :attribute field must be greater than :value.',
        'file' => 'File size in :attribute field must be greater than :value Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be greater than :value.',
        'array' => 'The number of elements in the :attribute field must be greater than :value.',
    ],
    'gte' => [
        'numeric' => 'The :attribute field must be greater than or equal to :value.',
        'file' => 'File size in :attribute must be greater than or equal to :value Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be greater than or equal to :value.',
        'array' => 'The number of elements in the :attribute field must be greater than or equal to :value.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected value for :attribute is wrong.',
    'in_array' => 'Field :attribute does not exist in :other.',
    'integer' => 'The :attribute field must be an integer.',
    'ip' => 'The :attribute field must be a valid IP address.',
    'ipv4' => 'The :attribute field must be a valid IPv4 address.',
    'ipv6' => 'The :attribute field must be a valid IPv6 address.',
    'json' => 'The :attribute field must be a JSON string.',
    'lt'       => [
        'numeric' => 'The :attribute field must be less than :value.',
        'file' => 'File size in :attribute must be less than :value Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be less than :value.',
        'array' => 'The number of elements in the :attribute field must be less than :value.',
    ],
    'lte' => [
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'file' => 'File size in :attribute must be less than or equal to :value Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be less than or equal to :value.',
        'array' => 'The number of elements in the :attribute field must be less than or equal to :value.',
    ],
    'max' => [
        'numeric' => 'The :attribute field cannot be greater than :max.',
        'file' => 'File size in :attribute field cannot be more than :max Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field cannot exceed :max.',
        'array' => 'The number of elements in the :attribute field cannot exceed :max.',
    ],
    'mimes' => 'The :attribute field must be one of the following file types: :values.',
    'mimetypes' => 'The :attribute field must be one of the following file types: :values.',
    'min'       => [
        'numeric' => 'The :attribute field must be at least :min.',
        'file' => 'File size in the :attribute field must be at least :min Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be at least :min.',
        'array' => 'The number of elements in the :attribute field must be at least :min.',
    ],
    'not_in' => 'The selected value for :attribute is wrong.',
    'not_regex' => 'The selected format for :attribute is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'password' => 'Wrong password.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute field is in the wrong format.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required when :other is not equal to :values.',
    'required_with' => 'The :attribute field is required when :values ​​is specified.',
    'required_with_all' => 'The :attribute field is required when :values ​​is specified.',
    'required_without' => 'The :attribute field is required when :values ​​is not specified.',
    'required_without_all' => 'The :attribute field is required when none of the :values ​​are specified.',
    'same' => 'The :attribute and :other fields must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be equal to :size.',
        'file' => 'File size in :attribute must be :size Kilobyte(s).',
        'string' => 'The number of characters in the :attribute field must be equal to :size.',
        'array' => 'The number of elements in the :attribute must be equal to :size.',
    ],
    'starts_with' => 'The :attribute field must start with one of the following values: :values',
    'string' => 'The :attribute field must be a string.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'This :attribute value already exists.',
    'uploaded' => 'Upload of the :attribute field failed.',
    'url' => 'The :attribute field is in the wrong format.',
    'uuid' => 'The :attribute field must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Собственные языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Здесь Вы можете указать собственные сообщения для атрибутов.
    | Это позволяет легко указать свое сообщение для заданного правила атрибута.
    |
    | http://laravel.com/docs/validation#custom-error-messages
    | Пример использования
    |
    |   'custom' => [
    |       'email' => [
    |           'required' => 'Нам необходимо знать Ваш электронный адрес!',
    |       ],
    |   ],
    |
    */

    'custom' => [
        'machines_amount' => [
            'required' => 'Number of cars required',
        ],
        'machine_number' => [
            'required' => 'You must select a car number',
        ],
        'species' => [
            'required' => 'You must select a breed',
        ],
        'binding' => [
            'required' => 'Required type of attachment',
        ],
        'type' => [
            'required' => 'Field "Type" is required',
        ],
        'well' => [
            'required' => 'Field "Backhole number" is required',
        ],
        'drill_time' => [
            'required' => 'Field "Drilling time" is required',
        ],
        'model' => [
            'required' => 'The field "Model" is required',
        ],
        'number' => [
            'required' => 'Field "Number" is required',
        ],
        'bucket_capacity' => [
            'required' => 'The field "Bucket capacity" is required',
        ],
        'duration' => [
            'required' => 'Field 'Duration' is required',
        ],
        'factor_value' => [
            'required' => 'The field "Adjustment value" is required',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Собственные названия атрибутов
    |--------------------------------------------------------------------------
    |
    | Последующие строки используются для подмены программных имен элементов
    | пользовательского интерфейса на удобочитаемые. Например, вместо имени
    | поля "email" в сообщениях будет выводиться "электронный адрес".
    |
    | Пример использования
    |
    |   'attributes' => [
    |       'email' => 'электронный адрес',
    |   ],
    |
    */

    'attributes' => [
        'name' => 'Name',
        'username' => 'Nickname',
        'email' => 'Email address',
        'first_name' => 'Name',
        'last_name' => 'Last name',
        'password' => 'Password',
        'password_confirmation' => 'Password confirmation',
        'city' => 'City',
        'country' => 'Country',
        'address' => 'Address',
        'phone' => 'Phone',
        'mobile' => 'Mob. room',
        'age' => 'Age',
        'sex' => 'gender',
        'gender' => 'gender',
        'day' => 'Day',
        'month' => 'Month',
        'year' => 'Year',
        'hour' => 'hour',
        'minute' => 'Minute',
        'second' => 'Second',
        'title' => 'title',
        'content' => 'Content',
        'description' => 'Description',
        'excerpt' => 'Excerpt',
        'date' => 'Date',
        'time' => 'Time',
        'available' => 'Available',
        'size' => 'Size',
    ],

];
