<?php

declare(strict_types=1);
/**
 * This file is part of CSBill project.
 *
 * (c) 2013-2017 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\ClientBundle\Tests\Form\Type;

use CSBill\ClientBundle\Entity\Client;
use CSBill\ClientBundle\Form\Type\ClientType;
use CSBill\ClientBundle\Form\Type\ContactDetailType;
use CSBill\CoreBundle\Tests\FormTestCase;
use CSBill\MoneyBundle\Form\Type\CurrencyType;
use Symfony\Component\Form\PreloadedExtension;

class ClientTypeTest extends FormTestCase
{
    public function testSubmit()
    {
        $company = $this->faker->company;
        $url = $this->faker->url;
        $currencyCode = 'USD';

        $formData = [
            'name' => $company,
            'website' => $url,
            'currency' => $currencyCode,
            'contacts' => [],
            'addresses' => [],
        ];

        $object = new Client();
        $object->setName($company);
        $object->setWebsite($url);
        $object->setCurrency($currencyCode);

        $this->assertFormData(ClientType::class, $formData, $object);
    }

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new ContactDetailType([]);

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type, new CurrencyType('en')], []),
        ];
    }
}
