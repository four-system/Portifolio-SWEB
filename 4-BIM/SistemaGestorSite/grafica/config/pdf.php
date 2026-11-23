<?php
/** Gerador PDF simples, sem dependências externas (relatórios textuais A4). */
function pdf_relatorio(array $linhas, string $nomeArquivo='relatorio_grafica.pdf'): void {
    $porPagina=48; $grupos=array_chunk($linhas,$porPagina); if(!$grupos)$grupos=[[]];
    $objetos=[]; $objetos[1]='<< /Type /Catalog /Pages 2 0 R >>';
    $fontId=3; $pageIds=[]; $next=4;
    foreach($grupos as $grupo){
        $pageId=$next++;$contentId=$next++;$pageIds[]=$pageId;
        $stream="BT\n/F1 10 Tf\n45 800 Td\n14 TL\n";
        foreach($grupo as $linha){
            $linha=(string)$linha;
            if(function_exists('mb_strimwidth'))$linha=mb_strimwidth($linha,0,100,'...','UTF-8'); // evita texto saindo da página
            if(function_exists('iconv')){$conv=@iconv('UTF-8','Windows-1252//TRANSLIT//IGNORE',$linha);if($conv!==false)$linha=$conv;}
            $linha=str_replace(["\\","(",")"],["\\\\","\\(","\\)"],$linha);
            $stream.='('.$linha.") Tj\nT*\n";
        }
        $stream.="ET";
        $objetos[$pageId]='<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 '.$fontId.' 0 R >> >> /Contents '.$contentId.' 0 R >>';
        $objetos[$contentId]='<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
    }
    $objetos[2]='<< /Type /Pages /Kids ['.implode(' ',array_map(fn($id)=>$id.' 0 R',$pageIds)).'] /Count '.count($pageIds).' >>';
    $objetos[$fontId]='<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
    ksort($objetos);$pdf="%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";$offsets=[0];$max=max(array_keys($objetos));
    for($i=1;$i<=$max;$i++){if(!isset($objetos[$i]))$objetos[$i]='null';$offsets[$i]=strlen($pdf);$pdf.=$i." 0 obj\n".$objetos[$i]."\nendobj\n";}
    $xref=strlen($pdf);$pdf.="xref\n0 ".($max+1)."\n0000000000 65535 f \n";for($i=1;$i<=$max;$i++)$pdf.=sprintf('%010d 00000 n ',$offsets[$i])."\n";
    $pdf.="trailer\n<< /Size ".($max+1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
    header('Content-Type: application/pdf');header('Content-Disposition: attachment; filename="'.$nomeArquivo.'"');header('Content-Length: '.strlen($pdf));echo $pdf;exit;
}
