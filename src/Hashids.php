<?php
declare(strict_types=1);

namespace isszz\hashids;

use think\App;
use think\Config;
use think\heler\Arr;

use Hashids\Hashids as HashidsParent;

class Hashids
{
    /**
     * The config instance.
     *
     * @var \think\Config
     */
    protected Config $config;

    /**
     * The active modes instances.
     *
     * @var array<string,object>
     */
    protected array $modes = [];

    /**
     * Create a new hashids instance.
     *
     * @param \think\Config $config
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get a mode instance.
     *
     * @param string|null $name
     * @throws \InvalidArgumentException
     * @return object
     */
    public function mode(string $name = null, array|string $prefix = ['', ''])
    {
        if (!empty($name) && $name == 'bilibili') {

            if (!isset($this->modes[$name])) {
                $this->modes[$name] = new Bilibili(
                    $prefix ?? $this->config->get('hashids.modes.bilibili.prefix', ['', ''])
                );
            }

            return $this->modes[$name];
        }

        $config = $this->config->get('hashids', []);

        if (empty($config) || empty($config['modes'])) {
            throw new \InvalidArgumentException('Get configuration is null');
        }

        $name = $name ?: $config['default'];


        if (!isset($this->modes[$name])) {
            $this->modes[$name] = $this->makeMode($name);
        }

        return $this->modes[$name];

    }

    /**
     * Make the mode instance.
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return object
     */
    protected function makeMode(string $name): object
    {
        if (!empty($name) && $name == 'bilibili') {
            return $this->mode($name);
        }
        
        $config = $this->getModeConfig($name);

        $config['salt'] = $config['salt'] ?? '';
        $config['length'] = $config['length'] ?? 0;
        $config['alphabet'] = $config['alphabet'] ?? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        return new HashidsParent($config['salt'], $config['length'], $config['alphabet']);
    }

    /**
     * Get the configuration for a mode.
     *
     * @param string|null $name
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getModeConfig(string $name = null): array
    {
        $name = $name ?: $this->getDefaultMode();

        $config = $this->config->get('hashids.modes.'. $name);

        if (!$config) {
            throw new InvalidArgumentException('Hashids modes ['. $name .'] not configured.');
        }

        return $config;
    }

    /**
     * Get the default mode name.
     *
     * @return string
     */
    public function getDefaultMode(): string
    {
        return $this->config->get('hashids.default');
    }

    /**
     * Set the default mode name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultMode(string $name): void
    {
        $this->config->set(['default' => $name], 'hashids');
    }

    /**
     * Dynamically pass methods to the default mode.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->mode()->$method(...$parameters);
    }
}