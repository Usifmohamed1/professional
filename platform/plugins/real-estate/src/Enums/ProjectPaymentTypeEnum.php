<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static ProjectPaymentTypeEnum CASH()
 * @method static ProjectPaymentTypeEnum INSTALLMENT()
 */
class ProjectPaymentTypeEnum extends Enum
{
    public const CASH = 'cash';

    public const INSTALLMENT = 'installment';

    public static $langPath = 'plugins/real-estate::project.payment_types';
}
