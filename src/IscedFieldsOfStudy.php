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
    const BROAD = "broad";
    const NARROW = "narrow";
    const DETAILED = "detailed";

    const LABEL = "label";
    const DEFAULT_LANGUAGE = "en";

    protected array $list;

    /**
     * Constructs the object.
     */
    public function __construct()
    {
        $this->list = static::list();
    }

    /**
     * Returns the list of fields of study.
     *
     * @return array<array<mixed>>
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Returns the list of labels indexed by fields of study.
     *
     * @param string $language    (optional) desired language, if available.
     * @param bool   $includeNull (optional) include items without label.
     *
     * @return array<string|null>
     */
    public function getLabeledList(
        string $language = self::DEFAULT_LANGUAGE,
        bool $includeNull = false,
    ): array {
        $labeled = [];

        foreach ($this->list as $code => $field) {
            $code = (string) $code;

            $labels = $field[self::LABEL];

            if (array_key_exists($language, $labels) || $includeNull) {
                $labeled[$code] = $labels[$language] ?? null;
            }
        }

        return $labeled;
    }

    /**
     * Returns the tree of fields of study.
     *
     * @return array<array<mixed>>
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
     * @return array<mixed>
     */
    public function get(string $code): array
    {
        if (!$this->exists($code)) {
            throw new \Exception("Code " . $code . " does not exist.");
        }
        return $this->list[$code];
    }

    /**
     * Returns the labels of a field of study given its code.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return array<mixed>
     */
    public function getLabels(string $code): array
    {
        return $this->get($code)[self::LABEL];
    }

    /**
     * Returns the label of a field of study given its code and language.
     *
     * @param string $code     The code of the field of study to check.
     * @param string $language (optional) desired language, if available.
     *
     * @return array<mixed>
     */
    public function getLabel(
        string $code,
        string $language = self::DEFAULT_LANGUAGE,
    ): string {
        return $this->getLabels($code)[$language];
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
        return $this->get($code)[self::BROAD];
    }

    /**
     * Returns the narrow field value for a field of study.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string
     */
    public function getNarrow(string $code): string
    {
        return $this->get($code)[self::NARROW];
    }

    /**
     * Returns the detailed field value for a field of study.
     *
     * @param string $code The code of the field of study to check.
     *
     * @return string
     */
    public function getDetailed(string $code): string
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
     * @return array<array<mixed>>
     */
    public static function list(): array
    {
        return [
            "00" => [
                self::LABEL => [
                    "en" => "Generic programmes and qualifications",
                ],
                self::BROAD => "00",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "001" => [
                self::LABEL => [
                    "en" => "Basic programmes and qualifications",
                ],
                self::BROAD => "00",
                self::NARROW => "001",
                self::DETAILED => null,
            ],
            "0011" => [
                self::LABEL => [
                    "en" => "Basic programmes and qualifications",
                ],
                self::BROAD => "00",
                self::NARROW => "001",
                self::DETAILED => "0011",
            ],
            "002" => [
                self::LABEL => [
                    "en" => "Literacy and numeracy",
                ],
                self::BROAD => "00",
                self::NARROW => "002",
                self::DETAILED => null,
            ],
            "0021" => [
                self::LABEL => [
                    "en" => "Literacy and numeracy",
                ],
                self::BROAD => "00",
                self::NARROW => "002",
                self::DETAILED => "0021",
            ],
            "003" => [
                self::LABEL => [
                    "en" => "Personal skills and development",
                ],
                self::BROAD => "00",
                self::NARROW => "003",
                self::DETAILED => null,
            ],
            "0031" => [
                self::LABEL => [
                    "en" => "Personal skills and development",
                ],
                self::BROAD => "00",
                self::NARROW => "003",
                self::DETAILED => "0031",
            ],
            "01" => [
                self::LABEL => [
                    "en" => "Education",
                ],
                self::BROAD => "01",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "011" => [
                self::LABEL => [
                    "en" => "Education",
                ],
                self::BROAD => "01",
                self::NARROW => "011",
                self::DETAILED => null,
            ],
            "0111" => [
                self::LABEL => [
                    "en" => "Education science",
                ],
                self::BROAD => "01",
                self::NARROW => "011",
                self::DETAILED => "0111",
            ],
            "0112" => [
                self::LABEL => [
                    "en" => "Training for pre-school teachers",
                ],
                self::BROAD => "01",
                self::NARROW => "011",
                self::DETAILED => "0112",
            ],
            "0113" => [
                self::LABEL => [
                    "en" => "Teacher training without subject specialisation",
                ],
                self::BROAD => "01",
                self::NARROW => "011",
                self::DETAILED => "0113",
            ],
            "0114" => [
                self::LABEL => [
                    "en" => "Teacher training with subject specialisation",
                ],
                self::BROAD => "01",
                self::NARROW => "011",
                self::DETAILED => "0114",
            ],
            "02" => [
                self::LABEL => [
                    "en" => "Arts and humanities'",
                ],
                self::BROAD => "02",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "021" => [
                self::LABEL => [
                    "en" => "Arts",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => null,
            ],
            "0211" => [
                self::LABEL => [
                    "en" => "Audio-visual techniques and media production",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => "0211",
            ],
            "0212" => [
                self::LABEL => [
                    "en" => "Fashion, interior and industrial design",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => "0212",
            ],
            "0213" => [
                self::LABEL => [
                    "en" => "Fine arts",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => "0213",
            ],
            "0214" => [
                self::LABEL => [
                    "en" => "Handicrafts",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => "0214",
            ],
            "0215" => [
                self::LABEL => [
                    "en" => "Music and performing arts",
                ],
                self::BROAD => "02",
                self::NARROW => "021",
                self::DETAILED => "0215",
            ],
            "022" => [
                self::LABEL => [
                    "en" => "Humanities (except languages)",
                ],
                self::BROAD => "02",
                self::NARROW => "022",
                self::DETAILED => null,
            ],
            "0221" => [
                self::LABEL => [
                    "en" => "Religion and theology",
                ],
                self::BROAD => "02",
                self::NARROW => "022",
                self::DETAILED => "0221",
            ],
            "0222" => [
                self::LABEL => [
                    "en" => "History and archaeology",
                ],
                self::BROAD => "02",
                self::NARROW => "022",
                self::DETAILED => "0222",
            ],
            "0223" => [
                self::LABEL => [
                    "en" => "Philosophy and ethics",
                ],
                self::BROAD => "02",
                self::NARROW => "022",
                self::DETAILED => "0223",
            ],
            "023" => [
                self::LABEL => [
                    "en" => "Languages",
                ],
                self::BROAD => "02",
                self::NARROW => "023",
                self::DETAILED => null,
            ],
            "0231" => [
                self::LABEL => [
                    "en" => "Language acquisition",
                ],
                self::BROAD => "02",
                self::NARROW => "023",
                self::DETAILED => "0231",
            ],
            "0232" => [
                self::LABEL => [
                    "en" => "Literature and linguistics",
                ],
                self::BROAD => "02",
                self::NARROW => "023",
                self::DETAILED => "0232",
            ],
            "03" => [
                self::LABEL => [
                    "en" => "Social sciences, journalism and information",
                ],
                self::BROAD => "03",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "031" => [
                self::LABEL => [
                    "en" => "Social and behavioural sciences",
                ],
                self::BROAD => "03",
                self::NARROW => "031",
                self::DETAILED => null,
            ],
            "0311" => [
                self::LABEL => [
                    "en" => "Economics",
                ],
                self::BROAD => "03",
                self::NARROW => "031",
                self::DETAILED => "0311",
            ],
            "0312" => [
                self::LABEL => [
                    "en" => "Political sciences and civics",
                ],
                self::BROAD => "03",
                self::NARROW => "031",
                self::DETAILED => "0312",
            ],
            "0313" => [
                self::LABEL => [
                    "en" => "Psychology",
                ],
                self::BROAD => "03",
                self::NARROW => "031",
                self::DETAILED => "0313",
            ],
            "0314" => [
                self::LABEL => [
                    "en" => "Sociology and cultural studies",
                ],
                self::BROAD => "03",
                self::NARROW => "031",
                self::DETAILED => "0314",
            ],
            "032" => [
                self::LABEL => [
                    "en" => "Journalism and information",
                ],
                self::BROAD => "03",
                self::NARROW => "032",
                self::DETAILED => null,
            ],
            "0321" => [
                self::LABEL => [
                    "en" => "Journalism and reporting",
                ],
                self::BROAD => "03",
                self::NARROW => "032",
                self::DETAILED => "0321",
            ],
            "0322" => [
                self::LABEL => [
                    "en" => "Library, information and archival studies",
                ],
                self::BROAD => "03",
                self::NARROW => "032",
                self::DETAILED => "0322",
            ],
            "04" => [
                self::LABEL => [
                    "en" => "Business, administration and law",
                ],
                self::BROAD => "04",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "041" => [
                self::LABEL => [
                    "en" => "Business and administration",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => null,
            ],
            "0411" => [
                self::LABEL => [
                    "en" => "Accounting and taxation",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0411",
            ],
            "0412" => [
                self::LABEL => [
                    "en" => "Finance, banking and insurance",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0412",
            ],
            "0413" => [
                self::LABEL => [
                    "en" => "Management and administration",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0413",
            ],
            "0414" => [
                self::LABEL => [
                    "en" => "Marketing and advertising",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0414",
            ],
            "0415" => [
                self::LABEL => [
                    "en" => "Secretarial and office work",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0415",
            ],
            "0416" => [
                self::LABEL => [
                    "en" => "Wholesale and retail sales",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0416",
            ],
            "0417" => [
                self::LABEL => [
                    "en" => "Work skills",
                ],
                self::BROAD => "04",
                self::NARROW => "041",
                self::DETAILED => "0417",
            ],
            "042" => [
                self::LABEL => [
                    "en" => "Law",
                ],
                self::BROAD => "04",
                self::NARROW => "042",
                self::DETAILED => null,
            ],
            "0421" => [
                self::LABEL => [
                    "en" => "Law",
                ],
                self::BROAD => "04",
                self::NARROW => "042",
                self::DETAILED => "0421",
            ],
            "05" => [
                self::LABEL => [
                    "en" => "Natural Sciences, mathematics and statistics",
                ],
                self::BROAD => "05",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "051" => [
                self::LABEL => [
                    "en" => "Biological and related sciences",
                ],
                self::BROAD => "05",
                self::NARROW => "051",
                self::DETAILED => null,
            ],
            "0511" => [
                self::LABEL => [
                    "en" => "Biology",
                ],
                self::BROAD => "05",
                self::NARROW => "051",
                self::DETAILED => "0511",
            ],
            "0512" => [
                self::LABEL => [
                    "en" => "Biochemistry",
                ],
                self::BROAD => "05",
                self::NARROW => "051",
                self::DETAILED => "0512",
            ],
            "052" => [
                self::LABEL => [
                    "en" => "Environment",
                ],
                self::BROAD => "05",
                self::NARROW => "052",
                self::DETAILED => null,
            ],
            "0521" => [
                self::LABEL => [
                    "en" => "Environmental sciences",
                ],
                self::BROAD => "05",
                self::NARROW => "052",
                self::DETAILED => "0521",
            ],
            "0522" => [
                self::LABEL => [
                    "en" => "Natural environments and wildlife",
                ],
                self::BROAD => "05",
                self::NARROW => "052",
                self::DETAILED => "0522",
            ],
            "053" => [
                self::LABEL => [
                    "en" => "Physical sciences",
                ],
                self::BROAD => "05",
                self::NARROW => "053",
                self::DETAILED => null,
            ],
            "0531" => [
                self::LABEL => [
                    "en" => "Chemistry",
                ],
                self::BROAD => "05",
                self::NARROW => "053",
                self::DETAILED => "0531",
            ],
            "0532" => [
                self::LABEL => [
                    "en" => "Earth Sciences",
                ],
                self::BROAD => "05",
                self::NARROW => "053",
                self::DETAILED => "0532",
            ],
            "0533" => [
                self::LABEL => [
                    "en" => "Physics",
                ],
                self::BROAD => "05",
                self::NARROW => "053",
                self::DETAILED => "0533",
            ],
            "054" => [
                self::LABEL => [
                    "en" => "Mathematics and statistics",
                ],
                self::BROAD => "05",
                self::NARROW => "054",
                self::DETAILED => null,
            ],
            "0541" => [
                self::LABEL => [
                    "en" => "Mathematics",
                ],
                self::BROAD => "05",
                self::NARROW => "054",
                self::DETAILED => "0541",
            ],
            "0542" => [
                self::LABEL => [
                    "en" => "Statistics",
                ],
                self::BROAD => "05",
                self::NARROW => "054",
                self::DETAILED => "0542",
            ],
            "06" => [
                self::LABEL => [
                    "en" => "Information and Communication Technologies (ICTs)",
                ],
                self::BROAD => "06",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "061" => [
                self::LABEL => [
                    "en" => "Information and Communication Technologies (ICTs)",
                ],
                self::BROAD => "06",
                self::NARROW => "061",
                self::DETAILED => null,
            ],
            "0611" => [
                self::LABEL => [
                    "en" => "Computer use",
                ],
                self::BROAD => "06",
                self::NARROW => "061",
                self::DETAILED => "0611",
            ],
            "0612" => [
                self::LABEL => [
                    "en" => "Database and network design and administration",
                ],
                self::BROAD => "06",
                self::NARROW => "061",
                self::DETAILED => "0612",
            ],
            "0613" => [
                self::LABEL => [
                    "en" => "Software and applications development and analysis",
                ],
                self::BROAD => "06",
                self::NARROW => "061",
                self::DETAILED => "0613",
            ],
            "07" => [
                self::LABEL => [
                    "en" => "Engineering, manufacturing and construction",
                ],
                self::BROAD => "07",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "071" => [
                self::LABEL => [
                    "en" => "Engineering and engineering trades",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => null,
            ],
            "0711" => [
                self::LABEL => [
                    "en" => "Chemical engineering and processes",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0711",
            ],
            "0712" => [
                self::LABEL => [
                    "en" => "Environmental protection technology",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0712",
            ],
            "0713" => [
                self::LABEL => [
                    "en" => "Electricity and energy",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0713",
            ],
            "0714" => [
                self::LABEL => [
                    "en" => "Electronics and automation",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0714",
            ],
            "0715" => [
                self::LABEL => [
                    "en" => "Mechanics and metal trade",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0715",
            ],
            "0716" => [
                self::LABEL => [
                    "en" => "Motor vehicles, ships and aircraft",
                ],
                self::BROAD => "07",
                self::NARROW => "071",
                self::DETAILED => "0716",
            ],
            "072" => [
                self::LABEL => [
                    "en" => "Manufacturing and processing",
                ],
                self::BROAD => "07",
                self::NARROW => "072",
                self::DETAILED => null,
            ],
            "0721" => [
                self::LABEL => [
                    "en" => "Food processing",
                ],
                self::BROAD => "07",
                self::NARROW => "072",
                self::DETAILED => "0721",
            ],
            "0722" => [
                self::LABEL => [
                    "en" => "Materials (glass, paper, plastic and wood)",
                ],
                self::BROAD => "07",
                self::NARROW => "072",
                self::DETAILED => "0722",
            ],
            "0723" => [
                self::LABEL => [
                    "en" => "Textiles (clothes, footwear and leather)",
                ],
                self::BROAD => "07",
                self::NARROW => "072",
                self::DETAILED => "0723",
            ],
            "0724" => [
                self::LABEL => [
                    "en" => "Mining and extraction",
                ],
                self::BROAD => "07",
                self::NARROW => "072",
                self::DETAILED => "0724",
            ],
            "073" => [
                self::LABEL => [
                    "en" => "Architecture and construction",
                ],
                self::BROAD => "07",
                self::NARROW => "073",
                self::DETAILED => null,
            ],
            "0731" => [
                self::LABEL => [
                    "en" => "Architecture and town planning",
                ],
                self::BROAD => "07",
                self::NARROW => "073",
                self::DETAILED => "0731",
            ],
            "0732" => [
                self::LABEL => [
                    "en" => "Building and civil engineering",
                ],
                self::BROAD => "07",
                self::NARROW => "073",
                self::DETAILED => "0732",
            ],
            "08" => [
                self::LABEL => [
                    "en" => "Agriculture, forestry, fisheries and veterinary",
                ],
                self::BROAD => "08",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "081" => [
                self::LABEL => [
                    "en" => "Agriculture",
                ],
                self::BROAD => "08",
                self::NARROW => "081",
                self::DETAILED => null,
            ],
            "0811" => [
                self::LABEL => [
                    "en" => "Crop and livestock production",
                ],
                self::BROAD => "08",
                self::NARROW => "081",
                self::DETAILED => "0811",
            ],
            "0812" => [
                self::LABEL => [
                    "en" => "Horticulture",
                ],
                self::BROAD => "08",
                self::NARROW => "081",
                self::DETAILED => "0812",
            ],
            "082" => [
                self::LABEL => [
                    "en" => "Forestry",
                ],
                self::BROAD => "08",
                self::NARROW => "082",
                self::DETAILED => null,
            ],
            "0821" => [
                self::LABEL => [
                    "en" => "Forestry",
                ],
                self::BROAD => "08",
                self::NARROW => "082",
                self::DETAILED => "0821",
            ],
            "083" => [
                self::LABEL => [
                    "en" => "Fisheries",
                ],
                self::BROAD => "08",
                self::NARROW => "083",
                self::DETAILED => null,
            ],
            "0831" => [
                self::LABEL => [
                    "en" => "Fisheries",
                ],
                self::BROAD => "08",
                self::NARROW => "083",
                self::DETAILED => "0831",
            ],
            "084" => [
                self::LABEL => [
                    "en" => "Veterinary",
                ],
                self::BROAD => "08",
                self::NARROW => "084",
                self::DETAILED => null,
            ],
            "0841" => [
                self::LABEL => [
                    "en" => "Veterinary",
                ],
                self::BROAD => "08",
                self::NARROW => "084",
                self::DETAILED => "0841",
            ],
            "09" => [
                self::LABEL => [
                    "en" => "Health and welfare",
                ],
                self::BROAD => "09",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "091" => [
                self::LABEL => [
                    "en" => "Health",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => null,
            ],
            "0911" => [
                self::LABEL => [
                    "en" => "Dental studies",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0911",
            ],
            "0912" => [
                self::LABEL => [
                    "en" => "Medicine",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0912",
            ],
            "0913" => [
                self::LABEL => [
                    "en" => "Nursing and midwifery",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0913",
            ],
            "0914" => [
                self::LABEL => [
                    "en" => "Medical diagnostic and treatment technology",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0914",
            ],
            "0915" => [
                self::LABEL => [
                    "en" => "Therapy and rehabilitation",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0915",
            ],
            "0916" => [
                self::LABEL => [
                    "en" => "Pharmacy",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0916",
            ],
            "0917" => [
                self::LABEL => [
                    "en" => "Traditional and complementary medicine and therapy",
                ],
                self::BROAD => "09",
                self::NARROW => "091",
                self::DETAILED => "0917",
            ],
            "10" => [
                self::LABEL => [
                    "en" => "Services",
                ],
                self::BROAD => "10",
                self::NARROW => null,
                self::DETAILED => null,
            ],
            "101" => [
                self::LABEL => [
                    "en" => "Personal services",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => null,
            ],
            "1011" => [
                self::LABEL => [
                    "en" => "Domestic services",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => "1011",
            ],
            "1012" => [
                self::LABEL => [
                    "en" => "Hair and beauty services",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => "1012",
            ],
            "1013" => [
                self::LABEL => [
                    "en" => "Hotel, restaurants and catering",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => "1013",
            ],
            "1014" => [
                self::LABEL => [
                    "en" => "Sports",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => "1014",
            ],
            "1015" => [
                self::LABEL => [
                    "en" => "Travel, tourism and leisure",
                ],
                self::BROAD => "10",
                self::NARROW => "101",
                self::DETAILED => "1015",
            ],
            "102" => [
                self::LABEL => [
                    "en" => "Hygiene and occupational health services",
                ],
                self::BROAD => "10",
                self::NARROW => "102",
                self::DETAILED => null,
            ],
            "1021" => [
                self::LABEL => [
                    "en" => "Community sanitation",
                ],
                self::BROAD => "10",
                self::NARROW => "102",
                self::DETAILED => "1021",
            ],
            "1022" => [
                self::LABEL => [
                    "en" => "Occupational health and safety",
                ],
                self::BROAD => "10",
                self::NARROW => "102",
                self::DETAILED => "1022",
            ],
            "103" => [
                self::LABEL => [
                    "en" => "Security services",
                ],
                self::BROAD => "10",
                self::NARROW => "103",
                self::DETAILED => null,
            ],
            "1031" => [
                self::LABEL => [
                    "en" => "Military and defence",
                ],
                self::BROAD => "10",
                self::NARROW => "103",
                self::DETAILED => "1031",
            ],
            "1032" => [
                self::LABEL => [
                    "en" => "Protection of persons and property",
                ],
                self::BROAD => "10",
                self::NARROW => "103",
                self::DETAILED => "1032",
            ],
            "104" => [
                self::LABEL => [
                    "en" => "Transport services",
                ],
                self::BROAD => "10",
                self::NARROW => "104",
                self::DETAILED => null,
            ],
            "1041" => [
                self::LABEL => [
                    "en" => "Transport services",
                ],
                self::BROAD => "10",
                self::NARROW => "104",
                self::DETAILED => "1041",
            ],
        ];
    }

}
