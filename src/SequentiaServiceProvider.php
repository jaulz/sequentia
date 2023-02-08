<?php

namespace Jaulz\Sequentia;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class SequentiaServiceProvider extends PackageServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->extendBlueprint();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('sequentia')
            ->hasConfigFile('sequentia')
            ->hasMigration('2013_01_09_142000_create_sequentia_extension')
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->publishConfigFile()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('jaulz/sequentia');
            });
    }

    public function extendBlueprint()
    {
      Blueprint::macro('sequentia', function (
        string $targetName = 'serial',
        array $groupBy = [],
        string $schema = null
      ) {
        /** @var \Illuminate\Database\Schema\Blueprint $this */
        $prefix = $this->prefix;
        $tableName = $this->table;
        $targetName = $targetName;
        $schema = $schema ?? config('sequentia.schema') ?? 'public';
  
        $command = $this->addCommand(
          'sequentia',
          compact('prefix', 'tableName', 'groupBy', 'targetName', 'schema')
        );
      });
  
      PostgresGrammar::macro('compileSequentia', function (
        Blueprint $blueprint,
        Fluent $command
      ) {
        /** @var \Illuminate\Database\Schema\Grammars\PostgresGrammar $this */
        $prefix = $command->prefix;
        $tableName = $command->tableName;
        $schema = $command->schema;
        $groupBy = $command->groupBy;
        $targetName = $command->targetName;
  
        return [
          sprintf(
            <<<SQL
    SELECT sequentia.create(%s, %s, %s, (SELECT ARRAY(SELECT jsonb_array_elements_text(%s::jsonb))));
  SQL
            ,
            $this->quoteString($schema),
            $this->quoteString($prefix . $tableName),
            $this->quoteString($targetName),
            $this->quoteString(json_encode($groupBy))
          ),
        ];
      });
    }
}