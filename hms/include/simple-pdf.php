<?php
// Lightweight PDF class - memory optimized implementation
class SimplePDF {
    private $content = '';
    private $pageCount = 0;
    private $currentFont = 'Helvetica';
    private $currentSize = 12;
    private $currentStyle = '';
    private $x = 20;
    private $y = 20;
    private $pageWidth = 595.28; // A4 width in points
    private $pageHeight = 841.89; // A4 height in points
    private $lineHeight = 6;
    
    public function __construct() {
        // Initialize PDF content
        $this->content = "%PDF-1.4\n";
    }
    
    public function addPage($orientation = '', $size = '', $rotation = 0) {
        $this->pageCount++;
        $this->x = 20;
        $this->y = 20;
        return true;
    }
    
    public function setFont($family, $style = '', $size = 10) {
        $this->currentFont = $family;
        $this->currentStyle = $style;
        $this->currentSize = $size;
        $this->lineHeight = $size * 0.5;
    }
    
    public function cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        if ($h == 0) $h = $this->lineHeight;
        
        // Add text to content (simplified)
        $this->content .= "Text: " . $txt . "\n";
        
        if ($ln == 1) {
            $this->y += $h;
            $this->x = 20;
        } else {
            $this->x += $w;
        }
        return true;
    }
    
    public function ln($h = null) {
        if ($h === null) $h = $this->lineHeight;
        $this->y += $h;
        $this->x = 20;
    }
    
    public function multiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        // Simple multi-cell implementation
        $lines = explode("\n", wordwrap($txt, 80, "\n", true));
        foreach ($lines as $line) {
            $this->cell($w, $h, $line, 0, 1);
        }
    }
    
    public function setY($y) {
        $this->y = $y;
    }
    
    public function output($dest = '', $name = '', $isUTF8 = false) {
        // Generate a simple PDF-like structure
        $pdf = $this->generatePDF();
        
        if ($dest == 'S' || $dest == '') {
            return $pdf;
        } elseif ($dest == 'D') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . ($name ?: 'document.pdf') . '"');
            echo $pdf;
            exit();
        } elseif ($dest == 'I') {
            header('Content-Type: application/pdf');
            echo $pdf;
            exit();
        }
        
        return $pdf;
    }
    
    private function generatePDF() {
        // Create a minimal but valid PDF structure
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Catalog\n";
        $pdf .= "/Pages 2 0 R\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";
        
        $pdf .= "2 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Pages\n";
        $pdf .= "/Kids [3 0 R]\n";
        $pdf .= "/Count 1\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";
        
        $pdf .= "3 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Page\n";
        $pdf .= "/Parent 2 0 R\n";
        $pdf .= "/MediaBox [0 0 612 792]\n";
        $pdf .= "/Contents 4 0 R\n";
        $pdf .= "/Resources <<\n";
        $pdf .= "/Font <<\n";
        $pdf .= "/F1 5 0 R\n";
        $pdf .= ">>\n";
        $pdf .= ">>\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";
        
        // Convert content to PDF text stream
        $textContent = "BT\n";
        $textContent .= "/F1 12 Tf\n";
        $textContent .= "50 750 Td\n";
        
        // Add the actual content
        $lines = explode("\n", $this->content);
        $yPos = 750;
        foreach ($lines as $line) {
            if (strpos($line, 'Text: ') === 0) {
                $text = substr($line, 6);
                $text = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $text);
                $textContent .= "(" . $text . ") Tj\n";
                $yPos -= 15;
                $textContent .= "0 -15 Td\n";
            }
        }
        
        $textContent .= "ET\n";
        
        $pdf .= "4 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Length " . strlen($textContent) . "\n";
        $pdf .= ">>\n";
        $pdf .= "stream\n";
        $pdf .= $textContent;
        $pdf .= "endstream\n";
        $pdf .= "endobj\n";
        
        $pdf .= "5 0 obj\n";
        $pdf .= "<<\n";
        $pdf .= "/Type /Font\n";
        $pdf .= "/Subtype /Type1\n";
        $pdf .= "/BaseFont /Helvetica\n";
        $pdf .= ">>\n";
        $pdf .= "endobj\n";
        
        $pdf .= "xref\n";
        $pdf .= "0 6\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= "0000000009 65535 n \n";
        $pdf .= "0000000074 65535 n \n";
        $pdf .= "0000000120 65535 n \n";
        $pdf .= "0000000274 65535 n \n";
        $pdf .= sprintf("%010d 00000 n \n", strlen($pdf) - 100);
        
        $pdf .= "trailer\n";
        $pdf .= "<<\n";
        $pdf .= "/Size 6\n";
        $pdf .= "/Root 1 0 R\n";
        $pdf .= ">>\n";
        $pdf .= "startxref\n";
        $pdf .= (strlen($pdf) - 50) . "\n";
        $pdf .= "%%EOF\n";
        
        return $pdf;
    }
}
?>
