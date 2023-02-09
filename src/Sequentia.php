<?php

namespace Jaulz\Sequentia;

use Illuminate\Support\Facades\DB;

class Sequentia
{
  public function getSchema() {
    return 'sequentia';
  }

  public function grant(string $role)
  {
    collect([
      'GRANT USAGE ON SCHEMA %1$s TO %2$s',
      'GRANT SELECT ON TABLE %1$s.definitions TO %2$s'
    ])->each(fn (string $statement) => DB::statement(sprintf($statement, Sequentia::getSchema(), $role)));
  }

  public function ungrant(string $role)
  {
  }
}