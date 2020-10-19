<?php
declare(strict_types=1);

namespace Weather\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Weather\Api\OpenWeatherMap;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiException;

/**
 * Class WeatherCommand
 * @author [Milan Zivkovic](https://github.com/zmilan)
 * @package Weather\Command
 */
class WeatherCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:weather';

    /**
     * OpenWeatherMap instance
     * @var \Weather\Api\OpenWeatherMap
     */
    protected OpenWeatherMap $api;

    /**
     * WeatherCommand constructor.
     *
     * @param \Weather\Api\OpenWeatherMap $api
     * @param string|null                 $name
     */
    public function __construct(OpenWeatherMap $api, string $name = null)
    {
        $this->api = $api;
        parent::__construct($name);
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
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \Exception
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

        $params = implode(', ', $params);

        // Show information about requested city and country
        $output->writeln([
            "Current weather for {$params}",
            '==============================================='
        ]);

        try {
            $data = $this->api->getData($params);
            // check if received data has right structure
            if (!isset($data['weather'][0]['main'], $data['main']['temp'])) {
                throw new WeatherApiDataException('We are not able to process response.');
            }
            // create some useful information for the user
            $weather = "{$data['weather'][0]['main']}, {$data['main']['temp']} degrees Celsius";
            $output->writeln($weather);
        } catch (WeatherApiException $e) {
            return $this->handleError($output, $e);
        }

        // Show some motivational message
        $output->writeLn([
            '===============================================',
            "\"There is no bad weather, there is just a non-adequate outfit.\". Enjoy your time in {$params}."
        ]);

        return Command::SUCCESS;
    }

    /**
     * Default behavior was to show some not good looking error if required argument is missing.
     * So idea is to make **city** optional but handle it like this and show __help__ message
     * instead of error.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \Exception
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
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Weather\Exception\WeatherApiException            $error
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