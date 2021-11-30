<?php

namespace App\Http;

// use App\ChilexpressPaymentCalculation;
use Config;
use Exception;
use App\Region;
use App\Models\Commune;
// use Webkul\Checkout\Facades\Cart;
// use Webkul\Checkout\Models\CartShippingRate;
// use Webkul\Core\Models\CountryState;
// use Webkul\Shipping\Facades\Shipping;
// use Webkul\Shipping\Services\ChilexpressService;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use App\Services\ChilexpressService;
use Barryvdh\Debugbar\Facade as Debugbar;


/**
 * Class Chilexpress.
 *
 */
class Chilexpress
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'chilexpress';

    protected $service;

    protected $states;

    const CODE_ATTRIBUTE_WEIGHT = 22;
    const CODE_ATTRIBUTE_HEIGHT = 20;
    const CODE_ATTRIBUTE_WIDTH = 19;
    const CODE_ATTRIBUTE_LENGTH = 21;

    const PRODUCT_TYPE_ENCOMIENDA = '3';

    const SERVICE_TYPE_DIA_HABIL_SIGUIENTE = 3;
    const SERVICE_TYPE_DIA_HABIL_SUBSIGUIENTE = 4;
    const SERVICE_TYPE_TERCER_DIA_HABIL = 5;


    public function __construct()
    {
        $this->service = app(ChilexpressService::class);

        $this->states = [
            'Tarapacá' => 'R1',
            'Antofagasta' => 'R2',
            'Atacama' => 'R3',
            'Coquimbo' => 'R4',
            'Valparaíso' => 'R5',
            'Región del Libertador Gral. Bernardo O’Higgins' => 'R6',
            'Región del Maule' => 'R7',
            'Región del Biobío' => 'R8',
            'Región de la Araucanía' => 'R9',
            'Región Metropolitana de Santiago' => 'RM',
            'Región de Los Lagos' => 'R10',
            'Región Aisén del Gral. Carlos Ibáñez del Campo' => 'R11',
            'Región de Magallanes y de la Antártica Chilena' => 'R12',
            'Región de Los Ríos' => 'R14',
            'Arica y Parinacota' => 'R15',
            'NUBLE' => 'R16',
        ];




        $this->delivery = [
            'ACHA' => 'ACHAO',
            'AHOS' => 'ALTO HOSPICIO',
            'ALAM' => 'LOS ALAMOS',
            'ALGA' => 'ALGARROBO',
            'ANCU' => 'ANCUD',
            'ANDA' => 'ANDACOLLO',
            'ANGO' => 'ANGOL',
            'ANTO' => 'ANTOFAGASTA',
            'ARAU' => 'ARAUCO',
            'ARIC' => 'ARICA',
            'BUIN' => 'BUIN',
            'BULN' => 'BULNES',
            'CABI' => 'CABILDO',
            'CABR' => 'CABRERO',
            'CALA' => 'CALAMA',
            'CALB' => 'CALBUCO',
            'CALD' => 'CALDERA',
            'CANE' => 'CANETE',
            'CARA' => 'CARAHUE',
            'CART' => 'CARTAGENA',
            'CASA' => 'CASABLANCA',
            'CAST' => 'CASTRO',
            'CAUQ' => 'CAUQUENES',
            'CCHA' => 'CONCHALI',
            'CCON' => 'CONCON',
            'CHAN' => 'CHANARAL',
            'CHCH' => 'CHILE CHICO',
            'CHEP' => 'CHEPICA',
            'CHIG' => 'CHIGUAYANTE',
            'CHIL' => 'CHILLAN',
            'CHIM' => 'CHIMBARONGO',
            'CHON' => 'CHONCHI',
            'CNAV' => 'CERRO NAVIA',
            'COCH' => 'COCHRANE',
            'COEL' => 'COELEMU',
            'COIC' => 'COIHUECO',
            'COIN' => 'COINCO',
            'COLB' => 'COLBUN',
            'COLI' => 'COLINA',
            'COLL' => 'COLLIPULLI',
            'COLT' => 'COLTAUCO',
            'COMB' => 'COMBARBALA',
            'CONC' => 'CONCEPCION',
            'CONS' => 'CONSTITUCION',
            'COPI' => 'COPIAPO',
            'COQU' => 'COQUIMBO',
            'CORO' => 'CORONEL',
            'COYH' => 'COYHAIQUE',
            'CRCV' => 'CURACAVI',
            'CTAN' => 'CALERA DE TANGO',
            'CURA' => 'CURANILAHUE',
            'CURC' => 'CURACAUTIN',
            'CURI' => 'CURICO',
            'DALC' => 'DALCAHUE',
            'DIEG' => 'DIEGO DE ALMAGRO',
            'DONI' => 'DONIHUE',
            'ECAR' => 'EL CARMEN',
            'ECEN' => 'ESTACION CENTRAL',
            'ELBO' => 'EL BOSQUE',
            'ELMO' => 'EL MONTE',
            'ENEA' => 'ENEA EXPRESS',
            'FREI' => 'FREIRINA',
            'FRER' => 'FREIRE',
            'FRUT' => 'FRUTILLAR',
            'FUTR' => 'FUTRONO',
            'GORB' => 'GORBEA',
            'GRAN' => 'GRANEROS',
            'HAQI' => 'HUALQUI',
            'HIJU' => 'HIJUELAS',
            'HORP' => 'HORNOPIREN',
            'HPEN' => 'HUALPEN',
            'HUAS' => 'HUASCO',
            'HUEC' => 'HUECHURABA',
            'ILLA' => 'ILLAPEL',
            'IMAI' => 'ISLA DE MAIPO',
            'INDE' => 'INDEPENDENCIA',
            'INTE' => 'INTERNACIONAL',
            'IQUI' => 'IQUIQUE',
            'LACA' => 'LA CALERA',
            'LACI' => 'LA CISTERNA',
            'LACR' => 'LA CRUZ',
            'LAFL' => 'LA FLORIDA',
            'LAGR' => 'LA GRANJA',
            'LAJA' => 'LAJA',
            'LALI' => 'LA LIGUA',
            'LAMP' => 'LAMPA',
            'LANC' => 'LANCO',
            'LAND' => 'LOS ANDES',
            'LANG' => 'LOS ANGELES',
            'LAPI' => 'LA PINTANA',
            'LARE' => 'LA REINA',
            'LASE' => 'LA SERENA',
            'LAUN' => 'LA UNION',
            'LAUT' => 'LAUTARO',
            'LCAB' => 'LAS CABRAS',
            'LCHE' => 'LITUECHE',
            'LCON' => 'LAS CONDES',
            'LEBU' => 'LEBU',
            'LIMA' => 'LIMACHE',
            'LINA' => 'LINARES',
            'LLAG' => 'LOS LAGOS',
            'LLAN' => 'LLANQUIHUE',
            'LLAY' => 'LLAY LLAY',
            'LMUE' => 'LOS MUERMOS',
            'LOBA' => 'LO BARNECHEA',
            'LOES' => 'LO ESPEJO',
            'LOLO' => 'LOLOL',
            'LONC' => 'LONCOCHE',
            'LONG' => 'LONGAVI',
            'LOPR' => 'LO PRADO',
            'LOSC' => 'CERRILLOS',
            'LOTA' => 'LOTA',
            'LRAN' => 'LAGO RANCO',
            'LVIL' => 'LOS VILOS',
            'MACH' => 'MACHALI',
            'MACU' => 'MACUL',
            'MALO' => 'MALLOA',
            'MARC' => 'MARCHIGUE',
            'MARI' => 'MARIA ELENA',
            'MAUL' => 'MAULLIN',
            'MEJI' => 'MEJILLONES',
            'MELI' => 'MELIPILLA',
            'MIPU' => 'MAIPU',
            'MOLI' => 'MOLINA',
            'MOPA' => 'MONTE PATRIA',
            'MULC' => 'MULCHEN',
            'NACI' => 'NACIMIENTO',
            'NANC' => 'NANCAGUA',
            'NEGR' => 'NEGRETE',
            'NOGA' => 'NOGALES',
            'NUNO' => 'NUNOA',
            'NVAI' => 'NUEVA IMPERIAL',
            'OLIV' => 'OLIVAR',
            'OLMU' => 'OLMUE',
            'OSOR' => 'OSORNO',
            'OVAL' => 'OVALLE',
            'PAIL' => 'PAILLACO',
            'PAIN' => 'PAINE',
            'PALM' => 'PALMILLA',
            'PALT' => 'PUENTE ALTO',
            'PANG' => 'PANGUIPULLI',
            'PARR' => 'PARRAL',
            'PAYS' => 'PUERTO AYSEN',
            'PCIS' => 'PUERTO CISNES',
            'PEDR' => 'PEDRO AGUIRRE CERDA',
            'PEMU' => 'PEMUCO',
            'PENA' => 'PENAFLOR',
            'PENC' => 'PENCO',
            'PERA' => 'PERALILLO',
            'PEUM' => 'PEUMO',
            'PHUR' => 'PADRE HURTADO',
            'PICD' => 'PICHIDEGUA',
            'PICH' => 'PICHILEMU',
            'PINT' => 'PINTO',
            'PIRQ' => 'PIRQUE',
            'PITR' => 'PITRUFQUEN',
            'PLAC' => 'PLACILLA SEXTA REGION',
            'PLCA' => 'PADRE LAS CASAS',
            'PLOL' => 'PENALOLEN',
            'PMON' => 'PUERTO MONTT',
            'PNAT' => 'NATALES',
            'PORV' => 'PORVENIR',
            'POZO' => 'POZO ALMONTE',
            'PROV' => 'PROVIDENCIA',
            'PUCH' => 'PUCHUNCAVI',
            'PUCO' => 'PUCON',
            'PUDA' => 'PUDAHUEL',
            'PUNI' => 'PUNITAQUI',
            'PUNT' => 'PUNTA ARENAS',
            'PURE' => 'PUREN',
            'PURR' => 'PURRANQUE',
            'PUYG' => 'PUYEHUE',
            'PVAR' => 'PUERTO VARAS',
            'QILI' => 'QUILICURA',
            'QLTA' => 'QUILLOTA',
            'QNOR' => 'QUINTA NORMAL',
            'QSCO' => 'EL QUISCO',
            'QTIL' => 'QUINTA DE TILCOCO',
            'QUEL' => 'QUELLON',
            'QUEM' => 'QUEMCHI',
            'QUIL' => 'QUILPUE',
            'QUIN' => 'QUINTERO',
            'QUIR' => 'QUIRIHUE',
            'QULL' => 'QUILLON',
            'RANC' => 'RANCAGUA',
            'RECO' => 'RECOLETA',
            'RENC' => 'RENCA',
            'RENG' => 'RENGO',
            'REQU' => 'REQUINOA',
            'RIOB' => 'RIO BUENO',
            'RNCO' => 'RENAICO',
            'RNEG' => 'RIO NEGRO',
            'ROME' => 'ROMERAL',
            'SALA' => 'SALAMANCA',
            'SANR' => 'SAN ROSENDO',
            'SANT' => 'SAN ANTONIO',
            'SBAR' => 'SANTA BARBARA',
            'SBER' => 'SAN BERNARDO',
            'SCAR' => 'SAN CARLOS',
            'SCRU' => 'SANTA CRUZ',
            'SDGO' => 'SANTO DOMINGO',
            'SFEL' => 'SAN FELIPE',
            'SFER' => 'SAN FERNANDO',
            'SFRA' => 'SAN FRANCISCO DE MOSTAZAL',
            'SGOR' => 'SIERRA GORDA',
            'SIGN' => 'SAN IGNACIO',
            'SJAV' => 'SAN JAVIER',
            'SJOA' => 'SAN JOAQUIN',
            'SMIG' => 'SAN MIGUEL',
            'SPAB' => 'SAN PABLO',
            'SPAT' => 'SAN PEDRO DE ATACAMA',
            'SPED' => 'SAN PEDRO DE LA PAZ',
            'SRAM' => 'SAN RAMON',
            'STGO' => 'SANTIAGO CENTRO',
            'SVIC' => 'SAN VICENTE DE TAGUA TAGUA',
            'TABO' => 'EL TABO',
            'TALA' => 'TALAGANTE',
            'TALC' => 'TALCA',
            'TALT' => 'TALTAL',
            'TAMA' => 'TIERRA AMARILLA',
            'TEMU' => 'TEMUCO',
            'TENO' => 'TENO',
            'THNO' => 'TALCAHUANO',
            'TILT' => 'TIL TIL',
            'TOCO' => 'TOCOPILLA',
            'TOME' => 'TOME',
            'TRAI' => 'TRAIGUEN',
            'TUCA' => 'TUCAPEL',
            'VALD' => 'VALDIVIA',
            'VALE' => 'VILLA ALEMANA',
            'VALG' => 'VILLA ALEGRE',
            'VALL' => 'VALLENAR',
            'VALP' => 'VALPARAISO',
            'VICT' => 'VICTORIA',
            'VICU' => 'VICUNA',
            'VILL' => 'VILLARRICA',
            'VINA' => 'VINA DEL MAR',
            'VITA' => 'VITACURA',
            'YUMB' => 'YUMBEL',
            'YUNG' => 'YUNGAY',
        ];
    }


    /**
     * Returns rate for Chilexpress
     *
     * @return CartShippingRate|false
     */
    public function calculateItem(CartItem $item, $iddestineCommune)
    {

        $result = null;
        $product =  $item->product;
        // if(!isset($cart->address_commune_id)){
        //     dd('no hay comuna seleccionada');
        //     return null;
        // }
        //Get Origin Commune
        $sellerAddress = $product->seller->addresses_data;


        $originCommune = Commune::find($sellerAddress[0]['commune_id']);
        $originProvince = $originCommune->attribute_province;

        $originState = $originProvince->attribute_region->name;
        // $originState = strtoupper($originState);
        // $originState = $this->replaceSpecialCharacters($originState);

        $originCoverages = $this->service->coverage($this->states[$originState]);



        //$sellerCity = strtoupper($originCommune->name);
        //$sellerCity = $this->replaceSpecialCharacters($sellerCity);
        if(!isset($originCommune->shipping_code)){
            $result['is_available'] = false;
            $result['message'] =  'Comuna del vendedor no configurada';
            return $result;
        }
        $sellerCity = json_decode($originCommune->shipping_code);

        $originCommuneCoverage = collect($originCoverages->coverageAreas)->where('coverageName', $sellerCity[0]->value)->first();

        if (empty($originCommuneCoverage)) {

            // return [
            //     'is_available' => false,
            //     'message' => 'El metodo de envio Chilexpress no esta disponible desde la comuna del vendedor'
            // ];


            $result['is_available'] = false;
            $result['message'] =  'El método de envio Chilexpress no esta disponible desde la comuna del vendedor';
            return $result;
        }

        //Get Destine Commune
        $destineCommune = Commune::find($iddestineCommune);

        $destineProvince = $destineCommune->attribute_province;

        $destineState = $destineProvince->attribute_region->name;
        // $destineState = strtoupper($destineState);
        // $destineState = $this->replaceSpecialCharacters($destineState);

        $destineCoverages = $this->service->coverage($this->states[$destineState]);

        //$customerCity = strtoupper($destineCommune->name);
        //$customerCity = $this->replaceSpecialCharacters($customerCity);

        $customerCity = json_decode($destineCommune->shipping_code);

        // If there is no coverage for the selected destine, we mark it so we can return it later

        $destineCommuneCoverage = collect($destineCoverages->coverageAreas)->where('coverageName', $customerCity[0]->value)->first();

        if (empty($destineCommuneCoverage)) {
            // return [
            //     'is_available' => false,
            //     'message' => 'El metodo de envio Chilexpress no esta disponible para la comuna de destino seleccionada'
            // ];
            $result['is_available'] = false;
            $result['message'] =  'El metodo de envio Chilexpress no esta disponible para la comuna de destino seleccionada';
            return $result;
        }
        $tmpitem =  [
            'item_id' => $item->id,
            'originCountyCode' => $originCommuneCoverage->countyCode,
            'destinationCountyCode' => $destineCommuneCoverage->countyCode,
            'package' => [
                'weight' => $product->weight  * $item->qty ? number_format($product->weight * $item->qty , 2) : '0',
                'height' => $product->height * $item->qty  ? number_format($product->height * $item->qty , 2) : '0',
                'width' =>  $product->width * $item->qty  ? number_format($product->width * $item->qty , 2) : '0',
                'length' => $product->depth * $item->qty  ? number_format($product->depth * $item->qty , 2) : '0',
            ],
            'productType' => self::PRODUCT_TYPE_ENCOMIENDA,
            'contentType' => '1',
            'declaredWorth' => $product->price,
            'deliveryTime' => 0,
        ];



        $calculation = $this->service->calculate($tmpitem);

        if (isset($calculation->data->courierServiceOptions)) {
            $servicesOptions = collect($calculation->data->courierServiceOptions);

            $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_DIA_HABIL_SIGUIENTE)->first();

            if (empty($service)) {
                $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_DIA_HABIL_SUBSIGUIENTE)->first();
            }

            if (empty($service)) {
                $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_TERCER_DIA_HABIL)->first();
            }

            // TO DO:
            // Save the type of service so we can show it later to the client and to the seller so they can
            // know that type of service shipping they have to do

            if (isset($service)) {
                // $service->cart_item_id = $item['item_id'];

                // $calculations[$sellerId][] = $service;
                $tmpitem['service'] = $service;

                $result['is_available'] = true;
                $result['message'] =  'ok';
                $result['item'] =  $tmpitem;
                return $result;

                return $tmpitem;
                // If there is no serviceType for the shipping, return "Chilexpress not available"
            } else {
                return [
                    'is_available' => false,
                    'message' => 'El método de envio Chilexpress no tiene opciones de envio para la comuna de destino'
                ];
            }
        }
    }



     /**
     * Returns rate for Chilexpress
     *
     * @return CartShippingRate|false
     */
    public function calculateItemBySeller($itemShipping,$sellerId, $communeOrigin, $communeDestine)
    {


        $result = null;
        //$product =  $item->product;
        // if(!isset($cart->address_commune_id)){
        //     dd('no hay comuna seleccionada');
        //     return null;
        // }
        //Get Origin Commune
        //$sellerAddress = $product->seller->addresses_data;


        $originCommune = Commune::find($communeOrigin);
        $originProvince = $originCommune->attribute_province;

        $originState = $originProvince->attribute_region->name;
        // $originState = strtoupper($originState);
        // $originState = $this->replaceSpecialCharacters($originState);

        try {
            $originCoverages = $this->service->coverage($this->states[$originState]);
        }catch(Exception $e){
            $result['is_available'] = false;
            $result['message'] =  'No disponible temporalmente para la comuna del vendedor, seleccione otro método de envio si es posible';
            return $result;
        }



        //$sellerCity = strtoupper($originCommune->name);
        //$sellerCity = $this->replaceSpecialCharacters($sellerCity);
        if(!isset($originCommune->shipping_code)){
            $result['is_available'] = false;
            $result['message'] =  'Comuna del vendedor no configurada';
            return $result;
        }
        $sellerCity = json_decode($originCommune->shipping_code);

        $originCommuneCoverage = collect($originCoverages->coverageAreas)->where('coverageName', $sellerCity[0]->value)->first();

        if (empty($originCommuneCoverage)) {

            // return [
            //     'is_available' => false,
            //     'message' => 'El metodo de envio Chilexpress no esta disponible desde la comuna del vendedor'
            // ];


            $result['is_available'] = false;
            $result['message'] =  'El método de envio Chilexpress no esta disponible desde la comuna del vendedor';
            return $result;
        }

        //Get Destine Commune
        $destineCommune = Commune::find($communeDestine);

        $destineProvince = $destineCommune->attribute_province;

        $destineState = $destineProvince->attribute_region->name;
        // $destineState = strtoupper($destineState);
        // $destineState = $this->replaceSpecialCharacters($destineState);

        try {
            $destineCoverages = $this->service->coverage($this->states[$destineState]);
        }catch(Exception $e){
            $result['is_available'] = false;
            $result['message'] =  'No disponible temporalmente para la comuna de destino, seleccione otro método de envio si es posible';
            return $result;
        }

        //$customerCity = strtoupper($destineCommune->name);
        //$customerCity = $this->replaceSpecialCharacters($customerCity);

        $customerCity = json_decode($destineCommune->shipping_code);

        // If there is no coverage for the selected destine, we mark it so we can return it later

        $destineCommuneCoverage = collect($destineCoverages->coverageAreas)->where('coverageName', $customerCity[0]->value)->first();

        if (empty($destineCommuneCoverage)) {
            // return [
            //     'is_available' => false,
            //     'message' => 'El metodo de envio Chilexpress no esta disponible para la comuna de destino seleccionada'
            // ];
            $result['is_available'] = false;
            $result['message'] =  'El metodo de envio Chilexpress no esta disponible para la comuna de destino seleccionada';
            return $result;
        }

        $tmpitem =  [
            'item_id' => $sellerId,//$itemShipping['sellerId'],
            'originCountyCode' => $originCommuneCoverage->countyCode,
            'destinationCountyCode' => $destineCommuneCoverage->countyCode,
            'package' => [
                'weight' => $itemShipping['shipping']['totalWeight'] ? number_format($itemShipping['shipping']['totalWeight'] , 2) : '0',
                'height' => $itemShipping['shipping']['totalHeight'] ? number_format($itemShipping['shipping']['totalHeight'] , 2) : '0',
                'width' =>  $itemShipping['shipping']['totalWidth']  ? number_format($itemShipping['shipping']['totalWidth']  , 2) : '0',
                'length' => $itemShipping['shipping']['totalDepth']  ? number_format($itemShipping['shipping']['totalDepth'] , 2) : '0',
            ],
            'productType' => self::PRODUCT_TYPE_ENCOMIENDA,
            'contentType' => '1',
            'declaredWorth' => $itemShipping['shipping']['totalPrice'],
            'deliveryTime' => 0,
        ];



        $calculation = $this->service->calculate($tmpitem);

        if (isset($calculation->data->courierServiceOptions)) {
            $servicesOptions = collect($calculation->data->courierServiceOptions);

            $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_DIA_HABIL_SIGUIENTE)->first();

            if (empty($service)) {
                $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_DIA_HABIL_SUBSIGUIENTE)->first();
            }

            if (empty($service)) {
                $service = $servicesOptions->where('serviceTypeCode', self::SERVICE_TYPE_TERCER_DIA_HABIL)->first();
            }

            // TO DO:
            // Save the type of service so we can show it later to the client and to the seller so they can
            // know that type of service shipping they have to do

            if (isset($service)) {
                // $service->cart_item_id = $item['item_id'];

                // $calculations[$sellerId][] = $service;
                $tmpitem['service'] = $service;

                $result['is_available'] = true;
                $result['message'] =  'ok';
                $result['item'] =  $tmpitem;
                return $result;

                return $tmpitem;
                // If there is no serviceType for the shipping, return "Chilexpress not available"
            } else {
                return [
                    'is_available' => false,
                    'message' => 'El método de envio Chilexpress no tiene opciones de envio para la comuna de destino'
                ];
            }
        }
    }

    /**
     * replace special characters in a string
     *
     * @param string $strung
     * @return string
     */
    public function replaceSpecialCharacters($string)
    {

        $safeString = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
        $safeString = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $safeString
        );

        $safeString = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $safeString
        );

        $safeString = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $safeString
        );

        $safeString = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $safeString
        );

        $safeString = str_replace('ñ', 'N', $safeString);

        $safeString = strtoupper($safeString);

        return $safeString;
    }
}
