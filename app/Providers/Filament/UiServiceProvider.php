<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class UiServiceProvider extends ServiceProvider
{
    /**
     * Filament UI customization defaults
     * https://github.com/iAmKevinMcKee/roadmap/blob/lesson-4/app/Providers/FilamentUiServiceProvider.php
     */
    public function boot(): void
    {
        // Various table presets
        Table::configureUsing(function (Table $table) {
            return $table
                ->reorderableColumns()
                ->columnManagerColumns(2)
                ->columnManagerTriggerAction(fn (Action $action) => $action->button()->label(__('filament-tables::table.column_manager.heading')))
                ->filtersTriggerAction(fn (Action $action) => $action->button()->label(__('filament-tables::table.filters.heading'))->slideOver()->closeModalByClickingAway())
                ->filtersFormWidth(Width::Small)
                ->paginationPageOptions([10, 25, 50, 100]);
        });

        // Make all columns toggleable
        Column::configureUsing(function (Column $column) {
            return $column
                ->toggleable();
        });

        // Sort- and searchable all text columns
        TextColumn::configureUsing(function (TextColumn $textColumn) {
            return $textColumn
                ->searchable() // BE CAREFUL, you may end up with 500 errors
                ->sortable(); // BE CAREFUL, you may end up with 500 errors
        });

        // Make notifications last 10 seconds by default
        Notification::configureUsing(function (Notification $notification) {
            return $notification->duration(10000);
        });

        // Use your preferred date displays
        Schema::configureUsing(function (Schema $schema) {
            return $schema
                ->defaultDateDisplayFormat('d/m/Y')
                ->defaultDateTimeDisplayFormat('h:i A')
                ->defaultTimeDisplayFormat('d/m/Y h:i A');
        });

        // Add sensible min and max dates so you don't end up with dates like 01/01/0000 or 01/01/3000
        DatePicker::configureUsing(function (DatePicker $datePicker) {
            return $datePicker
                ->minDate(Carbon::createFromDate(1900, 1, 1))
                ->maxDate(now()->addYears(50));
        });

        // Rich editor default settings
        RichEditor::configureUsing(function (RichEditor $richEditor): void {
            $richEditor
                ->columnSpanFull()
                ->toolbarButtons([
                    ['h2', 'h3'],
                    ['bold', 'italic', 'underline', 'link'],
                    ['alignStart', 'alignCenter', 'alignEnd'],
                    ['bulletList', 'orderedList'],
                    ['attachFiles'],
                ]);
        });

        // If an action is a modal, do not close by clicking away and default to slide over
        Action::configureUsing(function (Action $action) {
            $action
                ->closeModalByClickingAway(false)
                ->slideOver();
        });
    }
}
