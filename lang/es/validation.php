<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de idioma de validación
    |--------------------------------------------------------------------------
    |
    | El proyecto tiene APP_LOCALE=es desde el inicio, pero nunca existió una
    | carpeta lang/ — sin este archivo, Laravel cae de vuelta a sus mensajes
    | de validación en inglés (comportamiento estándar del framework cuando
    | falta la traducción del locale activo). Este archivo cubre todas las
    | reglas usadas en el proyecto; el array 'attributes' al final traduce
    | los nombres de campo para que el mensaje se lea de forma natural
    | ("El campo número de documento es obligatorio" en vez de "The numero
    | documento field is required").
    |
    */

    'accepted' => 'Debes aceptar el campo :attribute.',
    'accepted_if' => 'Debes aceptar el campo :attribute cuando :other sea :value.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo puede contener letras.',
    'alpha_dash' => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser una lista.',
    'ascii' => 'El campo :attribute solo puede contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'contains' => 'Falta un valor requerido en el campo :attribute.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other sea :value.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con uno de los siguientes valores: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe empezar con uno de los siguientes valores: :values.',
    'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes valores: :values.',
    'enum' => 'El valor seleccionado para :attribute no es válido.',
    'exists' => 'El valor seleccionado para :attribute no es válido.',
    'extensions' => 'El campo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe pesar más de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El campo :attribute debe pesar :value kilobytes o más.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'in_array' => 'El campo :attribute debe existir en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'list' => 'El campo :attribute debe ser una lista.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El campo :attribute debe pesar menos de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute no debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe pesar :value kilobytes o menos.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o menos.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute no debe estar presente.',
    'missing_if' => 'El campo :attribute no debe estar presente cuando :other sea :value.',
    'missing_unless' => 'El campo :attribute no debe estar presente a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute no debe estar presente cuando :values esté presente.',
    'missing_with_all' => 'El campo :attribute no debe estar presente cuando :values estén presentes.',
    'multiple_of' => 'El campo :attribute debe ser un múltiplo de :value.',
    'not_in' => 'El valor seleccionado para :attribute no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other sea :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other sea :value.',
    'present_with' => 'El campo :attribute debe estar presente cuando :values esté presente.',
    'present_with_all' => 'El campo :attribute debe estar presente cuando :values estén presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other sea :value.',
    'prohibited_if_accepted' => 'El campo :attribute está prohibido cuando :other es aceptado.',
    'prohibited_if_declined' => 'El campo :attribute está prohibido cuando :other es rechazado.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits' => 'El campo :attribute prohíbe que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_array_keys' => 'El campo :attribute debe contener valores para: :values.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other sea :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
    'required_if_declined' => 'El campo :attribute es obligatorio cuando :other es rechazado.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => 'Los campos :attribute y :other deben coincidir.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El campo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with' => 'El campo :attribute debe empezar con uno de los siguientes valores: :values.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'ulid' => 'El campo :attribute debe ser un ULID válido.',
    'unique' => 'Ya existe un registro con ese valor en el campo :attribute.',
    'uploaded' => 'El campo :attribute no se pudo subir.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Líneas de idioma de validación personalizadas
    |--------------------------------------------------------------------------
    |
    | Solo para casos donde el mensaje genérico de arriba no basta —
    | referencias cruzadas entre campos con nombre propio de negocio.
    | El resto de mensajes personalizados (como los de la cédula
    | ecuatoriana) viven directamente en su Rule/FormRequest.
    */

    'custom' => [
        'proxima_cita' => [
            'after' => 'La próxima cita debe ser posterior a la fecha de esta consulta.',
        ],
        'tratamientos.*.fecha' => [
            'after_or_equal' => 'La fecha del tratamiento no puede ser anterior a la fecha de la consulta.',
        ],
        'fecha_probable_parto' => [
            'after' => 'La fecha probable de parto debe ser posterior a la fecha de apertura.',
        ],
        'fecha_fin_periodo_lectivo' => [
            'after' => 'El fin del período lectivo debe ser posterior a la fecha de apertura.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de validación personalizados
    |--------------------------------------------------------------------------
    |
    | Traduce el nombre técnico del campo (numero_documento) al nombre que
    | ve la persona en el formulario (número de documento), para que el
    | mensaje final se lea como una frase en español y no como una etiqueta
    | de base de datos a medio traducir.
    */

    'attributes' => [
        // Personal / autenticación
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'current_password' => 'contraseña actual',
        'remember' => 'recordarme',
        'role' => 'rol',

        // Paciente
        'tipo_documento' => 'tipo de documento',
        'numero_documento' => 'número de documento',
        'primer_nombre' => 'primer nombre',
        'segundo_nombre' => 'segundo nombre',
        'primer_apellido' => 'primer apellido',
        'segundo_apellido' => 'segundo apellido',
        'sexo' => 'sexo',
        'fecha_nacimiento' => 'fecha de nacimiento',
        'telefono' => 'teléfono',
        'direccion' => 'dirección',
        'representante_nombre' => 'nombre del representante legal',
        'representante_tipo_documento' => 'tipo de documento del representante legal',
        'representante_documento' => 'documento del representante legal',
        'representante_parentesco' => 'parentesco del representante legal',
        'representante_telefono' => 'teléfono del representante legal',

        // Historia clínica
        'fecha_apertura' => 'fecha de apertura',
        'tipo_vigencia' => 'tipo de vigencia',
        'fecha_probable_parto' => 'fecha probable de parto',
        'fecha_fin_periodo_lectivo' => 'fin del período lectivo',

        // Consulta
        'fecha' => 'fecha',
        'motivo_consulta' => 'motivo de consulta',
        'enfermedad_actual' => 'enfermedad actual',
        'antecedentes_personales' => 'antecedentes personales',
        'antecedentes_personales_marcados' => 'antecedentes personales marcados',
        'antecedentes_familiares' => 'antecedentes familiares',
        'antecedentes_familiares_marcados' => 'antecedentes familiares marcados',
        'presion_arterial' => 'presión arterial',
        'temperatura' => 'temperatura',
        'pulso' => 'pulso',
        'frecuencia_respiratoria' => 'frecuencia respiratoria',
        'examen_estomatognatico' => 'examen del sistema estomatognático',
        'regiones_afectadas' => 'regiones afectadas',
        'diagnosticos' => 'diagnósticos',
        'diagnosticos.*.diagnostico_cie10_id' => 'código CIE-10',
        'diagnosticos.*.descripcion' => 'descripción del diagnóstico',
        'diagnosticos.*.estado' => 'estado del diagnóstico',
        'tratamientos' => 'tratamientos',
        'tratamientos.*.fecha' => 'fecha del tratamiento',
        'tratamientos.*.diagnostico_complicaciones' => 'diagnóstico y complicaciones',
        'tratamientos.*.procedimiento' => 'procedimiento',
        'tratamientos.*.prescripciones' => 'prescripciones',
        'tratamientos.*.proxima_cita' => 'próxima cita',
        'tratamientos.*.estado' => 'estado del tratamiento',
        'tratamientos.*.productos' => 'insumos del tratamiento',
        'tratamientos.*.productos.*.producto_id' => 'producto',
        'tratamientos.*.productos.*.cantidad' => 'cantidad del insumo',

        // Odontograma
        'tipo' => 'tipo',
        'denticion' => 'dentición',
        'hallazgos' => 'hallazgos',
        'hallazgos.*.pieza' => 'pieza dental',
        'hallazgos.*.condicion_id' => 'condición',
        'hallazgos.*.superficie' => 'cara de la pieza',
        'periodontal' => 'registro periodontal',
        'periodontal.*.pieza' => 'pieza dental',
        'periodontal.*.movilidad' => 'grado de movilidad',
        'periodontal.*.recesion' => 'grado de recesión',
        'ihos' => 'registro de higiene oral',
        'ihos.*.sextante_id' => 'sextante',
        'ihos.*.pieza_examinada' => 'pieza examinada',
        'ihos.*.placa' => 'grado de placa',
        'ihos.*.calculo' => 'grado de cálculo',
        'ihos.*.gingivitis' => 'grado de gingivitis',
        'enfermedad_periodontal' => 'enfermedad periodontal',
        'tipo_oclusion' => 'tipo de oclusión',
        'fluorosis' => 'fluorosis',

        // Agenda y citas
        'paciente_id' => 'paciente',
        'profesional_id' => 'profesional',
        'fecha_hora' => 'fecha y hora',
        'duracion_minutos' => 'duración',
        'estado' => 'estado',
        'motivo' => 'motivo',
        'notas' => 'notas',

        // Inventario
        'nombre' => 'nombre',
        'codigo_barras' => 'código de barras',
        'unidad_medida' => 'unidad de medida',
        'categoria' => 'categoría',
        'stock_minimo' => 'stock mínimo',
        'numero_lote' => 'número de lote',
        'fecha_caducidad' => 'fecha de caducidad',
        'fecha_ingreso' => 'fecha de ingreso',
        'proveedor' => 'proveedor',
        'costo_unitario' => 'costo unitario',
        'cantidad_inicial' => 'cantidad inicial',
        'cantidad' => 'cantidad',
        'producto_id' => 'producto',
        'lote_id' => 'lote',
    ],

];
