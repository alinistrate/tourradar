<?php

function xmlToCSV($text)
{
    if(! isValidXml($text)) {
        // we can throw an exception here
        return false;
    }
    $xml = simplexml_load_string($text);
    $output = "Title|Code|Duration|Inclusions|MinPrice\n";

    foreach($xml->TOUR as $item) {
        $output .= implode('|', getRowData($item)) . "\n";
    }

    return $output;
}

function isValidXml($text)
{
    $text = trim($text);
    if (empty($text)) {
        return false;
    }

    libxml_use_internal_errors(true);
    simplexml_load_string($text);
    $errors = libxml_get_errors();
    libxml_clear_errors();

    return empty($errors);
}

function getRowData($item)
{
    $output = array();
    $output[] = getTourTitle($item->Title);
    $output[] = getTourCode($item->Code);
    $output[] = getTourDuration($item->Duration);
    $output[] = getTourInclusions($item->Inclusions);
    $output[] = getTourMinPrice($item);

    return $output;
}

function getTourTitle($title)
{
    $title = (string) $title;
    return html_entity_decode($title, ENT_QUOTES, 'UTF-8');
}

function getTourCode($code)
{
    return (string) $code;
}

function getTourDuration($duration)
{
    return (int) $duration;
}

function getTourInclusions($inclusions)
{
    $inclusions = str_replace('&nbsp;', ' ', strip_tags((string) $inclusions));
    $inclusions = html_entity_decode($inclusions, ENT_QUOTES, 'UTF-8');
    $inclusions = trim(preg_replace('!\s+!', ' ', $inclusions));

    return $inclusions;
}

function getTourMinPrice($item)
{
    $itemsToCompare = array();
    foreach($item->DEP as $departure)
    {
        $departurePrice = (int) $departure['EUR'];
        $departureDiscount = (string) $departure['DISCOUNT'];
        $itemsToCompare[] = getDeparturePrice($departurePrice, $departureDiscount);
    }

    return min($itemsToCompare);
}

function getDeparturePrice($price, $discount = null)
{
    if(!empty($discount)) {
        $price = $price - ($price * ($discount / 100));
    }

    return number_format((float)$price, 2, '.', '');
}

$text = <<<XML
<?xml version="1.0"?>
<TOURS>
    <TOUR>
        <Title><![CDATA[Anzac &amp; Egypt Combo Tour]]></Title>
        <Code>AE-19</Code>
        <Duration>18</Duration>
        <Start>Istanbul</Start>
        <End>Cairo</End>
        <Inclusions>
            <![CDATA[<div style="margin: 1px 0px; padding: 1px 0px; border: 0px; outline: 0px; font-size: 14px; vertical-align: baseline; text-align: justify; line-height: 19px; color: rgb(6, 119, 179);">The tour price&nbsp; cover the following services: <b style="margin: 0px; padding: 0px; border: 0px; outline: 0px; vertical-align: baseline; background-color: transparent;">Accommodation</b>; 5, 4&nbsp;and&nbsp;3 star hotels&nbsp;&nbsp;</div>]]>
        </Inclusions>
        <DEP DepartureCode="AN-17" Starts="04/19/2015" GBP="1458" EUR="1724" USD="2350" DISCOUNT="15%" />
        <DEP DepartureCode="AN-18" Starts="04/22/2015" GBP="1558" EUR="1784" USD="2550" DISCOUNT="20%" />
        <DEP DepartureCode="AN-19" Starts="04/25/2015" GBP="1558" EUR="1784" USD="2550" />
    </TOUR>
</TOURS>
XML;

var_dump(xmlToCSV($text));