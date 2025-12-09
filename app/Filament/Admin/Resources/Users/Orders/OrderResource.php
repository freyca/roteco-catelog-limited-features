<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Admin\Exports\OrderExporter;
use App\Filament\Admin\Resources\Users\Orders\Pages\CreateOrder;
use App\Filament\Admin\Resources\Users\Orders\Pages\EditOrder;
use App\Filament\Admin\Resources\Users\Orders\Pages\ListOrders;
use App\Models\Address;
use App\Models\BaseProduct;
use App\Models\Order;
use App\Models\ProductSparePart;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-euro';

    public array $product_options = [];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('id')
                        ->name(__('Order id (automatically generated)').':')
                        ->disabled()
                        ->columnSpanFull(),

                    Section::make(__('Customer data'))
                        ->schema([
                            Select::make('user_id')
                                ->relationship('user', 'email')
                                ->label(__('Customer email'))
                                ->searchable()
                                ->preload()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $user_id = $state;

                                    if ($user_id === null) {
                                        $set('shipping_address_id', '');
                                        $set('billing_address_id', '');
                                    }
                                })
                                ->live(onBlur: true)
                                ->hintIconTooltip('asdasd')
                                ->hintAction(
                                    Action::make(__('Open user'))
                                        ->icon('heroicon-o-user-group')
                                        ->url(
                                            function (Get $get): string {
                                                $user_id = $get('user_id');

                                                return $user_id !== null ? route('filament.admin.resources.users.users.edit', $user_id) : route('filament.admin.resources.users.users.index');
                                            },
                                            shouldOpenInNewTab: true
                                        )
                                ),
                            Select::make('shipping_address_id')
                                ->relationship('shippingAddress', 'address')
                                ->options(
                                    function (Get $get) {
                                        return self::getAddressId($get);
                                    }
                                )
                                ->selectablePlaceholder(function (Get $get) {
                                    $user_id = $get('user_id');
                                    $order_id = $get('id');

                                    return match (true) {
                                        $order_id !== null => false,
                                        $user_id === null => true,
                                        default => true,
                                    };
                                })
                                ->columnSpanFull()
                                ->label(__('Shipping address'))
                                ->required(),
                            Select::make('billing_address_id')
                                ->relationship('billingAddress', 'address')
                                ->options(
                                    function (Get $get) {
                                        return self::getAddressId($get);
                                    }
                                )
                                ->selectablePlaceholder(function (Get $get) {
                                    $user_id = $get('user_id');
                                    $order_id = $get('id');

                                    return match (true) {
                                        $order_id !== null => false,
                                        $user_id === null => true,
                                        default => true,
                                    };
                                })
                                ->columnSpanFull()
                                ->label(__('Billing address')),
                        ]),
                ])->columns(2),

                Section::make([
                    static::getProductsRepeater(),
                ]),

                Section::make(__('Payment'))
                    ->schema([
                        TextInput::make('purchase_cost')
                            ->label(__('Price'))
                            ->required()
                            ->numeric(),

                        ToggleButtons::make('payment_method')
                            ->label(__('Payment method'))
                            ->inline()
                            ->options(PaymentMethod::class)
                            ->required(),

                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->inline()
                            ->options(OrderStatus::class)
                            ->required()
                            ->columnSpan('full'),

                    ])->columns(2),
            ])
            ->live();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class),
            ])
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('User'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchase_cost')
                    ->label(__('Purchase cost'))
                    ->money(
                        currency: 'eur',
                        locale: 'es'
                    )
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->badge(),
                TextColumn::make('payment_method')
                    ->label(__('Payment method'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->date()
                    ->label(__('Order date')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getProductsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->label(__('Order products'))
            ->relationship()
            ->schema([
                Select::make('orderable_type')
                    ->options([
                        ProductSparePart::class => 'Repuesto',
                    ])
                    ->afterStateUpdated(function (Set $set) {
                        $set('orderable_id', '');
                    })
                    ->live()
                    ->required()
                    ->columnSpan([
                        'md' => 5,
                    ]),

                Select::make('orderable_id')
                    ->label(__('Product'))
                    ->disabled(function (Get $get) {
                        return ! filled($get('orderable_type'));
                    })
                    ->options(function (Get $get) {
                        if (! filled($get('orderable_type'))) {
                            return;
                        }

                        $class_name = $get('orderable_type');

                        return $class_name::query()->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->distinct()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $class_name = $get('orderable_type');
                        $product = $class_name::find($state);

                        $set('unit_price', $product->price);
                    })
                    ->columnSpan([
                        'md' => 5,
                    ]),

                TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->required(),

                TextInput::make('unit_price')
                    ->label(__('Unit price'))
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->suffix('€')
                    ->columnSpan([
                        'md' => 3,
                    ]),
            ])
            ->defaultItems(1)
            ->columns([
                'md' => 10,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = strval(static::$model);

        return (string) $modelClass::whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Delivered])->count();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function getModelLabel(): string
    {
        return __('Orders');
    }

    public static function setProductPrice(?string $id, string $class_name, Set $set): void
    {
        /**
         * @var ?BaseProduct
         */
        $product = $class_name::find($id);

        if ($product === null) {
            $set('unit_price', '');
            $set('quantity', 1);

            return;
        }

        $price = $product->price_with_discount ? $product->price_with_discount : $product->price;
        $set('unit_price', $price);
    }

    public static function getAddressId(Get $get): ?array
    {
        $user_id = $get('user_id');
        $order_id = $get('id');

        if ($user_id === null && $order_id !== null) {
            return [Order::find(intval($order_id))?->shippingAddress?->address];
        }

        return match ($user_id) {
            null => Address::select('address')->pluck('address')->toArray(),
            default => User::find(intval($user_id))?->shippingAddresses->pluck('address', 'id')->toArray(),
        };
    }
}
