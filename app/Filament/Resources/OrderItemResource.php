<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Ítems de Órdenes';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Orden')
                    ->options(Order::all()->pluck('id', 'id')->map(fn($id) => "Orden #{$id}"))
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('variant_id', null)),

                Forms\Components\Select::make('variant_id')
                    ->label('Variante')
                    ->options(function (callable $get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return [];
                        }

                        return ProductVariant::where('product_id', $productId)
                            ->get()
                            ->pluck('nombre_color', 'id');
                    })
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->label('Nombre del producto')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated()
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, callable $set, callable $get) {
                        if (is_null($state)) {
                            $price = floatval($get('price') ?? 0);
                            $quantity = intval($get('quantity') ?? 0);
                            $set('total', $price * $quantity);
                        }
                    })
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Components\TextInput $component, $state, callable $set, callable $get) {
                        $price = floatval($get('price') ?? 0);
                        $quantity = intval($get('quantity') ?? 0);
                        $set('total', $price * $quantity);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Orden #')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.created_at')
                    ->label('Fecha Orden')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Categoría')
                    ->sortable(),

                Tables\Columns\TextColumn::make('variant.nombre_color')
                    ->label('Variante')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.status')
                    ->label('Estado de Orden')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'procesando' => 'info',
                        'enviado' => 'primary',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('order_id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('order_id')
                    ->label('Orden #')
                    ->options(Order::all()->pluck('id', 'id')->map(fn($id) => "Orden #{$id}"))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('product_category')
                    ->label('Categoría')
                    ->relationship('product.category', 'name'),

                Tables\Filters\SelectFilter::make('order_status')
                    ->label('Estado de Orden')
                    ->relationship('order', 'status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'procesando' => 'Procesando',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Orden')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'order',
                                    fn($query) => $query->whereDate('created_at', '>=', $date)
                                ),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'order',
                                    fn($query) => $query->whereDate('created_at', '<=', $date)
                                ),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['order', 'product', 'product.category', 'variant']);
    }
}
