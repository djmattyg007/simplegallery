<?php

/**
 * Inspired by rssi by Mark Vasilkov (https://github.com/mvasilkov/rssi)
 */
class JsTemplateBuilder
{
    const REGEX = '/([#\$%@])\{(.*?)\}/';

    /**
     * @var int
     */
    private $indentation = 1;

    /**
     * @var string
     */
    private $template;

    /**
     * Accepts a JSON-encoded string with the start and end quotation marks chopped off.
     *
     * @param string $template
     */
    private function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    private function _build()
    {
        $textParts = preg_split(self::REGEX, $this->template);
        preg_match_all(self::REGEX, $this->template, $templateParts, PREG_SET_ORDER);
        $partCount = count($templateParts);

        $result = "";
        for ($x = 0; $x < $partCount; $x++) {
            $result .= str_repeat(" ", $this->indentation * 4 + 4) . $this->buildTextPart($textParts[$x]) . "\n";
            $result .= str_repeat(" ", $this->indentation * 4 + 4) . $this->buildTemplatePart($templateParts[$x][1], $templateParts[$x][2]) . "\n";
        }
        $result .= str_repeat(" ", $this->indentation * 4 + 4) . $this->buildTextPart($textParts[$x]) . "\n";

        return $result;
    }

    /**
     * Accepts a JSON-encoded string with the start and end quotation marks chopped off.
     *
     * @param string $template
     * @return string
     */
    public static function build($template)
    {
        return (new self($template))->_build();
    }

    /**
     * Accepts any string with template control characters.
     *
     * @param string $template
     * @return string
     */
    public static function prepareAndBuild($template)
    {
        return self::build(substr(json_encode($template), 1, -1));
    }

    /**
     * @param string $part
     * @return string
     */
    private function buildTextPart($part)
    {
        return 'result += "' . $part . '";';
    }

    /**
     * @param string $control
     * @param string $inside
     * @return string
     */
    private function buildTemplatePart($control, $inside)
    {
        if ($control === '#') {
            // Escape for general HTML
            return 'result += helper.escapeHtml(obj["' . $inside . '"], false);';
        } elseif ($control === '$') {
            // Escape for HTML attribute
            return 'result += helper.escapeHtml(obj["' . $inside . '"], true);';
        } elseif ($control === '%') {
            // Do not escape
            return 'result += obj["' . $inside . '"];';
        } elseif ($control === '@') {
            $logic = explode(" ", $inside, 2);
            if ($logic[0] === 'if') {
                $this->indentation++;
                return 'if (typeof obj["' . $logic[1] . '"] !== "undefined" && obj["' . $logic[1] . '"]) {';
            } elseif ($logic[0] === 'else') {
                return '} else {';
            } elseif ($logic[0] === 'endif') {
                $this->indentation--;
                return '}';
            }
        }
    }
}
