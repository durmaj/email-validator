<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class EmailValidatorService
{
    private $params;
    private $root;

    public function __construct(ParameterBagInterface $params, $projectDir)
    {
        $this->params = $params;
        $this->root = $projectDir;
    }


    public function checkEmails($csvFileName){

        $correctAddresses = [];
        $wrongAddeesses = [];
        $summary = '';

        $csvFile = $this->root.'/csv/'.$csvFileName;
        $resultPath = $this->root.'/csv/validator_result/';

        //opening csv file
        $file = fopen($csvFile, "r");

        //checking if e-mail is valid and saving to dedicated array
        while (($row = fgetcsv($file, 1000, ",")) !== false)
        {
            $num = count($row);
            for ($i=0; $i < $num; $i++)
            {
                 if(filter_var($row[$i], FILTER_VALIDATE_EMAIL))
                 {
                     $correctAddresses[] = $row[$i];
                 } else {
                     $wrongAddeesses[] = $row[$i];
                 }
            }
        }
        fclose($file);

        //saving csv file with correct e-mail addresses
        $correctCsv = fopen('correct_emails.csv', 'w');
        fputcsv($correctCsv, $correctAddresses, ',');
        fclose($correctCsv);
        rename('correct_emails.csv', $resultPath.'correct_emails.csv');

        //changing csv delimiter to new line
        $replaceDelimiter = file_get_contents($resultPath.'correct_emails.csv');
        $replaceDelimiter = str_replace(",", "\n", $replaceDelimiter);
        file_put_contents($resultPath.'correct_emails.csv', $replaceDelimiter);

        //saving csv with wrong e-mail addresses
        $wrongCsv = fopen('wrong_emails.csv', 'w');
        fputcsv($wrongCsv, $wrongAddeesses, ',');
        fclose($wrongCsv);
        rename('wrong_emails.csv', $resultPath.'wrong_emails.csv');

        //changing csv delimiter to new line
        $replaceWrongDelimiter = file_get_contents($resultPath.'wrong_emails.csv');
        $replaceWrongDelimiter = str_replace(",", "\n", $replaceWrongDelimiter);
        file_put_contents($resultPath.'wrong_emails.csv', $replaceWrongDelimiter);

        return 'ok';

    }

}