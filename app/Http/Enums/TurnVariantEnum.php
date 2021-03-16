<?php


namespace App\Http\Enums;


class TurnVariantEnum
{
    public const ROCK = 1;
    public const SCISSORS = 2;
    public const PAPER = 3;

    public static function getConstants(): array
    {
        try {
            $oClass = new \ReflectionClass(__CLASS__);

            return $oClass->getConstants();
        } catch (\Exception $exception) {
            return [];
        }
    }
}
