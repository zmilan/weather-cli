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

use Weather\DTO\InputData;
use Weather\Exception\InvalidInputException;
use Weather\Exception\WeatherApiException;
use Weather\Handler\WeatherCommandHandler;

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
     * WeatherCommand constructor.
     *
     * @param WeatherCommandHandler $handler
     */
    public function __construct(private readonly WeatherCommandHandler $handler)
    {
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
        $inputData = new InputData(
            $input->getArgument('city'),
            $input->getArgument('country')
        );

        try {
            $weatherData = $this->handler->execute($inputData);
        } catch (InvalidInputException $invalidInputException) {
            $output->writeln($invalidInputException->getMessage());
            return $this->handleMissingCity($input, $output);
        } catch (WeatherApiException $weatherApiException) {
            return $this->handleError($output, $weatherApiException);
        }

        // create some useful information for the user
        $metric = $weatherData->unit->label();
        $weather = "{$weatherData->description}, {$weatherData->temperature}{$metric}";
        $output->writeln($weather);

        // Show some motivational message
        $output->writeLn([
            '===============================================',
            "\"There is no bad weather, there is just a non-adequate outfit.\". Enjoy!"
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