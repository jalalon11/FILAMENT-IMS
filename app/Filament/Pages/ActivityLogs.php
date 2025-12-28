<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.activity-logs';

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->with('user'))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->label('Date & Time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->default('System')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper(str_replace('_', ' ', $state)))
                    ->color(fn(ActivityLog $record): string => $record->action_color),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Type')
                    ->formatStateUsing(fn(?string $state) => $state ? class_basename($state) : '-')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'stock_in' => 'Stock In',
                        'stock_out' => 'Stock Out',
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn(Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn(Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('No activity logs')
            ->emptyStateDescription('Activity logs will appear here as users interact with the system.');
    }
}
