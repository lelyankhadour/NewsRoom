
<?php



uses(Tests\TestCase::class)
    ->beforeEach(fn () => config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']))
    ->in( 'Unit','Feature');
    afterEach(function () {
    Mockery::close();
});