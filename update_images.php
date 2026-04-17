<?php

use App\Models\Category;

$categories_data = [
    'operating-room-equipment' => 'https://www.medline.com/media/mkt/pages/surgery-operating-room/operating-room-equipment.jpg',
    'laboratory-equipment' => 'https://www.shutterstock.com/image-photo/modern-medical-laboratory-equipment-scientific-600nw-2152865389.jpg',
    'icu-monitoring' => 'https://www.philips.com/c-dam/b2bhc/master/resource-catalog/landing/patient-monitoring/patient-monitoring-hero.jpg',
    'medical-imaging' => 'https://www.siemens-healthineers.com/en-us/imaging/mri/magnetic-resonance-imaging-mri-scanners/magnetom-free-max/_jcr_content/root/container/container_1462057973/container/image.coreimg.jpeg/1638274536652/siemens-healthineers-mri-magnetom-free-max.jpeg',
    'dentistry-equipment' => 'https://www.dentsplysirona.com/en/explore/treatment-centers/intego/_jcr_content/root/container/container/image.coreimg.jpeg/1605624444444/intego-treatment-center.jpeg',
    'orthopedics-rehabilitation' => 'https://www.medline.com/media/mkt/pages/orthopedics/orthopedic-supplies.jpg',
    'medical-consumables' => 'https://www.medline.com/media/mkt/pages/medical-supplies/medical-supplies-hero.jpg',
    'dialysis-equipment' => 'https://www.freseniusmedicalcare.com/fileadmin/_processed_/d/e/csm_5008S_CorDiax_Haemodialysis_System_5008S_CorDiax_f284a1e944.jpg',
    'cardiology-equipment' => 'https://www.gehealthcare.com/-/jssmedia/feature/gehc/imaging/cardiovascular/cardiovascular-hero.jpg',
    'test' => 'https://www.medline.com/media/mkt/pages/nursing-home-supplies/nursing-home-supplies-hero.jpg',
];

foreach ($categories_data as $slug => $imageUrl) {
    $category = Category::where('slug', $slug)->first();
    if ($category) {
        $category->update([
            'featured_image' => $imageUrl,
        ]);
        echo "Updated image for: {$category->name}\n";
    }
}
