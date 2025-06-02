<?php

namespace Worksofallen\LaravelERD\Diagram;

class Ribbon
{
    public function __construct(
        public readonly string $text,
        public readonly string $bgColour,
        public readonly string $textColour = 'white',
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'ribbonText' => $this->text,
            'ribbonColour' => $this->bgColour,
            'ribbonTextColour' => $this->textColour,
        ];
    }
}
