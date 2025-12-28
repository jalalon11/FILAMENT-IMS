<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayActivityLog extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = "Today's Activity Log";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->whereDate('created_at', today())
                    ->with('user')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('h:i A')
                    ->label('Time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper(str_replace('_', ' ', $state)))
                    ->color(fn(ActivityLog $record): string => $record->action_color),
                Tables\Columns\TextColumn::make('description')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->default('System'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('No activity today')
            ->emptyStateDescription('Your activities will appear here as you use the system.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
