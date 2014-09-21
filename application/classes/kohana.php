<?php defined('SYSPATH') or die('No direct script access.');

/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2010-2012 phpdreamer
 * php5shop.com
 * email: phpdreamer@rambler.ru
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3.
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/.
 */
class Kohana extends Kohana_Core
{
    /**
     * Initializes the environment:
     *
     * - Disables register_globals and magic_quotes_gpc
     * - Determines the current environment
     * - Set global settings
     * - Sanitizes GET, POST, and COOKIE variables
     * - Converts GET, POST, and COOKIE variables to the global character set
     *
     * Any of the global settings can be set here:
     *
     * Type      | Setting    | Description                                    | Default Value
     * ----------|------------|------------------------------------------------|---------------
     * `boolean` | errors     | use internal error and exception handling?     | `TRUE`
     * `boolean` | profile    | do internal benchmarking?                      | `TRUE`
     * `boolean` | caching    | cache the location of files between requests?  | `FALSE`
     * `string`  | charset    | character set used for all input and output    | `"utf-8"`
     * `string`  | base_url   | set the base URL for the application           | `"/"`
     * `string`  | index_file | set the index.php file name                    | `"index.php"`
     * `string`  | cache_dir  | set the cache directory path                   | `APPPATH."cache"`
     *
     * @throws  Kohana_Exception
     * @param   array
     * @return  void
     */
    public static function init(array $settings = NULL)
    {
        if (defined('IN_PRODUCTION') && IN_PRODUCTION)
            Kohana::$environment = Kohana::PRODUCTION;

        parent::init($settings);
    }

    /**
     * Inline exception handler, displays the error message, source of the
     * exception, and the stack trace of the error.
     *
     * @uses    Kohana::exception_text
     * @param   object   exception object
     * @return  boolean
     */
    public static function exception_handler(Exception $e)
    {
        try
        {
            // Get the exception information
            $type = get_class($e);
            $code = $e->getCode();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();

            // Create a text version of the exception
            $error = Kohana::exception_text($e);

            if (is_object(Kohana::$log))
            {
                // Add this exception to the log
                Kohana::$log->add(Kohana::ERROR, $error);

                // Make sure the logs are written
                Kohana::$log->write();
            }

            if (Kohana::$is_cli)
            {
                // Just display the text of the exception
                echo "\n{$error}\n";

                return TRUE;
            }

            // Get the exception backtrace
            $trace = $e->getTrace();

            if ($e instanceof ErrorException)
            {
                if (isset(Kohana::$php_errors[$code]))
                {
                    // Use the human-readable error name
                    $code = Kohana::$php_errors[$code];
                }

                if (version_compare(PHP_VERSION, '5.3', '<'))
                {
                    // Workaround for a bug in ErrorException::getTrace() that exists in
                    // all PHP 5.2 versions. @see http://bugs.php.net/bug.php?id=45895
                    for ($i = count($trace) - 1; $i > 0; --$i)
                    {
                        if (isset($trace[$i - 1]['args']))
                        {
                            // Re-position the args
                            $trace[$i]['args'] = $trace[$i - 1]['args'];

                            // Remove the args
                            unset($trace[$i - 1]['args']);
                        }
                    }
                }
            }

            if (!headers_sent())
            {
                // Make sure the proper content type is sent with a 500 status
                header('Content-Type: text/html; charset=' . Kohana::$charset, TRUE, 500);
            }

            // Start an output buffer
            ob_start();

            if (self::$environment == Kohana::PRODUCTION)
                echo $error;
            else // Include the exception HTML
                include Kohana::find_file('views', 'kohana/error');

            // Display the contents of the output buffer
            echo ob_get_clean();


            return TRUE;
        }
        catch (Exception $e)
        {
            // Clean the output buffer if one exists
            ob_get_level() and ob_clean();

            // Display the exception text
            echo Kohana::exception_text($e), "\n";

            // Exit with an error status
            exit(1);
        }
    }

}
