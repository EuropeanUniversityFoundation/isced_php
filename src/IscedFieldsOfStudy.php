<?php

/**
 * @file IscedFieldsOfStudy.php
 *
 * Provides codes for ISCED-F fields of study.
 */

declare(strict_types=1);

namespace Isced;

/**
 * ISCED-F fields of study.
 */
final class IscedFieldsOfStudy
{
    public const LABEL = "label";
    public const BROAD = "broad";
    public const NARROW = "narrow";
    public const DETAILED = "detailed";

    /**
     * List of fields of study.
     *
     * @var array<string, array<string, string|null>>
     */
    protected $list;

    /**
     * Statically cached list.
     *
     * @var array<string, array<string, string|null>>|null
     */
    private static $cachedList;

    /**
     * Constructs the object.
     */
    public function __construct()
    {
        $this->list = self::list();
    }

    /**
     * Returns the list of fields of study.
     *
     * @return array<string, array<string, string|null>>
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Returns the list of labels indexed by fields of study.
     *
     * @return array<string|null>
     */
    public function getLabeledList(): array
    {
        $labeledList = [];

        foreach ($this->list as $code => $field) {
            $code = (string) $code;
            $labeledList[$code] = $field[self::LABEL];
        }

        return $labeledList;
    }

    /**
     * Returns the tree of fields of study.
     *
     * @return array<string, array<string, array<string, null>>>
     */
    public function getTree(): array
    {
        $tree = [];

        foreach ($this->list as $code => $field) {
            $code = (string) $code;

            if ($code === $field[self::BROAD]) {
                $tree[$code] = [];
            } elseif ($code === $field[self::NARROW]) {
                $broad = $field[self::BROAD];
                $tree[$broad][$code] = [];
            } elseif ($code === $field[self::DETAILED]) {
                $broad = $field[self::BROAD];
                $narrow = $field[self::NARROW];
                $tree[$broad][$narrow][$code] = null;
            }
        }

        return $tree;
    }

    /**
     * Checks whether a field of study exists.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return bool
     */
    public function exists(string $code): bool
    {
        return array_key_exists($code, $this->list);
    }

    /**
     * Returns a field of study given its code.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return array<string, string|null>
     */
    public function get(string $code): array
    {
        if (!$this->exists($code)) {
            throw new \Exception("Code " . $code . " does not exist.");
        }
        return $this->list[$code];
    }

    /**
     * Returns the label of a field of study given its code.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string
     */
    public function getLabel(string $code): string
    {
        /** @var string */
        return $this->get($code)[self::LABEL];
    }

    /**
     * Returns the broad field value for a field of study.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string
     */
    public function getBroad(string $code): string
    {
        /** @var string */
        return $this->get($code)[self::BROAD];
    }

    /**
     * Returns the narrow field value for a field of study.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string|null
     */
    public function getNarrow(string $code): ?string
    {
        return $this->get($code)[self::NARROW];
    }

    /**
     * Returns the detailed field value for a field of study.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string|null
     */
    public function getDetailed(string $code): ?string
    {
        return $this->get($code)[self::DETAILED];
    }

    /**
     * Checks whether a field of study is a broad field.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return bool
     */
    public function isBroad(string $code): bool
    {
        return $code === $this->getBroad($code);
    }

    /**
     * Checks whether a field of study is a narrow field.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return bool
     */
    public function isNarrow(string $code): bool
    {
        return $code === $this->getNarrow($code);
    }

    /**
     * Checks whether a field of study is a detailed field.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return bool
     */
    public function isDetailed(string $code): bool
    {
        return $code === $this->getDetailed($code);
    }

    /**
     * Curated list of ISCED-F 2013 fields of study.
     *
     * @return array<string, array<string, string|null>>
     */
    public static function list(): array
    {
        if (self::$cachedList === null) {
            /** @var array<string, array<string, string|null>> $data */
            $data = require __DIR__ . '/../data/isced.php';
            self::$cachedList = $data;
        }

        return self::$cachedList;
    }
}
