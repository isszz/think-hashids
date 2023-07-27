<?php
declare(strict_types=1);

namespace isszz\hashids;

class Bilibili
{
	const x = 177451812;
	const a = 8728348608;
	const s = [11, 10, 3, 8, 4, 6];
	const t = 'fZodR9XQDSUm21yCkr6zBqiveYah8bt4xsWpHnJE7jL5VG3guMTKNPAwcF';

    /**
     * prefix
     *
     * @var array
     */
	public $prefix = ['', ''];

    /**
     * Create a new Bilibili instance.
     *
     * @param string|array $prefix
     *
     * @return void
     */
	public function __construct(string|array $prefix = ['', ''])
	{
		if (!$prefix) {
			return;
		}
		
		if (is_string($prefix)) {
			[$this->prefix[0], $this->prefix[1]] = str_split($prefix);
		} else {
			$this->prefix = array_slice($prefix, 0, 2);
		}
	}

    /**
     * Encode ID
     *
     * @param int $x
     *
     * @return string
     */
	public function encode(int $x): string
	{
		$x = ($x ^ self::x) + self::a;

		$r = array_merge($this->prefix, ['1', ' ', ' ', '4', ' ', '1', ' ', '7', ' ', ' ']);

		for ($i = 0; $i < 6; $i++) {
			$r[self::s[$i]] = self::t[bcmod((string) floor($x / pow(58, $i)), '58')];
		}

		return implode('', $r);
	}

    /**
     * Decode to the original ID values
     *
     * @param string $x
     *
     * @return string
     */
	public function decode(string $x): int
	{
		$r = $this->getTable();

		if (!($prefix = implode('', $this->prefix))) {
			// 未设置前缀时补位
			$x = '  '. $x;
		}

		$s = 0;
		for ($i = 0; $i < 6; $i++) {
			$s += $r[$x[self::s[$i]]] * pow(58, $i);
		}

		return ($s - self::a) ^ self::x;
	}

	public function getTable(): array
	{
		$r = [];

		for ($i = 0; $i < 58; $i++) {
			$r[self::t[$i]] = $i;
		}
		return $r;
	}
}
