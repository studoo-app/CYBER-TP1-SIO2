<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;

class AuditLogService
{

    public function __construct(
        private readonly string $authLogsFilename,
        private readonly LoggerInterface $authenticationLogger
    )
    {
    }

    public function logSuccess(string $identifier,string $clientIp) :void
    {
        $trace = new Log($identifier,Log::SUCCESS,"Authenticated successfully",$clientIp);
        $this->save($trace);
    }

    public function logFailure(string $identifier, string $message,string $clientIp) :void{
        $trace = new Log($identifier,Log::FAIL,$message,$clientIp);
        $this->save($trace);
    }

    public function getLogs(): array
    {
        $raw = file_get_contents($this->authLogsFilename);
        $logs = [];
        foreach(explode("\n",$raw) as $line){
            $logEntry = [];
            if(strlen($line)>0){
                if (preg_match('/\[([^]]+)\]/', $line, $dateResult) && !empty($dateResult[1])) {
                    $resultDate = $dateResult[1];
                    $logEntry["date"] = new \DateTimeImmutable($resultDate);
                }

                if (preg_match('/\{([^}]+)\}/', $line, $objectResult) && !empty($objectResult[0])) {
                    $resultObject = $objectResult[0];
                    $logEntry["details"] = Log::buildFromLog(json_decode($resultObject,true));
                }

                $logs[] = $logEntry;
            }
        }

        return $logs;
    }

    private function save(Log $log){
        $this->authenticationLogger->info(json_encode($log->toArray()));
    }
}