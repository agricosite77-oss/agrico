<?php

namespace App\Services;

class MunsellLookupService
{
    /**
     * Complete lookup table reference from Full-Munsell-Code.docx
     * Maps Full Munsell Code => [description, soil_organic_index, soil_type]
     */
    protected static ?array $lookupTable = null;

    public static function getLookupTable(): array
    {
        if (static::$lookupTable === null) {
            static::$lookupTable = array (
  '10R 2.5/1' => 
  array (
    'description' => 'Dark red black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Highly organic, wet/forest soil',
  ),
  '10R 2.5/2' => 
  array (
    'description' => 'Dark red',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic, red derived',
  ),
  '10R 3/1' => 
  array (
    'description' => 'Reddish black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Organic-rich red soil',
  ),
  '10R 3/2' => 
  array (
    'description' => 'Dark reddish brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic red soil',
  ),
  '10R 3/3' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Iron-rich red soil',
  ),
  '10R 3/4' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Ferric/red soil',
  ),
  '10R 3/6' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Oxidized red soil',
  ),
  '10R 4/1' => 
  array (
    'description' => 'Red gray',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic red, moist',
  ),
  '10R 4/2' => 
  array (
    'description' => 'Dark red',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Red soil with organic coating',
  ),
  '10R 4/3' => 
  array (
    'description' => 'Red brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Red-yellow soil',
  ),
  '10R 4/4' => 
  array (
    'description' => 'Red brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Ferric loam',
  ),
  '10R 4/6' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Oxisol/Ultisol-like',
  ),
  '10R 4/8' => 
  array (
    'description' => 'Bright red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Highly leached, iron-rich',
  ),
  '10R 5/1' => 
  array (
    'description' => 'Pinkish gray',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Weathered red with organic subsoil',
  ),
  '10R 5/2' => 
  array (
    'description' => 'Pale red',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weakly developed red soil',
  ),
  '10R 5/3' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Red-yellow intergrade',
  ),
  '10R 5/4' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Red-yellow soil',
  ),
  '10R 5/6' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Strongly oxidized',
  ),
  '10R 5/8' => 
  array (
    'description' => 'Bright red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Highly leached, dry',
  ),
  '10R 6/1' => 
  array (
    'description' => 'Grayish pink',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, partially leached',
  ),
  '10R 6/2' => 
  array (
    'description' => 'Pale red',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly humic red subsoil',
  ),
  '10R 6/3' => 
  array (
    'description' => 'Light reddish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Subsoil, low organic',
  ),
  '10R 6/4' => 
  array (
    'description' => 'Light reddish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon red soil',
  ),
  '10R 6/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Leached, well drained',
  ),
  '10R 6/8' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Exposed, dry, iron-rich',
  ),
  '10R 7/1' => 
  array (
    'description' => 'Pinkish white',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, light-colored',
  ),
  '10R 7/2' => 
  array (
    'description' => 'Pale pink',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly developed, light',
  ),
  '10R 7/3' => 
  array (
    'description' => 'Pink',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Subsoil, low organic',
  ),
  '10R 7/4' => 
  array (
    'description' => 'Light pinkish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached subsoil',
  ),
  '10R 7/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '10R 7/8' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Highly oxidized, dry',
  ),
  '10R 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, possibly siliceous',
  ),
  '10R 8/2' => 
  array (
    'description' => 'Pale pink',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached, eluvial',
  ),
  '10R 8/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Eluvial/leached horizon',
  ),
  '10R 8/4' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry surface',
  ),
  '2.5YR 2.5/1' => 
  array (
    'description' => 'Blackish red',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic, dark red topsoil',
  ),
  '2.5YR 2.5/2' => 
  array (
    'description' => 'Dark reddish brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic red soil',
  ),
  '2.5YR 2.5/3' => 
  array (
    'description' => 'Dark reddish brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic red soil',
  ),
  '2.5YR 2.5/4' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Red topsoil',
  ),
  '2.5YR 3/1' => 
  array (
    'description' => 'Dark reddish gray',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Moist, high organic',
  ),
  '2.5YR 3/2' => 
  array (
    'description' => 'Dark reddish brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic red soil',
  ),
  '2.5YR 3/3' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Red-brown loam',
  ),
  '2.5YR 3/4' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Ferric red soil',
  ),
  '2.5YR 3/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Red-yellow transition',
  ),
  '2.5YR 4/1' => 
  array (
    'description' => 'Reddish gray',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Moist, organic-influenced red',
  ),
  '2.5YR 4/2' => 
  array (
    'description' => 'Dark reddish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Humic red subsoil',
  ),
  '2.5YR 4/3' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Red-brown subsoil',
  ),
  '2.5YR 4/4' => 
  array (
    'description' => 'Reddish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Ferric loam',
  ),
  '2.5YR 4/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Oxidized red subsoil',
  ),
  '2.5YR 4/8' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Strongly oxidized',
  ),
  '2.5YR 5/1' => 
  array (
    'description' => 'Pinkish gray',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Weathered red, moist',
  ),
  '2.5YR 5/2' => 
  array (
    'description' => 'Pale reddish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weakly humic red',
  ),
  '2.5YR 5/3' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Red-yellow soil',
  ),
  '2.5YR 5/4' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Red-yellow Ultisol-like',
  ),
  '2.5YR 5/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached red soil',
  ),
  '2.5YR 5/8' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Highly leached, dry',
  ),
  '2.5YR 6/1' => 
  array (
    'description' => 'Pale reddish gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, light red',
  ),
  '2.5YR 6/2' => 
  array (
    'description' => 'Pale reddish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Leached red subsoil',
  ),
  '2.5YR 6/3' => 
  array (
    'description' => 'Light reddish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon, low organic',
  ),
  '2.5YR 6/4' => 
  array (
    'description' => 'Light reddish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Red-yellow subsoil',
  ),
  '2.5YR 6/6' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Well drained, leached',
  ),
  '2.5YR 6/8' => 
  array (
    'description' => 'Red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Dry, exposed red soil',
  ),
  '2.5YR 7/1' => 
  array (
    'description' => 'Pinkish white',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, eluvial',
  ),
  '2.5YR 7/2' => 
  array (
    'description' => 'Pale pink',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached red soil',
  ),
  '2.5YR 7/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '2.5YR 7/4' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Weakly developed',
  ),
  '2.5YR 7/6' => 
  array (
    'description' => 'Light yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached',
  ),
  '2.5YR 7/8' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Dry, oxidized',
  ),
  '2.5YR 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, quartz-rich',
  ),
  '2.5YR 8/2' => 
  array (
    'description' => 'Pale pink',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached horizon',
  ),
  '2.5YR 8/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light-colored surface',
  ),
  '2.5YR 8/4' => 
  array (
    'description' => 'Pale yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, low organic',
  ),
  '5YR 2.5/1' => 
  array (
    'description' => 'Black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic topsoil',
  ),
  '5YR 2.5/2' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic topsoil',
  ),
  '5YR 3/1' => 
  array (
    'description' => 'Very dark gray',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic, moist',
  ),
  '5YR 3/2' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic brown forest soil',
  ),
  '5YR 3/3' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Brown topsoil',
  ),
  '5YR 3/4' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Brown loam',
  ),
  '5YR 4/1' => 
  array (
    'description' => 'Dark gray',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Moist, organic-influenced',
  ),
  '5YR 4/2' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic brown soil',
  ),
  '5YR 4/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Brown loam topsoil',
  ),
  '5YR 4/4' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Brown mineral soil',
  ),
  '5YR 4/6' => 
  array (
    'description' => 'Dark yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Brown subsoil',
  ),
  '5YR 5/1' => 
  array (
    'description' => 'Gray',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Moist, silty brown',
  ),
  '5YR 5/2' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered brown soil',
  ),
  '5YR 5/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Typical brown soil',
  ),
  '5YR 5/4' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Brown-yellow subsoil',
  ),
  '5YR 5/6' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized brown soil',
  ),
  '5YR 5/8' => 
  array (
    'description' => 'Strong brown',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Leached, well drained',
  ),
  '5YR 6/1' => 
  array (
    'description' => 'Light gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, partially leached',
  ),
  '5YR 6/2' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Leached brown soil',
  ),
  '5YR 6/3' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly developed',
  ),
  '5YR 6/4' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon, dry',
  ),
  '5YR 6/6' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached subsoil',
  ),
  '5YR 6/8' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached',
  ),
  '5YR 7/1' => 
  array (
    'description' => 'Very pale gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, light',
  ),
  '5YR 7/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached, eluvial',
  ),
  '5YR 7/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light-colored',
  ),
  '5YR 7/4' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, low organic',
  ),
  '5YR 7/6' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '5YR 7/8' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Dry, exposed',
  ),
  '5YR 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, siliceous',
  ),
  '5YR 8/2' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached horizon',
  ),
  '5YR 8/3' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light surface',
  ),
  '5YR 8/4' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '7.5YR 2.5/1' => 
  array (
    'description' => 'Black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic topsoil',
  ),
  '7.5YR 2.5/2' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic dark topsoil',
  ),
  '7.5YR 2.5/3' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Dark brown forest soil',
  ),
  '7.5YR 3/1' => 
  array (
    'description' => 'Very dark grayish brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic, moist',
  ),
  '7.5YR 3/2' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic topsoil',
  ),
  '7.5YR 3/3' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Brown topsoil',
  ),
  '7.5YR 3/4' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Brown loam',
  ),
  '7.5YR 4/1' => 
  array (
    'description' => 'Dark grayish brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Moist, organic-rich',
  ),
  '7.5YR 4/2' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic brown soil',
  ),
  '7.5YR 4/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Brown loam',
  ),
  '7.5YR 4/4' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Typical brown soil',
  ),
  '7.5YR 4/6' => 
  array (
    'description' => 'Dark yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Yellowish brown subsoil',
  ),
  '7.5YR 5/1' => 
  array (
    'description' => 'Grayish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Weathered, moist',
  ),
  '7.5YR 5/2' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered brown soil',
  ),
  '7.5YR 5/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Brown-yellow soil',
  ),
  '7.5YR 5/4' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Yellowish brown soil',
  ),
  '7.5YR 5/6' => 
  array (
    'description' => 'Strong brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized subsoil',
  ),
  '7.5YR 5/8' => 
  array (
    'description' => 'Yellowish red',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Leached, well drained',
  ),
  '7.5YR 6/1' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, light brown',
  ),
  '7.5YR 6/2' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Leached brown soil',
  ),
  '7.5YR 6/3' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly developed',
  ),
  '7.5YR 6/4' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon, dry',
  ),
  '7.5YR 6/6' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized subsoil',
  ),
  '7.5YR 6/8' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached',
  ),
  '7.5YR 7/1' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, light',
  ),
  '7.5YR 7/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached, eluvial',
  ),
  '7.5YR 7/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light-colored',
  ),
  '7.5YR 7/4' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, low organic',
  ),
  '7.5YR 7/6' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '7.5YR 7/8' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Dry, exposed',
  ),
  '7.5YR 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, siliceous',
  ),
  '7.5YR 8/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached horizon',
  ),
  '7.5YR 8/3' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light surface',
  ),
  '7.5YR 8/4' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '7.5YR 8/6' => 
  array (
    'description' => 'Bright yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '10YR 2/1' => 
  array (
    'description' => 'Black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Peaty/high organic topsoil',
  ),
  '10YR 2/2' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic dark topsoil',
  ),
  '10YR 3/1' => 
  array (
    'description' => 'Very dark gray',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic, moist',
  ),
  '10YR 3/2' => 
  array (
    'description' => 'Very dark brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic topsoil',
  ),
  '10YR 3/3' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Brown topsoil',
  ),
  '10YR 3/4' => 
  array (
    'description' => 'Dark yellowish brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Yellowish brown loam',
  ),
  '10YR 3/6' => 
  array (
    'description' => 'Dark yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Yellowish brown subsoil',
  ),
  '10YR 4/1' => 
  array (
    'description' => 'Dark gray',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Moist, organic-influenced',
  ),
  '10YR 4/2' => 
  array (
    'description' => 'Dark brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic yellowish brown',
  ),
  '10YR 4/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Yellowish brown soil',
  ),
  '10YR 4/4' => 
  array (
    'description' => 'Dark yellowish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Typical yellowish brown',
  ),
  '10YR 4/6' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Oxidized yellowish brown',
  ),
  '10YR 5/1' => 
  array (
    'description' => 'Gray',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Moist, silty yellowish brown',
  ),
  '10YR 5/2' => 
  array (
    'description' => 'Grayish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered yellowish brown',
  ),
  '10YR 5/3' => 
  array (
    'description' => 'Brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Yellowish brown soil',
  ),
  '10YR 5/4' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Yellowish brown subsoil',
  ),
  '10YR 5/6' => 
  array (
    'description' => 'Yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized subsoil',
  ),
  '10YR 5/8' => 
  array (
    'description' => 'Deep yellowish brown',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Leached, well drained',
  ),
  '10YR 6/1' => 
  array (
    'description' => 'Light gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, partially leached',
  ),
  '10YR 6/2' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Leached yellowish brown',
  ),
  '10YR 6/3' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly developed',
  ),
  '10YR 6/4' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon, dry',
  ),
  '10YR 6/6' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized subsoil',
  ),
  '10YR 6/8' => 
  array (
    'description' => 'Reddish yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached',
  ),
  '10YR 7/1' => 
  array (
    'description' => 'Very pale gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, light',
  ),
  '10YR 7/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached, eluvial',
  ),
  '10YR 7/3' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light-colored',
  ),
  '10YR 7/4' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, low organic',
  ),
  '10YR 7/6' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '10YR 7/8' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Dry, exposed',
  ),
  '10YR 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, siliceous',
  ),
  '10YR 8/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached horizon',
  ),
  '10YR 8/3' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light surface',
  ),
  '10YR 8/4' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '10YR 8/6' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '10YR 8/8' => 
  array (
    'description' => 'Bright yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Exposed, highly oxidized',
  ),
  '2.5Y 2.5/1' => 
  array (
    'description' => 'Black',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic, wet/grassland',
  ),
  '2.5Y 3/1' => 
  array (
    'description' => 'Very dark gray',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'High organic, moist',
  ),
  '2.5Y 3/2' => 
  array (
    'description' => 'Very dark grayish brown',
    'soil_organic_index' => 'Very High (>8%)',
    'soil_type' => 'Humic dark soil',
  ),
  '2.5Y 3/3' => 
  array (
    'description' => 'Dark olive brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic olive soil',
  ),
  '2.5Y 4/1' => 
  array (
    'description' => 'Dark gray',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Moist, organic-influenced',
  ),
  '2.5Y 4/2' => 
  array (
    'description' => 'Dark grayish brown',
    'soil_organic_index' => 'High (6–8%)',
    'soil_type' => 'Humic grayish brown',
  ),
  '2.5Y 4/3' => 
  array (
    'description' => 'Olive brown',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Olive-brown soil',
  ),
  '2.5Y 4/4' => 
  array (
    'description' => 'Olive brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Typical olive soil',
  ),
  '2.5Y 5/1' => 
  array (
    'description' => 'Gray',
    'soil_organic_index' => 'Moderate–High (4–6%)',
    'soil_type' => 'Moist, silty olive',
  ),
  '2.5Y 5/2' => 
  array (
    'description' => 'Grayish brown',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered olive soil',
  ),
  '2.5Y 5/3' => 
  array (
    'description' => 'Light olive brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Olive subsoil',
  ),
  '2.5Y 5/4' => 
  array (
    'description' => 'Pale olive',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Weakly oxidized olive',
  ),
  '2.5Y 5/6' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized olive subsoil',
  ),
  '2.5Y 6/1' => 
  array (
    'description' => 'Light gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Weathered, leached olive',
  ),
  '2.5Y 6/2' => 
  array (
    'description' => 'Pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Leached grayish brown',
  ),
  '2.5Y 6/3' => 
  array (
    'description' => 'Pale yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'B-horizon, dry',
  ),
  '2.5Y 6/4' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Oxidized subsoil',
  ),
  '2.5Y 6/6' => 
  array (
    'description' => 'Light yellowish brown',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '2.5Y 6/8' => 
  array (
    'description' => 'Yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '2.5Y 7/1' => 
  array (
    'description' => 'Very pale gray',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly weathered, light',
  ),
  '2.5Y 7/2' => 
  array (
    'description' => 'Very pale brown',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached, eluvial',
  ),
  '2.5Y 7/3' => 
  array (
    'description' => 'Very pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light-colored',
  ),
  '2.5Y 7/4' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, low organic',
  ),
  '2.5Y 7/6' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '2.5Y 7/8' => 
  array (
    'description' => 'Bright yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Exposed, dry',
  ),
  '2.5Y 8/1' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Moderate (3–4%)',
    'soil_type' => 'Highly leached, calcareous/siliceous',
  ),
  '2.5Y 8/2' => 
  array (
    'description' => 'White',
    'soil_organic_index' => 'Low–Moderate (2–3%)',
    'soil_type' => 'Bleached horizon',
  ),
  '2.5Y 8/3' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Dry, light surface',
  ),
  '2.5Y 8/4' => 
  array (
    'description' => 'Pale yellow',
    'soil_organic_index' => 'Low (1–2%)',
    'soil_type' => 'Leached, dry',
  ),
  '2.5Y 8/6' => 
  array (
    'description' => 'Yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Strongly leached, dry',
  ),
  '2.5Y 8/8' => 
  array (
    'description' => 'Bright yellow',
    'soil_organic_index' => 'Very Low (<1%)',
    'soil_type' => 'Exposed, highly oxidized',
  ),
);
        }

        return static::$lookupTable;
    }

    public static function lookup(string $fullCode): ?array
    {
        $code = trim($fullCode);
        $table = static::getLookupTable();

        if (isset($table[$code])) {
            return $table[$code];
        }

        // Try normalized spacing (e.g. "10YR 4/3" vs "10YR  4/3")
        $normalized = preg_replace('/\s+/', ' ', $code);
        return $table[$normalized] ?? null;
    }

    public static function getSoilOrganicIndex(string $fullCode): ?string
    {
        $item = static::lookup($fullCode);
        return $item['soil_organic_index'] ?? null;
    }

    public static function getSoilType(string $fullCode): ?string
    {
        $item = static::lookup($fullCode);
        return $item['soil_type'] ?? null;
    }

    public static function getDescription(string $fullCode): ?string
    {
        $item = static::lookup($fullCode);
        return $item['description'] ?? null;
    }

    /**
     * Returns ordered list of unique SOC estimate ranges
     */
    public static function getAllOrganicIndices(): array
    {
        return [
            'Very High (>8%)',
            'High (6–8%)',
            'Moderate–High (4–6%)',
            'Moderate (3–4%)',
            'Low–Moderate (2–3%)',
            'Low (1–2%)',
            'Very Low (<1%)',
        ];
    }

    /**
     * Returns all unique Soil Types sorted alphabetically
     */
    public static function getAllSoilTypes(): array
    {
        $table = static::getLookupTable();
        $types = [];
        foreach ($table as $row) {
            if (!empty($row['soil_type'])) {
                $types[$row['soil_type']] = true;
            }
        }
        $list = array_keys($types);
        sort($list, SORT_NATURAL | SORT_FLAG_CASE);
        return $list;
    }
}
