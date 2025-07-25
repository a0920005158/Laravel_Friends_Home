<?php

namespace Predisx\Profiles;

use Predisx\ClientException;
use Predisx\Commands\Processors\ICommandProcessor;
use Predisx\Commands\Processors\IProcessingSupport;

abstract class ServerProfile implements IServerProfile, IProcessingSupport {
    private static $_profiles;
    private $_registeredCommands;
    private $_processor;

    public function __construct() {
        $this->_registeredCommands = $this->getSupportedCommands();
    }

    protected abstract function getSupportedCommands();

    public static function getDefault() {
        return self::get('default');
    }

    public static function getDevelopment() {
        return self::get('dev');
    }

    private static function getDefaultProfiles() {
        return array(
            '1.2'     => '\Predisx\Profiles\ServerVersion12',
            '2.0'     => '\Predisx\Profiles\ServerVersion20',
            '2.2'     => '\Predisx\Profiles\ServerVersion22',
            '2.4'     => '\Predisx\Profiles\ServerVersion24',
            'default' => '\Predisx\Profiles\ServerVersion22',
            'dev'     => '\Predisx\Profiles\ServerVersionNext',
        );
    }

    public static function define($alias, $profileClass) {
        if (!isset(self::$_profiles)) {
            self::$_profiles = self::getDefaultProfiles();
        }
        $profileReflection = new \ReflectionClass($profileClass);
        if (!$profileReflection->isSubclassOf('\Predisx\Profiles\IServerProfile')) {
            throw new ClientException(
                "Cannot register '$profileClass' as it is not a valid profile class"
            );
        }
        self::$_profiles[$alias] = $profileClass;
    }

    public static function get($version) {
        if (!isset(self::$_profiles)) {
            self::$_profiles = self::getDefaultProfiles();
        }
        if (!isset(self::$_profiles[$version])) {
            throw new ClientException("Unknown server profile: $version");
        }
        $profile = self::$_profiles[$version];
        return new $profile();
    }

    public function supportsCommands(Array $commands) {
        foreach ($commands as $command) {
            if ($this->supportsCommand($command) === false) {
                return false;
            }
        }
        return true;
    }

    public function supportsCommand($command) {
        return isset($this->_registeredCommands[strtolower($command)]);
    }

    public function createCommand($method, $arguments = array()) {
        $method = strtolower($method);
        if (!isset($this->_registeredCommands[$method])) {
            throw new ClientException("'$method' is not a registered Redis command");
        }
        $commandClass = $this->_registeredCommands[$method];
        $command = new $commandClass();
        $command->setArguments($arguments);
        if (isset($this->_processor)) {
            $this->_processor->process($command);
        }
        return $command;
    }

    public function defineCommands(Array $commands) {
        foreach ($commands as $alias => $command) {
            $this->defineCommand($alias, $command);
        }
    }

    public function defineCommand($alias, $command) {
        $commandReflection = new \ReflectionClass($command);
        if (!$commandReflection->isSubclassOf('\Predisx\Commands\ICommand')) {
            throw new ClientException("Cannot register '$command' as it is not a valid Redis command");
        }
        $this->_registeredCommands[strtolower($alias)] = $command;
    }

    public function setProcessor(ICommandProcessor $processor) {
        if (!isset($processor)) {
            unset($this->_processor);
            return;
        }
        $this->_processor = $processor;
    }

    public function getProcessor() {
        return $this->_processor;
    }

    public function __toString() {
        return $this->getVersion();
    }
}
