<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Service\EmailValidatorService;

class ValidatorCommand extends Command
{

    protected static $defaultName = 'validate:email';
    private $emailValidator;

    public function __construct(EmailValidatorService $emailValidatorService)
    {
        $this->emailValidator = $emailValidatorService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Checks the validity of e-mail addresses provided in CSV file')
             ->setHelp('This command checks the validity of e-mail addresses provided in CSV file');

        $this->addArgument('filename', InputArgument::REQUIRED, 'The name of CSV file with e-mail addresses');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Starting e-mail validator'
        ]);

        $csvFileName = $input->getArgument('filename');

        $result = $this->emailValidator->checkEmails($csvFileName);

        if ($result)
        {
            $output->writeln(['Valitation completed. Please see the results in csv/validator_result folder']);
        } else {
            $output->writeln(['Sorry, something went wrong.']);
        }


    }
}