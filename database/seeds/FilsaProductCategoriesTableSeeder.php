<?php

use Illuminate\Database\Seeder;

class FilsaProductCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_categories')->delete();
        
        \DB::table('product_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Infantil y Juvenil',
                'code' => 'infantil-y-juvenil',
                'slug' => 'infantil-y-juvenil',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:31:24',
                'updated_at' => '2020-11-16 12:31:24',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Niños 0 a 5 años',
                'code' => 'ninos-0-a-5-anos',
                'slug' => 'ninos-0-a-5-anos',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 1,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:32:06',
                'updated_at' => '2020-11-16 12:32:06',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Niños 6 a 11 años',
                'code' => 'ninos-6-a-11-anos',
                'slug' => 'ninos-6-a-11-anos',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 1,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:32:41',
                'updated_at' => '2020-11-16 12:32:52',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Juvenil',
                'code' => 'juvenil',
                'slug' => 'juvenil',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 1,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:33:16',
                'updated_at' => '2020-11-16 12:33:16',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Juegos y Ocio',
                'code' => 'juegos-y-ocio',
                'slug' => 'juegos-y-ocio',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 4,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:33:50',
                'updated_at' => '2020-11-16 12:34:18',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Literatura',
                'code' => 'literatura',
                'slug' => 'literatura',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:34:35',
                'updated_at' => '2020-11-16 12:34:35',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Latinoamericana',
                'code' => 'latinoamericana',
                'slug' => 'latinoamericana',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 6,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:34:53',
                'updated_at' => '2020-11-16 12:34:53',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Contemporánea',
                'code' => 'contemporanea',
                'slug' => 'contemporanea',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 6,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:35:26',
                'updated_at' => '2020-11-16 12:35:26',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Novela',
                'code' => 'novela',
                'slug' => 'novela',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 6,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:35:56',
                'updated_at' => '2020-11-16 12:35:56',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Poesía',
                'code' => 'poesia',
                'slug' => 'poesia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 6,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:36:21',
                'updated_at' => '2020-11-16 12:36:21',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Grandes Clásicos',
                'code' => 'grandes-clasicos',
                'slug' => 'grandes-clasicos',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 6,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:37:42',
                'updated_at' => '2020-11-16 12:37:42',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Arte',
                'code' => 'arte',
                'slug' => 'arte',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:38:15',
                'updated_at' => '2020-11-16 12:38:15',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Bellas Artes',
                'code' => 'bellas-artes',
                'slug' => 'bellas-artes',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 12,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:38:52',
                'updated_at' => '2020-11-16 12:38:52',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Diseño',
                'code' => 'diseno',
                'slug' => 'diseno',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 12,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:39:06',
                'updated_at' => '2020-11-16 12:39:06',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Arquitectura y Urbanismo',
                'code' => 'arquitectura-y-urbanismo',
                'slug' => 'arquitectura-y-urbanismo',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 12,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:39:24',
                'updated_at' => '2020-11-16 12:39:24',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Cine y Música',
                'code' => 'cine-y-musica',
                'slug' => 'cine-y-musica',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 12,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:39:47',
                'updated_at' => '2020-11-16 12:39:47',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Ciencias',
                'code' => 'ciencias',
                'slug' => 'ciencias',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:40:09',
                'updated_at' => '2020-11-16 12:40:09',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Ciencias de La Comunicación',
                'code' => 'ciencias-de-la-comunicacin',
                'slug' => 'ciencias-de-la-comunicacin',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:40:28',
                'updated_at' => '2020-11-16 12:40:28',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Ciencias de La Educación',
                'code' => 'ciencias-de-la-educacin',
                'slug' => 'ciencias-de-la-educacin',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:40:57',
                'updated_at' => '2020-11-16 12:40:57',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Ciencias Sociales',
                'code' => 'ciencias-sociales',
                'slug' => 'ciencias-sociales',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:41:14',
                'updated_at' => '2020-11-16 12:41:14',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Derecho',
                'code' => 'derecho',
                'slug' => 'derecho',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:41:29',
                'updated_at' => '2020-11-16 12:41:29',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Historia y Geografía',
                'code' => 'historia-y-geografia',
                'slug' => 'historia-y-geografia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:41:48',
                'updated_at' => '2020-11-16 12:41:48',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Psicología',
                'code' => 'psicologia',
                'slug' => 'psicologia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:42:10',
                'updated_at' => '2020-11-16 12:42:10',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Religión',
                'code' => 'religion',
                'slug' => 'religion',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:42:55',
                'updated_at' => '2020-11-16 12:42:55',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Ciencias Agrarias Y De La Naturaleza',
                'code' => 'ciencias-agrarias-y-de-la-naturaleza',
                'slug' => 'ciencias-agrarias-y-de-la-naturaleza',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:43:17',
                'updated_at' => '2020-11-16 12:43:17',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Ciencias Médicas',
                'code' => 'ciencias-medicas',
                'slug' => 'ciencias-medicas',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:43:34',
                'updated_at' => '2020-11-16 12:43:34',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Zoología y Animales Domésticos',
                'code' => 'zoologia-y-animales-domesticos',
                'slug' => 'zoologia-y-animales-domesticos',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:43:58',
                'updated_at' => '2020-11-16 12:43:58',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Ciencias Físicas y Elementales',
                'code' => 'ciencias-fisicas-y-elementales',
                'slug' => 'ciencias-fisicas-y-elementales',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:44:17',
                'updated_at' => '2020-11-16 12:44:17',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Ingeniería y Tecnología',
                'code' => 'ingenieria-y-tecnologia',
                'slug' => 'ingenieria-y-tecnologia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 17,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:44:43',
                'updated_at' => '2020-11-16 12:44:43',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'Comics',
                'code' => 'comics',
                'slug' => 'comics',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:45:03',
                'updated_at' => '2020-11-16 12:53:45',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Comics',
                'code' => 'comics-2',
                'slug' => 'comics-2',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 30,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:45:31',
                'updated_at' => '2020-11-16 12:45:31',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Ciencia Ficción',
                'code' => 'ciencia-ficcion',
                'slug' => 'ciencia-ficcion',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 30,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:47:02',
                'updated_at' => '2020-11-16 12:47:02',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Manga',
                'code' => 'manga',
                'slug' => 'manga',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 30,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:47:27',
                'updated_at' => '2020-11-16 12:47:27',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Otras Temáticas',
                'code' => 'otras-tematicas',
                'slug' => 'otras-tematicas',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:47:44',
                'updated_at' => '2020-11-16 12:47:44',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Ilustración',
                'code' => 'ilustracion',
                'slug' => 'ilustracion',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:48:01',
                'updated_at' => '2020-11-16 12:48:01',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Turismo y Viajes',
                'code' => 'turismo-y-viajes',
                'slug' => 'turismo-y-viajes',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:48:18',
                'updated_at' => '2020-11-16 12:48:18',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Economía y Negocios',
                'code' => 'economia-y-negocios',
                'slug' => 'economia-y-negocios',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:49:13',
                'updated_at' => '2020-11-16 12:49:13',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Autoayuda',
                'code' => 'autoayuda',
                'slug' => 'autoayuda',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:49:44',
                'updated_at' => '2020-11-16 12:49:56',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Gastronomía',
                'code' => 'gastronomia',
                'slug' => 'gastronomia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:50:12',
                'updated_at' => '2020-11-16 12:50:12',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Fotografía',
                'code' => 'fotografia',
                'slug' => 'fotografia',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:50:35',
                'updated_at' => '2020-11-16 12:50:35',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Informática',
                'code' => 'informatica',
                'slug' => 'informatica',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:50:48',
                'updated_at' => '2020-11-16 12:50:48',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Diccionarios',
                'code' => 'diccionarios',
                'slug' => 'diccionarios',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 34,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:51:09',
                'updated_at' => '2020-11-16 12:51:09',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Entretención',
                'code' => 'entretencion',
                'slug' => 'entretencion',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => NULL,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:51:26',
                'updated_at' => '2020-11-16 12:51:26',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Deporte',
                'code' => 'deporte',
                'slug' => 'deporte',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 43,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:51:41',
                'updated_at' => '2020-11-16 12:51:41',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Hogar Jardín y Manualidades',
                'code' => 'hogar-jardin-y-manualidades',
                'slug' => 'hogar-jardin-y-manualidades',
                'position' => 0,
                'image' => NULL,
                'icon' => 'empty',
                'display_mode' => 'products_and_description',
                'parent_id' => 43,
                'status' => 1,
                'company_id' => 1,
                'created_at' => '2020-11-16 12:53:14',
                'updated_at' => '2020-11-16 12:53:14',
                'deleted_at' => NULL,
                'description' => NULL,
                'json_value' => NULL,
            ),
        ));
        
        
    }
}