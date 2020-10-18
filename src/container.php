<?php
declare(strict_types=1);

use Pimple\Container;
use Symfony\Component\Console\Application;
use Weather\Command\WeatherCommand;
use Weather\Api\OpenWeatherMap;
use Symfony\Component\Dotenv\Dotenv;

$env = __DIR__ . '/../.env';
if (!file_exists($env)) {
    throw new RuntimeException(
        "\n"
        ."It seems that you don't have set .env file.\n\n"
        ."You can copy .env.example from the project root.\n\n"
        ."\n\n"
    );
}

$dotenv = new Dotenv();
$dotenv->load($env);

if (!isset($_ENV['OPEN_WEATHER_MAP_URL'], $_ENV['OPEN_WEATHER_MAP_KEY'])) {
    throw new RuntimeException("\n"
        ."[ERROR] weather depends on OpenWeatherMap service.\n"
        ."It seems that those dependencies aren't properly set.\n\n"
        ."Perhaps you forgot to fill \"OPEN_WEATHER_MAP_URL\" and \"OPEN_WEATHER_MAP_KEY\" variables\n"
        ."inside of .env file.\n\n"
        ."\n\n");
}

$container = new Container();

$container['api'] = static fn () => new OpenWeatherMap(
    $_ENV['OPEN_WEATHER_MAP_URL'],
    $_ENV['OPEN_WEATHER_MAP_KEY'],
    !empty($_ENV['OPEN_WEATHER_MAP_UNITS']) ? $_ENV['OPEN_WEATHER_MAP_UNITS'] : null
);

$container['command.weather'] = static fn ($container) => new WeatherCommand($container['api']);

$container['commands'] = static fn ($container) => [ $container['command.weather'] ];

$container['command.default'] = static fn ($container) => $container['command.weather'];

$container['application'] = static function ($container) {
    $application = new Application('Weather CLI', '0.1.0');
    $application->addCommands($container['commands']);
    $application->setDefaultCommand($container['command.default']->getName(), true);

    return $application;
};

return $container;