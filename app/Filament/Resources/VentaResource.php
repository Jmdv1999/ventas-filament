<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Models\Producto;
use App\Models\Venta;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as TablesActionsAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Grid::make(12)->schema([
                    Forms\Components\Select::make('cliente_id')

                        ->relationship(name: "cliente", titleAttribute: "nombre")
                        ->preload()
                        ->searchable()
                        ->required()
                        ->columnSpan(8),
                    Forms\Components\TextInput::make('total')
                        ->numeric()
                        ->prefix('$')
                        ->columnSpan(4),
                    Forms\Components\Repeater::make('detalle')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('producto_id')
                                ->relationship('producto', 'descripcion')
                                ->required()

                                ->options(function (callable $get) {
                                    $productomarcado = collect($get('detalle'))->pluck('producto_id');
                                    
                                    return Producto::whereNotIn('id', $productomarcado)
                                        ->where('cantidad', '>', 0)
                                        ->pluck('descripcion', 'id');
                                })
                                ->reactive()

                                ->afterStateUpdated(fn ($state, callable $set) => $set('precio_unitario', Producto::find($state)?->precio ?? 0))
                                ->rule(function ($get, $state) {
                                    $productosSeleccionados = collect($get('detalle'))->pluck('producto_id');
                                    return $productosSeleccionados->contains($state) ? 'unique' : '';
                                }),
                            Forms\Components\TextInput::make('cantidad')
                                ->integer()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('subtotal', $get('cantidad') * $get('precio_unitario')))

                                ->rule(function ($get) {
                                    $productoId = $get('producto_id');
                                    $producto = Producto::find($productoId);
                                    return $producto ? 'max:' . $producto->cantidad : 'max:0';
                                }),
                            Forms\Components\TextInput::make('precio_unitario')
                                ->numeric()
                                ->required()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('subtotal')
                                ->numeric()
                                ->prefix('$')

                        ])
                        ->columns(4)
                        ->columnSpan(12)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('total', collect($state)->sum('subtotal')))
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->prefix('$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                TablesActionsAction::make('verDetalles')
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->action(function (Venta $record, $livewire) {
                        $livewire->emit('abrirModalDetalles', $record->id);
                    })
                    ->modalHeading('Detalles de la Venta')
                    ->modalSubmitAction(false)
                    ->closeModalByClickingAway(true)
                    ->modalCancelAction(false)
                    ->modalWidth('lg')
                    ->modalContent(function (Venta $record) {
                        return view('filament.resources.venta-resource.detalles', ['venta' => $record]);
                    }),

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
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
