<?php

namespace Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource;
use Webkul\Software\Models\Program;

class ManageProgramFeatures extends ManageRecords
{
    protected static string $resource = ProgramFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateFeatureProducts')
                ->label('Generate Feature Products')
                ->icon('heroicon-o-cube')
                ->color('info')
                ->form([
                    Select::make('program_id')
                        ->label('Program')
                        ->options(Program::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->required()
                        ->searchable(),
                    TextInput::make('base_product_name')
                        ->label('Base Product Name')
                        ->default('PS Features')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    try {
                        DB::transaction(function () use ($data): void {
                            $program = Program::query()->with('features')->findOrFail((int) $data['program_id']);

                            if ($program->features->isEmpty()) {
                                throw new \RuntimeException('This program has no features to generate variants from.');
                            }

                            $templateService = Product::query()
                                ->where('type', ProductType::SERVICE->value)
                                ->whereNull('parent_id')
                                ->orderBy('id')
                                ->first();

                            if (! $templateService) {
                                throw new \RuntimeException('Create at least one service product first to use as a template.');
                            }

                            $baseProduct = Product::query()->firstOrCreate(
                                [
                                    'name'      => (string) $data['base_product_name'],
                                    'parent_id' => null,
                                ],
                                [
                                    'type'            => ProductType::SERVICE->value,
                                    'service_tracking'=> $templateService->service_tracking ?? 'none',
                                    'reference'       => null,
                                    'price'           => 0,
                                    'cost'            => 0,
                                    'enable_sales'    => true,
                                    'enable_purchase' => false,
                                    'is_favorite'     => false,
                                    'is_configurable' => false,
                                    'uom_id'          => $templateService->uom_id,
                                    'uom_po_id'       => $templateService->uom_po_id,
                                    'category_id'     => $templateService->category_id,
                                    'company_id'      => $templateService->company_id,
                                    'creator_id'      => Auth::id(),
                                ]
                            );

                            if (($baseProduct->type?->value ?? $baseProduct->type) !== ProductType::SERVICE->value) {
                                throw new \RuntimeException('Base product must be of type service.');
                            }

                            $program->update(['product_id' => $baseProduct->id]);

                            foreach ($program->features as $feature) {
                                $variantProduct = Product::query()->updateOrCreate(
                                    [
                                        'parent_id' => $baseProduct->id,
                                        'name'      => $feature->name,
                                    ],
                                    [
                                        'type'            => ProductType::SERVICE->value,
                                        'service_tracking'=> $templateService->service_tracking ?? 'none',
                                        'reference'       => null,
                                        'price'           => (float) ($feature->amount ?? 0),
                                        'cost'            => 0,
                                        'enable_sales'    => true,
                                        'enable_purchase' => false,
                                        'is_favorite'     => false,
                                        'is_configurable' => false,
                                        'uom_id'          => $templateService->uom_id,
                                        'uom_po_id'       => $templateService->uom_po_id,
                                        'category_id'     => $templateService->category_id,
                                        'company_id'      => $templateService->company_id,
                                        'creator_id'      => Auth::id(),
                                    ]
                                );

                                $feature->update(['product_id' => $variantProduct->id]);
                            }
                        });

                        Notification::make()
                            ->title('Feature products generated')
                            ->body('Base product and variants were created and linked to program features.')
                            ->success()
                            ->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Generation failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make()->label('New Feature')->icon('heroicon-o-plus-circle'),
        ];
    }
}
