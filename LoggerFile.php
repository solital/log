<?php

namespace Solital\Core\Logger;

use Solital\Core\Exceptions\RuntimeException;
use Psr\Log\{LogLevel, LoggerInterface};
use Solital\Core\Kernel\Application;

class LoggerFile implements LoggerInterface
{
    /**
     * File name and path of log file.
     * 
     * @var string
     */
    private string $log_file;

    /**
     * @var string
     */
    #private string $log_file_name;

    /**
     * @var string
     */
    #private string $log_dir_name;

    /**
     * Log channel--namespace for log lines.
     * Used to identify and correlate groups of similar log lines.
     * 
     * @var string
     */
    private string $channel;

    /**
     * Lowest log level to log.
     * 
     * @var mixed
     */
    private $log_level;

    /**
     * Whether to log to standard out.
     * 
     * @var bool
     */
    private string $stdout;

    /**
     * Log fields separated by tabs to form a TSV (CSV with tabs).
     */
    const TAB = "\t";

    /**
     * Special minimum log level which will not log any log levels.
     */
    const LOG_LEVEL_NONE = 'none';

    /**
     * Log level hierachy
     */
    const LEVELS = [
        self::LOG_LEVEL_NONE => -1,
        LogLevel::DEBUG      => 0,
        LogLevel::INFO       => 1,
        LogLevel::NOTICE     => 2,
        LogLevel::WARNING    => 3,
        LogLevel::ERROR      => 4,
        LogLevel::CRITICAL   => 5,
        LogLevel::ALERT      => 6,
        LogLevel::EMERGENCY  => 7,
    ];

    /**
     * Logger constructor
     *
     * @param string $channel   Logger channel associated with this logger.
     * @param string $log_file
     * @param string $log_level (optional) Lowest log level to log.
     */
    public function __construct(string $channel, string $log_file, string $log_level = LogLevel::DEBUG)
    {
        $this->log_file  = Application::getRootApp("Storage/log/" . date('Y-m-d.H-i-s') . "-" . $log_file . ".txt", Application::DEBUG);
        /* $this->log_file_name = date('Y-m-d.H-i-s') . "-" . $log_file . ".txt";
        $this->log_dir_name = Application::getRootApp("Storage/log/", Application::DEBUG); */
        $this->channel   = $channel;
        $this->stdout    = false;
        $this->setLogLevel($log_level);
    }

    /**
     * Set the lowest log level to log.
     *
     * @param string $log_level
     * 
     * @return void
     */
    public function setLogLevel(string $log_level): void
    {
        if (!array_key_exists($log_level, self::LEVELS)) {
            $this->exception->errorHandler(500, "Log level $log_level is not a valid log level. Must be one of (" . implode(', ', array_keys(self::LEVELS)) . ')', __FILE__, __LINE__);
        }

        $this->log_level = self::LEVELS[$log_level];
    }

    /**
     * Set the log channel which identifies the log line.
     *
     * @param string $channel
     * 
     * @return void
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * Set the standard out option on or off.
     * If set to true, log lines will also be printed to standard out.
     *
     * @param bool $stdout
     * 
     * @return void
     */
    public function setOutput(bool $stdout): void
    {
        $this->stdout = $stdout;
    }

    /**
     * Log a debug message.
     * Fine-grained informational events that are most useful to debug an application.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array $context Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     * 
     * @return void
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::DEBUG)) {
            $this->log(LogLevel::DEBUG, $message, $context);
        }
    }

    /**
     * Log an info message.
     * Interesting events and informational messages that highlight the progress of the application at coarse-grained level.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::INFO)) {
            $this->log(LogLevel::INFO, $message, $context);
        }
    }

    /**
     * Log an notice message.
     * Normal but significant events.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $data    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::NOTICE)) {
            $this->log(LogLevel::NOTICE, $message, $context);
        }
    }

    /**
     * Log a warning message.
     * Exceptional occurrences that are not errors--undesirable things that are not necessarily wrong.
     * Potentially harmful situations which still allow the application to continue running.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::WARNING)) {
            $this->log(LogLevel::WARNING, $message, $context);
        }
    }

    /**
     * Log an error message.
     * Error events that might still allow the application to continue running.
     * Runtime errors that do not require immediate action but should typically be logged and monitored.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::ERROR)) {
            $this->log(LogLevel::ERROR, $message, $context);
        }
    }

    /**
     * Log a critical condition.
     * Application components being unavailable, unexpected exceptions, etc.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::CRITICAL)) {
            $this->log(LogLevel::CRITICAL, $message, $context);
        }
    }

    /**
     * Log an alert.
     * This should trigger an email or SMS alert and wake you up.
     * Example: Entire site down, database unavailable, etc.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::ALERT)) {
            $this->log(LogLevel::ALERT, $message, $context);
        }
    }

    /**
     * Log an emergency.
     * System is unsable.
     * This should trigger an email or SMS alert and wake you up.
     *
     * @param string|\Stringable $message Content of log event.
     * @param array  $context    Associative array of contextual support data that goes with the log event.
     *
     * @throws \RuntimeException
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        if ($this->logAtThisLevel(LogLevel::EMERGENCY)) {
            $this->log(LogLevel::EMERGENCY, $message, $context);
        }
    }

    /**
     * Log a message.
     * Generic log routine that all severity levels use to log an event.
     *
     * @param string|\Stringable $level   Log level
     * @param string $message Content of log event.
     * @param array  $data    Potentially multidimensional associative array of support data that goes with the log event.
     *
     * @throws \RuntimeException when log file cannot be opened for writing.
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        // Build log line
        $pid                    = getmypid();
        list($exception, $data) = $this->handleException($context);
        $data                   = $data ? json_encode($data, \JSON_UNESCAPED_SLASHES) : '{}';
        $data                   = $data ?: '{}'; // Fail-safe incase json_encode fails.
        $log_line               = $this->formatLogLine($level, $pid, $message, $data, $exception);

        // Log to file
        try {
            file_put_contents($this->log_file, $log_line);
            /* $fh = fopen($this->log_file, 'a');
            fwrite($fh, $log_line);
            fclose($fh); */
        } catch (\Throwable $e) {
            throw new RuntimeException("Could not open log file {$this->log_file} for writing to SimpleLog channel {$this->channel}!", 0, $e);
        }

        // Log to stdout if option set to do so.
        if ($this->stdout) {
            print($log_line);
        }
    }

    /**
     * Determine if the logger should log at a certain log level.
     *
     * @param  string $level
     *
     * @return bool True if we log at this level; false otherwise.
     */
    private function logAtThisLevel($level): bool
    {
        return self::LEVELS[$level] >= $this->log_level;
    }

    /**
     * Handle an exception in the data context array.
     * If an exception is included in the data context array, extract it.
     *
     * @param  array  $data
     *
     * @return array  [exception, data (without exception)]
     */
    private function handleException(array $data = null): array
    {
        if (isset($data['exception']) && $data['exception'] instanceof \Throwable) {
            $exception      = $data['exception'];
            $exception_data = $this->buildExceptionData($exception);
            unset($data['exception']);
        } else {
            $exception_data = '{}';
        }

        return [$exception_data, $data];
    }

    /**
     * Build the exception log data.
     *
     * @param  \Throwable $e
     *
     * @return string JSON {message, code, file, line, trace}
     */
    private function buildExceptionData(\Throwable $e): string
    {
        $exceptionData = json_encode(
            [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTrace()
            ],
            \JSON_UNESCAPED_SLASHES
        );

        // Fail-safe in case json_encode failed
        return $exceptionData ?: '{"message":"' . $e->getMessage() . '"}';
    }

    /**
     * Format the log line.
     * YYYY-mm-dd HH:ii:ss.uuuuuu  [loglevel]  [channel]  [pid:##]  Log message content  {"Optional":"JSON Contextual Support Data"}  {"Optional":"Exception Data"}
     *
     * @param  string $level
     * @param  int    $pid
     * @param  string $message
     * @param  string $data
     * @param  string $exception_data
     *
     * @return string
     */
    private function formatLogLine(string $level, int $pid, string $message, string $data, string $exception_data): string
    {
        return
            $this->getTime()                              . self::TAB .
            "[$level]"                                    . self::TAB .
            "[{$this->channel}]"                          . self::TAB .
            "[pid:$pid]"                                  . self::TAB .
            str_replace(\PHP_EOL, '   ', trim($message))  . self::TAB .
            str_replace(\PHP_EOL, '   ', $data)           . self::TAB .
            str_replace(\PHP_EOL, '   ', $exception_data) . \PHP_EOL;
    }

    /**
     * Get current date time.
     * Format: YYYY-mm-dd HH:ii:ss.uuuuuu
     * Microsecond precision for PHP 7.1 and greater
     *
     * @return string Date time
     */
    private function getTime(): string
    {
        return (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s.u');
    }
}