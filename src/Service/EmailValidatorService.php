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

    public function checkEmails($csvFileName)
    {
        $correctAddresses = [];
        $correctCounter = 0;

        $wrongAddresses = [];
        $wrongCounter = 0;

        $totalCounter = 0;

        $csvFile = $this->root.'/csv/'.$csvFileName;
        $resultPath = $this->root.'/csv/validator_result/';

        //opening CSV file
        $file = fopen($csvFile, "r");

        //checking if e-mail is valid and saving to dedicated array
        while (($row = fgetcsv($file, 0, ",")) !== false)
        {
            $totalCounter++;
            $num = count($row);
            for ($i=0; $i < $num; $i++)
            {
                 if(filter_var($row[$i], FILTER_VALIDATE_EMAIL))
                 {
                     $correctAddresses[] = $row[$i];
                     $correctCounter++;
                 } else {
                     $wrongAddresses[] = $row[$i];
                     $wrongCounter++;
                 }
            }
        }
        fclose($file);

        //saving CSV file with correct e-mail addresses
        $this->saveCsv('correct_emails.csv', $resultPath, $correctAddresses);

        //changing CSV delimiter to new line
        $this->changeDelimiter($resultPath.'correct_emails.csv');


        //saving CSV with wrong e-mail addresses
        $this->saveCsv('wrong_emails.csv', $resultPath, $wrongAddresses);


        //changing CSV delimiter to new line
        $this->changeDelimiter($resultPath.'wrong_emails.csv');

        //creating TXT file with report
        $this->createReport($totalCounter, $correctCounter, $wrongCounter, $resultPath);

        return true;

    }

    // saving CSV file and moving it to provided directory
    public function saveCsv($filename, $path, $content)
    {
        $correctCsv = fopen($filename, 'w');
        fputcsv($correctCsv, $content, ',');
        fclose($correctCsv);
        rename($filename, $path.$filename);
    }

    // changing the delimiter in provided CSV file
    public function changeDelimiter($file)
    {
        $replaceDelimiter = file_get_contents($file);
        $replaceDelimiter = str_replace(",", "\n", $replaceDelimiter);
        file_put_contents($file, $replaceDelimiter);
    }

    // creating TXT file with report from e-mail validation
    public function createReport($total, $correct, $wrong, $path)
    {
        $content = "E-mail validation result: \n
                    Total e-mails: $total
                    Correct e-mails: $correct \n
                    Wrong e-mails: $wrong";

        $fp = fopen("report.txt","wb");
        fwrite($fp,$content);
        fclose($fp);
        rename("report.txt", $path."report.txt");
    }

}