<?php

declare(strict_types=1);

/*
 * This file is part of CSBill project.
 *
 * (c) 2013-2016 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\MoneyBundle;

use CSBill\MoneyBundle\Entity\Money;
use Money\Currency;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CSBillMoneyBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /** @var Currency $currency */
        $currency = $this->container->get('currency');

        Money::setBaseCurrency($currency->getCode());
    }
}
