<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation;

/**
 * StreamedJsonResponse represents a streamed HTTP response for JSON.
 *
 * A StreamedJsonResponse uses a structure and generics to create an
 * efficient resource-saving JSON response.
 *
 * It is recommended to use flush() function after a specific number of items to directly stream the data.
 *
 * @see flush()
 *
 * @author Alexander Schranz <alexander@sulu.io>
 *
 * Example usage:
 *
 *     function loadArticles(): \Generator
 *         // some streamed loading
 *         yield ['title' => 'Article 1'];
 *         yield ['title' => 'Article 2'];
 *         yield ['title' => 'Article 3'];
 *         // recommended to use flush() after every specific number of items
 *     }),
 *
 *     $response = new StreamedJsonResponse(
 *         // json structure with generators in which will be streamed
 *         [
 *             '_embedded' => [
 *                 'articles' => loadArticles(), // any generator which you want to stream as list of data
 *             ],
 *         ],
 *     );
 */
class StreamedJsonResponse extends StreamedResponse
{
    /**
     * @var mixed[]
     * @readonly
     */
    private $data;
    /**
     * @var int
     */
    private $encodingOptions = JsonResponse::DEFAULT_ENCODING_OPTIONS;
    private const PLACEHOLDER = '__symfony_json__';
    /**
     * @param mixed[]                        $data            JSON Data containing PHP generators which will be streamed as list of data
     * @param int                            $status          The HTTP status code (200 "OK" by default)
     * @param array<string, string|string[]> $headers         An array of HTTP headers
     * @param int                            $encodingOptions Flags for the json_encode() function
     */
    public function __construct(array $data, int $status = 200, array $headers = [], int $encodingOptions = JsonResponse::DEFAULT_ENCODING_OPTIONS)
    {
        $this->data = $data;
        $this->encodingOptions = $encodingOptions;
        parent::__construct(\Closure::fromCallable([$this, 'stream']), $status, $headers);
        if (!$this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }
    }
    private function stream() : void
    {
        $generators = [];
        $structure = $this->data;
        \array_walk_recursive($structure, function (&$item, $key) use(&$generators) {
            if (self::PLACEHOLDER === $key) {
                // if the placeholder is already in the structure it should be replaced with a new one that explode
                // works like expected for the structure
                $generators[] = $key;
            }
            // generators should be used but for better DX all kind of Traversable and objects are supported
            if (\is_object($item)) {
                $generators[] = $item;
                $item = self::PLACEHOLDER;
            } elseif (self::PLACEHOLDER === $item) {
                // if the placeholder is already in the structure it should be replaced with a new one that explode
                // works like expected for the structure
                $generators[] = $item;
            }
        });
        $jsonEncodingOptions = $this->encodingOptions;
        $keyEncodingOptions = $jsonEncodingOptions & ~\JSON_NUMERIC_CHECK;
        $jsonParts = \explode('"' . self::PLACEHOLDER . '"', \json_encode($structure, $jsonEncodingOptions));
        foreach ($generators as $index => $generator) {
            // send first and between parts of the structure
            echo $jsonParts[$index];
            if ($generator instanceof \JsonSerializable || !$generator instanceof \Traversable) {
                // the placeholders, JsonSerializable and none traversable items in the structure are rendered here
                echo \json_encode($generator, $jsonEncodingOptions);
                continue;
            }
            $isFirstItem = \true;
            $startTag = '[';
            foreach ($generator as $key => $item) {
                if ($isFirstItem) {
                    $isFirstItem = \false;
                    // depending on the first elements key the generator is detected as a list or map
                    // we can not check for a whole list or map because that would hurt the performance
                    // of the streamed response which is the main goal of this response class
                    if (0 !== $key) {
                        $startTag = '{';
                    }
                    echo $startTag;
                } else {
                    // if not first element of the generic, a separator is required between the elements
                    echo ',';
                }
                if ('{' === $startTag) {
                    echo \json_encode((string) $key, $keyEncodingOptions) . ':';
                }
                echo \json_encode($item, $jsonEncodingOptions);
            }
            if ($isFirstItem) {
                // indicates that the generator was empty
                echo '[';
            }
            echo '[' === $startTag ? ']' : '}';
        }
        // send last part of the structure
        \end($jsonParts);
        // send last part of the structure
        echo $jsonParts[\key($jsonParts)];
    }
}
