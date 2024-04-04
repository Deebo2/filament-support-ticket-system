<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTickets extends BaseWidget
{

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                ->latest()
                ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->description(fn (Ticket $record): string => $record?->description ?? ''),
                BadgeColumn::make('status')
                ->colors([
                    'danger'=> Ticket::STATUS['Closed'],
                    'warning'=> Ticket::STATUS['archived'],
                    'success'=> Ticket::STATUS['Open']
                ])
                ->sortable(),
                BadgeColumn::make('priority')
                ->colors([
                    'danger'=> Ticket::PRIORITY['High'],
                    'warning'=> Ticket::PRIORITY['Medium'],
                    'success'=> Ticket::PRIORITY['Low']
                ])
                ->sortable(),
                TextColumn::make('assignedTo.name'),
                TextColumn::make('assignedBy.name'),
                TextInputColumn::make('comment'),
                TextColumn::make('created_at')
                ->sortable()
                ->dateTime(),

            ])->paginated(false);
    }
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
