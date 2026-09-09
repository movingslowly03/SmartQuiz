<?php

function extractText($filePath, $fileType)
{
    $fileType = strtoupper(trim($fileType));

    switch($fileType)
    {
        case "TXT":
            return extractTxt($filePath);

        case "DOCX":
            return extractDocx($filePath);

        case "PDF":
            return extractPdf($filePath);

        default:
            return "";
    }
}

/* ===========================================
   TXT
=========================================== */

function extractTxt($filePath)
{
    if(!file_exists($filePath))
    {
        return "";
    }

    return cleanText(file_get_contents($filePath));
}

/* ===========================================
   DOCX
=========================================== */

function extractDocx($filePath)
{
    if(!class_exists("ZipArchive"))
    {
        return "";
    }

    if(!file_exists($filePath))
    {
        return "";
    }

    $zip = new ZipArchive();

    if($zip->open($filePath) !== TRUE)
    {
        return "";
    }

    $xml = $zip->getFromName("word/document.xml");

    $zip->close();

    if(!$xml)
    {
        return "";
    }

    $xml = str_replace("</w:p>", "\n", $xml);
    $xml = str_replace("</w:tr>", "\n", $xml);
    $xml = str_replace("</w:tc>", " ", $xml);

    $text = strip_tags($xml);

    return cleanText($text);
}

/* ===========================================
   PDF
=========================================== */

function extractPdf($filePath)
{
    if(!file_exists($filePath))
    {
        return "";
    }

    $content = @file_get_contents($filePath);

    if(!$content)
    {
        return "";
    }

    $text = "";

    preg_match_all('/\((.*?)\)/s', $content, $matches);

    if(isset($matches[1]))
    {
        foreach($matches[1] as $match)
        {
            $text .= " ".$match;
        }
    }

    return cleanText($text);
}

/* ===========================================
   CLEAN TEXT
=========================================== */

function cleanText($text)
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

    $text = preg_replace('/[[:cntrl:]]/', ' ', $text);

    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

?>