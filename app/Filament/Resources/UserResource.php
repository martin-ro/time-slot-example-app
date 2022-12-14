<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;
use ZepFietje\FilamentDateTimeSlotPicker\DateTimeSlotPicker;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('timezone')
                    ->options([
                        'UTC' => 'UTC',
                        'Asia/Manila' => 'Asia/Manila',
                        'Europe/Berlin' => 'Europe/Berlin',
                    ])
                    ->reactive()
                    ->default(fn(Model $record) => $record->timezone)
                    ->afterStateUpdated(function (Model $record, $state) {
                        $record->update(['timezone' => $state]);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('timezone'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('book')
                    ->icon('heroicon-s-plus')
                    ->form([
                        DateTimeSlotPicker::make('lesson')
                            ->hintIcon('heroicon-o-clock')
                            ->hint(__('Based on your timezone (:timezone)', ['timezone' => auth()->user()->timezone]))
                            ->timezone(auth()->user()->timezone)
                            ->options(function (Model $record) {
                                $availableSlots = $record
                                    ->lessons()
                                    ->where('user_id', $record->id)
                                    ->get()
                                    ->map(function ($item) {
                                        return [$item['start'], $item['id']];
                                    })
                                    ->toArray();

                                return $availableSlots;
                            })
                    ])
                    ->modalWidth('xl')
                    ->action(fn(array $data) => [
                        ray($data),

                        Notification::make('Booked')
                            ->title('Booked')
                            ->status('success')
                            ->body('ID: ' . $data['lesson'][1] . ' Start: ' . $data['lesson'][0])
                        ->send()
                    ])
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
