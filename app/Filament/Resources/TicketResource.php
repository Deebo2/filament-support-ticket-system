<?php

namespace App\Filament\Resources;
use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\LabelsRelationManager;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected function getDefaultTableSortColumn(): string {
    //     return 'created_at';
    // }
    // protected function getDefaultFilterDirection(): string {
    //     return 'desc';
    // }

    public static function form(Form $form): Form
    {
        return $form
        ->columns(1)
            ->schema([
                TextInput::make('title')
                ->required(),
                Select::make('priority')
                ->options(self::$model::PRIORITY)
                ->required()->in(self::$model::PRIORITY),
                Select::make('assigned_to')
                ->options(
                User::whereHas('roles', function (Builder $query) {
                    $query->where('title', Role::Roles['Agent']);
                })
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray()
                )
                ->required(),
                Textarea::make('description')
                ->required(),
                Textarea::make('comment'),
                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->description(fn (Ticket $record): string => $record?->description ?? ''),
                SelectColumn::make('status')
                ->disabled(!auth()->user()->hasPermission('ticket_edit'))
                ->options(self::$model::STATUS)
                ->disablePlaceholderSelection()
                ->sortable(),
                BadgeColumn::make('priority')
                ->colors([
                    'danger'=> self::$model::PRIORITY['High'],
                    'warning'=> self::$model::PRIORITY['Medium'],
                    'success'=> self::$model::PRIORITY['Low']
                ])
                ->sortable(),
                TextColumn::make('assignedTo.name'),
                TextColumn::make('assignedBy.name'),
                TextInputColumn::make('comment')
                ->disabled(!auth()->user()->hasPermission('ticket_edit')),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),



            ])
            ->filters([
                SelectFilter::make('Status')
                ->options(self::$model::STATUS),
                SelectFilter::make('Priority')
                ->options(self::$model::PRIORITY),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            CategoriesRelationManager::class,
            LabelsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
