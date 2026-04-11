<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\Software\Http\Requests\API\Legacy\ProductLegacyRequest;
use Webkul\Software\Models\ProgramEdition;

class ProductLegacyController extends Controller
{
    public function getProduct(ProductLegacyRequest $request): JsonResponse
    {
        $products = ProgramEdition::query()
            ->join('software_programs', 'software_programs.id', '=', 'software_program_editions.program_id')
            ->where('software_programs.name', $request->validated('name'))
            ->selectRaw('software_programs.id as ID')
            ->selectRaw('software_programs.name as Product_Name')
            ->selectRaw('software_program_editions.name as Edition')
            ->selectRaw('software_program_editions.license_cost as License_Cost')
            ->get();

        return response()->json($products);
    }
}
