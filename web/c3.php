<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

/**
 * C3 - Codeception Code Coverage
 *
 * @author tigre
 */

// $_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DEBUG'] = 1;

if (isset($_COOKIE['CODECEPTION_CODECOVERAGE'])) {
    $cookie = json_decode($_COOKIE['CODECEPTION_CODECOVERAGE'], true);

    // Correção de JSON codificado incorretamente no cookie de cobertura de código com WebDriver.
    // @see https://github.com/Codeception/Codeception/issues/874
    if (!is_array($cookie)) {
        $cookie = json_decode($cookie, true);
    }

    if ($cookie) {
        foreach ($cookie as $key => $value) {
            $_SERVER["HTTP_X_CODECEPTION_" . strtoupper($key)] = $value;
        }
    }
}

if (!array_key_exists('HTTP_X_CODECEPTION_CODECOVERAGE', $_SERVER)) {
    return;
}

if (!function_exists('__c3_error')) {
    function __c3_error($message)
    {
        $errorLogFile = defined('C3_CODECOVERAGE_ERROR_LOG_FILE') ? C3_CODECOVERAGE_ERROR_LOG_FILE : C3_CODECOVERAGE_MEDIATE_STORAGE . DIRECTORY_SEPARATOR . 'error.txt';

        if (is_writable($errorLogFile)) {
            file_put_contents($errorLogFile, $message);
        } else {
            $message = "Não foi possível gravar o erro no arquivo de log ($errorLogFile), mensagem original: $message";
        }

        if (!headers_sent()) {
            header('X-Codeception-CodeCoverage-Error: ' . str_replace("\n", ' ', $message), true, 500);
        }

        setcookie('CODECEPTION_CODECOVERAGE_ERROR', $message);
    }
}

// Carregamento automático de classes de Codeception
if (!class_exists('\\Codeception\\Codecept')) {
    if (file_exists(__DIR__ . '/codecept.phar')) {
        require_once 'phar://' . __DIR__ . '/codecept.phar/autoload.php';
    } elseif (stream_resolve_include_path(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';

        // Necessário para carregar alguns métodos disponíveis apenas em codeception/autoload.php
        if (stream_resolve_include_path(__DIR__ . '/vendor/codeception/codeception/autoload.php')) {
            require_once __DIR__ . '/vendor/codeception/codeception/autoload.php';
        }
    } elseif (stream_resolve_include_path('Codeception/autoload.php')) {
        require_once 'Codeception/autoload.php';
    } else {
        __c3_error('Codeception não está carregado. Verifique se o pacote PHAR ou Composer ou PEAR pode ser usado');
    }
}

// Carregar configuração de codificação
$configDistFile = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'codeception.dist.yml';
$configFile = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'codeception.yml';

if (isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_CONFIG'])) {
    $configFile = realpath(__DIR__) . DIRECTORY_SEPARATOR . $_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_CONFIG'];
}

if (file_exists($configFile)) {
    // Use codeception.yml para configuração.
} elseif (file_exists($configDistFile)) {
    // Use codeception.dist.yml para configuração.
    $configFile = $configDistFile;
} else {
    __c3_error(sprintf("Arquivo de configuração de codeception '%s' não encontrado", $configFile));
}

try {
    \Codeception\Configuration::config($configFile);
} catch (\Exception $e) {
    __c3_error($e->getMessage());
}

if (!defined('C3_CODECOVERAGE_MEDIATE_STORAGE')) {
    // Solução alternativa para o problema 'zend_mm_heap danificado'
    gc_disable();

    if ((int)ini_get('memory_limit') < 384) {
        ini_set('memory_limit', '384M');
    }

    define('C3_CODECOVERAGE_MEDIATE_STORAGE', Codeception\Configuration::logDir() . 'c3tmp');
    define('C3_CODECOVERAGE_PROJECT_ROOT', Codeception\Configuration::projectDir());
    define('C3_CODECOVERAGE_TESTNAME', $_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE']);

    function __c3_build_html_report(PHP_CodeCoverage $codeCoverage, $path)
    {
        $writer = new PHP_CodeCoverage_Report_HTML();
        $writer->process($codeCoverage, $path . 'html');

        if (file_exists($path . '.tar')) {
            unlink($path . '.tar');
        }

        $phar = new PharData($path . '.tar');
        $phar->setSignatureAlgorithm(Phar::SHA1);

        $files = $phar->buildFromDirectory($path . 'html');
        array_map('unlink', $files);

        if (in_array('GZ', Phar::getSupportedCompression())) {
            if (file_exists($path . '.tar.gz')) {
                unlink($path . '.tar.gz');
            }

            $phar->compress(Phar::GZ);

            // Fecha o arquivo para que possamos renomeá-lo
            unset($phar);
            rename($path . '.tar', $path . '.tar.gz');
        }

        return $path . '.tar.gz';
    }

    function __c3_build_clover_report(PHP_CodeCoverage $codeCoverage, $path)
    {
        $writer = new PHP_CodeCoverage_Report_Clover();
        $writer->process($codeCoverage, $path . '.clover.xml');
        return $path . '.clover.xml';
    }

    function __c3_send_file($filename)
    {
        if (!headers_sent()) {
            readfile($filename);
        }

        return __c3_exit();
    }

    /**
     * @param $filename
     * @return null|PHP_CodeCoverage
     */
    function __c3_factory($filename)
    {
        $phpCoverage = is_readable($filename) ? unserialize(file_get_contents($filename)) : new PHP_CodeCoverage();

        if (isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_SUITE'])) {
            $suite = $_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_SUITE'];

            try {
                $settings = \Codeception\Configuration::suiteSettings($suite, \Codeception\Configuration::config());
            } catch (\Exception $e) {
                __c3_error($e->getMessage());
            }
        } else {
            $settings = \Codeception\Configuration::config();
        }

        try {
            \Codeception\Coverage\Filter::setup($phpCoverage)
                ->whiteList($settings)
                ->blackList($settings);
        } catch (\Exception $e) {
            __c3_error($e->getMessage());
        }

        return $phpCoverage;
    }

    function __c3_exit()
    {
        if (!isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DEBUG'])) {
            exit;
        }

        return null;
    }

    function __c3_clear()
    {
        \Codeception\Util\FileSystem::doEmptyDir(C3_CODECOVERAGE_MEDIATE_STORAGE);
    }

    function __c3_exec()
    {
        if (!isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE'])) {
            return;
        }

        $codeCoverage = __c3_factory(C3_CODECOVERAGE_MEDIATE_STORAGE . DIRECTORY_SEPARATOR . 'c3');

        if (!isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DATA'])) {
            __c3_send_file(__c3_build_html_report($codeCoverage, C3_CODECOVERAGE_MEDIATE_STORAGE));
        }

        if (!isset($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DEBUG'])) {
            __c3_clear();
        }

        switch ($_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DATA']) {
            case 'clover':
                __c3_send_file(__c3_build_clover_report($codeCoverage, C3_CODECOVERAGE_MEDIATE_STORAGE));
                break;

            default:
                __c3_send_file(__c3_build_html_report($codeCoverage, C3_CODECOVERAGE_MEDIATE_STORAGE));
                break;
        }
    }

    register_shutdown_function('__c3_exit');
    register_shutdown_function('__c3_exec');
}

// @codeCoverageIgnoreEnd
