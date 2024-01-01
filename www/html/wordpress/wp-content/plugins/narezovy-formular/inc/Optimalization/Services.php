<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Optimalization;

class Services {
    
    public $code_to_sku = [                                                     // Ardis service to DOD service codes transform
        'Z90006' => '618428',                                                   // Řezání
        'Z90007' => '618428',                                                   // Řezání
        'Z90008' => '618428',                                                   // Řezání
        'Z90018' => '618452',                                                   // Řezání PD
        'Z90021' => '618425',                                                   // Olepování do 25mm
        'Z90022' => '618451',                                                   // Olepování nad 25mm    
        'Z90023' => '618451',                                                   // Olepování nad 25mm
        'Z90028' => '618422'                                                    // tupl
    ];

    public $aditional_services = [                                              // aditional services for every order
        ['ItemCode' => '618427', 'Quantity' => 1],                              // packing
        ['ItemCode' => '618424', 'Quantity' => 1]                               // optimalization
    ];


    
    public function get_DOD_services($items_list){

        foreach ($items_list as $key => $value) {
            if (array_key_exists($value['ItemCode'], $this->code_to_sku)) {
                $items_list[$key]['ItemCode'] = $this->code_to_sku[$value['ItemCode']];
            }

            if($items_list[$key]['ItemCode'] == '618428' && $items_list[$key]['Quantity'] <= 10) {             // cutting less then 10m on single board
                $items_list[$key]['ItemCode'] = '618426';
                $items_list[$key]['Quantity']= 1;
            }
        }
        
        $unique_items_list = $this->unique_services($items_list);                   // add up duplicated services
        
        $to_return = array_merge($unique_items_list, $this->aditional_services);
        
        return $to_return;
    }    

    private function unique_services($items_list) {                                 // there can be duplicated services, beacouse one DOD service code can be equal to multiple ardis codes

        $uniqueItems = [];
        foreach ($items_list as $item) {
            $itemCode = $item['ItemCode'];

            if (isset($uniqueItems[$itemCode])) {
                $uniqueItems[$itemCode]['Quantity'] +=  (float) $item['Quantity'];
            } else {
                $uniqueItems[$itemCode] = $item;
                $uniqueItems[$itemCode]['Quantity'] = (float) $item['Quantity'];
            }
        }
        
        return $uniqueItems;
    }
    
}