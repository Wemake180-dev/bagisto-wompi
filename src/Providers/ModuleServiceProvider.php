<?php

namespace Webkul\Wompi\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     */
    protected $models = [
        \Webkul\Wompi\Models\WompiTransaction::class,
    ];
}
