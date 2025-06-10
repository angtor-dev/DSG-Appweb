<?php


/**
 * Establece valores en propiedades de la clase.
 *
 * Recibe un array asociativo clave-valor y asigna los valores a las
 * propiedades correspondientes. Si la propiedad existe como setter, llama
 * al setter. Si la propiedad existe como propiedad de lectura y escritura,
 * asigna el valor directamente.
 *
 * @param array $data
 * @return void
 */
// public function setterArray(array $data) : void
// {
//     foreach ($data as $key => $value) {
//         $propiedad = $key;
//         $setterMethod = 'set_' . $propiedad;
//         if(method_exists($this, $setterMethod)){
//             $this->$setterMethod($value);
//         } elseif(property_exists($this, $propiedad)){
//             $this->$propiedad = $value;
//         }
//     }
// }
?>