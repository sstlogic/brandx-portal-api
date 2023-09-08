<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Storage;
use Symfony\Component\Console\Command\Command as BaseCommand;

class GenerateTriggerSQLQueries extends Command
{
    const TABLES = [
        'users' => 'user_id',
        'resources' => 'resource_id',
        'reservation_instances' => 'reservation_instance_id',
    ];
    const ACTIONS = ['insert', 'update', 'delete'];

    protected $signature = 'triggers:generate {--drop} {--write}';

    protected $description = 'Command description';

    public function handle(): int
    {
        if ($this->option('drop')) {
            $commands = $this->generateDropTriggerStatements();
        } else {
            $commands = $this->generateTriggerStatements();
        }

        $statement = $commands->implode(PHP_EOL . PHP_EOL);

        $filename = 'triggers.sql';

        Storage::put($filename, $statement);
        $path = Storage::path($filename);
        exec("pbcopy < $path");

        if (! $this->option('write')) {
            Storage::delete($filename);
        }

        return BaseCommand::SUCCESS;
    }

    private function tables(): Collection
    {
        return collect(self::TABLES)->map(function ($idName, $tableName) {
            return [
                'id' => $idName,
                'table' => $tableName,
            ];
        });
    }

    private function generateTableTriggerStatementForAction(string $tableName, string $idName, string $action): string
    {
        $template =
            'CREATE TRIGGER `%s` BEFORE UPDATE
ON `booked`.`%s`
FOR EACH row
begin
  SET @id = NEW.%s;
  INSERT INTO actions
              (`table_name`,
               `action_type`,
               `id_name`,
               `id_value`)
  VALUES     ("%s",
              "%s",
              "%s",
              @id);
end;';

        return sprintf(
            $template,
            $this->generateTriggerName($tableName, $action),
            $tableName,
            $idName,
            $tableName,
            $action,
            $idName
        );
    }

    private function generateTableDropTriggersStatementForAction(string $tableName, string $action): string
    {
        $template = 'DROP TRIGGER `%s`;';

        return sprintf($template, $this->generateTriggerName($tableName, $action));
    }

    private function generateTriggerName(string $tableName, string $action): string
    {
        return "log_{$action}_action_{$tableName}";
    }

    private function generateDropTriggerStatements(): Collection
    {
        return collect(self::ACTIONS)->map(function ($action) {
            return $this->tables()->map(function ($table) use ($action) {
                return $this->generateTableDropTriggersStatementForAction($table['table'], $action);
            });
        })->flatten();
    }

    private function generateTriggerStatements(): Collection
    {
        return collect(self::ACTIONS)->map(function ($action) {
            return $this->tables()->map(function ($table) use ($action) {
                return $this->generateTableTriggerStatementForAction($table['table'], $table['id'], $action);
            });
        })->flatten();
    }
}
