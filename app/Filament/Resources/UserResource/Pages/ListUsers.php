<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Teacher;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use ZepFietje\FilamentDateTimeSlotPicker\DateTimeSlotPicker;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('book')
                ->label('Open Wizard')
                ->steps([
                    Step::make('Select user')
                        ->schema([
                            Select::make('user')
                                ->options(User::all()->pluck('name', 'id'))
                                ->afterStateUpdated(function ($component) {
                                    $currentStep = $component->getContainer()->getParentComponent();
                                    $nextStep = $currentStep->getContainer()->getComponents()[1] ?? null;
                                    $nextStepForm = $nextStep?->getChildComponentContainer();
                                    // Calling fill() will initialize this part of the form with the correct property
                                    $nextStepForm?->fill();
                                })
                        ]),

                    Step::make('Select date/time')
                        ->schema([
                            TextInput::make('name')
                                ->default(function (Closure $get) {
                                    if ($get('user')) {
                                        $name = User::findOrFail($get('user'))->name;
                                    }

                                    return $name ?? 'No user selected in previous step';
                                }),

                            Select::make('lesson_select')
                                ->options(function (Closure $get) {
                                    if ($get('user')) {
                                        $user = User::findOrFail($get('user'));

                                        $slots = $user
                                            ->lessons()
                                            ->pluck('start', 'id');

                                        return $slots;
                                    }

                                    return $slots ?? [];
                                }),

                            DateTimeSlotPicker::make('lesson')
                                ->label('')
                                ->hintIcon('heroicon-o-clock')
                                ->hint(__('Based on your timezone (:timezone)', ['timezone' => auth()->user()->timezone]))
                                ->required()
                                ->timezone(auth()->user()->timezone)
                                ->options(function (Closure $get) {

                                    /**
                                     * This does not work
                                     */
//                                    if ($get('user')) {
//                                        $user = User::findOrFail($get('user'));
//
//                                        $slots = $user
//                                            ->lessons()
//                                            ->get()
//                                            ->map(function ($item) {
//                                                return [$item['start'], $item['id']];
//                                            })
//                                            ->toArray();
//
//                                    }
//
//                                    return $slots ?? [];


                                    /**
                                     * This also does not work which is strange, the only
                                     * difference to the working example below is the `if ($get('user'))`
                                     * check.
                                     */
//                                    if ($get('user')) {
//                                      $slots = [[now()->addMinutes(30), fake()->uuid]];
//                                    }
//
//                                    return $slots ?? [];

                                    /**
                                     * This works
                                     */
                                    $slots = [[now()->addMinutes(30), fake()->uuid]];
                                    return $slots ?? [];
                                }),
                        ])
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
                ->requiresConfirmation()
        ];
    }
}
