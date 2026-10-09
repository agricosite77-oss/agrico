<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EncycolorpediaService
{
    /**
     * Calibrated standard 10YR Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated10YRMatrix = [
        // Value 8/
        '10YR 8/1' => ['hue' => '10YR', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#dbcfc5'],
        '10YR 8/2' => ['hue' => '10YR', 'value' => '8', 'chroma' => '2', 'name' => 'very pale brown', 'hex' => '#decbbb'],
        '10YR 8/3' => ['hue' => '10YR', 'value' => '8', 'chroma' => '3', 'name' => 'very pale brown', 'hex' => '#e2c2a4'],
        '10YR 8/4' => ['hue' => '10YR', 'value' => '8', 'chroma' => '4', 'name' => 'very pale brown', 'hex' => '#e6c092'],
        '10YR 8/6' => ['hue' => '10YR', 'value' => '8', 'chroma' => '6', 'name' => 'yellow', 'hex' => '#ebb675'],
        '10YR 8/8' => ['hue' => '10YR', 'value' => '8', 'chroma' => '8', 'name' => 'yellow', 'hex' => '#f0ab48'],

        // Value 7/
        '10YR 7/1' => ['hue' => '10YR', 'value' => '7', 'chroma' => '1', 'name' => 'light gray', 'hex' => '#beb2a9'],
        '10YR 7/2' => ['hue' => '10YR', 'value' => '7', 'chroma' => '2', 'name' => 'light gray', 'hex' => '#c7b19c'],
        '10YR 7/3' => ['hue' => '10YR', 'value' => '7', 'chroma' => '3', 'name' => 'very pale brown', 'hex' => '#cba985'],
        '10YR 7/4' => ['hue' => '10YR', 'value' => '7', 'chroma' => '4', 'name' => 'very pale brown', 'hex' => '#cea16f'],
        '10YR 7/6' => ['hue' => '10YR', 'value' => '7', 'chroma' => '6', 'name' => 'yellow', 'hex' => '#d39650'],
        '10YR 7/8' => ['hue' => '10YR', 'value' => '7', 'chroma' => '8', 'name' => 'yellow', 'hex' => '#d9923c'],

        // Value 6/
        '10YR 6/1' => ['hue' => '10YR', 'value' => '6', 'chroma' => '1', 'name' => 'gray', 'hex' => '#a5958d'],
        '10YR 6/2' => ['hue' => '10YR', 'value' => '6', 'chroma' => '2', 'name' => 'light brownish gray', 'hex' => '#ae927b'],
        '10YR 6/3' => ['hue' => '10YR', 'value' => '6', 'chroma' => '3', 'name' => 'pale brown', 'hex' => '#b28d6a'],
        '10YR 6/4' => ['hue' => '10YR', 'value' => '6', 'chroma' => '4', 'name' => 'light yellowish brown', 'hex' => '#b68455'],
        '10YR 6/6' => ['hue' => '10YR', 'value' => '6', 'chroma' => '6', 'name' => 'brownish yellow', 'hex' => '#b87b37'],
        '10YR 6/8' => ['hue' => '10YR', 'value' => '6', 'chroma' => '8', 'name' => 'brownish yellow', 'hex' => '#be7a21'],

        // Value 5/
        '10YR 5/1' => ['hue' => '10YR', 'value' => '5', 'chroma' => '1', 'name' => 'gray', 'hex' => '#7e726b'],
        '10YR 5/2' => ['hue' => '10YR', 'value' => '5', 'chroma' => '2', 'name' => 'grayish brown', 'hex' => '#8e7662'],
        '10YR 5/3' => ['hue' => '10YR', 'value' => '5', 'chroma' => '3', 'name' => 'brown', 'hex' => '#946f4e'],
        '10YR 5/4' => ['hue' => '10YR', 'value' => '5', 'chroma' => '4', 'name' => 'yellowish brown', 'hex' => '#95693b'],
        '10YR 5/6' => ['hue' => '10YR', 'value' => '5', 'chroma' => '6', 'name' => 'yellowish brown', 'hex' => '#a46724'],
        '10YR 5/8' => ['hue' => '10YR', 'value' => '5', 'chroma' => '8', 'name' => 'yellowish brown', 'hex' => '#a5610e'],

        // Value 4/
        '10YR 4/1' => ['hue' => '10YR', 'value' => '4', 'chroma' => '1', 'name' => 'dark gray', 'hex' => '#5f534b'],
        '10YR 4/2' => ['hue' => '10YR', 'value' => '4', 'chroma' => '2', 'name' => 'dark grayish brown', 'hex' => '#735a44'],
        '10YR 4/3' => ['hue' => '10YR', 'value' => '4', 'chroma' => '3', 'name' => 'brown', 'hex' => '#7a5332'],
        '10YR 4/4' => ['hue' => '10YR', 'value' => '4', 'chroma' => '4', 'name' => 'dark yellowish brown', 'hex' => '#794c22'],
        '10YR 4/6' => ['hue' => '10YR', 'value' => '4', 'chroma' => '6', 'name' => 'dark yellowish brown', 'hex' => '#824c18'],

        // Value 3/
        '10YR 3/1' => ['hue' => '10YR', 'value' => '3', 'chroma' => '1', 'name' => 'very dark gray', 'hex' => '#3c312f'],
        '10YR 3/2' => ['hue' => '10YR', 'value' => '3', 'chroma' => '2', 'name' => 'very dark grayish brown', 'hex' => '#4c3a2d'],
        '10YR 3/3' => ['hue' => '10YR', 'value' => '3', 'chroma' => '3', 'name' => 'dark brown', 'hex' => '#4f2f1f'],
        '10YR 3/4' => ['hue' => '10YR', 'value' => '3', 'chroma' => '4', 'name' => 'dark yellowish brown', 'hex' => '#613314'],
        '10YR 3/6' => ['hue' => '10YR', 'value' => '3', 'chroma' => '6', 'name' => 'dark yellowish brown', 'hex' => '#5f2b0f'],

        // Value 2/
        '10YR 2/1' => ['hue' => '10YR', 'value' => '2', 'chroma' => '1', 'name' => 'black', 'hex' => '#2a2423'],
        '10YR 2/2' => ['hue' => '10YR', 'value' => '2', 'chroma' => '2', 'name' => 'very dark brown', 'hex' => '#2b2120'],
    ];

    /**
     * Calibrated standard 10R Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated10RMatrix = [
        // Value 8/ (Row 1)
        '10R 8/1' => ['hue' => '10R', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#edcfca'],
        '10R 8/2' => ['hue' => '10R', 'value' => '8', 'chroma' => '2', 'name' => 'pinkish white', 'hex' => '#ebc5bc'],
        '10R 8/3' => ['hue' => '10R', 'value' => '8', 'chroma' => '3', 'name' => 'pink', 'hex' => '#f0b8ac'],
        '10R 8/4' => ['hue' => '10R', 'value' => '8', 'chroma' => '4', 'name' => 'pink', 'hex' => '#eeb0a0'],

        // Value 7/ (Row 2)
        '10R 7/1' => ['hue' => '10R', 'value' => '7', 'chroma' => '1', 'name' => 'light gray', 'hex' => '#d8b8b2'],
        '10R 7/2' => ['hue' => '10R', 'value' => '7', 'chroma' => '2', 'name' => 'pale red', 'hex' => '#d4a89e'],
        '10R 7/3' => ['hue' => '10R', 'value' => '7', 'chroma' => '3', 'name' => 'pale red', 'hex' => '#d89888'],
        '10R 7/4' => ['hue' => '10R', 'value' => '7', 'chroma' => '4', 'name' => 'pale red', 'hex' => '#d49080'],
        '10R 7/6' => ['hue' => '10R', 'value' => '7', 'chroma' => '6', 'name' => 'light red', 'hex' => '#cc6050'],
        '10R 7/8' => ['hue' => '10R', 'value' => '7', 'chroma' => '8', 'name' => 'light red', 'hex' => '#c85840'],

        // Value 6/ (Row 3)
        '10R 6/1' => ['hue' => '10R', 'value' => '6', 'chroma' => '1', 'name' => 'reddish gray', 'hex' => '#bf9e98'],
        '10R 6/2' => ['hue' => '10R', 'value' => '6', 'chroma' => '2', 'name' => 'pale red', 'hex' => '#b88878'],
        '10R 6/3' => ['hue' => '10R', 'value' => '6', 'chroma' => '3', 'name' => 'pale red', 'hex' => '#bc7a68'],
        '10R 6/4' => ['hue' => '10R', 'value' => '6', 'chroma' => '4', 'name' => 'pale red', 'hex' => '#b87060'],
        '10R 6/6' => ['hue' => '10R', 'value' => '6', 'chroma' => '6', 'name' => 'light red', 'hex' => '#b04840'],
        '10R 6/8' => ['hue' => '10R', 'value' => '6', 'chroma' => '8', 'name' => 'light red', 'hex' => '#ac4030'],

        // Value 5/ (Row 4)
        '10R 5/1' => ['hue' => '10R', 'value' => '5', 'chroma' => '1', 'name' => 'reddish gray', 'hex' => '#a88880'],
        '10R 5/2' => ['hue' => '10R', 'value' => '5', 'chroma' => '2', 'name' => 'weak red', 'hex' => '#9c6858'],
        '10R 5/3' => ['hue' => '10R', 'value' => '5', 'chroma' => '3', 'name' => 'weak red', 'hex' => '#9e5848'],
        '10R 5/4' => ['hue' => '10R', 'value' => '5', 'chroma' => '4', 'name' => 'weak red', 'hex' => '#9a5040'],
        '10R 5/6' => ['hue' => '10R', 'value' => '5', 'chroma' => '6', 'name' => 'red', 'hex' => '#923830'],
        '10R 5/8' => ['hue' => '10R', 'value' => '5', 'chroma' => '8', 'name' => 'red', 'hex' => '#8e3020'],

        // Value 4/ (Row 5)
        '10R 4/1' => ['hue' => '10R', 'value' => '4', 'chroma' => '1', 'name' => 'dark reddish gray', 'hex' => '#8c6860'],
        '10R 4/2' => ['hue' => '10R', 'value' => '4', 'chroma' => '2', 'name' => 'weak red', 'hex' => '#804a38'],
        '10R 4/3' => ['hue' => '10R', 'value' => '4', 'chroma' => '3', 'name' => 'weak red', 'hex' => '#823830'],
        '10R 4/4' => ['hue' => '10R', 'value' => '4', 'chroma' => '4', 'name' => 'weak red', 'hex' => '#7e3028'],
        '10R 4/6' => ['hue' => '10R', 'value' => '4', 'chroma' => '6', 'name' => 'red', 'hex' => '#782820'],
        '10R 4/8' => ['hue' => '10R', 'value' => '4', 'chroma' => '8', 'name' => 'red', 'hex' => '#6e2018'],

        // Value 3/ (Row 6)
        '10R 3/1' => ['hue' => '10R', 'value' => '3', 'chroma' => '1', 'name' => 'dark reddish gray', 'hex' => '#6a4a44'],
        '10R 3/2' => ['hue' => '10R', 'value' => '3', 'chroma' => '2', 'name' => 'dusky red', 'hex' => '#5e3028'],
        '10R 3/3' => ['hue' => '10R', 'value' => '3', 'chroma' => '3', 'name' => 'dusky red', 'hex' => '#602820'],
        '10R 3/4' => ['hue' => '10R', 'value' => '3', 'chroma' => '4', 'name' => 'dusky red', 'hex' => '#5c2018'],
        '10R 3/6' => ['hue' => '10R', 'value' => '3', 'chroma' => '6', 'name' => 'dark red', 'hex' => '#501812'],

        // Value 2.5/ (Row 7)
        '10R 2.5/1' => ['hue' => '10R', 'value' => '2.5', 'chroma' => '1', 'name' => 'reddish black', 'hex' => '#503030'],
        '10R 2.5/2' => ['hue' => '10R', 'value' => '2.5', 'chroma' => '2', 'name' => 'very dusky red', 'hex' => '#3e2018'],
    ];

    /**
     * Calibrated standard 7.5YR Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated7_5YRMatrix = [
        // Value 8/
        '7.5YR 8/1' => ['hue' => '7.5YR', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#e4cfc7'],
        '7.5YR 8/2' => ['hue' => '7.5YR', 'value' => '8', 'chroma' => '2', 'name' => 'pinkish white', 'hex' => '#eac8b8'],
        '7.5YR 8/3' => ['hue' => '7.5YR', 'value' => '8', 'chroma' => '3', 'name' => 'pink', 'hex' => '#f0c4a9'],
        '7.5YR 8/4' => ['hue' => '7.5YR', 'value' => '8', 'chroma' => '4', 'name' => 'pink', 'hex' => '#f4be91'],
        '7.5YR 8/6' => ['hue' => '7.5YR', 'value' => '8', 'chroma' => '6', 'name' => 'reddish yellow', 'hex' => '#f9b36c'],

        // Value 7/
        '7.5YR 7/1' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '1', 'name' => 'light gray', 'hex' => '#c7b8b2'],
        '7.5YR 7/2' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '2', 'name' => 'pinkish gray', 'hex' => '#c9a995'],
        '7.5YR 7/3' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '3', 'name' => 'pink', 'hex' => '#d2a68a'],
        '7.5YR 7/4' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '4', 'name' => 'pink', 'hex' => '#d79a6e'],
        '7.5YR 7/6' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '6', 'name' => 'reddish yellow', 'hex' => '#e1934e'],
        '7.5YR 7/8' => ['hue' => '7.5YR', 'value' => '7', 'chroma' => '8', 'name' => 'reddish yellow', 'hex' => '#e48a3b'],

        // Value 6/
        '7.5YR 6/1' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '1', 'name' => 'gray', 'hex' => '#a2918b'],
        '7.5YR 6/2' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '2', 'name' => 'pinkish gray', 'hex' => '#b08e7d'],
        '7.5YR 6/3' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '3', 'name' => 'light brown', 'hex' => '#b4886e'],
        '7.5YR 6/4' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '4', 'name' => 'light brown', 'hex' => '#b67c50'],
        '7.5YR 6/6' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '6', 'name' => 'reddish yellow', 'hex' => '#c07738'],
        '7.5YR 6/8' => ['hue' => '7.5YR', 'value' => '6', 'chroma' => '8', 'name' => 'reddish yellow', 'hex' => '#cc6d21'],

        // Value 5/
        '7.5YR 5/1' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '1', 'name' => 'gray', 'hex' => '#7c6f6c'],
        '7.5YR 5/2' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '2', 'name' => 'brown', 'hex' => '#856a5a'],
        '7.5YR 5/3' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '3', 'name' => 'brown', 'hex' => '#8f694e'],
        '7.5YR 5/4' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '4', 'name' => 'brown', 'hex' => '#97643b'],
        '7.5YR 5/6' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '6', 'name' => 'strong brown', 'hex' => '#a15e1c'],
        '7.5YR 5/8' => ['hue' => '7.5YR', 'value' => '5', 'chroma' => '8', 'name' => 'strong brown', 'hex' => '#ae5b05'],

        // Value 4/
        '7.5YR 4/1' => ['hue' => '7.5YR', 'value' => '4', 'chroma' => '1', 'name' => 'dark gray', 'hex' => '#5f524b'],
        '7.5YR 4/2' => ['hue' => '7.5YR', 'value' => '4', 'chroma' => '2', 'name' => 'brown', 'hex' => '#6e5140'],
        '7.5YR 4/3' => ['hue' => '7.5YR', 'value' => '4', 'chroma' => '3', 'name' => 'brown', 'hex' => '#744b32'],
        '7.5YR 4/4' => ['hue' => '7.5YR', 'value' => '4', 'chroma' => '4', 'name' => 'brown', 'hex' => '#7b4019'],
        '7.5YR 4/6' => ['hue' => '7.5YR', 'value' => '4', 'chroma' => '6', 'name' => 'strong brown', 'hex' => '#86430d'],

        // Value 3/
        '7.5YR 3/1' => ['hue' => '7.5YR', 'value' => '3', 'chroma' => '1', 'name' => 'very dark gray', 'hex' => '#443530'],
        '7.5YR 3/2' => ['hue' => '7.5YR', 'value' => '3', 'chroma' => '2', 'name' => 'dark brown', 'hex' => '#4c3420'],
        '7.5YR 3/3' => ['hue' => '7.5YR', 'value' => '3', 'chroma' => '3', 'name' => 'dark brown', 'hex' => '#522c15'],
        '7.5YR 3/4' => ['hue' => '7.5YR', 'value' => '3', 'chroma' => '4', 'name' => 'dark brown', 'hex' => '#592c11'],

        // Value 2.5/
        '7.5YR 2.5/1' => ['hue' => '7.5YR', 'value' => '2.5', 'chroma' => '1', 'name' => 'black', 'hex' => '#312420'],
        '7.5YR 2.5/2' => ['hue' => '7.5YR', 'value' => '2.5', 'chroma' => '2', 'name' => 'very dark brown', 'hex' => '#39251b'],
        '7.5YR 2.5/3' => ['hue' => '7.5YR', 'value' => '2.5', 'chroma' => '3', 'name' => 'very dark brown', 'hex' => '#432315'],
    ];

    /**
     * Calibrated standard 5YR Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated5YRMatrix = [
        // Value 8/
        '5YR 8/1' => ['hue' => '5YR', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#dfc9c8'],
        '5YR 8/2' => ['hue' => '5YR', 'value' => '8', 'chroma' => '2', 'name' => 'pinkish white', 'hex' => '#ebc4bd'],
        '5YR 8/3' => ['hue' => '5YR', 'value' => '8', 'chroma' => '3', 'name' => 'pink', 'hex' => '#efbdab'],
        '5YR 8/4' => ['hue' => '5YR', 'value' => '8', 'chroma' => '4', 'name' => 'pink', 'hex' => '#f7b499'],

        // Value 7/
        '5YR 7/1' => ['hue' => '5YR', 'value' => '7', 'chroma' => '1', 'name' => 'light gray', 'hex' => '#b9a5a3'],
        '5YR 7/2' => ['hue' => '5YR', 'value' => '7', 'chroma' => '2', 'name' => 'pinkish gray', 'hex' => '#c39c90'],
        '5YR 7/3' => ['hue' => '5YR', 'value' => '7', 'chroma' => '3', 'name' => 'pink', 'hex' => '#ce967e'],
        '5YR 7/4' => ['hue' => '5YR', 'value' => '7', 'chroma' => '4', 'name' => 'pink', 'hex' => '#d88c6a'],
        '5YR 7/6' => ['hue' => '5YR', 'value' => '7', 'chroma' => '6', 'name' => 'reddish yellow', 'hex' => '#e38558'],
        '5YR 7/8' => ['hue' => '5YR', 'value' => '7', 'chroma' => '8', 'name' => 'reddish yellow', 'hex' => '#e8814d'],

        // Value 6/
        '5YR 6/1' => ['hue' => '5YR', 'value' => '6', 'chroma' => '1', 'name' => 'gray', 'hex' => '#9e8a86'],
        '5YR 6/2' => ['hue' => '5YR', 'value' => '6', 'chroma' => '2', 'name' => 'pinkish gray', 'hex' => '#a88075'],
        '5YR 6/3' => ['hue' => '5YR', 'value' => '6', 'chroma' => '3', 'name' => 'light reddish brown', 'hex' => '#b07b67'],
        '5YR 6/4' => ['hue' => '5YR', 'value' => '6', 'chroma' => '4', 'name' => 'light reddish brown', 'hex' => '#ba7252'],
        '5YR 6/6' => ['hue' => '5YR', 'value' => '6', 'chroma' => '6', 'name' => 'reddish yellow', 'hex' => '#c26a3b'],
        '5YR 6/8' => ['hue' => '5YR', 'value' => '6', 'chroma' => '8', 'name' => 'reddish yellow', 'hex' => '#d0642b'],

        // Value 5/
        '5YR 5/1' => ['hue' => '5YR', 'value' => '5', 'chroma' => '1', 'name' => 'gray', 'hex' => '#7a6a64'],
        '5YR 5/2' => ['hue' => '5YR', 'value' => '5', 'chroma' => '2', 'name' => 'reddish gray', 'hex' => '#886a5c'],
        '5YR 5/3' => ['hue' => '5YR', 'value' => '5', 'chroma' => '3', 'name' => 'reddish brown', 'hex' => '#8d5f48'],
        '5YR 5/4' => ['hue' => '5YR', 'value' => '5', 'chroma' => '4', 'name' => 'reddish brown', 'hex' => '#9e5d37'],
        '5YR 5/6' => ['hue' => '5YR', 'value' => '5', 'chroma' => '6', 'name' => 'yellowish red', 'hex' => '#aa5321'],
        '5YR 5/8' => ['hue' => '5YR', 'value' => '5', 'chroma' => '8', 'name' => 'yellowish red', 'hex' => '#bf500e'],

        // Value 4/
        '5YR 4/1' => ['hue' => '5YR', 'value' => '4', 'chroma' => '1', 'name' => 'dark gray', 'hex' => '#62544e'],
        '5YR 4/2' => ['hue' => '5YR', 'value' => '4', 'chroma' => '2', 'name' => 'dark reddish gray', 'hex' => '#694b3e'],
        '5YR 4/3' => ['hue' => '5YR', 'value' => '4', 'chroma' => '3', 'name' => 'reddish brown', 'hex' => '#74462f'],
        '5YR 4/4' => ['hue' => '5YR', 'value' => '4', 'chroma' => '4', 'name' => 'reddish brown', 'hex' => '#863d1b'],
        '5YR 4/6' => ['hue' => '5YR', 'value' => '4', 'chroma' => '6', 'name' => 'yellowish red', 'hex' => '#953708'],

        // Value 3/
        '5YR 3/1' => ['hue' => '5YR', 'value' => '3', 'chroma' => '1', 'name' => 'very dark gray', 'hex' => '#423632'],
        '5YR 3/2' => ['hue' => '5YR', 'value' => '3', 'chroma' => '2', 'name' => 'dark reddish brown', 'hex' => '#4a3128'],
        '5YR 3/3' => ['hue' => '5YR', 'value' => '3', 'chroma' => '3', 'name' => 'dark reddish brown', 'hex' => '#562d1d'],
        '5YR 3/4' => ['hue' => '5YR', 'value' => '3', 'chroma' => '4', 'name' => 'dark reddish brown', 'hex' => '#63200a'],

        // Value 2.5/
        '5YR 2.5/1' => ['hue' => '5YR', 'value' => '2.5', 'chroma' => '1', 'name' => 'black', 'hex' => '#292322'],
        '5YR 2.5/2' => ['hue' => '5YR', 'value' => '2.5', 'chroma' => '2', 'name' => 'dark reddish brown', 'hex' => '#2f221f'],
    ];

    /**
     * Calibrated standard 2.5YR Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated2_5YRMatrix = [
        // Value 8/ (Row 1)
        '2.5YR 8/1' => ['hue' => '2.5YR', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#edd0ca'],
        '2.5YR 8/2' => ['hue' => '2.5YR', 'value' => '8', 'chroma' => '2', 'name' => 'pinkish white', 'hex' => '#edcabf'],
        '2.5YR 8/3' => ['hue' => '2.5YR', 'value' => '8', 'chroma' => '3', 'name' => 'pink', 'hex' => '#f2beae'],
        '2.5YR 8/4' => ['hue' => '2.5YR', 'value' => '8', 'chroma' => '4', 'name' => 'pink', 'hex' => '#f5b4a0'],

        // Value 7/ (Row 2)
        '2.5YR 7/1' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '1', 'name' => 'light reddish gray', 'hex' => '#cdb0a8'],
        '2.5YR 7/2' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '2', 'name' => 'pale red', 'hex' => '#d0a090'],
        '2.5YR 7/3' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '3', 'name' => 'light reddish brown', 'hex' => '#d89080'],
        '2.5YR 7/4' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '4', 'name' => 'light reddish brown', 'hex' => '#dc8870'],
        '2.5YR 7/6' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '6', 'name' => 'light red', 'hex' => '#d86050'],
        '2.5YR 7/8' => ['hue' => '2.5YR', 'value' => '7', 'chroma' => '8', 'name' => 'light red', 'hex' => '#d85040'],

        // Value 6/ (Row 3)
        '2.5YR 6/1' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '1', 'name' => 'reddish gray', 'hex' => '#aa8c88'],
        '2.5YR 6/2' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '2', 'name' => 'pale red', 'hex' => '#b08078'],
        '2.5YR 6/3' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '3', 'name' => 'light reddish brown', 'hex' => '#b87060'],
        '2.5YR 6/4' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '4', 'name' => 'light reddish brown', 'hex' => '#bc6850'],
        '2.5YR 6/6' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '6', 'name' => 'light red', 'hex' => '#c05040'],
        '2.5YR 6/8' => ['hue' => '2.5YR', 'value' => '6', 'chroma' => '8', 'name' => 'light red', 'hex' => '#c84030'],

        // Value 5/ (Row 4)
        '2.5YR 5/1' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '1', 'name' => 'reddish gray', 'hex' => '#886868'],
        '2.5YR 5/2' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '2', 'name' => 'weak red', 'hex' => '#8c5c54'],
        '2.5YR 5/3' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '3', 'name' => 'reddish brown', 'hex' => '#985048'],
        '2.5YR 5/4' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '4', 'name' => 'reddish brown', 'hex' => '#9e4438'],
        '2.5YR 5/6' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '6', 'name' => 'red', 'hex' => '#a83028'],
        '2.5YR 5/8' => ['hue' => '2.5YR', 'value' => '5', 'chroma' => '8', 'name' => 'red', 'hex' => '#b83020'],

        // Value 4/ (Row 5)
        '2.5YR 4/1' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '1', 'name' => 'dark reddish gray', 'hex' => '#685050'],
        '2.5YR 4/2' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '2', 'name' => 'weak red', 'hex' => '#6c4038'],
        '2.5YR 4/3' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '3', 'name' => 'reddish brown', 'hex' => '#743428'],
        '2.5YR 4/4' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '4', 'name' => 'reddish brown', 'hex' => '#7a2c20'],
        '2.5YR 4/6' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '6', 'name' => 'red', 'hex' => '#8a2018'],
        '2.5YR 4/8' => ['hue' => '2.5YR', 'value' => '4', 'chroma' => '8', 'name' => 'red', 'hex' => '#941810'],

        // Value 3/ (Row 6)
        '2.5YR 3/1' => ['hue' => '2.5YR', 'value' => '3', 'chroma' => '1', 'name' => 'dark reddish gray', 'hex' => '#4a3030'],
        '2.5YR 3/2' => ['hue' => '2.5YR', 'value' => '3', 'chroma' => '2', 'name' => 'dusky red', 'hex' => '#502818'],
        '2.5YR 3/3' => ['hue' => '2.5YR', 'value' => '3', 'chroma' => '3', 'name' => 'dark reddish brown', 'hex' => '#562010'],
        '2.5YR 3/4' => ['hue' => '2.5YR', 'value' => '3', 'chroma' => '4', 'name' => 'dark reddish brown', 'hex' => '#5c1a0c'],
        '2.5YR 3/6' => ['hue' => '2.5YR', 'value' => '3', 'chroma' => '6', 'name' => 'dark red', 'hex' => '#782015'],

        // Value 2.5/ (Row 7)
        '2.5YR 2.5/1' => ['hue' => '2.5YR', 'value' => '2.5', 'chroma' => '1', 'name' => 'reddish black', 'hex' => '#2e2020'],
        '2.5YR 2.5/2' => ['hue' => '2.5YR', 'value' => '2.5', 'chroma' => '2', 'name' => 'very dusky red', 'hex' => '#341810'],
        '2.5YR 2.5/3' => ['hue' => '2.5YR', 'value' => '2.5', 'chroma' => '3', 'name' => 'dark reddish brown', 'hex' => '#3a1408'],
        '2.5YR 2.5/4' => ['hue' => '2.5YR', 'value' => '2.5', 'chroma' => '4', 'name' => 'dark reddish brown', 'hex' => '#401008'],
    ];

    /**
     * Calibrated standard 2.5Y Munsell Soil Color matrix dataset with official soil color names and sRGB HEX codes.
     */
    protected static array $calibrated2_5YMatrix = [
        // Value 8/ (Row 1)
        '2.5Y 8/1' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '1', 'name' => 'white', 'hex' => '#e2d6c4'],
        '2.5Y 8/2' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '2', 'name' => 'pale brown', 'hex' => '#e6d4b2'],
        '2.5Y 8/3' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '3', 'name' => 'pale brown', 'hex' => '#edd0a0'],
        '2.5Y 8/4' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '4', 'name' => 'pale brown', 'hex' => '#edd098'],
        '2.5Y 8/6' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '6', 'name' => 'yellow', 'hex' => '#edcc70'],
        '2.5Y 8/8' => ['hue' => '2.5Y', 'value' => '8', 'chroma' => '8', 'name' => 'yellow', 'hex' => '#f0c840'],

        // Value 7/ (Row 2)
        '2.5Y 7/1' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '1', 'name' => 'light gray', 'hex' => '#c2b8a6'],
        '2.5Y 7/2' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '2', 'name' => 'light gray', 'hex' => '#c6b49e'],
        '2.5Y 7/3' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '3', 'name' => 'pale brown', 'hex' => '#c8b085'],
        '2.5Y 7/4' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '4', 'name' => 'pale brown', 'hex' => '#ccac78'],
        '2.5Y 7/6' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '6', 'name' => 'yellow', 'hex' => '#d0b055'],
        '2.5Y 7/8' => ['hue' => '2.5Y', 'value' => '7', 'chroma' => '8', 'name' => 'yellow', 'hex' => '#d8a838'],

        // Value 6/ (Row 3)
        '2.5Y 6/1' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '1', 'name' => 'gray', 'hex' => '#a49888'],
        '2.5Y 6/2' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '2', 'name' => 'light brownish gray', 'hex' => '#a89470'],
        '2.5Y 6/3' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '3', 'name' => 'light yellowish brown', 'hex' => '#ac9068'],
        '2.5Y 6/4' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '4', 'name' => 'light yellowish brown', 'hex' => '#ac8c55'],
        '2.5Y 6/6' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '6', 'name' => 'olive yellow', 'hex' => '#b49030'],
        '2.5Y 6/8' => ['hue' => '2.5Y', 'value' => '6', 'chroma' => '8', 'name' => 'olive yellow', 'hex' => '#be8c08'],

        // Value 5/ (Row 4)
        '2.5Y 5/1' => ['hue' => '2.5Y', 'value' => '5', 'chroma' => '1', 'name' => 'gray', 'hex' => '#82786c'],
        '2.5Y 5/2' => ['hue' => '2.5Y', 'value' => '5', 'chroma' => '2', 'name' => 'grayish brown', 'hex' => '#887860'],
        '2.5Y 5/3' => ['hue' => '2.5Y', 'value' => '5', 'chroma' => '3', 'name' => 'light olive brown', 'hex' => '#8e7448'],
        '2.5Y 5/4' => ['hue' => '2.5Y', 'value' => '5', 'chroma' => '4', 'name' => 'light olive brown', 'hex' => '#8e7230'],
        '2.5Y 5/6' => ['hue' => '2.5Y', 'value' => '5', 'chroma' => '6', 'name' => 'light olive brown', 'hex' => '#946c10'],

        // Value 4/ (Row 5)
        '2.5Y 4/1' => ['hue' => '2.5Y', 'value' => '4', 'chroma' => '1', 'name' => 'dark gray', 'hex' => '#605850'],
        '2.5Y 4/2' => ['hue' => '2.5Y', 'value' => '4', 'chroma' => '2', 'name' => 'dark grayish brown', 'hex' => '#66503c'],
        '2.5Y 4/3' => ['hue' => '2.5Y', 'value' => '4', 'chroma' => '3', 'name' => 'olive brown', 'hex' => '#6c5428'],
        '2.5Y 4/4' => ['hue' => '2.5Y', 'value' => '4', 'chroma' => '4', 'name' => 'olive brown', 'hex' => '#705420'],

        // Value 3/ (Row 6)
        '2.5Y 3/1' => ['hue' => '2.5Y', 'value' => '3', 'chroma' => '1', 'name' => 'very dark gray', 'hex' => '#3c3028'],
        '2.5Y 3/2' => ['hue' => '2.5Y', 'value' => '3', 'chroma' => '2', 'name' => 'very dark grayish brown', 'hex' => '#403018'],
        '2.5Y 3/3' => ['hue' => '2.5Y', 'value' => '3', 'chroma' => '3', 'name' => 'dark olive brown', 'hex' => '#342410'],

        // Value 2.5/ (Row 7)
        '2.5Y 2.5/1' => ['hue' => '2.5Y', 'value' => '2.5', 'chroma' => '1', 'name' => 'black', 'hex' => '#2a2520'],
    ];

    /**
     * Fetch Munsell notation metadata and verified sRGB/HEX values from Encycolorpedia API.
     * Fallback to calibrated standard Munsell matrices if API is offline or rate-limited.
     */
    public function fetchMunsellData(string $notation): array
    {
        $cleanNotation = trim($notation);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json, text/plain, */*',
            ])->timeout(3)->get('https://encycolorpedia.com/api', [
                'q' => $cleanNotation,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && is_array($data)) {
                    $hexRaw = $data['hex'] ?? $data['code'] ?? null;
                    $nameRaw = $data['name'] ?? $data['soil_name'] ?? null;

                    if ($hexRaw) {
                        return [
                            'munsell_notation' => $cleanNotation,
                            'color_name'       => $nameRaw ?: ($this->getCombinedMatrix()[$cleanNotation]['name'] ?? 'soil color'),
                            'hex_code'         => '#' . ltrim(strtolower($hexRaw), '#'),
                            'rgb'              => $data['rgb'] ?? null,
                            'source'           => 'encycolorpedia_api',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Encycolorpedia API lookup failed for notation {$cleanNotation}: " . $e->getMessage());
        }

        $combined = static::getCombinedMatrix();
        $fallback = $combined[$cleanNotation] ?? [
            'name' => 'soil color',
            'hex'  => '#8b5cf6',
        ];

        return [
            'munsell_notation' => $cleanNotation,
            'color_name'       => $fallback['name'],
            'hex_code'         => strtolower($fallback['hex']),
            'rgb'              => null,
            'source'           => 'calibrated_matrix',
        ];
    }

    /**
     * Get all 36 calibrated 10YR Munsell soil color entries.
     */
    public static function getCalibrated10YRMatrix(): array
    {
        return static::$calibrated10YRMatrix;
    }

    /**
     * Get all 35 calibrated 10R Munsell soil color entries.
     */
    public static function getCalibrated10RMatrix(): array
    {
        return static::$calibrated10RMatrix;
    }

    /**
     * Get all 35 calibrated 7.5YR Munsell soil color entries.
     */
    public static function getCalibrated7_5YRMatrix(): array
    {
        return static::$calibrated7_5YRMatrix;
    }

    /**
     * Get all 33 calibrated 5YR Munsell soil color entries.
     */
    public static function getCalibrated5YRMatrix(): array
    {
        return static::$calibrated5YRMatrix;
    }

    /**
     * Get all 37 calibrated 2.5YR Munsell soil color entries.
     */
    public static function getCalibrated2_5YRMatrix(): array
    {
        return static::$calibrated2_5YRMatrix;
    }

    /**
     * Get all 31 calibrated 2.5Y Munsell soil color entries.
     */
    public static function getCalibrated2_5YMatrix(): array
    {
        return static::$calibrated2_5YMatrix;
    }

    /**
     * Get combined matrix across all supported hues.
     */
    public static function getCombinedMatrix(): array
    {
        return array_merge(
            static::$calibrated10YRMatrix,
            static::$calibrated10RMatrix,
            static::$calibrated7_5YRMatrix,
            static::$calibrated5YRMatrix,
            static::$calibrated2_5YRMatrix,
            static::$calibrated2_5YMatrix
        );
    }
}
