<?php
declare(strict_types=1);

namespace Weather\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Weather\Api\OpenWeatherMap;
use Weather\Enum\Unit;
use Weather\Exception\WeatherApiException;

/**
 * Class WeatherCommand
 * @author [Milan Zivkovic](https://github.com/zmilan)
 * @package Weather\Command
 */
class WeatherCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static string $defaultName = './weather';

    /**
     * OpenWeatherMap instance
     * @var OpenWeatherMap
     */
    protected OpenWeatherMap $api;

    /**
     * WeatherCommand constructor.
     *
     * @param OpenWeatherMap $api
     */
    public function __construct(OpenWeatherMap $api)
    {
        $this->api = $api;
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->setDescription('Check current weather for any city.')
            ->addArgument('city',
                // Intentionally optional, since in case of missing REQUIRED parameter error is shown instead of help
                InputArgument::OPTIONAL,
                'The name of the city you wish to check.'
            )
            ->addArgument('country',
                InputArgument::OPTIONAL,
                'The name of the country where the city is from.')
            ->setHelp('This command allows checking weather for any city based on its name.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $city = $input->getArgument('city');
        $country = $input->getArgument('country');

        if (empty($city)) {
            return $this->handleMissingCity($input, $output);
        }

        $params = [$city];
        if ($country) {
            $params[] = $country;
        }

        $query = implode(', ', $params);

        // Show information about requested city and country
        $output->writeln([
            "Current weather for {$query}",
            '==============================================='
        ]);

        try {
            $weatherData = $this->api->getWeather($query);
            // create some useful information for the user
            $metric = $this->api->apiConfig->units->label();
            $weather = "{$weatherData->description}, {$weatherData->temperature}{$metric}";
            $output->writeln($weather);
        } catch (WeatherApiException $e) {
            return $this->handleError($output, $e);
        }

        // Show some motivational message
        $output->writeLn([
            '===============================================',
            "\"There is no bad weather, there is just a non-adequate outfit.\". Enjoy your time in {$query}."
        ]);

        return Command::SUCCESS;
    }

    /**
     * Default behavior was to show some not good-looking error if required argument is missing.
     * So idea is to make **city** optional but handle it like this and show __help__ message
     * instead of error.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function handleMissingCity(InputInterface $input, OutputInterface $output): int
    {
        $help = new HelpCommand();
        $help->setCommand($this);

        return $help->run($input, $output);
    }

    /**
     * Display error information and return FAILURE code
     *
     * @param OutputInterface $output
     * @param WeatherApiException $error
     *
     * @return int
     */
    protected function handleError(OutputInterface $output, WeatherApiException $error): int
    {
        // Show some useful error message to the user
        $output->writeln([
            'An error occurred',
            "Code: {$error->getCode()}",
            "Message: {$error->getMessage()}",
        ]);

        return Command::FAILURE;
    }
}