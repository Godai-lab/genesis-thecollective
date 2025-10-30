<?php

namespace App\Supports;

final class ContentCategory
{
    /**
     * Devuelve el array completo de categorías.
     * @return array<int, array{id:string,name:string,vector_store:string}>
     */
    public static function all(): array
    {
        return [
            ['id' => 'alimentacion',      'name' => 'Alimentación y Bebidas',                      'vector_store' => 'vs_67e2ae4650588191b31d8b8224d0ac47'],
            ['id' => 'moda',              'name' => 'Moda y Belleza',                              'vector_store' => 'vs_67e2c08d6dbc81918e82f768e2e40ca9'],
            ['id' => 'salud',             'name' => 'Salud y Bienestar',                           'vector_store' => 'vs_67e2c1889d80819189d3e9f982470163'],
            ['id' => 'tecnologia',        'name' => 'Tecnología y Electrónica',                    'vector_store' => 'vs_67e2c3211aec8191845de6144c927a39'],
            ['id' => 'educacion',         'name' => 'Educación y Formación',                       'vector_store' => 'vs_67e2c39720e48191b46f2d0a02138ba1'],
            ['id' => 'turismo',           'name' => 'Turismo y Entretenimiento',                   'vector_store' => 'vs_67e2c5798354819186b610df3b1488d1'],
            ['id' => 'automotriz',        'name' => 'Automotriz y Transporte',                     'vector_store' => 'vs_67e2c62a81bc8191b666df23826ce005'],
            ['id' => 'bienes_raices',     'name' => 'Bienes Raíces y Construcción',                'vector_store' => 'vs_67e2c6e31c708191b345afc4c53aa916'],
            ['id' => 'servicios',         'name' => 'Servicios Profesionales',                     'vector_store' => 'vs_67e2c7fc2c6c819188bef5fd84dad404'],
            ['id' => 'deportes',          'name' => 'Deportes y Fitness',                          'vector_store' => 'vs_67e2c9854cd08191b5afe3b76fe4a36c'],
            ['id' => 'medicina',          'name' => 'Salud y Medicina',                            'vector_store' => 'vs_67e2ca0cb83881918d49eed54fdbfdc2'],
            ['id' => 'ecommerce',         'name' => 'E-commerce y Tiendas Online',                 'vector_store' => 'vs_67e2cb4b90d08191a7b757db58fcbd0b'],
            ['id' => 'bienestar',         'name' => 'Bienestar y Estilo de Vida',                  'vector_store' => 'vs_67e2cc0850ec8191abea747444f70980'],
            ['id' => 'hogar',             'name' => 'Hogar y Decoración',                          'vector_store' => 'vs_67e2cdc951e08191a8f4f5a74789a235'],
            ['id' => 'financiero',        'name' => 'Servicios Financieros',                       'vector_store' => 'vs_67e2cf37e2088191ba8654ad414bce1a'],
            ['id' => 'energia',           'name' => 'Energía y Sostenibilidad',                    'vector_store' => 'vs_67e2d462492c8191b316476dd2aec789'],
            ['id' => 'agronegocios',      'name' => 'Agronegocios y Agroindustria',                'vector_store' => 'vs_67e2d55450d881919f61b5ee38eb1075'],
            ['id' => 'medios',            'name' => 'Medios, Comunicación y Contenido Digital',    'vector_store' => 'vs_67e2d75ad19c81918ad687bf856b68b0'],
            ['id' => 'logistica',         'name' => 'Logística y Cadena de Suministro',            'vector_store' => 'vs_WIikAxBR2wfrELhu6On7ALVt'],
            ['id' => 'emprendimiento',    'name' => 'Emprendimiento e Innovación',                 'vector_store' => 'vs_67e2d8fa265c8191a14d802f759ae7e0'],
            ['id' => 'arte',              'name' => 'Arte, Cultura y Creatividad',                 'vector_store' => 'vs_67e2da50d71c8191a17c2df1d768296c'],
            ['id' => 'b2b',               'name' => 'Negocios B2B y Servicios Industriales',       'vector_store' => 'vs_67e2dcfc62ac8191b5048aa381ec4336'],
            ['id' => 'gaming',            'name' => 'Gaming y eSports',                            'vector_store' => 'vs_67e2ddf1a2cc81919b720e353d43c2dd'],
        ];
    }

    /**
     * Devuelve solo los vector_store_id indexados por slug.
     * @return array<string,string>
     */
    public static function vectorMap(): array
    {
        return array_column(self::all(), 'vector_store', 'id');
    }
}